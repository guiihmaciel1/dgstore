{{-- Modal: Novo Produto com Scanner IMEI --}}
<div id="create-product-modal" 
     style="display: none; position: fixed; inset: 0; z-index: 50; align-items: flex-start; justify-content: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); overflow-y: auto; padding: 2rem 1rem;"
     onclick="if(event.target===this) closeCreateModal()">
    
    <div style="background: #141414; border-radius: 1rem; box-shadow: 0 25px 50px rgba(0,0,0,0.5); width: 100%; max-width: 56rem; border: 1px solid rgba(255,255,255,0.08); margin: auto;">
        
        {{-- Header --}}
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.125rem; height: 1.125rem; color: #a4a4a4;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #e3e3e3;">Novo Produto</h3>
                    <p style="font-size: 0.75rem; color: #666;">Escaneie o IMEI ou preencha manualmente</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()"
                    style="padding: 0.375rem; color: #666; background: none; border: none; cursor: pointer; border-radius: 0.375rem;"
                    onmouseover="this.style.color='#e3e3e3';this.style.background='rgba(255,255,255,0.06)'" 
                    onmouseout="this.style.color='#666';this.style.background='none'">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="create-product-form" method="POST" action="{{ route('products.store') }}">
            @csrf

            {{-- Seção IMEI + Scanner --}}
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.04); background: rgba(255,255,255,0.02);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <svg style="width: 1rem; height: 1rem; color: #818181;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h2M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span style="font-size: 0.8125rem; font-weight: 600; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.05em;">Identificação por IMEI</span>
                    <span style="font-size: 0.6875rem; color: #555; font-style: italic;">(opcional)</span>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: stretch;">
                    <div style="flex: 1; position: relative;">
                        <input type="text" name="imei" id="modal-imei" maxlength="16" placeholder="Digite o IMEI ou escaneie o barcode da caixa"
                               style="width: 100%; padding: 0.75rem 0.875rem; padding-right: 5rem; background: #0d0d0d; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; font-size: 0.9375rem; font-family: 'Geist', monospace; letter-spacing: 0.05em;"
                               onfocus="this.style.borderColor='rgba(255,255,255,0.15)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'"
                               oninput="onImeiInput(this.value)">
                        <div id="imei-lookup-indicator" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);">
                            <div style="width: 1.25rem; height: 1.25rem; border: 2px solid rgba(255,255,255,0.15); border-top-color: #e3e3e3; border-radius: 50%; animation: spin 0.6s linear infinite;"></div>
                        </div>
                        <div id="imei-lookup-found" style="display: none; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%);">
                            <svg style="width: 1.25rem; height: 1.25rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <button type="button" id="scan-btn" onclick="toggleScanner()"
                            style="padding: 0.75rem 1rem; background: rgba(255,255,255,0.06); color: #a4a4a4; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 500; white-space: nowrap;"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                        <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Câmera
                    </button>
                </div>

                {{-- Scanner container --}}
                <div id="scanner-container" style="display: none; margin-top: 0.75rem;">
                    <div style="border-radius: 0.5rem; overflow: hidden; border: 1px solid rgba(255,255,255,0.08); background: #000; position: relative;">
                        <div id="reader" style="width: 100%;"></div>
                        <button type="button" onclick="stopScanner()" 
                                style="position: absolute; top: 0.5rem; right: 0.5rem; z-index: 10; padding: 0.25rem 0.625rem; background: rgba(0,0,0,0.7); color: white; border: none; border-radius: 0.375rem; cursor: pointer; font-size: 0.75rem;">
                            Fechar
                        </button>
                    </div>
                    <p style="font-size: 0.6875rem; color: #555; margin-top: 0.375rem; text-align: center;">Aponte para o código de barras do IMEI na caixa do aparelho</p>
                </div>

                {{-- Resultado da consulta TAC --}}
                <div id="tac-result" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: rgba(34,197,94,0.06); border: 1px solid rgba(34,197,94,0.15); border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #22c55e;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 600; color: #22c55e;">Dispositivo identificado</span>
                    </div>
                    <p id="tac-device-name" style="font-size: 0.9375rem; font-weight: 600; color: #e3e3e3;"></p>
                    <p id="tac-device-type" style="font-size: 0.75rem; color: #818181; margin-top: 0.125rem;"></p>
                </div>

                <div id="tac-not-found" style="display: none; margin-top: 0.75rem; padding: 0.625rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem;">
                    <p style="font-size: 0.75rem; color: #818181;">
                        <svg style="width: 0.875rem; height: 0.875rem; display: inline; vertical-align: -2px; margin-right: 0.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Dispositivo não encontrado na base TAC. Preencha os dados manualmente.
                    </p>
                </div>
            </div>

            {{-- Formulário de dados --}}
            <div style="padding: 1.25rem 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.875rem;" id="product-form-grid">
                    
                    {{-- Nome --}}
                    <div style="grid-column: span 3;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Nome do Produto <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="name" id="modal-name" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                               onfocus="this.style.borderColor='#666'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                    </div>

                    {{-- SKU --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            SKU <span style="color: #fca5a5;">*</span>
                        </label>
                        <div style="display: flex; gap: 0.25rem;">
                            <input type="text" name="sku" id="modal-sku" required
                                   style="flex: 1; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                            <button type="button" onclick="modalGenerateSku()"
                                    style="padding: 0.5rem; background: #222; color: #a4a4a4; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.08); cursor: pointer; font-size: 0.75rem;"
                                    onmouseover="this.style.background='#2a2a2a'" onmouseout="this.style.background='#222'">
                                Gerar
                            </button>
                        </div>
                    </div>

                    {{-- Categoria --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Categoria <span style="color: #fca5a5;">*</span>
                        </label>
                        <select name="category" id="modal-category" required
                                style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem; background: #141414;">
                            <option value="">Selecione...</option>
                            @foreach(\App\Domain\Product\Enums\ProductCategory::grouped() as $group => $items)
                                <optgroup label="{{ $group }}">
                                    @foreach($items as $category)
                                        <option value="{{ $category->value }}">{{ $category->label() }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    {{-- Condição --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Condição <span style="color: #fca5a5;">*</span>
                        </label>
                        <select name="condition" id="modal-condition" required onchange="toggleModalSeminovoFields()"
                                style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem; background: #141414;">
                            @foreach($conditions as $condition)
                                <option value="{{ $condition->value }}">{{ $condition->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Modelo <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="model" id="modal-model" placeholder="Ex: 15 Pro Max" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Armazenamento --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Armazenamento <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="storage" placeholder="Ex: 256GB" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Cor --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Cor <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="color" placeholder="Ex: Preto" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    @if(auth()->user()->canViewFinancials())
                    {{-- Preço Custo --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Preço Custo</label>
                        <input type="number" step="0.01" min="0" name="cost_price" placeholder="0,00"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    @endif

                    {{-- Preço Final --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Preço Final</label>
                        <input type="number" step="0.01" min="0" name="sale_price" placeholder="0,00"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    @if(auth()->user()->canViewFinancials())
                    {{-- Preço Repasse --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Preço Repasse</label>
                        <input type="number" step="0.01" min="0" name="resale_price" placeholder="0,00"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                    @endif

                    {{-- Qtd Estoque --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Estoque <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="number" name="stock_quantity" value="1" min="0" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Alerta Mínimo --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Alerta Mín. <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="number" name="min_stock_alert" value="1" min="0" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Fornecedor --}}
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Fornecedor</label>
                        <input type="text" name="supplier"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Observações --}}
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Observações</label>
                        <input type="text" name="notes"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>

                {{-- Campos Seminovo --}}
                <div id="modal-seminovo-fields" style="display: none; margin-top: 0.875rem; padding: 1rem; background: rgba(251,191,36,0.04); border: 1px solid rgba(251,191,36,0.2); border-radius: 0.5rem;">
                    <p style="font-size: 0.8125rem; font-weight: 600; color: #fbbf24; margin-bottom: 0.75rem;">Informações do Seminovo</p>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.875rem;">
                        <div>
                            <label style="display: flex; align-items: center; cursor: pointer; gap: 0.5rem;">
                                <input type="checkbox" name="has_box" value="1" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: #e3e3e3;">
                                <span style="font-size: 0.8125rem; color: #a4a4a4;">Tem caixa</span>
                            </label>
                        </div>
                        <div>
                            <label style="display: flex; align-items: center; cursor: pointer; gap: 0.5rem;">
                                <input type="checkbox" name="has_cable" value="1" style="width: 1rem; height: 1rem; border-radius: 0.25rem; accent-color: #e3e3e3;">
                                <span style="font-size: 0.8125rem; color: #a4a4a4;">Tem cabo</span>
                            </label>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Saúde da Bateria (%)</label>
                            <input type="number" name="battery_health" min="0" max="100" placeholder="Ex: 87"
                                   style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="padding: 1rem 1.5rem; background: #1a1a1a; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; border-radius: 0 0 1rem 1rem;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="active" value="1" checked style="width: 1rem; height: 1rem; border-radius: 0.25rem; margin-right: 0.375rem;">
                    <span style="font-size: 0.875rem; color: #a4a4a4;">Produto ativo</span>
                </label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <div id="modal-submit-status" style="display: none; font-size: 0.75rem; color: #22c55e; margin-right: 0.5rem;"></div>
                    <button type="button" onclick="closeCreateModal()"
                            style="padding: 0.5rem 1rem; background: #141414; color: #a4a4a4; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.08); cursor: pointer;">
                        Cancelar
                    </button>
                    <button type="submit" id="modal-submit-btn"
                            style="padding: 0.5rem 1.25rem; background: #111827; color: white; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                            onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'">
                        Cadastrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- html5-qrcode CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
    let html5QrCode = null;
    let scannerRunning = false;
    let imeiLookupTimeout = null;

    function openCreateModal() {
        document.getElementById('create-product-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.getElementById('create-product-form').reset();
        document.getElementById('tac-result').style.display = 'none';
        document.getElementById('tac-not-found').style.display = 'none';
        document.getElementById('imei-lookup-found').style.display = 'none';
        document.getElementById('imei-lookup-indicator').style.display = 'none';
        document.getElementById('modal-submit-status').style.display = 'none';
        toggleModalSeminovoFields();
    }

    function closeCreateModal() {
        stopScanner();
        document.getElementById('create-product-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function toggleModalSeminovoFields() {
        const condition = document.getElementById('modal-condition').value;
        const fields = document.getElementById('modal-seminovo-fields');
        fields.style.display = (condition === 'used' || condition === 'refurbished') ? 'block' : 'none';
    }

    function onImeiInput(value) {
        const clean = value.replace(/\D/g, '');
        
        document.getElementById('tac-result').style.display = 'none';
        document.getElementById('tac-not-found').style.display = 'none';
        document.getElementById('imei-lookup-found').style.display = 'none';

        clearTimeout(imeiLookupTimeout);

        if (clean.length >= 14) {
            imeiLookupTimeout = setTimeout(() => lookupImei(clean), 400);
        }
    }

    function lookupImei(imei) {
        document.getElementById('imei-lookup-indicator').style.display = 'block';
        document.getElementById('imei-lookup-found').style.display = 'none';
        document.getElementById('tac-result').style.display = 'none';
        document.getElementById('tac-not-found').style.display = 'none';

        fetch(`{{ route('products.imei-lookup') }}?imei=${encodeURIComponent(imei)}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('imei-lookup-indicator').style.display = 'none';

                if (data.found) {
                    document.getElementById('imei-lookup-found').style.display = 'block';
                    document.getElementById('tac-result').style.display = 'block';
                    document.getElementById('tac-device-name').textContent = data.suggested_name;
                    document.getElementById('tac-device-type').textContent = data.device_type ? `Tipo: ${data.device_type}` : '';

                    const nameField = document.getElementById('modal-name');
                    const modelField = document.getElementById('modal-model');
                    const categoryField = document.getElementById('modal-category');

                    if (!nameField.value) nameField.value = data.suggested_name;
                    if (!modelField.value) modelField.value = data.model;
                    if (categoryField.value === '' && data.suggested_category) {
                        categoryField.value = data.suggested_category;
                    }

                    modalGenerateSku();
                } else {
                    document.getElementById('tac-not-found').style.display = 'block';
                }
            })
            .catch(() => {
                document.getElementById('imei-lookup-indicator').style.display = 'none';
                document.getElementById('tac-not-found').style.display = 'block';
            });
    }

    function toggleScanner() {
        if (scannerRunning) {
            stopScanner();
        } else {
            startScanner();
        }
    }

    function startScanner() {
        const container = document.getElementById('scanner-container');
        container.style.display = 'block';

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("reader");
        }

        const config = {
            fps: 10,
            qrbox: { width: 300, height: 100 },
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.QR_CODE,
                Html5QrcodeSupportedFormats.ITF,
            ],
        };

        html5QrCode.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                const clean = decodedText.replace(/\D/g, '');
                if (clean.length >= 14 && clean.length <= 16) {
                    document.getElementById('modal-imei').value = clean;
                    stopScanner();
                    lookupImei(clean);
                }
            },
            () => {}
        ).then(() => {
            scannerRunning = true;
            document.getElementById('scan-btn').querySelector('span, svg').style.color = '#22c55e';
        }).catch((err) => {
            container.style.display = 'none';
            console.error('Erro ao iniciar câmera:', err);
            alert('Não foi possível acessar a câmera. Verifique as permissões do navegador.');
        });
    }

    function stopScanner() {
        if (html5QrCode && scannerRunning) {
            html5QrCode.stop().then(() => {
                scannerRunning = false;
                document.getElementById('scanner-container').style.display = 'none';
            }).catch(() => {
                scannerRunning = false;
                document.getElementById('scanner-container').style.display = 'none';
            });
        } else {
            document.getElementById('scanner-container').style.display = 'none';
        }
    }

    function modalGenerateSku() {
        const category = document.getElementById('modal-category').value || 'smartphone';
        const model = document.getElementById('modal-model').value || '';
        fetch(`{{ route('products.generate-sku') }}?category=${category}&model=${encodeURIComponent(model)}`)
            .then(r => r.json())
            .then(data => { document.getElementById('modal-sku').value = data.sku; });
    }

    document.getElementById('create-product-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('modal-submit-btn');
        const status = document.getElementById('modal-submit-status');
        btn.disabled = true;
        btn.textContent = 'Salvando...';

        const formData = new FormData(this);

        fetch('{{ route("products.store-quick") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => Promise.reject(d));
            return r.json();
        })
        .then(data => {
            status.textContent = `✓ ${data.name} cadastrado!`;
            status.style.display = 'inline';
            btn.textContent = 'Cadastrar';
            btn.disabled = false;
            
            setTimeout(() => {
                closeCreateModal();
                window.location.reload();
            }, 800);
        })
        .catch(err => {
            btn.textContent = 'Cadastrar';
            btn.disabled = false;

            if (err.errors) {
                const messages = Object.values(err.errors).flat().join('\n');
                alert('Erros de validação:\n' + messages);
            } else {
                alert(err.message || 'Erro ao cadastrar produto.');
            }
        });
    });
</script>

<style>
    @keyframes spin {
        to { transform: translateY(-50%) rotate(360deg); }
    }
    @media (max-width: 768px) {
        #product-form-grid { grid-template-columns: repeat(2, 1fr) !important; }
        #product-form-grid > div[style*="grid-column: span 3"] { grid-column: span 2 !important; }
    }
    #reader video { border-radius: 0.375rem; }
    #reader { min-height: 200px; }
</style>
