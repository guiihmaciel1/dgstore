<x-app-layout>
    <x-slot name="title">Editar Produto</x-slot>
    <div class="py-4">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho compacto -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center;">
                    <a href="{{ route('products.show', $product) }}" style="margin-right: 0.75rem; padding: 0.375rem; color: #6b7280; border-radius: 0.375rem;"
                       onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <svg style="height: 1.25rem; width: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <div>
                        <h1 style="font-size: 1.25rem; font-weight: 700; color: #111827;">Editar Produto</h1>
                        <p style="font-size: 0.75rem; color: #6b7280;">{{ Str::limit($product->name, 50) }}</p>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb;">
                <form method="POST" action="{{ route('products.update', $product) }}">
                    @csrf
                    @method('PUT')
                    
                    <div style="padding: 1rem 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.875rem;">
                            <!-- Nome -->
                            <div style="grid-column: span 3;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Nome do Produto <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;"
                                       onfocus="this.style.borderColor='#111827';this.style.boxShadow='0 0 0 1px #111827'" onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>

                            <!-- SKU -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    SKU <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Categoria -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Categoria <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="category" required
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                        <optgroup label="{{ $group }}">
                                            @foreach($items as $category)
                                                <option value="{{ $category->value }}" {{ old('category', $product->category->value) == $category->value ? 'selected' : '' }}>
                                                    {{ $category->label() }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Condição -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Condição <span style="color: #dc2626;">*</span>
                                </label>
                                <select name="condition" id="condition" required onchange="toggleSeminovoFields()"
                                        style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; background: white;">
                                    @foreach($conditions as $condition)
                                        <option value="{{ $condition->value }}" {{ old('condition', $product->condition->value) == $condition->value ? 'selected' : '' }}>
                                            {{ $condition->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Modelo -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Modelo <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="model" value="{{ old('model', $product->model) }}" placeholder="Ex: 15 Pro Max" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Armazenamento -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Armazenamento <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="storage" value="{{ old('storage', $product->storage) }}" placeholder="Ex: 256GB" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Cor -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Cor <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="text" name="color" value="{{ old('color', $product->color) }}" placeholder="Ex: Preto" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- IMEI/Serial -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">IMEI/Serial</label>
                                <input type="text" name="imei" value="{{ old('imei', $product->imei) }}" placeholder="Para eletrônicos"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            @if(auth()->user()->canViewFinancials())
                            <!-- Preço Custo -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Preço Custo</label>
                                <input type="number" step="0.01" min="0" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" placeholder="0,00"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                            @endif

                            <!-- Preço Final -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Preço Final</label>
                                <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" placeholder="0,00"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            @if(auth()->user()->canViewFinancials())
                            <!-- Preço Repasse -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Preço Repasse</label>
                                <input type="number" step="0.01" min="0" name="resale_price" value="{{ old('resale_price', $product->resale_price) }}" placeholder="0,00"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                            @endif

                            <!-- Qtd Estoque -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Estoque <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Alerta Mínimo -->
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">
                                    Alerta Mín. <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="0" required
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Fornecedor -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Fornecedor</label>
                                <input type="text" name="supplier" value="{{ old('supplier', $product->supplier) }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>

                            <!-- Observações -->
                            <div style="grid-column: span 2;">
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Observações</label>
                                <input type="text" name="notes" value="{{ old('notes', $product->notes) }}"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                            </div>
                        </div>

                        <!-- Campos Seminovo -->
                        <div id="seminovo-fields" style="display: none; margin-top: 0.875rem; padding: 1rem; background: #fffbeb; border: 1px solid #fde68a; border-radius: 0.5rem;">
                            <p style="font-size: 0.8125rem; font-weight: 600; color: #92400e; margin-bottom: 0.75rem;">Informações do Seminovo</p>
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.875rem;">
                                <div>
                                    <label style="display: flex; align-items: center; cursor: pointer; gap: 0.5rem;">
                                        <input type="checkbox" name="has_box" value="1" {{ old('has_box', $product->has_box) ? 'checked' : '' }}
                                               style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: #111827;">
                                        <span style="font-size: 0.8125rem; color: #374151;">Tem caixa</span>
                                    </label>
                                </div>
                                <div>
                                    <label style="display: flex; align-items: center; cursor: pointer; gap: 0.5rem;">
                                        <input type="checkbox" name="has_cable" value="1" {{ old('has_cable', $product->has_cable) ? 'checked' : '' }}
                                               style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: #111827;">
                                        <span style="font-size: 0.8125rem; color: #374151;">Tem cabo</span>
                                    </label>
                                </div>
                                <div>
                                    <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Saúde da Bateria (%)</label>
                                    <input type="number" name="battery_health" value="{{ old('battery_health', $product->battery_health) }}" min="0" max="100" placeholder="Ex: 87"
                                           style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rodapé -->
                    <div style="padding: 0.75rem 1.25rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <button type="button" onclick="if(confirm('Tem certeza que deseja excluir este produto?')) document.getElementById('delete-form').submit();"
                                    style="padding: 0.5rem 0.75rem; background: #fee2e2; color: #dc2626; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#fecaca'" onmouseout="this.style.background='#fee2e2'">
                                Excluir
                            </button>
                            <label style="display: flex; align-items: center; cursor: pointer;">
                                <input type="checkbox" name="active" value="1" {{ old('active', $product->active) ? 'checked' : '' }}
                                       style="width: 1rem; height: 1rem; border-radius: 0.25rem; margin-right: 0.375rem;">
                                <span style="font-size: 0.875rem; color: #374151;">Ativo</span>
                            </label>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('products.show', $product) }}" 
                               style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; text-decoration: none; border: 1px solid #d1d5db;">
                                Cancelar
                            </a>
                            <button type="submit" 
                                    style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                                    onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                                Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Form de exclusão oculto -->
            <form id="delete-form" method="POST" action="{{ route('products.destroy', $product) }}" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <!-- Modal Device Details -->
    <div id="device-details-modal" style="display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center; background: rgba(0,0,0,0.5);"
         onclick="if(event.target===this) this.style.display='none'">
        <div style="background: white; border-radius: 0.75rem; box-shadow: 0 25px 50px rgba(0,0,0,0.25); width: 100%; max-width: 40rem; max-height: 90vh; display: flex; flex-direction: column; margin: 1rem;">
            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 1rem; font-weight: 600; color: #111827;">Colar Device Details</h3>
                <button type="button" onclick="document.getElementById('device-details-modal').style.display='none'"
                        style="padding: 0.25rem; color: #6b7280; background: none; border: none; cursor: pointer; border-radius: 0.25rem;"
                        onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">
                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div style="padding: 1rem 1.25rem; flex: 1; overflow-y: auto;">
                <p style="font-size: 0.8125rem; color: #6b7280; margin-bottom: 0.75rem;">Cole o texto completo do Device Details abaixo. Os dados serão interpretados automaticamente.</p>
                <textarea id="device-details-raw" rows="14" placeholder="Cole aqui o texto do Device Details..."
                          style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.75rem; font-family: monospace; resize: vertical; line-height: 1.5;"></textarea>
                <div id="device-details-parse-error" style="display: none; margin-top: 0.5rem; padding: 0.5rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.375rem; font-size: 0.75rem; color: #dc2626;"></div>
            </div>
            <div style="padding: 0.75rem 1.25rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="document.getElementById('device-details-modal').style.display='none'"
                        style="padding: 0.5rem 1rem; background: white; color: #374151; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: 1px solid #d1d5db; cursor: pointer;">
                    Cancelar
                </button>
                <button type="button" onclick="parseDeviceDetails()"
                        style="padding: 0.5rem 1rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                        onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                    Interpretar e Salvar
                </button>
            </div>
        </div>
    </div>

    <script>
        function toggleSeminovoFields() {
            const condition = document.getElementById('condition').value;
            const fields = document.getElementById('seminovo-fields');
            fields.style.display = (condition === 'used' || condition === 'refurbished') ? 'block' : 'none';
        }

        document.addEventListener('DOMContentLoaded', toggleSeminovoFields);

        const DEVICE_DETAIL_LABELS = {
            SerialNumber: 'Número de Série',
            InternationalMobileEquipmentIdentity: 'IMEI',
            InternationalMobileEquipmentIdentity2: 'IMEI 2',
            ProductType: 'Tipo do Dispositivo',
            ModelNumber: 'Número do Modelo',
            ProductVersion: 'Versão iOS',
            RegionInfo: 'Região',
            DeviceName: 'Nome do Dispositivo',
            DeviceColor: 'Cor (código)',
            ActivationState: 'Estado de Ativação',
            BluetoothAddress: 'Bluetooth MAC',
            WiFiAddress: 'WiFi MAC',
            PhoneNumber: 'Telefone',
            IntegratedCircuitCardIdentity: 'ICCID (SIM 1)',
            IntegratedCircuitCardIdentity2: 'ICCID (SIM 2)',
            HardwareModel: 'Modelo Hardware',
            CPUArchitecture: 'Arquitetura CPU',
            BasebandVersion: 'Versão Baseband',
            BuildVersion: 'Build',
            FirmwareVersion: 'Firmware',
            UniqueDeviceID: 'UDID',
            TimeZone: 'Fuso Horário',
        };

        const SUMMARY_KEYS = [
            'SerialNumber', 'InternationalMobileEquipmentIdentity', 'InternationalMobileEquipmentIdentity2',
            'ProductType', 'ModelNumber', 'ProductVersion', 'RegionInfo', 'DeviceName',
            'ActivationState', 'BluetoothAddress', 'WiFiAddress', 'PhoneNumber',
        ];

        function parseDeviceDetails() {
            const raw = document.getElementById('device-details-raw').value.trim();
            const errorEl = document.getElementById('device-details-parse-error');
            errorEl.style.display = 'none';

            if (!raw) {
                errorEl.textContent = 'Cole o texto do Device Details antes de interpretar.';
                errorEl.style.display = 'block';
                return;
            }

            const parsed = {};
            const lines = raw.split('\n');
            let validLines = 0;

            for (const line of lines) {
                const trimmed = line.trim();
                if (!trimmed) continue;

                const match = trimmed.match(/^(\S+)\s+(.+)$/);
                if (match) {
                    parsed[match[1]] = match[2].trim();
                    validLines++;
                } else if (/^\S+$/.test(trimmed)) {
                    parsed[trimmed] = '';
                    validLines++;
                }
            }

            if (validLines < 3) {
                errorEl.textContent = 'Texto não parece ser um Device Details válido. Verifique o formato (chave + espaços + valor).';
                errorEl.style.display = 'block';
                return;
            }

            const json = JSON.stringify(parsed);
            document.getElementById('device_details_input').value = json;

            if (parsed.InternationalMobileEquipmentIdentity) {
                const imeiField = document.querySelector('input[name="imei"]');
                if (imeiField && !imeiField.value) {
                    imeiField.value = parsed.InternationalMobileEquipmentIdentity;
                }
            }

            renderDeviceDetailsSummary(parsed);
            document.getElementById('device-details-modal').style.display = 'none';
            document.getElementById('device-details-raw').value = '';
        }

        function renderDeviceDetailsSummary(data) {
            const container = document.getElementById('device-details-summary');
            let html = '<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.375rem 1rem;">';

            for (const key of SUMMARY_KEYS) {
                if (data[key]) {
                    const label = DEVICE_DETAIL_LABELS[key] || key;
                    html += `<div style="display: flex; justify-content: space-between; padding: 0.25rem 0; border-bottom: 1px solid #fef3c7;">
                        <span style="font-size: 0.6875rem; font-weight: 500; color: #92400e;">${label}</span>
                        <span style="font-size: 0.6875rem; color: #78350f; font-family: monospace;">${escapeHtml(data[key])}</span>
                    </div>`;
                }
            }

            const totalKeys = Object.keys(data).length;
            html += '</div>';
            html += `<p style="font-size: 0.6875rem; color: #92400e; margin-top: 0.5rem; font-style: italic;">${totalKeys} propriedades importadas do dispositivo.</p>`;
            container.innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: repeat(4"] { grid-template-columns: repeat(2, 1fr) !important; }
            div[style*="grid-column: span 3"] { grid-column: span 2 !important; }
        }
    </style>
</x-app-layout>
