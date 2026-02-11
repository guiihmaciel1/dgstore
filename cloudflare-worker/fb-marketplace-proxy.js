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
const FB_MARKETPLACE_URL = 'https://www.facebook.com/marketplace/';

const HEADERS = {
  'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
  'Accept': '*/*',
  'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
  'Sec-Fetch-Site': 'same-origin',
  'Sec-Fetch-Mode': 'cors',
  'Sec-Fetch-Dest': 'empty',
  'Origin': 'https://www.facebook.com',
  'Referer': 'https://www.facebook.com/marketplace/',
};

// Cache do token LSD (dura ~1h)
let cachedLsd = null;
let cachedLsdTime = 0;
const LSD_CACHE_TTL = 3600000; // 1 hora

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
      if (!lsd) {
        return new Response(JSON.stringify({ error: 'Failed to get LSD token' }), {
          status: 502,
          headers: { 'Content-Type': 'application/json' },
        });
      }

      // 2. Ler o body original
      const originalBody = await request.text();

      // 3. Adicionar o token LSD ao body
      const bodyWithLsd = originalBody + '&lsd=' + encodeURIComponent(lsd);

      // 4. Forward para o Facebook GraphQL
      const fbResponse = await fetch(FB_GRAPHQL_URL, {
        method: 'POST',
        headers: {
          ...HEADERS,
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-FB-LSD': lsd,
        },
        body: bodyWithLsd,
      });

      const fbBody = await fbResponse.text();

      return new Response(fbBody, {
        status: fbResponse.status,
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
          'X-FB-Status': fbResponse.status.toString(),
          'X-LSD-Used': lsd.substring(0, 8) + '...',
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
 * Obtém o token LSD visitando a página do Facebook Marketplace.
 * O token está embutido no HTML da página.
 */
async function getLsdToken() {
  // Usar cache se ainda válido
  if (cachedLsd && (Date.now() - cachedLsdTime) < LSD_CACHE_TTL) {
    return cachedLsd;
  }

  try {
    const response = await fetch(FB_MARKETPLACE_URL, {
      headers: {
        'User-Agent': HEADERS['User-Agent'],
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language': 'pt-BR,pt;q=0.9',
      },
    });

    const html = await response.text();

    // Extrair LSD token do HTML
    // Padrão 1: "LSD",[],{"token":"XXXXX"}
    let match = html.match(/"LSD"\s*,\s*\[\]\s*,\s*\{\s*"token"\s*:\s*"([^"]+)"/);
    if (match) {
      cachedLsd = match[1];
      cachedLsdTime = Date.now();
      return cachedLsd;
    }

    // Padrão 2: name="lsd" value="XXXXX"
    match = html.match(/name="lsd"\s+value="([^"]+)"/);
    if (match) {
      cachedLsd = match[1];
      cachedLsdTime = Date.now();
      return cachedLsd;
    }

    // Padrão 3: {"lsd":"XXXXX"
    match = html.match(/"lsd"\s*:\s*"([^"]+)"/);
    if (match) {
      cachedLsd = match[1];
      cachedLsdTime = Date.now();
      return cachedLsd;
    }

    return null;
  } catch (error) {
    return null;
  }
}
