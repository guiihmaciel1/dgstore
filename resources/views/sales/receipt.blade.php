<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprovante #{{ $sale->sale_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h3 {
            margin: 0 0 10px;
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .info-label {
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
        }
        .totals table {
            width: 50%;
            margin-left: auto;
        }
        .totals table td {
            border: none;
            padding: 5px 0;
        }
        .total-row {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000 !important;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DG Store</h1>
        <p>Loja de iPhones e Acessórios</p>
    </div>

    <div class="info-section">
        <h3>Dados da Venda</h3>
        <div class="info-row">
            <span class="info-label">Número:</span>
            <span>{{ $sale->sale_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Data:</span>
            <span>{{ $sale->sold_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Vendedor:</span>
            <span>{{ $sale->user?->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Pagamento:</span>
            <span>{{ $sale->payment_method->label() }}@if($sale->installments > 1) ({{ $sale->installments }}x)@endif</span>
        </div>
    </div>

    @if($sale->customer)
    <div class="info-section">
        <h3>Cliente</h3>
        <div class="info-row">
            <span class="info-label">Nome:</span>
            <span>{{ $sale->customer->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Telefone:</span>
            <span>{{ $sale->customer->formatted_phone }}</span>
        </div>
        @if($sale->customer->cpf)
        <div class="info-row">
            <span class="info-label">CPF:</span>
            <span>{{ $sale->customer->formatted_cpf }}</span>
        </div>
        @endif
    </div>
    @endif

    <div class="info-section">
        <h3>Itens</h3>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th class="text-center">Qtd</th>
                    <th class="text-right">Preço Unit.</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>
                        {{ $item->product_name }}<br>
                        <small>SKU: {{ $item->product_sku }}</small>
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ $item->formatted_unit_price }}</td>
                    <td class="text-right">{{ $item->formatted_subtotal }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="totals">
        <table>
            <tr>
                <td class="info-label">Subtotal:</td>
                <td class="text-right">{{ $sale->formatted_subtotal }}</td>
            </tr>
            @if($sale->discount > 0)
            <tr>
                <td class="info-label">Desconto:</td>
                <td class="text-right">-{{ $sale->formatted_discount }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td>TOTAL:</td>
                <td class="text-right">{{ $sale->formatted_total }}</td>
            </tr>
        </table>
    </div>

    @if($sale->notes)
    <div class="info-section">
        <h3>Observações</h3>
        <p>{{ $sale->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Obrigado pela preferência!</p>
        <p>Documento emitido em {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
