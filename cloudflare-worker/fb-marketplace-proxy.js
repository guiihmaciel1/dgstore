/**
 * Cloudflare Worker - Proxy Genérico
 *
 * Rotas:
 *   POST /          → Facebook Marketplace GraphQL (com LSD automático)
 *   GET  /ml/*      → Proxy para api.mercadolibre.com (contorna bloqueio de IP)
 *
 * Free tier: 100.000 requests/dia
 */

const FB_GRAPHQL_URL = 'https://www.facebook.com/api/graphql/';
const ML_API_BASE = 'https://api.mercadolibre.com';

const BROWSER_HEADERS = {
  'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',
  'Accept': '*/*',
  'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
};

// URLs para extrair token LSD do Facebook
const LSD_URLS = [
  'https://www.facebook.com/',
  'https://m.facebook.com/',
  'https://www.facebook.com/marketplace/',
];

let cachedLsd = null;
let cachedLsdTime = 0;
const LSD_CACHE_TTL = 1800000; // 30 minutos

export default {
  async fetch(request, env) {
    const url = new URL(request.url);

    // CORS preflight
    if (request.method === 'OPTIONS') {
      return corsResponse(null, 204);
    }

    // Verificar secret (opcional)
    if (env.PROXY_SECRET) {
      const secret = request.headers.get('X-Proxy-Secret');
      if (secret !== env.PROXY_SECRET) {
        return corsResponse(JSON.stringify({ error: 'Unauthorized' }), 401);
      }
    }

    // ── Rota: Mercado Livre Proxy ──────────────────
    if (url.pathname.startsWith('/ml/')) {
      return handleMlProxy(request, url);
    }

    // ── Rota: Facebook GraphQL (POST /) ────────────
    if (request.method === 'POST') {
      return handleFacebookGraphQL(request);
    }

    return corsResponse(JSON.stringify({
      status: 'ok',
      routes: {
        'POST /': 'Facebook Marketplace GraphQL proxy',
        'GET /ml/*': 'Mercado Livre API proxy (ex: /ml/sites/MLB/search?q=iphone&condition=used)',
      },
    }), 200);
  },
};

// ── Mercado Livre Proxy ────────────────────────────

async function handleMlProxy(request, url) {
  try {
    // Extrair o path após /ml/ e reconstruir a URL do ML
    const mlPath = url.pathname.replace('/ml/', '/');
    const mlUrl = ML_API_BASE + mlPath + url.search;

    // Forward headers relevantes (ex: Authorization token)
    const headers = {
      'User-Agent': BROWSER_HEADERS['User-Agent'],
      'Accept': 'application/json',
      'Accept-Language': 'pt-BR,pt;q=0.9',
    };

    // Passar Authorization se presente
    const auth = request.headers.get('Authorization');
    if (auth) {
      headers['Authorization'] = auth;
    }

    const mlResponse = await fetch(mlUrl, {
      method: request.method,
      headers,
    });

    const body = await mlResponse.text();

    return new Response(body, {
      status: mlResponse.status,
      headers: {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
        'X-ML-Status': mlResponse.status.toString(),
        'X-Proxied-URL': mlUrl,
      },
    });
  } catch (error) {
    return corsResponse(JSON.stringify({ error: error.message }), 500);
  }
}

// ── Facebook GraphQL ───────────────────────────────

async function handleFacebookGraphQL(request) {
  try {
    const lsd = await getLsdToken();
    const originalBody = await request.text();

    let finalBody = originalBody;
    if (lsd && !originalBody.includes('lsd=')) {
      finalBody = originalBody + '&lsd=' + encodeURIComponent(lsd);
    }

    const headers = {
      ...BROWSER_HEADERS,
      'Content-Type': 'application/x-www-form-urlencoded',
      'Sec-Fetch-Site': 'same-origin',
      'Sec-Fetch-Mode': 'cors',
      'Sec-Fetch-Dest': 'empty',
      'Origin': 'https://www.facebook.com',
      'Referer': 'https://www.facebook.com/marketplace/',
    };

    if (lsd) {
      headers['X-FB-LSD'] = lsd;
    }

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
    return corsResponse(JSON.stringify({ error: error.message }), 500);
  }
}

// ── Helpers ────────────────────────────────────────

function corsResponse(body, status) {
  return new Response(body, {
    status,
    headers: {
      'Content-Type': 'application/json',
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'GET, POST, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type, Authorization, X-Proxy-Secret',
    },
  });
}

// ── LSD Token (Facebook) ──────────────────────────

async function getLsdToken() {
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

async function extractLsdFromUrl(url) {
  try {
    const response = await fetch(url, {
      headers: {
        'User-Agent': BROWSER_HEADERS['User-Agent'],
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language': 'pt-BR,pt;q=0.9',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
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

function extractLsdFromHtml(html) {
  let match = html.match(/name="lsd"\s+value="([^"]+)"/);
  if (match) return match[1];

  match = html.match(/"LSD"\s*,\s*\[\]\s*,\s*\{\s*"token"\s*:\s*"([^"]+)"/);
  if (match) return match[1];

  match = html.match(/"lsd"\s*:\s*"([A-Za-z0-9_-]+)"/);
  if (match) return match[1];

  match = html.match(/["&?]lsd=([A-Za-z0-9_-]+)/);
  if (match) return match[1];

  match = html.match(/DTSG(?:Initial)?Data.*?"token"\s*:\s*"([^"]+)"/);
  if (match) return match[1];

  return null;
}
