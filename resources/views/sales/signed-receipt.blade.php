<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo nº #{{ $sale->sale_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 10mm 14mm; }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            line-height: 1.3;
            color: #000;
            border: 2px solid #000;
            padding: 0;
        }

        table { border-collapse: collapse; width: 100%; }

        .header-row td {
            border: 1px solid #000;
            vertical-align: top;
            padding: 8px 10px;
        }

        .emitente-nome {
            font-size: 15px;
            font-weight: bold;
        }

        .emitente-detalhe {
            font-size: 7.5px;
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
            font-size: 6.5px;
            line-height: 1.4;
        }

        .danfe-entrada {
            font-size: 9px;
            font-weight: bold;
            margin-top: 3px;
        }

        .numero-box {
            text-align: center;
            vertical-align: middle;
        }

        .numero-value {
            font-size: 10px;
            font-weight: bold;
        }

        .numero-serie {
            font-size: 7px;
            margin-top: 3px;
        }

        .section-title {
            background-color: #d9d9d9;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 2px 8px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .fields-row td {
            border: 1px solid #000;
            padding: 2px 6px 4px 6px;
            vertical-align: top;
        }

        .field-label {
            font-size: 5.5px;
            color: #333;
            text-transform: uppercase;
            margin-bottom: 1px;
        }

        .field-value {
            font-size: 8.5px;
            font-weight: normal;
            color: #000;
            min-height: 12px;
        }

        .field-value-bold {
            font-size: 8.5px;
            font-weight: bold;
            color: #000;
        }

        .items-table th {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 5.5px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            background-color: #f0f0f0;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            font-size: 7.5px;
            vertical-align: top;
        }

        .items-table .center { text-align: center; }
        .items-table .right { text-align: right; }

        .footer-fixed {
            position: fixed;
            bottom: -1px;
            left: -1px;
            right: -1px;
        }

        .footer-bar {
            border: 1px solid #000;
            padding: 4px 8px;
            font-size: 7px;
            text-align: center;
            background: #fff;
        }

        .dados-adicionais-row td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 8px;
            vertical-align: top;
        }

        .info-label {
            font-size: 5.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>
    {{-- CABECALHO --}}
    <table class="header-row">
        <tr>
            <td style="width: 50%;">
                <div class="emitente-nome">DG STORE LTDA</div>
                <div class="emitente-detalhe">
                    Rua Rahme Trad Bechara Hage, 2061<br>
                    Sala 45, 4º andar — Centro Empresarial Anthurium<br>
                    Higienópolis - S.J. do Rio Preto - SP — CEP 15085-430<br>
                    WhatsApp: (17) 99649-8338
                </div>
            </td>
            <td style="width: 25%;" class="danfe-box">
                <div class="danfe-title">RECIBO DE VENDA</div>
                <div class="danfe-subtitle">
                    Documento Auxiliar<br>
                    de Controle Interno
                </div>
                <div class="danfe-entrada">SAÍDA</div>
            </td>
            <td style="width: 25%;" class="numero-box">
                <div class="numero-value">Recibo nº #{{ $sale->sale_number }}</div>
                <div class="numero-serie">Folha 1/1</div>
            </td>
        </tr>
    </table>

    {{-- NATUREZA DA OPERACAO --}}
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

    {{-- DESTINATARIO --}}
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
            <td style="width: 25%;">
                <div class="field-label">Forma de Pagamento</div>
                <div class="field-value">{{ $sale->payment_method->label() }}@if($sale->installments > 1) ({{ $sale->installments }}x)@endif</div>
            </td>
            <td style="width: 18%;">
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
                        @forelse($sale->tradeIns as $ti)
                            @php
                                $parts = [$ti->device_name];
                                if ($ti->storage) $parts[] = $ti->storage;
                                if ($ti->color) $parts[] = $ti->color;
                                $desc = implode(' ', $parts);
                                if ($ti->battery_health) $desc .= ' · Bat. ' . $ti->battery_health . '%';
                            @endphp
                            {{ $desc }}@if(!$loop->last)<br>@endif
                        @empty
                            Aparelho recebido
                        @endforelse
                    </div>
                </td>
                @endif
            @else
                <td style="width: 32%;">
                    <div class="field-label">Trade-in</div>
                    <div class="field-value">
                        @if($sale->trade_in_value > 0 && $sale->tradeIns->isNotEmpty())
                            @foreach($sale->tradeIns as $ti)
                                @php
                                    $parts = [$ti->device_name];
                                    if ($ti->storage) $parts[] = $ti->storage;
                                    if ($ti->color) $parts[] = $ti->color;
                                    $desc = implode(' ', $parts);
                                    if ($ti->battery_health) $desc .= ' · Bat. ' . $ti->battery_health . '%';
                                @endphp
                                {{ $desc }}@if(!$loop->last)<br>@endif
                            @endforeach
                        @else
                            ---
                        @endif
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="field-label">Vendedor</div>
                    <div class="field-value">{{ $sale->seller?->name ?? $sale->seller_name ?? $sale->user?->name ?? '---' }}</div>
                </td>
            @endif
        </tr>
    </table>

    {{-- PRODUTOS / SERVICOS --}}
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
                $itemCondition = $snapshot['condition'] ?? $product?->condition?->value ?? null;
                $itemStorage = $snapshot['storage'] ?? $product?->storage ?? null;
                $itemColor = $snapshot['color'] ?? $product?->color ?? null;
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
                <td class="center" style="font-size: 6.5px;">{{ $itemImei ?? '---' }}</td>
                <td class="center">{{ $item->quantity }}</td>
                <td class="center">UN</td>
                <td class="right">{{ $item->formatted_unit_price }}</td>
                <td class="right">{{ $item->formatted_subtotal }}</td>
                <td class="center">{{ $batteryHealth !== null ? $batteryHealth . '%' : '---' }}</td>
                <td class="center" style="font-size: 6.5px;">{{ !empty($accessories) ? implode(' ', $accessories) : '---' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- CALCULO DO TOTAL --}}
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
                            @php
                                $parts = [$ti->device_name];
                                if ($ti->storage) $parts[] = $ti->storage;
                                if ($ti->color) $parts[] = $ti->color;
                                $desc = implode(' ', $parts);
                                if ($ti->battery_health) $desc .= ' · ' . $ti->battery_health . '%';
                                $acc = [];
                                if ($ti->has_box) $acc[] = 'Cx:S';
                                if ($ti->has_cable) $acc[] = 'Cb:S';
                                if (!empty($acc)) $desc .= ' · ' . implode(' ', $acc);
                            @endphp
                            {{ $desc }}@if(!$loop->last)<br>@endif
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

    {{-- GARANTIA --}}
    <div class="section-title">Garantia</div>
    <table class="fields-row">
        <tr>
            <td>
                @php
                    $hasNew = $sale->items->contains(function ($item) {
                        $condition = ($item->product_snapshot ?? [])['condition']
                            ?? $item->product?->condition?->value
                            ?? null;
                        return $condition === 'new';
                    });
                    $hasUsed = $sale->items->contains(function ($item) {
                        $condition = ($item->product_snapshot ?? [])['condition']
                            ?? $item->product?->condition?->value
                            ?? null;
                        return in_array($condition, ['used', 'refurbished']);
                    });
                @endphp
                <div class="field-value">
                    @if($hasNew)
                        <strong>Produtos Apple Novos:</strong> 1 (um) ano de garantia diretamente com o fabricante (Apple).
                    @endif
                    @if($hasNew && $hasUsed)<br>@endif
                    @if($hasUsed)
                        <strong>Produtos Seminovos:</strong> Garantia de 90 (noventa) dias. Produto conferido na presença do comprador.
                    @endif
                    @if(!$hasNew && !$hasUsed)
                        Consulte as condições de garantia do produto adquirido.
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- DADOS ADICIONAIS --}}
    <div class="section-title">Dados Adicionais</div>
    <table class="dados-adicionais-row">
        <tr>
            <td style="width: 70%;">
                <div class="info-label">Informações Complementares</div>
                <div class="field-value">
                    @if($sale->notes)
                        {{ $sale->notes }}
                    @endif
                    <br>Venda realizada na DG Store - SJRP/SP. Emissão: {{ now()->format('d/m/Y H:i') }}.
                </div>
            </td>
            <td style="width: 30%;">
                <div class="info-label">Reservado ao Emitente</div>
                <div class="field-value">DG STORE LTDA</div>
            </td>
        </tr>
    </table>

    {{-- RODAPE FIXO NO FUNDO --}}
    <div class="footer-fixed">
        <div class="footer-bar">
            DATA E HORA DA IMPRESSÃO: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
