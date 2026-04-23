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
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Declaração de Garantia</title>
    <style>
        @page {
            margin: 60px 70px;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            line-height: 1.7;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 26px;
            font-weight: 800;
            margin: 0 0 6px 0;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .separator {
            border: none;
            border-top: 1px solid #ccc;
            margin: 30px 0;
        }
        .product-info {
            margin: 20px 0 20px 20px;
        }
        .product-info p {
            margin: 3px 0;
            font-size: 13px;
        }
        .product-info strong {
            font-weight: 700;
        }
        .customer-info {
            margin: 20px 0 20px 20px;
        }
        .customer-info p {
            margin: 3px 0;
            font-size: 13px;
        }
        .customer-info strong {
            font-weight: 700;
        }
        .body-text {
            font-size: 13px;
            text-align: justify;
        }
        .warranty-highlight {
            font-weight: 700;
        }
        .exclusions {
            margin: 15px 0 15px 10px;
            font-size: 12.5px;
        }
        .exclusions p {
            margin: 3px 0;
        }
        .exclusions-title {
            text-decoration: underline;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .location {
            margin-top: 40px;
            font-size: 13px;
        }
        .signature {
            text-align: center;
            margin-top: 80px;
        }
        .signature .name {
            font-size: 15px;
            font-weight: 700;
            font-style: italic;
            margin-bottom: 2px;
        }
        .signature .company {
            font-size: 14px;
            font-weight: 700;
            color: #2563eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DG STORE</h1>
        <h2>DECLARAÇÃO DE GARANTIA</h2>
    </div>

    <hr class="separator">

    <p class="body-text">
        A DG STORE declara, para os devidos fins, que o produto abaixo descrito:
    </p>

    <div class="product-info">
        <p><strong>Produto:</strong> {{ $product?->name ?? $warranty->product_name }}</p>
        @if($product?->storage)
            <p><strong>Armazenamento:</strong> {{ $product->storage }}</p>
        @endif
        @if($product?->color)
            <p><strong>Cor:</strong> {{ $product->color }}</p>
        @endif
        @if($isUsed && $product?->battery_health)
            <p><strong>Saúde da bateria:</strong> {{ $product->battery_health }}%</p>
        @endif
    </div>

    <p class="body-text" style="margin-top: 20px;">
        foi vendido ao cliente abaixo identificado, em perfeitas condições de uso e funcionamento:
    </p>

    @if($customer)
    <div class="customer-info">
        <p><strong>Cliente:</strong> {{ $customer->name }}</p>
        @if($customer->cpf)
            <p><strong>CPF:</strong> {{ $customer->cpf }}</p>
        @endif
        @if($customer->phone)
            <p><strong>Telefone:</strong> {{ $customer->phone }}</p>
        @endif
        @if($customer->address)
            <p><strong>Endereço:</strong> {{ $customer->address }}</p>
        @endif
    </div>
    @endif

    <p class="body-text" style="margin-top: 20px;">
        Concedemos ao cliente uma <span class="warranty-highlight">garantia de {{ $diasExtenso }} dias</span>,
        contados a partir da data de hoje ({{ $soldAt ? $soldAt->format('d/m/Y') : now()->format('d/m/Y') }}),
        cobrindo eventuais defeitos de funcionamento de origem técnica.
    </p>

    <div class="exclusions">
        <p class="exclusions-title">A garantia não cobre:</p>
        <p>- Danos causados por mau uso</p>
        <p>- Quedas, impactos ou contato com líquidos</p>
        <p>- Intervenções ou reparos realizados por terceiros não autorizados</p>
    </div>

    <p class="location">
        São José do Rio Preto/SP, {{ $dataFormatada }}
    </p>

    <div class="signature">
        <div class="name">Danilo Soares Vinha</div>
        <div class="company">DG STORE</div>
    </div>
</body>
</html>
