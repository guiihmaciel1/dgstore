@php
    $product = $warranty->saleItem?->product;
    $sale = $warranty->saleItem?->sale;
    $soldAt = $sale?->sold_at;
    $months = $warranty->customer_warranty_months;
    $days = $months * 30;

    $extenso = [
        30 => '30 (trinta)',
        60 => '60 (sessenta)',
        90 => '90 (noventa)',
        120 => '120 (cento e vinte)',
        180 => '180 (cento e oitenta)',
        360 => '360 (trezentos e sessenta)',
    ];
    $diasExtenso = $extenso[$days] ?? $days;

    $mesesPt = [
        1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
        5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
        9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
    ];

    $dataFormatada = $soldAt
        ? $soldAt->format('d') . ' de ' . $mesesPt[(int)$soldAt->format('m')] . ' de ' . $soldAt->format('Y')
        : now()->format('d') . ' de ' . $mesesPt[(int)now()->format('m')] . ' de ' . now()->format('Y');

    $isUsed = $product && in_array($product->condition?->value, ['used', 'refurbished']);
    $customer = $sale?->customer;
    $snapshot = $warranty->saleItem?->product_snapshot ?? [];
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Declaração de Garantia - {{ $warranty->product_name }}</title>
    <style>
        @page {
            margin: 40px 50px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            color: #1a1a1a;
            padding: 0;
        }

        .header {
            text-align: center;
            padding-bottom: 18px;
            border-bottom: 3px solid #1a1a1a;
            margin-bottom: 20px;
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
            margin-bottom: 22px;
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
            margin-bottom: 18px;
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
            width: 110px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .info-table .value {
            color: #1a1a1a;
        }

        .body-text {
            font-size: 12px;
            text-align: justify;
            line-height: 1.8;
            margin-bottom: 15px;
        }

        .warranty-box {
            border: 2px solid #1a1a1a;
            padding: 15px 20px;
            margin: 20px 0;
            background-color: #fafafa;
        }

        .warranty-box-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
            margin-bottom: 10px;
            color: #1a1a1a;
        }

        .warranty-box-content {
            text-align: center;
        }

        .warranty-days {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
        }

        .warranty-period {
            font-size: 10px;
            color: #4a4a4a;
            margin-top: 4px;
        }

        .exclusions {
            margin: 18px 0;
            padding: 12px 15px;
            background-color: #f8f8f8;
            border-left: 3px solid #c62828;
        }

        .exclusions-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #c62828;
            margin-bottom: 8px;
        }

        .exclusions-list {
            width: 100%;
            border-collapse: collapse;
        }

        .exclusions-list td {
            padding: 3px 0;
            font-size: 11px;
            color: #333;
        }

        .exclusions-list .bullet {
            width: 15px;
            vertical-align: top;
            color: #c62828;
            font-weight: bold;
        }

        .location-date {
            margin-top: 25px;
            font-size: 11px;
            color: #4a4a4a;
        }

        .signature-section {
            margin-top: 50px;
            text-align: center;
        }

        .signature-line {
            width: 280px;
            border-bottom: 1px solid #1a1a1a;
            margin: 0 auto;
            padding-bottom: 8px;
        }

        .signature-name {
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
            color: #1a1a1a;
        }

        .signature-role {
            font-size: 10px;
            color: #4a4a4a;
            margin-top: 2px;
        }

        .signature-location {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 12px;
            border-top: 1px solid #ccc;
        }

        .footer .emission {
            font-size: 9px;
            color: #999;
        }

        .product-badge {
            display: inline-block;
            padding: 1px 6px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid;
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
    <div class="document-title">Declaração de Garantia</div>

    {{-- Dados do Produto e Cliente em duas colunas --}}
    <table class="two-columns">
        <tr>
            <td>
                <div class="section">
                    <div class="section-title">Produto</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nome:</td>
                            <td class="value">{{ $product?->name ?? $warranty->product_name }}</td>
                        </tr>
                        @if($product?->model)
                        <tr>
                            <td class="label">Modelo:</td>
                            <td class="value">{{ $product->model }}</td>
                        </tr>
                        @endif
                        @if($product?->storage)
                        <tr>
                            <td class="label">Armazenamento:</td>
                            <td class="value">{{ $product->storage }}</td>
                        </tr>
                        @endif
                        @if($product?->color)
                        <tr>
                            <td class="label">Cor:</td>
                            <td class="value">{{ $product->color }}</td>
                        </tr>
                        @endif
                        @if($product?->condition)
                        <tr>
                            <td class="label">Condição:</td>
                            <td class="value">
                                <span class="product-badge" style="
                                    @if($product->condition->value === 'new') color: #2e7d32; border-color: #2e7d32;
                                    @elseif($product->condition->value === 'refurbished') color: #1565c0; border-color: #1565c0;
                                    @else color: #e65100; border-color: #e65100;
                                    @endif
                                ">{{ $product->condition->label() }}</span>
                            </td>
                        </tr>
                        @endif
                        @if($product?->imei)
                        <tr>
                            <td class="label">IMEI:</td>
                            <td class="value">{{ $product->imei }}</td>
                        </tr>
                        @elseif($warranty->imei)
                        <tr>
                            <td class="label">IMEI:</td>
                            <td class="value">{{ $warranty->imei }}</td>
                        </tr>
                        @endif
                        @if($isUsed && $product?->battery_health)
                        <tr>
                            <td class="label">Bateria:</td>
                            <td class="value">{{ $product->battery_health }}%</td>
                        </tr>
                        @endif
                        @if($product?->has_box !== null)
                        <tr>
                            <td class="label">Caixa Original:</td>
                            <td class="value">{{ $product->has_box ? 'Sim' : 'Não' }}</td>
                        </tr>
                        @endif
                        @if($product?->has_cable !== null)
                        <tr>
                            <td class="label">Cabo Original:</td>
                            <td class="value">{{ $product->has_cable ? 'Sim' : 'Não' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            <td>
                @if($customer)
                <div class="section">
                    <div class="section-title">Cliente</div>
                    <table class="info-table">
                        <tr>
                            <td class="label">Nome:</td>
                            <td class="value">{{ $customer->name }}</td>
                        </tr>
                        @if($customer->cpf)
                        <tr>
                            <td class="label">CPF:</td>
                            <td class="value">{{ $customer->formatted_cpf }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Telefone:</td>
                            <td class="value">{{ $customer->formatted_phone }}</td>
                        </tr>
                        @if($customer->address)
                        <tr>
                            <td class="label">Endereço:</td>
                            <td class="value">{{ $customer->address }}</td>
                        </tr>
                        @endif
                        @if($customer->instagram)
                        <tr>
                            <td class="label">Instagram:</td>
                            <td class="value">{{ $customer->formatted_instagram }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <div class="section">
                    <div class="section-title">Dados da Compra</div>
                    <table class="info-table">
                        @if($sale)
                        <tr>
                            <td class="label">Nº Venda:</td>
                            <td class="value">{{ $sale->sale_number }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="label">Data:</td>
                            <td class="value">{{ $soldAt?->format('d/m/Y') ?? 'Não informada' }}</td>
                        </tr>
                    </table>
                </div>
                @endif
            </td>
        </tr>
    </table>

    {{-- Termos da Garantia --}}
    <div class="section">
        <div class="section-title">Termos da Garantia</div>

        <p class="body-text">
            A <strong>DG STORE</strong> declara, para os devidos fins, que o produto acima descrito foi vendido ao cliente identificado, em perfeitas condições de uso e funcionamento.
        </p>

        <div class="warranty-box">
            <div class="warranty-box-title">Período de Garantia</div>
            <div class="warranty-box-content">
                <div class="warranty-days">{{ $diasExtenso }} dias</div>
                <div class="warranty-period">
                    A partir de {{ $soldAt ? $soldAt->format('d/m/Y') : now()->format('d/m/Y') }}
                    @if($warranty->customer_warranty_until)
                        até {{ $warranty->customer_warranty_until->format('d/m/Y') }}
                    @endif
                </div>
            </div>
        </div>

        <p class="body-text">
            A garantia cobre eventuais defeitos de funcionamento de origem técnica, desde que constatados dentro do período acima e respeitadas as condições de uso adequadas do aparelho.
        </p>
    </div>

    {{-- Exclusões --}}
    <div class="exclusions">
        <div class="exclusions-title">A garantia não cobre</div>
        <table class="exclusions-list">
            <tr>
                <td class="bullet">•</td>
                <td>Danos causados por mau uso, negligência ou uso inadequado do aparelho</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td>Quedas, impactos, pressão excessiva ou contato com líquidos</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td>Intervenções, reparos ou modificações realizados por terceiros não autorizados</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td>Desgaste natural de componentes (bateria, botões, tela)</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td>Danos causados por uso de acessórios não originais ou incompatíveis</td>
            </tr>
        </table>
    </div>

    {{-- Data e Local --}}
    <div class="location-date">
        São José do Rio Preto/SP, {{ $dataFormatada }}.
    </div>

    {{-- Assinatura --}}
    <div class="signature-section">
        <div class="signature-line">
            <svg xmlns="http://www.w3.org/2000/svg" width="220" height="60" viewBox="0 0 220 60">
                <path d="M15,48 C13,42 12,35 14,28 C16,20 20,14 24,12 C30,9 38,12 42,18 C46,24 47,34 44,40 C41,46 36,50 30,50 C24,50 18,48 15,48 M15,48 C18,48 22,47 28,46" fill="none" stroke="#1a237e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M52,22 C56,18 62,16 66,18 C70,20 72,26 70,32 C68,38 62,42 56,42 C50,42 48,38 50,34 C52,30 58,28 64,30 C68,31 70,34 70,38 C70,44 66,50 60,52 C54,54 50,50 52,46" fill="none" stroke="#1a237e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M72,36 C78,32 84,30 90,32 C94,33 92,38 88,38" fill="none" stroke="#1a237e" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M95,24 C100,20 106,20 108,24 C110,28 104,32 100,34 C96,36 94,38 96,42 C98,46 104,46 108,44" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M112,16 C112,22 112,28 112,36 C112,40 114,42 117,41 M108,28 L118,28" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M120,30 C120,26 124,24 128,26 C132,28 132,34 128,38 C124,40 120,38 120,34 C120,30 122,30 126,32" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M132,38 C132,32 134,26 136,24 C138,26 140,30 142,32 C144,34 146,34 148,32" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M148,30 C152,28 156,28 158,30 C160,32 158,36 154,38 C150,40 146,38 148,36" fill="none" stroke="#1a237e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M158,34 C164,30 172,28 180,30 C188,32 194,36 200,34" fill="none" stroke="#1a237e" stroke-width="1.5" stroke-linecap="round" opacity="0.7"/>
                <path d="M12,54 C40,52 80,50 120,51 C160,52 190,50 210,48" fill="none" stroke="#1a237e" stroke-width="0.8" stroke-linecap="round" opacity="0.3"/>
            </svg>
        </div>
        <div class="signature-name">Danilo Soares Vinha</div>
        <div class="signature-role">DG STORE</div>
        <div class="signature-location">São José do Rio Preto - SP</div>
    </div>

    {{-- Rodapé --}}
    <div class="footer">
        <div class="emission">Documento emitido em {{ now()->format('d/m/Y \à\s H:i') }}</div>
    </div>
</body>
</html>
