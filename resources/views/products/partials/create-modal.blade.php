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
                        <input type="text" name="imei" id="modal-imei" maxlength="16" placeholder="Digite o IMEI ou tire foto da etiqueta"
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
                    <button type="button" id="photo-btn" onclick="openCamera()"
                            style="padding: 0.75rem 1rem; background: rgba(255,255,255,0.06); color: #a4a4a4; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; font-size: 0.8125rem; font-weight: 500; white-space: nowrap;"
                            onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.06)'">
                        <svg style="width: 1.125rem; height: 1.125rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Foto IA
                    </button>
                    <input type="file" id="camera-input" accept="image/*" capture="environment" style="display: none;" onchange="onPhotoSelected(this)">
                </div>

                {{-- Indicador de análise IA --}}
                <div id="ai-analyzing" style="display: none; margin-top: 0.75rem; padding: 0.875rem; background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15); border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <div style="width: 1.25rem; height: 1.25rem; border: 2px solid rgba(99,102,241,0.3); border-top-color: #818cf8; border-radius: 50%; animation: spin 0.6s linear infinite;"></div>
                        <div>
                            <p style="font-size: 0.8125rem; font-weight: 500; color: #818cf8;">Analisando etiqueta com IA...</p>
                            <p style="font-size: 0.6875rem; color: #666; margin-top: 0.125rem;">Extraindo IMEI, modelo, cor e armazenamento</p>
                        </div>
                    </div>
                </div>

                {{-- Resultado da análise IA --}}
                <div id="ai-result" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15); border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 600; color: #818cf8;">Dados extraídos pela IA</span>
                    </div>
                    <div id="ai-result-details" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.25rem 1rem;"></div>
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

<script>
    let imeiLookupTimeout = null;

    function openCreateModal() {
        document.getElementById('create-product-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.getElementById('create-product-form').reset();
        hideAllFeedback();
        toggleModalSeminovoFields();
    }

    function closeCreateModal() {
        document.getElementById('create-product-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function hideAllFeedback() {
        ['tac-result', 'tac-not-found', 'imei-lookup-found', 'imei-lookup-indicator', 
         'modal-submit-status', 'ai-analyzing', 'ai-result'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
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
                    fillFieldIfEmpty('modal-name', data.suggested_name);
                    fillFieldIfEmpty('modal-model', data.model);
                    if (document.getElementById('modal-category').value === '' && data.suggested_category) {
                        document.getElementById('modal-category').value = data.suggested_category;
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

    function fillFieldIfEmpty(id, value) {
        const el = document.getElementById(id);
        if (el && !el.value && value) el.value = value;
    }

    function fillField(id, value) {
        const el = document.getElementById(id);
        if (el && value) el.value = value;
    }

    // --- Foto IA ---
    function openCamera() {
        document.getElementById('camera-input').click();
    }

    function onPhotoSelected(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            analyzeWithAI(e.target.result);
        };
        reader.readAsDataURL(file);
        input.value = '';
    }

    function analyzeWithAI(base64DataUrl) {
        document.getElementById('ai-analyzing').style.display = 'block';
        document.getElementById('ai-result').style.display = 'none';
        document.getElementById('tac-result').style.display = 'none';
        document.getElementById('tac-not-found').style.display = 'none';

        fetch('{{ route("products.analyze-box") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ image: base64DataUrl }),
        })
        .then(r => r.json())
        .then(data => {
            document.getElementById('ai-analyzing').style.display = 'none';

            if (!data.success) {
                alert(data.error || 'Erro ao analisar imagem.');
                return;
            }

            const ext = data.extracted || {};
            showAiResult(ext);

            if (ext.imei) {
                fillField('modal-imei', ext.imei);
                document.getElementById('imei-lookup-found').style.display = 'block';
            }

            if (ext.product_name) {
                fillField('modal-name', 'Apple ' + ext.product_name);
                const modelOnly = ext.product_name.replace(/^iPhone\s*/i, '').trim();
                fillField('modal-model', modelOnly || ext.product_name);
            }

            if (ext.color) {
                const colorField = document.querySelector('#create-product-form input[name="color"]');
                if (colorField) colorField.value = ext.color;
            }

            if (ext.storage) {
                const storageField = document.querySelector('#create-product-form input[name="storage"]');
                if (storageField) storageField.value = ext.storage;
            }

            if (data.tac_lookup && data.tac_lookup.found) {
                document.getElementById('tac-result').style.display = 'block';
                document.getElementById('tac-device-name').textContent = data.tac_lookup.suggested_name;
                document.getElementById('tac-device-type').textContent = '';
                if (document.getElementById('modal-category').value === '') {
                    document.getElementById('modal-category').value = data.tac_lookup.suggested_category || 'smartphone';
                }
            } else {
                document.getElementById('modal-category').value = 'smartphone';
            }

            modalGenerateSku();
        })
        .catch(err => {
            document.getElementById('ai-analyzing').style.display = 'none';
            console.error('Erro na análise IA:', err);
            alert('Erro ao processar a imagem. Tente novamente.');
        });
    }

    function showAiResult(extracted) {
        const container = document.getElementById('ai-result-details');
        const labels = {
            imei: 'IMEI', imei2: 'IMEI 2', serial: 'Serial', model_number: 'Model No.',
            product_name: 'Produto', color: 'Cor', storage: 'Armazenamento', part_number: 'Part No.'
        };
        let html = '';
        for (const [key, label] of Object.entries(labels)) {
            if (extracted[key]) {
                html += `<div style="display: flex; justify-content: space-between; padding: 0.25rem 0; border-bottom: 1px solid rgba(99,102,241,0.1);">
                    <span style="font-size: 0.6875rem; font-weight: 500; color: #818cf8;">${label}</span>
                    <span style="font-size: 0.6875rem; color: #e3e3e3; font-family: monospace;">${extracted[key]}</span>
                </div>`;
            }
        }
        container.innerHTML = html;
        document.getElementById('ai-result').style.display = html ? 'block' : 'none';
    }

    // --- SKU e Submit ---
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

        fetch('{{ route("products.store-quick") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: new FormData(this),
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
            setTimeout(() => { closeCreateModal(); window.location.reload(); }, 800);
        })
        .catch(err => {
            btn.textContent = 'Cadastrar';
            btn.disabled = false;
            if (err.errors) {
                alert('Erros de validação:\n' + Object.values(err.errors).flat().join('\n'));
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
</style>
