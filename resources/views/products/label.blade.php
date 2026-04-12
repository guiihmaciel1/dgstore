<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Etiqueta - {{ $product->full_name }}</title>
    <style>
        @page {
            size: 7cm 5cm;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 7cm;
            height: 5cm;
        }
        .label {
            width: 7cm;
            height: 5cm;
            padding: 0.4cm 0.5cm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            page-break-after: always;
            position: relative;
        }
        .label:last-child {
            page-break-after: avoid;
        }
        .store-name {
            font-size: 9px;
            font-weight: 700;
            color: #111;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .product-name {
            font-size: 11px;
            font-weight: 700;
            color: #111;
            line-height: 1.3;
            margin-bottom: 3px;
        }
        .condition-badge {
            display: inline-block;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 2px 6px;
            border-radius: 3px;
            margin-bottom: 4px;
        }
        .condition-new {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .condition-used {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .price {
            font-size: 22px;
            font-weight: 900;
            color: #111;
            line-height: 1;
            margin: 2px 0;
        }
        .price-label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .details {
            font-size: 7px;
            color: #888;
            margin-top: 2px;
        }
        .battery {
            font-size: 8px;
            color: #444;
            font-weight: 600;
        }
        .sku {
            font-size: 7px;
            color: #aaa;
            font-family: 'Courier New', monospace;
        }
        .bottom-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
    </style>
</head>
<body>
    @foreach($products as $product)
    <div class="label">
        <div>
            <div class="store-name">DG Store</div>
            <div class="product-name">{{ $product->full_name }}</div>
            @php
                $isNew = $product->condition->value === 'new';
            @endphp
            <span class="condition-badge {{ $isNew ? 'condition-new' : 'condition-used' }}">
                {{ $isNew ? 'Novo' : 'Seminovo' }}
            </span>
        </div>

        <div>
            <div class="price-label">Preço</div>
            <div class="price">R$ {{ number_format((float) $product->sale_price, 2, ',', '.') }}</div>
        </div>

        <div class="bottom-row">
            <div>
                @if(!$isNew && $product->battery_health)
                    <div class="battery">Bateria: {{ $product->battery_health }}%</div>
                @endif
                @if($product->storage)
                    <span class="details">{{ $product->storage }}</span>
                @endif
            </div>
            <div class="sku">{{ $product->sku }}</div>
        </div>
    </div>
    @endforeach
</body>
</html>
