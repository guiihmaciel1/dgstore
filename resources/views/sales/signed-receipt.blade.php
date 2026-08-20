<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo Assinado #{{ $sale->sale_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.4;
            color: #000;
            padding: 15px 20px;
        }

        table {
            border-collapse: collapse;
        }

        .border-box {
            border: 1px solid #000;
        }

        .border-box td,
        .border-box th {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        .header-table {
            width: 100%;
            margin-bottom: 0;
        }

        .header-table td {
            border: 1px solid #000;
            vertical-align: middle;
        }

        .emitente-cell {
            width: 60%;
            padding: 10px 12px;
        }

        .emitente-razao {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .emitente-info {
            font-size: 9px;
            color: #333;
        }

        .doc-type-cell {
            width: 20%;
            text-align: center;
            padding: 8px;
        }

        .doc-type-title {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .doc-type-subtitle {
            font-size: 8px;
            color: #333;
        }

        .doc-number-cell {
            width: 20%;
            text-align: center;
            padding: 8px;
        }

        .doc-number {
            font-size: 12px;
            font-weight: bold;
        }

        .doc-serie {
            font-size: 9px;
            color: #333;
            margin-top: 2px;
        }

        .section-header {
            background-color: #e8e8e8;
            padding: 3px 6px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #000;
            border-top: none;
        }

        .data-table {
            width: 100%;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 2px 6px;
            vertical-align: top;
        }

        .field-label {
            font-size: 7px;
            color: #555;
            text-transform: uppercase;
            display: block;
            margin-bottom: 1px;
        }

        .field-value {
            font-size: 9px;
            font-weight: bold;
            color: #000;
        }

        .items-table {
            width: 100%;
            margin-top: -1px;
        }

        .items-table thead th {
            background-color: #e8e8e8;
            padding: 4px 5px;
            font-size: 7px;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
        }

        .items-table thead th:first-child {
            text-align: left;
        }

        .items-table tbody td {
            padding: 4px 5px;
            border: 1px solid #000;
            font-size: 9px;
            vertical-align: top;
        }

        .items-table .text-center {
            text-align: center;
        }

        .items-table .text-right {
            text-align: right;
        }

        .totals-row {
            width: 100%;
            margin-top: -1px;
        }

        .totals-row td {
            border: 1px solid #000;
            padding: 4px 8px;
        }

        .additional-info {
            width: 100%;
            margin-top: -1px;
        }

        .additional-info td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9px;
        }

        .signature-area {
            width: 100%;
            margin-top: 30px;
        }

        .signature-area td {
            width: 50%;
            text-align: center;
            padding: 0 30px;
            vertical-align: bottom;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
            padding-bottom: 30px;
        }

        .signature-label {
            font-size: 9px;
            color: #333;
        }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 8px;
        }

        .condition-tag {
            display: inline;
            padding: 0 3px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            border: 1px solid;
        }

        .product-specs {
            font-size: 8px;
            color: #444;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    {{-- Cabeçalho estilo DANFE --}}
    <table class="header-table">
        <tr>
            <td class="emitente-cell">
                <div class="emitente-razao">DG STORE LTDA</div>
                <div class="emitente-info">
                    CNPJ: 18.047.139/0001-56<br>
                    São José do Rio Preto - SP<br>
                    Loja de iPhones e Acessórios
                </div>
            </td>
            <td class="doc-type-cell">
                <div class="doc-type-title">RECIBO DE VENDA</div>
                <div class="doc-type-subtitle">Documento não fiscal</div>
            </td>
            <td class="doc-number-cell">
                <div class="doc-number">Nº {{ $sale->sale_number }}</div>
                <div class="doc-serie">
                    Emissão: {{ $sale->sold_at->format('d/m/Y') }}<br>
                    Hora: {{ $sale->sold_at->format('H:i') }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Destinatário / Cliente --}}
    <div class="section-header">Destinatário / Comprador</div>
    <table class="data-table">
        <tr>
            <td style="width: 55%;">
                <span class="field-label">Nome</span>
                <span class="field-value">{{ $sale->customer?->name ?? 'Consumidor Final' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="field-label">CPF</span>
                <span class="field-value">{{ $sale->customer?->formatted_cpf ?? '---' }}</span>
            </td>
            <td style="width: 20%;">
                <span class="field-label">Data da Compra</span>
                <span class="field-value">{{ $sale->sold_at->format('d/m/Y') }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="field-label">Endereço</span>
                <span class="field-value">{{ $sale->customer?->address ?? '---' }}</span>
            </td>
            <td>
                <span class="field-label">Telefone</span>
                <span class="field-value">{{ $sale->customer?->formatted_phone ?? '---' }}</span>
            </td>
            <td>
                <span class="field-label">Instagram</span>
                <span class="field-value">{{ $sale->customer?->formatted_instagram ?? '---' }}</span>
            </td>
        </tr>
    </table>

    {{-- Dados da Venda --}}
    <div class="section-header">Dados da Venda</div>
    <table class="data-table">
        <tr>
            <td style="width: 25%;">
                <span class="field-label">Vendedor</span>
                <span class="field-value">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name ?? '---' }}</span>
            </td>
            <td style="width: 20%;">
                <span class="field-label">Tipo de Venda</span>
                <span class="field-value">{{ $sale->sale_type?->label() ?? 'Cliente Final' }}</span>
            </td>
            <td style="width: 25%;">
                <span class="field-label">Forma de Pagamento</span>
                <span class="field-value">
                    {{ $sale->payment_method->label() }}@if($sale->installments > 1) ({{ $sale->installments }}x)@endif
                </span>
            </td>
            <td style="width: 15%;">
                <span class="field-label">Status Pagamento</span>
                <span class="field-value">{{ $sale->payment_status->label() }}</span>
            </td>
            <td style="width: 15%;">
                <span class="field-label">Entrega</span>
                <span class="field-value">{{ $sale->delivery_type ?? 'Retirada' }}</span>
            </td>
        </tr>
    </table>

    {{-- Pagamento Detalhado (misto) --}}
    @if($sale->hasMixedPayment())
    <div class="section-header">Detalhamento do Pagamento</div>
    <table class="data-table">
        <tr>
            @if($sale->pix_payment > 0)
            <td>
                <span class="field-label">PIX</span>
                <span class="field-value">{{ $sale->formatted_pix_payment }}</span>
            </td>
            @endif
            @if($sale->cash_payment > 0)
            <td>
                <span class="field-label">Dinheiro</span>
                <span class="field-value">{{ $sale->formatted_cash_payment }}</span>
            </td>
            @endif
            @if($sale->card_payment > 0)
            <td>
                <span class="field-label">Cartão{{ $sale->installments > 1 ? ' ('.$sale->installments.'x)' : '' }}</span>
                <span class="field-value">{{ $sale->formatted_card_payment }}</span>
            </td>
            @endif
            @if($sale->trade_in_value > 0)
            <td>
                <span class="field-label">Trade-in</span>
                <span class="field-value">{{ $sale->formatted_trade_in_value }}</span>
            </td>
            @endif
        </tr>
    </table>
    @endif

    {{-- Tabela de Produtos --}}
    <div class="section-header">Produtos / Serviços</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 35%;">Produto</th>
                <th style="width: 18%;">Descrição</th>
                <th style="width: 7%;">Qtd</th>
                <th style="width: 7%;">Cond.</th>
                <th style="width: 16%;">IMEI</th>
                <th style="width: 8%;">V. Unit.</th>
                <th style="width: 9%;">V. Total</th>
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
                    <strong>{{ $item->product_name }}</strong>
                    @if($batteryHealth !== null || $hasBox !== null || $hasCable !== null)
                    <div class="product-specs">
                        @if($batteryHealth !== null)Bat: {{ $batteryHealth }}%@endif
                        @if($hasBox !== null) &bull; Caixa: {{ $hasBox ? 'Sim' : 'Não' }}@endif
                        @if($hasCable !== null) &bull; Cabo: {{ $hasCable ? 'Sim' : 'Não' }}@endif
                    </div>
                    @endif
                </td>
                <td class="text-center">
                    @if($itemModel){{ $itemModel }}@endif
                    @if($itemStorage) {{ $itemStorage }}@endif
                    @if($itemColor) {{ $itemColor }}@endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">
                    @if($itemCondition === 'new') Novo
                    @elseif($itemCondition === 'used') Semi
                    @elseif($itemCondition === 'refurbished') Recond.
                    @else ---
                    @endif
                </td>
                <td class="text-center">{{ $itemImei ?? '---' }}</td>
                <td class="text-right">{{ $item->formatted_unit_price }}</td>
                <td class="text-right">{{ $item->formatted_subtotal }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totais --}}
    <table class="totals-row">
        <tr>
            @if($sale->discount > 0)
            <td style="width: 25%;">
                <span class="field-label">Desconto</span>
                <span class="field-value" style="color: #c00;">- {{ $sale->formatted_discount }}</span>
            </td>
            @endif
            @if($sale->trade_in_value > 0)
            <td style="width: 25%;">
                <span class="field-label">Trade-in</span>
                <span class="field-value" style="color: #060;">- {{ $sale->formatted_trade_in_value }}</span>
            </td>
            @endif
            <td style="width: {{ ($sale->discount > 0 || $sale->trade_in_value > 0) ? '25%' : '50%' }};">
                <span class="field-label">Subtotal</span>
                <span class="field-value">{{ $sale->formatted_subtotal }}</span>
            </td>
            <td style="width: {{ ($sale->discount > 0 || $sale->trade_in_value > 0) ? '25%' : '50%' }}; text-align: right;">
                <span class="field-label">Valor Total</span>
                <span class="field-value" style="font-size: 13px;">{{ $sale->formatted_total }}</span>
            </td>
        </tr>
    </table>

    {{-- Informações Adicionais --}}
    <div class="section-header">Informações Adicionais</div>
    <table class="additional-info">
        <tr>
            <td>
                @if($sale->notes)
                    {{ $sale->notes }}
                @else
                    Venda realizada na DG Store - São José do Rio Preto/SP.
                    Documento emitido em {{ now()->format('d/m/Y \à\s H:i') }}.
                @endif
            </td>
        </tr>
    </table>

    {{-- Área de Assinaturas --}}
    <table class="signature-area">
        <tr>
            <td>
                <div class="signature-line">
                    <svg xmlns="http://www.w3.org/2000/svg" width="180" height="50" viewBox="0 0 220 60">
                        <path d="M15,48 C13,42 12,35 14,28 C16,20 20,14 24,12 C30,9 38,12 42,18 C46,24 47,34 44,40 C41,46 36,50 30,50 C24,50 18,48 15,48 M15,48 C18,48 22,47 28,46" fill="none" stroke="#1a237e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M52,22 C56,18 62,16 66,18 C70,20 72,26 70,32 C68,38 62,42 56,42 C50,42 48,38 50,34 C52,30 58,28 64,30 C68,31 70,34 70,38 C70,44 66,50 60,52 C54,54 50,50 52,46" fill="none" stroke="#1a237e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M72,36 C78,32 84,30 90,32 C94,33 92,38 88,38" fill="none" stroke="#1a237e" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M95,24 C100,20 106,20 108,24 C110,28 104,32 100,34 C96,36 94,38 96,42 C98,46 104,46 108,44" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M112,16 C112,22 112,28 112,36 C112,40 114,42 117,41 M108,28 L118,28" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M120,30 C120,26 124,24 128,26 C132,28 132,34 128,38 C124,40 120,38 120,34 C120,30 122,30 126,32" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M132,38 C132,32 134,26 136,24 C138,26 140,30 142,32 C144,34 146,34 148,32" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M148,30 C152,28 156,28 158,30 C160,32 158,36 154,38 C150,40 146,38 148,36" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M158,34 C164,30 172,28 180,30 C188,32 194,36 200,34" fill="none" stroke="#1a237e" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                    </svg>
                </div>
                <div class="signature-label">
                    <strong>DG STORE LTDA</strong><br>
                    CNPJ: 18.047.139/0001-56<br>
                    Vendedor: {{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name ?? '---' }}
                </div>
            </td>
            <td>
                <div class="signature-line">&nbsp;</div>
                <div class="signature-label">
                    <strong>{{ $sale->customer?->name ?? 'Comprador' }}</strong><br>
                    CPF: {{ $sale->customer?->formatted_cpf ?? '___.___.___-__' }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Rodapé --}}
    <div class="footer">
        Documento emitido em {{ now()->format('d/m/Y \à\s H:i') }} &bull; DG Store - São José do Rio Preto/SP &bull; CNPJ: 18.047.139/0001-56
    </div>
</body>
</html>
