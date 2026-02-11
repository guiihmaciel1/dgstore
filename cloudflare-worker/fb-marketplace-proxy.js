/**
 * Cloudflare Worker - Facebook Marketplace GraphQL Proxy
 *
 * Faz proxy das requisições GraphQL para o Facebook Marketplace.
 * Inclui obtenção automática do token LSD (anti-CSRF) necessário
 * para que o Facebook aceite as requisições.
 *
 * Free tier: 100.000 requests/dia
 */

const FB_GRAPHQL_URL = 'https://www.facebook.com/api/graphql/';

const BROWSER_HEADERS = {
  'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
  'Accept': '*/*',
  'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
  'Sec-Fetch-Site': 'same-origin',
  'Sec-Fetch-Mode': 'cors',
  'Sec-Fetch-Dest': 'empty',
  'Origin': 'https://www.facebook.com',
  'Referer': 'https://www.facebook.com/marketplace/',
};

// URLs para tentar extrair o token LSD
const LSD_URLS = [
  'https://www.facebook.com/',
  'https://m.facebook.com/',
  'https://www.facebook.com/marketplace/',
];

// Cache do token LSD (dura ~30min)
let cachedLsd = null;
let cachedLsdTime = 0;
const LSD_CACHE_TTL = 1800000; // 30 minutos

export default {
  async fetch(request, env) {
    // CORS preflight
    if (request.method === 'OPTIONS') {
      return new Response(null, {
        headers: {
          'Access-Control-Allow-Origin': '*',
          'Access-Control-Allow-Methods': 'POST, OPTIONS',
          'Access-Control-Allow-Headers': 'Content-Type, X-Proxy-Secret',
        },
      });
    }

    if (request.method !== 'POST') {
      return new Response(JSON.stringify({ error: 'Method not allowed' }), {
        status: 405,
        headers: { 'Content-Type': 'application/json' },
      });
    }

    // Verificar secret (opcional)
    if (env.PROXY_SECRET) {
      const secret = request.headers.get('X-Proxy-Secret');
      if (secret !== env.PROXY_SECRET) {
        return new Response(JSON.stringify({ error: 'Unauthorized' }), {
          status: 401,
          headers: { 'Content-Type': 'application/json' },
        });
      }
    }

    try {
      // 1. Obter token LSD do Facebook
      const lsd = await getLsdToken();

      // 2. Ler o body original
      const originalBody = await request.text();

      // 3. Preparar body com LSD (se disponível)
      let finalBody = originalBody;
      if (lsd && !originalBody.includes('lsd=')) {
        finalBody = originalBody + '&lsd=' + encodeURIComponent(lsd);
      }

      // 4. Preparar headers
      const headers = { ...BROWSER_HEADERS, 'Content-Type': 'application/x-www-form-urlencoded' };
      if (lsd) {
        headers['X-FB-LSD'] = lsd;
      }

      // 5. Forward para o Facebook GraphQL
      const fbResponse = await fetch(FB_GRAPHQL_URL, {
        method: 'POST',
        headers,
        body: finalBody,
      });

      const fbBody = await fbResponse.text();

      return new Response(fbBody, {
        status: fbResponse.status,
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
          'X-FB-Status': fbResponse.status.toString(),
          'X-LSD-Used': lsd ? lsd.substring(0, 8) + '...' : 'none',
        },
      });
    } catch (error) {
      return new Response(JSON.stringify({ error: error.message }), {
        status: 500,
        headers: { 'Content-Type': 'application/json' },
      });
    }
  },
};

/**
 * Obtém o token LSD visitando páginas do Facebook.
 * Tenta múltiplas URLs até encontrar o token.
 */
async function getLsdToken() {
  // Usar cache se ainda válido
  if (cachedLsd && (Date.now() - cachedLsdTime) < LSD_CACHE_TTL) {
    return cachedLsd;
  }

  for (const url of LSD_URLS) {
    const token = await extractLsdFromUrl(url);
    if (token) {
      cachedLsd = token;
      cachedLsdTime = Date.now();
      return token;
    }
  }

  return null;
}

/**
 * Faz GET em uma URL do Facebook e extrai o token LSD do HTML.
 */
async function extractLsdFromUrl(url) {
  try {
    const response = await fetch(url, {
      headers: {
        'User-Agent': BROWSER_HEADERS['User-Agent'],
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Upgrade-Insecure-Requests': '1',
      },
      redirect: 'follow',
    });

    if (!response.ok) return null;

    const html = await response.text();
    return extractLsdFromHtml(html);
  } catch {
    return null;
  }
}

/**
 * Extrai o token LSD de um HTML do Facebook usando vários padrões regex.
 */
function extractLsdFromHtml(html) {
  // Padrão 1: input hidden name="lsd" value="XXXXX" (formulário de login)
  let match = html.match(/name="lsd"\s+value="([^"]+)"/);
  if (match) return match[1];

  // Padrão 2: "LSD",[],{"token":"XXXXX"} (React Server Component)
  match = html.match(/"LSD"\s*,\s*\[\]\s*,\s*\{\s*"token"\s*:\s*"([^"]+)"/);
  if (match) return match[1];

  // Padrão 3: "lsd":"XXXXX" (inline JSON)
  match = html.match(/"lsd"\s*:\s*"([A-Za-z0-9_-]+)"/);
  if (match) return match[1];

  // Padrão 4: lsd=XXXXX em URL ou parâmetro
  match = html.match(/["&?]lsd=([A-Za-z0-9_-]+)/);
  if (match) return match[1];

  // Padrão 5: DTSGInitialData / DTSGInitData com token
  match = html.match(/DTSG(?:Initial)?Data.*?"token"\s*:\s*"([^"]+)"/);
  if (match) return match[1];

  return null;
}
