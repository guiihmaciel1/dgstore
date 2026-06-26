<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovante #{{ $sale->sale_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #1a1a1a;
            padding: 30px 40px;
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #1a1a1a;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 4px;
        }

        .header .subtitle {
            font-size: 12px;
            color: #4a4a4a;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header .store-info {
            font-size: 10px;
            color: #666;
            margin-top: 6px;
        }

        .document-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding: 8px 0;
            background-color: #f0f0f0;
        }

        .two-columns {
            width: 100%;
            margin-bottom: 20px;
        }

        .two-columns td {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .two-columns td:first-child {
            padding-right: 15px;
        }

        .two-columns td:last-child {
            padding-left: 15px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1a1a1a;
            border-bottom: 2px solid #1a1a1a;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-table .label {
            font-weight: bold;
            color: #4a4a4a;
            width: 100px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .info-table .value {
            color: #1a1a1a;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        .items-table thead th {
            background-color: #1a1a1a;
            color: #ffffff;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .items-table thead th:first-child {
            text-align: left;
        }

        .items-table tbody td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }

        .items-table .product-name {
            font-weight: bold;
            font-size: 12px;
        }

        .items-table .product-details {
            font-size: 9px;
            color: #4a4a4a;
            margin-top: 3px;
        }

        .items-table .product-specs {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
            padding: 4px 0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .totals-section {
            width: 100%;
            margin-top: 15px;
        }

        .totals-section td:first-child {
            width: 60%;
        }

        .totals-section td:last-child {
            width: 40%;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px 10px;
        }

        .totals-table .label-col {
            text-align: right;
            font-size: 10px;
            color: #4a4a4a;
            text-transform: uppercase;
        }

        .totals-table .value-col {
            text-align: right;
            font-weight: bold;
            width: 120px;
        }

        .totals-table .total-final td {
            border-top: 2px solid #1a1a1a;
            padding-top: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .payment-detail {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f8f8;
            border: 1px solid #e0e0e0;
        }

        .payment-detail-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 6px;
            color: #4a4a4a;
        }

        .payment-detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .payment-detail-table td {
            padding: 2px 0;
            font-size: 10px;
        }

        .notes-section {
            margin-top: 20px;
            padding: 12px;
            background-color: #fafafa;
            border-left: 3px solid #4a4a4a;
        }

        .notes-section .notes-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4a4a4a;
            margin-bottom: 5px;
        }

        .notes-section p {
            font-size: 11px;
            color: #333;
        }

        .signature-section {
            margin-top: 40px;
            text-align: center;
            padding-top: 20px;
        }

        .signature-line {
            width: 280px;
            border-bottom: 1px solid #1a1a1a;
            margin: 0 auto;
            padding-bottom: 8px;
        }

        .signature-name {
            font-size: 11px;
            font-weight: bold;
            margin-top: 8px;
            color: #1a1a1a;
        }

        .signature-location {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #ccc;
        }

        .footer .thanks {
            font-size: 12px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 4px;
        }

        .footer .emission {
            font-size: 9px;
            color: #999;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 2px;
        }

        .badge-new {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .badge-used {
            background-color: #fff3e0;
            color: #e65100;
        }

        .badge-refurbished {
            background-color: #e3f2fd;
            color: #1565c0;
        }

        .condition-tag {
            display: inline-block;
            padding: 1px 5px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    {{-- Cabeçalho --}}
    <div class="header">
        <h1>DG STORE</h1>
        <div class="subtitle">Loja de iPhones e Acessórios</div>
        <div class="store-info">São José do Rio Preto - SP</div>
    </div>

    {{-- Título do Documento --}}
    <div class="document-title">Comprovante de Venda</div>

    {{-- Dados da Venda e Cliente em duas colunas --}}
    <table class="two-columns">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Dados da Venda</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nº Venda:</td>
                            <td class="value">{{ $sale->sale_number }}</td>
                        </tr>
                        <tr>
                            <td class="label">Data:</td>
                            <td class="value">{{ $sale->sold_at->format('d/m/Y \à\s H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Vendedor:</td>
                            <td class="value">{{ $sale->user?->name ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tipo:</td>
                            <td class="value">{{ $sale->sale_type?->label() ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Pagamento:</td>
                            <td class="value">
                                {{ $sale->payment_method->label() }}@if($sale->installments > 1) ({{ $sale->installments }}x de {{ $sale->formatted_installment_value }})@endif
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                @if($sale->customer)
                <div class="section">
                    <div class="section-title">Cliente</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nome:</td>
                            <td class="value">{{ $sale->customer->name }}</td>
                        </tr>
                        @if($sale->customer->cpf)
                        <tr>
                            <td class="label">CPF:</td>
                            <td class="value">{{ $sale->customer->formatted_cpf }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Telefone:</td>
                            <td class="value">{{ $sale->customer->formatted_phone }}</td>
                        </tr>
                        @if($sale->customer->address)
                        <tr>
                            <td class="label">Endereço:</td>
                            <td class="value">{{ $sale->customer->address }}</td>
                        </tr>
                        @endif
                        @if($sale->customer->instagram)
                        <tr>
                            <td class="label">Instagram:</td>
                            <td class="value">{{ $sale->customer->formatted_instagram }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Tabela de Itens --}}
    <div class="section">
        <div class="section-title">Itens da Venda</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Produto</th>
                    <th class="text-center" style="width: 8%;">Qtd</th>
                    <th class="text-right" style="width: 21%;">Preço Unit.</th>
                    <th class="text-right" style="width: 21%;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                @php
                    $snapshot = $item->product_snapshot ?? [];
                    $product = $item->product;
                    $itemModel = $snapshot['model'] ?? $product?->model ?? null;
                    $itemStorage = $snapshot['storage'] ?? $product?->storage ?? null;
                    $itemColor = $snapshot['color'] ?? $product?->color ?? null;
                    $itemCondition = $snapshot['condition'] ?? $product?->condition?->value ?? null;
                    $itemImei = $snapshot['imei'] ?? $product?->imei ?? null;
                    $batteryHealth = $product?->battery_health ?? null;
                    $hasBox = $product?->has_box ?? null;
                    $hasCable = $product?->has_cable ?? null;
                @endphp
                <tr>
                    <td>
                        <div class="product-name">
                            {{ $item->product_name }}
                            @if($itemCondition)
                                <span class="condition-tag" style="
                                    @if($itemCondition === 'new') color: #2e7d32; border-color: #2e7d32;
                                    @elseif($itemCondition === 'refurbished') color: #1565c0; border-color: #1565c0;
                                    @else color: #e65100; border-color: #e65100;
                                    @endif
                                ">
                                    @if($itemCondition === 'new') Novo
                                    @elseif($itemCondition === 'used') Seminovo
                                    @else Recondicionado
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="product-details">
                            SKU: {{ $item->product_sku }}
                            @if($itemModel) &nbsp;|&nbsp; Modelo: {{ $itemModel }} @endif
                            @if($itemStorage) &nbsp;|&nbsp; {{ $itemStorage }} @endif
                            @if($itemColor) &nbsp;|&nbsp; {{ $itemColor }} @endif
                        </div>
                        @if($itemImei || $batteryHealth !== null || $hasBox !== null || $hasCable !== null)
                        <div class="product-specs">
                            @if($itemImei) <strong>IMEI:</strong> {{ $itemImei }} @endif
                            @if($batteryHealth !== null)
                                &nbsp;&nbsp;<strong>Bateria:</strong> {{ $batteryHealth }}%
                            @endif
                            @if($hasBox !== null || $hasCable !== null)
                                <br>
                                @if($hasBox !== null)
                                    <strong>Caixa Original:</strong> {{ $hasBox ? 'Sim' : 'Não' }}
                                @endif
                                @if($hasCable !== null)
                                    &nbsp;&nbsp;<strong>Cabo Original:</strong> {{ $hasCable ? 'Sim' : 'Não' }}
                                @endif
                            @endif
                        </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $item->formatted_unit_price }}</td>
                    <td class="text-right">{{ $item->formatted_subtotal }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totais e Pagamento --}}
    <table class="totals-section">
        <tr>
            <td style="vertical-align: top;">
                {{-- Detalhamento de pagamento misto --}}
                @if($sale->hasMixedPayment())
                <div class="payment-detail">
                    <div class="payment-detail-title">Detalhamento do Pagamento</div>
                    <table class="payment-detail-table">
                        @if($sale->pix_payment > 0)
                        <tr>
                            <td>PIX:</td>
                            <td class="text-right"><strong>{{ $sale->formatted_pix_payment }}</strong></td>
                        </tr>
                        @endif
                        @if($sale->cash_payment > 0)
                        <tr>
                            <td>Dinheiro:</td>
                            <td class="text-right"><strong>{{ $sale->formatted_cash_payment }}</strong></td>
                        </tr>
                        @endif
                        @if($sale->card_payment > 0)
                        <tr>
                            <td>Cartão{{ $sale->installments > 1 ? ' ('.$sale->installments.'x)' : '' }}:</td>
                            <td class="text-right"><strong>{{ $sale->formatted_card_payment }}</strong></td>
                        </tr>
                        @endif
                        @if($sale->hasTradeIn())
                        <tr>
                            <td>Trade-in:</td>
                            <td class="text-right"><strong>{{ $sale->formatted_trade_in_value }}</strong></td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endif
            </td>
            <td style="vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td class="label-col">Subtotal:</td>
                        <td class="value-col">{{ $sale->formatted_subtotal }}</td>
                    </tr>
                    @if($sale->discount > 0)
                    <tr>
                        <td class="label-col">Desconto:</td>
                        <td class="value-col" style="color: #c62828;">- {{ $sale->formatted_discount }}</td>
                    </tr>
                    @endif
                    @if($sale->trade_in_value > 0)
                    <tr>
                        <td class="label-col">Trade-in:</td>
                        <td class="value-col" style="color: #2e7d32;">- {{ $sale->formatted_trade_in_value }}</td>
                    </tr>
                    @endif
                    <tr class="total-final">
                        <td class="label-col">TOTAL:</td>
                        <td class="value-col">{{ $sale->formatted_total }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Observações --}}
    @if($sale->notes)
    <div class="notes-section">
        <div class="notes-title">Observações</div>
        <p>{{ $sale->notes }}</p>
    </div>
    @endif

    {{-- Assinatura --}}
    <div class="signature-section">
        <div class="signature-line">
            <svg xmlns="http://www.w3.org/2000/svg" width="220" height="60" viewBox="0 0 220 60">
                <!-- Letra D cursiva -->
                <path d="M15,48 C13,42 12,35 14,28 C16,20 20,14 24,12 C30,9 38,12 42,18 C46,24 47,34 44,40 C41,46 36,50 30,50 C24,50 18,48 15,48 M15,48 C18,48 22,47 28,46" fill="none" stroke="#1a237e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Letra G cursiva -->
                <path d="M52,22 C56,18 62,16 66,18 C70,20 72,26 70,32 C68,38 62,42 56,42 C50,42 48,38 50,34 C52,30 58,28 64,30 C68,31 70,34 70,38 C70,44 66,50 60,52 C54,54 50,50 52,46" fill="none" stroke="#1a237e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Flourish entre D e G para Store -->
                <path d="M72,36 C78,32 84,30 90,32 C94,33 92,38 88,38" fill="none" stroke="#1a237e" stroke-width="1.8" stroke-linecap="round"/>
                <!-- Letra S cursiva -->
                <path d="M95,24 C100,20 106,20 108,24 C110,28 104,32 100,34 C96,36 94,38 96,42 C98,46 104,46 108,44" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Letra t cursiva -->
                <path d="M112,16 C112,22 112,28 112,36 C112,40 114,42 117,41 M108,28 L118,28" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Letra o cursiva -->
                <path d="M120,30 C120,26 124,24 128,26 C132,28 132,34 128,38 C124,40 120,38 120,34 C120,30 122,30 126,32" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Letra r cursiva -->
                <path d="M132,38 C132,32 134,26 136,24 C138,26 140,30 142,32 C144,34 146,34 148,32" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Letra e cursiva -->
                <path d="M148,30 C152,28 156,28 158,30 C160,32 158,36 154,38 C150,40 146,38 148,36" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <!-- Flourish final -->
                <path d="M158,34 C164,30 172,28 180,30 C188,32 194,36 200,34" fill="none" stroke="#1a237e" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                <!-- Underline decorativo -->
                <path d="M12,54 C40,52 80,50 120,51 C160,52 190,50 210,48" fill="none" stroke="#1a237e" stroke-width="0.8" stroke-linecap="round" opacity="0.3"/>
            </svg>
        </div>
        <div class="signature-name">DG Store</div>
        <div class="signature-location">São José do Rio Preto - SP</div>
    </div>

    {{-- Rodapé --}}
    <div class="footer">
        <div class="thanks">Obrigado pela preferência!</div>
        <div class="emission">Documento emitido em {{ now()->format('d/m/Y \à\s H:i') }}</div>
    </div>
</body>
</html>
