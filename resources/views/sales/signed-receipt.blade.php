<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo #{{ $sale->sale_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            line-height: 1.3;
            color: #000;
            padding: 10px 15px;
        }

        table { border-collapse: collapse; width: 100%; }

        .outer-border {
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 0;
        }

        .header-row {
            width: 100%;
            border: 1px solid #000;
        }

        .header-row td {
            border: 1px solid #000;
            vertical-align: top;
            padding: 6px 8px;
        }

        .emitente-nome {
            font-size: 16px;
            font-weight: bold;
        }

        .emitente-detalhe {
            font-size: 8px;
            margin-top: 3px;
            line-height: 1.5;
        }

        .danfe-box {
            text-align: center;
            vertical-align: middle;
        }

        .danfe-title {
            font-size: 9px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .danfe-subtitle {
            font-size: 7px;
            line-height: 1.4;
        }

        .danfe-entrada {
            font-size: 10px;
            font-weight: bold;
            margin-top: 3px;
        }

        .numero-box {
            text-align: center;
            vertical-align: middle;
        }

        .numero-value {
            font-size: 11px;
            font-weight: bold;
        }

        .numero-serie {
            font-size: 8px;
            margin-top: 3px;
        }

        .section-title {
            background-color: #d9d9d9;
            border: 1px solid #000;
            border-top: none;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .fields-row {
            width: 100%;
        }

        .fields-row td {
            border: 1px solid #000;
            padding: 1px 5px 3px 5px;
            vertical-align: top;
        }

        .field-label {
            font-size: 6px;
            color: #333;
            text-transform: uppercase;
            margin-bottom: 0;
        }

        .field-value {
            font-size: 9px;
            font-weight: normal;
            color: #000;
            min-height: 12px;
        }

        .field-value-bold {
            font-size: 9px;
            font-weight: bold;
            color: #000;
        }

        .items-header {
            background-color: #d9d9d9;
            border: 1px solid #000;
            border-top: none;
            padding: 2px 6px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-table {
            width: 100%;
        }

        .items-table th {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background-color: #f0f0f0;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 8px;
            vertical-align: top;
        }

        .items-table .center { text-align: center; }
        .items-table .right { text-align: right; }

        .info-adicional {
            border: 1px solid #000;
            border-top: none;
            padding: 6px 8px;
            font-size: 8px;
            min-height: 40px;
        }

        .info-adicional-header {
            font-size: 6px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 3px;
        }

        .footer-row {
            width: 100%;
            margin-top: 3px;
        }

        .footer-row td {
            border: 1px solid #000;
            padding: 2px 6px;
            font-size: 7px;
            text-align: center;
        }

        .recibo-assinatura {
            border: 1px solid #000;
            border-top: none;
            padding: 8px;
        }

        .assinatura-table {
            width: 100%;
            margin-top: 15px;
        }

        .assinatura-table td {
            width: 50%;
            text-align: center;
            padding: 0 20px;
            vertical-align: bottom;
        }

        .assinatura-line {
            border-bottom: 1px solid #000;
            padding-bottom: 25px;
            margin-bottom: 4px;
        }

        .assinatura-nome {
            font-size: 8px;
        }
    </style>
</head>
<body>
    {{-- CABEÇALHO - Estilo NF-e/DANFE --}}
    <table class="header-row">
        <tr>
            {{-- Emitente --}}
            <td style="width: 50%;">
                <div class="emitente-nome">DG STORE LTDA</div>
                <div class="emitente-detalhe">
                    Rua Rahme Trad Bechara Hage, 2061<br>
                    Higienópolis - S.J. do Rio Preto - SP
                </div>
            </td>
            {{-- Tipo documento --}}
            <td style="width: 25%;" class="danfe-box">
                <div class="danfe-title">RECIBO DE VENDA</div>
                <div class="danfe-subtitle">
                    Documento Auxiliar<br>
                    de Controle Interno
                </div>
                <div class="danfe-entrada">SAÍDA</div>
            </td>
            {{-- Número --}}
            <td style="width: 25%;" class="numero-box">
                <div class="numero-value">Nº {{ $sale->sale_number }}</div>
                <div class="numero-serie">
                    Folha 1/1
                </div>
            </td>
        </tr>
    </table>

    {{-- NATUREZA DA OPERAÇÃO --}}
    <table class="fields-row">
        <tr>
            <td style="width: 60%;">
                <div class="field-label">Natureza da Operação</div>
                <div class="field-value">VENDA DE MERCADORIA</div>
            </td>
            <td style="width: 20%;">
                <div class="field-label">Data de Emissão</div>
                <div class="field-value">{{ $sale->sold_at->format('d/m/Y') }}</div>
            </td>
            <td style="width: 20%;">
                <div class="field-label">Hora de Emissão</div>
                <div class="field-value">{{ $sale->sold_at->format('H:i:s') }}</div>
            </td>
        </tr>
    </table>

    {{-- DESTINATÁRIO / REMETENTE --}}
    <div class="section-title">Destinatário / Remetente</div>
    <table class="fields-row">
        <tr>
            <td style="width: 50%;">
                <div class="field-label">Nome / Razão Social</div>
                <div class="field-value-bold">{{ $sale->customer?->name ?? 'CONSUMIDOR FINAL' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="field-label">CPF / CNPJ</div>
                <div class="field-value">{{ $sale->customer?->formatted_cpf ?? '---' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="field-label">Data da Compra</div>
                <div class="field-value">{{ $sale->sold_at->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>
    <table class="fields-row">
        <tr>
            <td style="width: 50%;">
                <div class="field-label">Endereço</div>
                <div class="field-value">{{ $sale->customer?->address ?? '---' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="field-label">Telefone</div>
                <div class="field-value">{{ $sale->customer?->formatted_phone ?? '---' }}</div>
            </td>
            <td style="width: 25%;">
                <div class="field-label">Instagram</div>
                <div class="field-value">{{ $sale->customer?->formatted_instagram ?? '---' }}</div>
            </td>
        </tr>
    </table>

    {{-- PAGAMENTOS --}}
    <div class="section-title">Pagamentos</div>
    <table class="fields-row">
        <tr>
            <td style="width: 30%;">
                <div class="field-label">Forma de Pagamento</div>
                <div class="field-value">{{ $sale->payment_method->label() }}@if($sale->installments > 1) ({{ $sale->installments }}x)@endif</div>
            </td>
            <td style="width: 20%;">
                <div class="field-label">Valor Total</div>
                <div class="field-value-bold">{{ $sale->formatted_total }}</div>
            </td>
            @if($sale->hasMixedPayment())
                @if($sale->pix_payment > 0)
                <td>
                    <div class="field-label">PIX</div>
                    <div class="field-value">{{ $sale->formatted_pix_payment }}</div>
                </td>
                @endif
                @if($sale->cash_payment > 0)
                <td>
                    <div class="field-label">Dinheiro</div>
                    <div class="field-value">{{ $sale->formatted_cash_payment }}</div>
                </td>
                @endif
                @if($sale->card_payment > 0)
                <td>
                    <div class="field-label">Cartão{{ $sale->installments > 1 ? ' ('.$sale->installments.'x)' : '' }}</div>
                    <div class="field-value">{{ $sale->formatted_card_payment }}</div>
                </td>
                @endif
                @if($sale->trade_in_value > 0)
                <td>
                    <div class="field-label">Trade-in</div>
                    <div class="field-value">
                        @foreach($sale->tradeIns as $ti)
                            {{ $ti->device_name }}@if($ti->storage) {{ $ti->storage }}@endif@if($ti->color) {{ $ti->color }}@endif
                            @if($ti->battery_health) · Bat. {{ $ti->battery_health }}%@endif
                            @if(!$loop->last)<br>@endif
                        @endforeach
                        @if($sale->tradeIns->isEmpty())
                            Aparelho recebido
                        @endif
                    </div>
                </td>
                @endif
            @else
                <td style="width: 25%;">
                    <div class="field-label">Vendedor</div>
                    <div class="field-value">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name ?? '---' }}</div>
                </td>
                <td style="width: 25%;">
                    <div class="field-label">Tipo de Venda</div>
                    <div class="field-value">{{ $sale->sale_type?->label() ?? 'Cliente Final' }}</div>
                </td>
            @endif
        </tr>
    </table>

    {{-- DADOS DOS PRODUTOS / SERVIÇOS --}}
    <div class="section-title">Dados dos Produtos / Serviços</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">Item</th>
                <th style="width: 28%;">Descrição do Produto / Serviço</th>
                <th style="width: 14%;">IMEI</th>
                <th style="width: 6%;">Qtd</th>
                <th style="width: 6%;">Un</th>
                <th style="width: 11%;">Valor Unit.</th>
                <th style="width: 11%;">Valor Total</th>
                <th style="width: 9%;">Bat.</th>
                <th style="width: 10%;">Acessórios</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $index => $item)
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

                $descParts = [$item->product_name];
                if ($itemStorage) $descParts[] = $itemStorage;
                if ($itemColor) $descParts[] = $itemColor;
                if ($itemCondition === 'new') $descParts[] = '(Novo)';
                elseif ($itemCondition === 'used') $descParts[] = '(Seminovo)';
                elseif ($itemCondition === 'refurbished') $descParts[] = '(Recondicionado)';

                $accessories = [];
                if ($hasBox !== null) $accessories[] = 'Cx:' . ($hasBox ? 'S' : 'N');
                if ($hasCable !== null) $accessories[] = 'Cb:' . ($hasCable ? 'S' : 'N');
            @endphp
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ implode(' ', $descParts) }}</td>
                <td class="center" style="font-size: 7px;">{{ $itemImei ?? '---' }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="center">UN</td>
                <td class="right">{{ $item->formatted_unit_price }}</td>
                <td class="right">{{ $item->formatted_subtotal }}</td>
                <td class="center">{{ $batteryHealth !== null ? $batteryHealth . '%' : '---' }}</td>
                <td class="center" style="font-size: 7px;">{{ !empty($accessories) ? implode(' ', $accessories) : '---' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- CÁLCULO DO TOTAL --}}
    <div class="section-title">Cálculo do Total</div>
    <table class="fields-row">
        <tr>
            <td style="width: 15%;">
                <div class="field-label">Subtotal Produtos</div>
                <div class="field-value">{{ $sale->formatted_subtotal }}</div>
            </td>
            <td style="width: 12%;">
                <div class="field-label">Desconto</div>
                <div class="field-value">{{ $sale->discount > 0 ? $sale->formatted_discount : 'R$ 0,00' }}</div>
            </td>
            <td style="width: 33%;">
                <div class="field-label">Trade-in</div>
                <div class="field-value">
                    @if($sale->trade_in_value > 0 && $sale->tradeIns->isNotEmpty())
                        @foreach($sale->tradeIns as $ti)
                            {{ $ti->device_name }}@if($ti->storage) {{ $ti->storage }}@endif@if($ti->color) {{ $ti->color }}@endif
                            @if($ti->battery_health) · {{ $ti->battery_health }}%@endif
                            @php $acc = []; if($ti->has_box) $acc[] = 'Cx:S'; if($ti->has_cable) $acc[] = 'Cb:S'; @endphp
                            @if(!empty($acc)) · {{ implode(' ', $acc) }}@endif
                            @if(!$loop->last)<br>@endif
                        @endforeach
                    @else
                        ---
                    @endif
                </div>
            </td>
            <td style="width: 15%;">
                <div class="field-label">Vendedor</div>
                <div class="field-value">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name ?? '---' }}</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div class="field-label">Valor Total da Venda</div>
                <div class="field-value-bold" style="font-size: 12px;">{{ $sale->formatted_total }}</div>
            </td>
        </tr>
    </table>

    {{-- INFORMAÇÕES ADICIONAIS --}}
    <div class="section-title">Dados Adicionais</div>
    <table class="fields-row">
        <tr>
            <td style="width: 70%;">
                <div class="field-label">Informações Complementares</div>
                <div class="field-value" style="min-height: 30px;">
                    @if($sale->notes)
                        {{ $sale->notes }}
                    @endif
                    <br>Venda realizada na DG Store - SJRP/SP. Emissão: {{ now()->format('d/m/Y H:i') }}.
                </div>
            </td>
            <td style="width: 30%;">
                <div class="field-label">Reservado ao Emitente</div>
                <div class="field-value" style="min-height: 30px;">
                    DG STORE LTDA
                </div>
            </td>
        </tr>
    </table>

    {{-- RODAPÉ --}}
    <table class="footer-row">
        <tr>
            <td>
                DATA E HORA DA IMPRESSÃO: {{ now()->format('d/m/Y H:i:s') }}
            </td>
        </tr>
    </table>
</body>
</html>
