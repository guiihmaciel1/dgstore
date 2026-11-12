<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conferência de caixa
    |--------------------------------------------------------------------------
    |
    | Em horário comercial, a home pede para conferir banco e espécie a cada
    | "interval_hours". Dias: 1 = segunda ... 7 = domingo.
    |
    */

    'cash_check' => [
        'interval_hours' => (int) env('CASH_CHECK_INTERVAL_HOURS', 3),
        'days' => array_map('intval', explode(',', env('CASH_CHECK_DAYS', '1,2,3,4,5,6'))),
        'start_hour' => (int) env('CASH_CHECK_START_HOUR', 9),
        'end_hour' => (int) env('CASH_CHECK_END_HOUR', 18),
    ],

];
