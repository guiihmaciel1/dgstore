<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pipeline de Vendas (CRM)
    |--------------------------------------------------------------------------
    |
    | Desativado por padrão. Enquanto estiver desligado, todas as rotas do CRM
    | exibem a tela "Pipeline desativado" e os jobs/alertas do CRM ficam pausados.
    |
    | Para reativar, defina CRM_PIPELINE_ENABLED=true no .env (e rode config:cache
    | em produção). Atenção: o webhook da UazAPI NÃO cria mais clientes/leads
    | automaticamente, independentemente desta chave.
    |
    */

    'pipeline_enabled' => (bool) env('CRM_PIPELINE_ENABLED', false),

];
