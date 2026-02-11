/**
 * Cloudflare Worker - Facebook Marketplace GraphQL Proxy
 *
 * Este worker faz proxy das requisições GraphQL para o Facebook Marketplace.
 * O IP do Cloudflare não é bloqueado pelo Facebook, então isso contorna
 * o bloqueio de IP de datacenter.
 *
 * Deploy:
 * 1. Acesse https://dash.cloudflare.com → Workers & Pages → Create
 * 2. Dê um nome (ex: fb-marketplace-proxy)
 * 3. Cole este código no editor
 * 4. Deploy
 * 5. Copie a URL (ex: https://fb-marketplace-proxy.seu-usuario.workers.dev)
 * 6. No .env do Laravel: FB_MARKETPLACE_PROXY_URL=https://fb-marketplace-proxy.seu-usuario.workers.dev
 *
 * Segurança (opcional):
 * - Defina a variável de ambiente PROXY_SECRET no Worker
 * - No .env do Laravel: FB_MARKETPLACE_PROXY_SECRET=seu_segredo
 * - O header X-Proxy-Secret será verificado
 *
 * Free tier: 100.000 requests/dia
 */

const FB_GRAPHQL_URL = 'https://www.facebook.com/api/graphql/';

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

    // Apenas POST
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
      // Ler o body da requisição
      const body = await request.text();

      // Forward para o Facebook GraphQL
      const fbResponse = await fetch(FB_GRAPHQL_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
          'Accept': '*/*',
          'Accept-Language': 'pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7',
          'Origin': 'https://www.facebook.com',
          'Referer': 'https://www.facebook.com/marketplace/',
          'Sec-Fetch-Site': 'same-origin',
        },
        body: body,
      });

      // Retornar a resposta do Facebook
      const fbBody = await fbResponse.text();

      return new Response(fbBody, {
        status: fbResponse.status,
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
          'X-FB-Status': fbResponse.status.toString(),
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
