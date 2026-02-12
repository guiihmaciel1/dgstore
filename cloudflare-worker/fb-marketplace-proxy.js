/**
 * Cloudflare Worker - Proxy Genérico
 *
 * Rotas:
 *   POST /              → Facebook Marketplace GraphQL (com LSD automático)
 *   GET  /ml-search     → Scrape busca do ML (usados) via HTML
 *   GET  /ml/*          → Proxy direto para api.mercadolibre.com
 *   GET  /              → Status/info
 *
 * Free tier: 100.000 requests/dia
 */

const FB_GRAPHQL_URL = 'https://www.facebook.com/api/graphql/';
const ML_API_BASE = 'https://api.mercadolibre.com';

const BROWSER_UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36';

// URLs para extrair token LSD do Facebook
const LSD_URLS = [
  'https://www.facebook.com/',
  'https://m.facebook.com/',
];

let cachedLsd = null;
let cachedLsdTime = 0;
const LSD_CACHE_TTL = 1800000;

export default {
  async fetch(request, env) {
    const url = new URL(request.url);

    if (request.method === 'OPTIONS') {
      return corsResponse(null, 204);
    }

    // Verificar secret
    if (env.PROXY_SECRET) {
      const secret = request.headers.get('X-Proxy-Secret');
      if (secret !== env.PROXY_SECRET) {
        return corsResponse(JSON.stringify({ error: 'Unauthorized' }), 401);
      }
    }

    // ── Rota: ML Scrape (HTML) ─────────────────────
    if (url.pathname === '/ml-search') {
      return handleMlScrape(url);
    }

    // ── Rota: ML API Proxy ─────────────────────────
    if (url.pathname.startsWith('/ml/')) {
      return handleMlProxy(request, url);
    }

    // ── Rota: Facebook GraphQL ─────────────────────
    if (request.method === 'POST') {
      return handleFacebookGraphQL(request);
    }

    return corsResponse(JSON.stringify({
      status: 'ok',
      routes: {
        'POST /': 'Facebook Marketplace GraphQL proxy',
        'GET /ml-search?q=iphone+15+pro+max&limit=50': 'Scrape ML search (usados) via HTML',
        'GET /ml/*': 'Mercado Livre API proxy',
      },
    }), 200);
  },
};

// ── ML Scrape via HTML ─────────────────────────────

async function handleMlScrape(url) {
  const query = url.searchParams.get('q') || 'iphone';
  const limit = parseInt(url.searchParams.get('limit') || '50', 10);

  // Formato da URL de busca do ML para usados
  // "iphone 15 pro max" → "iphone-15-pro-max"
  const slug = query.toLowerCase().replace(/\s+/g, '-');
  const mlUrl = `https://lista.mercadolivre.com.br/${encodeURIComponent(slug)}_Condition_2230581_NoIndex_true`;

  try {
    const response = await fetch(mlUrl, {
      headers: {
        'User-Agent': BROWSER_UA,
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept-Encoding': 'gzip, deflate, br',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Upgrade-Insecure-Requests': '1',
        'Cache-Control': 'no-cache',
      },
      redirect: 'follow',
    });

    const html = await response.text();
    const finalUrl = response.url;

    // Verificar se recebemos a página de login (bloqueio)
    if (html.includes('loginType=negative_traffic') || html.includes('Para continuar, acesse')) {
      return corsResponse(JSON.stringify({
        success: false,
        error: 'ML blocked - login wall',
        status: response.status,
        final_url: finalUrl,
        html_length: html.length,
      }), 200);
    }

    // Extrair listings do HTML
    const listings = parseMLHtml(html, limit);

    return corsResponse(JSON.stringify({
      success: true,
      query: query,
      condition: 'used',
      total: listings.length,
      source_url: mlUrl,
      results: listings,
    }), 200);

  } catch (error) {
    return corsResponse(JSON.stringify({
      success: false,
      error: error.message,
    }), 500);
  }
}

/**
 * Extrai dados de anúncios do HTML da busca do Mercado Livre.
 */
