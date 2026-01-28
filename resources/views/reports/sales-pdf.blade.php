<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Vendas</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 20px; }
        .period { margin-top: 5px; color: #666; }
        .summary { display: table; width: 100%; margin-bottom: 20px; }
        .summary-item { display: table-cell; width: 25%; text-align: center; padding: 10px; border: 1px solid #ddd; }
        .summary-value { font-size: 18px; font-weight: bold; color: #333; }
        .summary-label { font-size: 10px; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        .text-right { text-align: right; }
        .section-title { font-size: 14px; font-weight: bold; margin: 15px 0 10px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logodg.png') }}" alt="DG Store" style="height: 60px; margin-bottom: 10px;">
        <h1>Relatório de Vendas</h1>
        <p class="period">Período: {{ $report['period']['start'] }} a {{ $report['period']['end'] }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="summary-value">{{ $report['summary']['total_sales'] }}</div>
            <div class="summary-label">TOTAL DE VENDAS</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($report['summary']['total_revenue'], 2, ',', '.') }}</div>
            <div class="summary-label">FATURAMENTO</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($report['summary']['total_discount'], 2, ',', '.') }}</div>
            <div class="summary-label">DESCONTOS</div>
        </div>
        <div class="summary-item">
            <div class="summary-value">R$ {{ number_format($report['summary']['average_ticket'], 2, ',', '.') }}</div>
            <div class="summary-label">TICKET MÉDIO</div>
        </div>
    </div>

    <div class="section-title">Vendas por Forma de Pagamento</div>
    <table>
        <thead>
            <tr>
                <th>Forma de Pagamento</th>
                <th class="text-right">Quantidade</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_payment_method'] as $method => $data)
                @php $methodEnum = \App\Domain\Sale\Enums\PaymentMethod::from($method); @endphp
                <tr>
                    <td>{{ $methodEnum->label() }}</td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">R$ {{ number_format($data['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Vendas por Vendedor</div>
    <table>
        <thead>
            <tr>
                <th>Vendedor</th>
                <th class="text-right">Quantidade</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_seller'] as $seller)
                <tr>
                    <td>{{ $seller['seller_name'] }}</td>
                    <td class="text-right">{{ $seller['count'] }}</td>
                    <td class="text-right">R$ {{ number_format($seller['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Lista de Vendas</div>
    <table>
        <thead>
            <tr>
                <th>Venda</th>
                <th>Data</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th>Status</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['sales'] as $sale)
                <tr>
                    <td>{{ $sale->sale_number }}</td>
                    <td>{{ $sale->sold_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->customer?->name ?? '-' }}</td>
                    <td>{{ $sale->user?->name }}</td>
                    <td>{{ $sale->payment_status->label() }}</td>
                    <td class="text-right">R$ {{ number_format($sale->total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="text-align: center; color: #666; margin-top: 30px; font-size: 10px;">
        Relatório gerado em {{ now()->format('d/m/Y H:i') }}
    </p>
</body>
</html>
