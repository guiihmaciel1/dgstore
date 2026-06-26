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
        <div class="store-info">Catanduva - SP</div>
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
            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQwIiBoZWlnaHQ9IjcwIiB2aWV3Qm94PSIwIDAgMjQwIDcwIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxwYXRoIGQ9Ik0yMCw0NSBDMjUsNTAgMzUsNTIgNDAsNDUgQzQ1LDM4IDM1LDI1IDMwLDIwIEMyNSwxNSAyMCwyMCAxOCwyOCBDMTYsMzYgMjAsNDggMzAsNTAgQzQwLDUyIDUwLDQ1IDU1LDM4IEM2MCwzMSA1OCwyNSA1NSwyOCBDNTIsMzEgNTAsNDAgNTUsNDUgQzYwLDUwIDcwLDQ1IDc1LDQwIiBmaWxsPSJub25lIiBzdHJva2U9IiMxYTIzN2UiIHN0cm9rZS13aWR0aD0iMi41IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz48cGF0aCBkPSJNODAsMjAgQzg1LDE4IDk1LDIwIDk4LDI4IEMxMDEsMzYgOTUsNDggODgsNTAgQzgxLDUyIDc4LDQ1IDgwLDM4IEM4MiwzMSA5MCwyOCA5NSwzMCBDMTAwLDMyIDEwMiw0MCAxMDAsNDUiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzFhMjM3ZSIgc3Ryb2tlLXdpZHRoPSIyLjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPjxwYXRoIGQ9Ik0xMTUsMzUgQzExOCwyMCAxMjIsMTUgMTI4LDIwIEMxMzQsMjUgMTMwLDQwIDEyNSw0NSBDMTIwLDUwIDExNSw0NSAxMTgsMzggQzEyMSwzMSAxMzAsMjggMTM1LDMyIiBmaWxsPSJub25lIiBzdHJva2U9IiMxYTIzN2UiIHN0cm9rZS13aWR0aD0iMi41IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz48cGF0aCBkPSJNMTM4LDI4IEMxNDIsMjUgMTQ4LDIyIDE1MiwyNSBDMTU2LDI4IDE1NCwzNSAxNTAsMzggQzE0Niw0MSAxNDIsNDAgMTQyLDM4IiBmaWxsPSJub25lIiBzdHJva2U9IiMxYTIzN2UiIHN0cm9rZS13aWR0aD0iMi41IiBzdHJva2UtbGluZWNhcD0icm91bmQiLz48cGF0aCBkPSJNMTU1LDI1IEMxNTgsMjIgMTYyLDIwIDE2NSwyMiBDMTY4LDI0IDE2OCwzMCAxNjUsMzUgQzE2Miw0MCAxNTgsMzggMTYwLDMzIEMxNjIsMjggMTY4LDI1IDE3MiwzMCBDMTc2LDM1IDE3NCw0MiAxNzgsNDAiIGZpbGw9Im5vbmUiIHN0cm9rZT0iIzFhMjM3ZSIgc3Ryb2tlLXdpZHRoPSIyLjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPjxwYXRoIGQ9Ik0xODAsMjggQzE4MywyMiAxODgsMjAgMTkyLDI1IEMxOTYsMzAgMTk0LDM4IDE5MCw0MCBDMTg2LDQyIDE4NCwzOCAxODYsMzMgQzE4OCwyOCAxOTQsMjUgMTk4LDMwIEMyMDIsMzUgMjAwLDQyIDIwNSw0MCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMWEyMzdlIiBzdHJva2Utd2lkdGg9IjIuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PHBhdGggZD0iTTIwNSwzMCBDMjEwLDI1IDIxNSwyOCAyMTgsMzUgQzIyMSw0MiAyMTgsNDggMjE1LDQ1IEMyMTIsNDIgMjE1LDM1IDIyMCwzMiIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMWEyMzdlIiBzdHJva2Utd2lkdGg9IjIuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PHBhdGggZD0iTTUsMjAgQzEwLDE4IDE1LDIyIDE4LDMwIEMyMSwzOCAxNSw1MiAzMCw1NSBDNDUsNTggNzAsNTIgMTEwLDUwIEMxNTAsNDggMTkwLDUyIDIyMCw0OCIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjMWEyMzdlIiBzdHJva2Utd2lkdGg9IjEiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWRhc2hhcnJheT0iMiw0IiBvcGFjaXR5PSIwLjQiLz48L3N2Zz4=" alt="Assinatura DG Store" style="width: 200px; height: 55px;">
        </div>
        <div class="signature-name">DG Store</div>
        <div class="signature-location">Catanduva - SP</div>
    </div>

    {{-- Rodapé --}}
    <div class="footer">
        <div class="thanks">Obrigado pela preferência!</div>
        <div class="emission">Documento emitido em {{ now()->format('d/m/Y \à\s H:i') }}</div>
    </div>
</body>
</html>