function parseMLHtml(html, limit) {
  const listings = [];

  // Padrão 1: JSON-LD structured data (mais confiável se presente)
  const jsonLdMatch = html.match(/<script type="application\/ld\+json">([\s\S]*?)<\/script>/g);
  if (jsonLdMatch) {
    for (const script of jsonLdMatch) {
      try {
        const jsonStr = script.replace(/<\/?script[^>]*>/g, '');
        const data = JSON.parse(jsonStr);
        if (data['@type'] === 'ItemList' && data.itemListElement) {
          for (const item of data.itemListElement) {
            if (listings.length >= limit) break;
            const offer = item.item || item;
            if (offer.name && offer.offers) {
              listings.push({
                title: offer.name,
                price: parseFloat(offer.offers.price || offer.offers.lowPrice || 0),
                currency: offer.offers.priceCurrency || 'BRL',
                url: offer.url || offer['@id'] || null,
                condition: 'used',
              });
            }
          }
        }
      } catch { /* ignore parse errors */ }
    }
  }

  if (listings.length > 0) return listings;

  // Padrão 2: Regex nos elementos HTML (fallback)
  // Buscar items com preço no formato da lista de resultados
  const itemRegex = /class="ui-search-item__group[^"]*"[\s\S]*?<a[^>]*href="([^"]*)"[\s\S]*?class="ui-search-item__title[^"]*"[^>]*>([^<]+)<[\s\S]*?aria-label="([^"]*?)"/g;
  let match;
  while ((match = itemRegex.exec(html)) !== null && listings.length < limit) {
    const url = match[1];
    const title = match[2].trim();
    const priceLabel = match[3];
    const price = parseBRLPrice(priceLabel);
    if (title && price > 0) {
      listings.push({ title, price, currency: 'BRL', url, condition: 'used' });
    }
  }

  if (listings.length > 0) return listings;

  // Padrão 3: Extrair preços mais genéricos
  const priceRegex = /class="[^"]*price-tag-fraction[^"]*"[^>]*>([^<]+)</g;
  const titleRegex = /class="[^"]*ui-search-item__title[^"]*"[^>]*>([^<]+)</g;

  const prices = [];
  const titles = [];

  while ((match = priceRegex.exec(html)) !== null) {
    prices.push(parseBRLPrice(match[1]));
  }
  while ((match = titleRegex.exec(html)) !== null) {
    titles.push(match[1].trim());
  }

  const count = Math.min(titles.length, prices.length, limit);
  for (let i = 0; i < count; i++) {
    if (prices[i] > 0) {
      listings.push({
        title: titles[i],
        price: prices[i],
        currency: 'BRL',
        url: null,
        condition: 'used',
      });
    }
  }

  // Padrão 4: Tentar __PRELOADED_STATE__ ou dados embutidos
  if (listings.length === 0) {
    const stateMatch = html.match(/__PRELOADED_STATE__\s*=\s*({[\s\S]*?});\s*<\/script>/);
    if (stateMatch) {
      try {
        const state = JSON.parse(stateMatch[1]);
        const results = state?.initialState?.results || [];
        for (const item of results) {
          if (listings.length >= limit) break;
          if (item.title && item.price?.amount) {
            listings.push({
              title: item.title,
              price: item.price.amount,
              currency: item.price.currency_id || 'BRL',
              url: item.permalink || null,
              condition: item.condition || 'used',
            });
          }
        }
      } catch { /* ignore */ }
    }
  }

  return listings;
}

function parseBRLPrice(str) {
  if (!str) return 0;
  // "4.500" → 4500, "4.500,00" → 4500, "4500" → 4500
  let clean = str.replace(/[^\d.,]/g, '');
  if (/,\d{2}$/.test(clean)) {
    clean = clean.replace(/\./g, '').replace(',', '.');
  } else if (/^\d{1,3}(\.\d{3})+$/.test(clean)) {
    clean = clean.replace(/\./g, '');
  } else {
    clean = clean.replace(/,/g, '');
  }
  return parseFloat(clean) || 0;
}

// ── ML API Proxy ───────────────────────────────────

async function handleMlProxy(request, url) {
  try {
    const mlPath = url.pathname.replace('/ml/', '/');
    const mlUrl = ML_API_BASE + mlPath + url.search;

    const headers = {
      'User-Agent': BROWSER_UA,
      'Accept': 'application/json',
      'Accept-Language': 'pt-BR,pt;q=0.9',
    };

    const auth = request.headers.get('Authorization');
    if (auth) headers['Authorization'] = auth;

    const mlResponse = await fetch(mlUrl, { method: request.method, headers });
    const body = await mlResponse.text();

    return new Response(body, {
      status: mlResponse.status,
      headers: {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
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
      'User-Agent': BROWSER_UA,
      'Accept': '*/*',
      'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
      'Content-Type': 'application/x-www-form-urlencoded',
      'Sec-Fetch-Site': 'same-origin',
      'Sec-Fetch-Mode': 'cors',
      'Sec-Fetch-Dest': 'empty',
      'Origin': 'https://www.facebook.com',
      'Referer': 'https://www.facebook.com/marketplace/',
    };
    if (lsd) headers['X-FB-LSD'] = lsd;

    const fbResponse = await fetch(FB_GRAPHQL_URL, { method: 'POST', headers, body: finalBody });
    const fbBody = await fbResponse.text();

    return new Response(fbBody, {
      status: fbResponse.status,
      headers: {
        'Content-Type': 'application/json',
        'Access-Control-Allow-Origin': '*',
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

// ── LSD Token ──────────────────────────────────────

async function getLsdToken() {
  if (cachedLsd && (Date.now() - cachedLsdTime) < LSD_CACHE_TTL) return cachedLsd;
  for (const url of LSD_URLS) {
    const token = await extractLsdFromUrl(url);
    if (token) { cachedLsd = token; cachedLsdTime = Date.now(); return token; }
  }
  return null;
}

async function extractLsdFromUrl(url) {
  try {
    const r = await fetch(url, {
      headers: { 'User-Agent': BROWSER_UA, 'Accept': 'text/html', 'Accept-Language': 'pt-BR,pt;q=0.9' },
      redirect: 'follow',
    });
    if (!r.ok) return null;
    return extractLsdFromHtml(await r.text());
  } catch { return null; }
}

function extractLsdFromHtml(html) {
  let m = html.match(/name="lsd"\s+value="([^"]+)"/);
  if (m) return m[1];
  m = html.match(/"LSD"\s*,\s*\[\]\s*,\s*\{\s*"token"\s*:\s*"([^"]+)"/);
  if (m) return m[1];
  m = html.match(/"lsd"\s*:\s*"([A-Za-z0-9_-]+)"/);
  if (m) return m[1];
  return null;
}
