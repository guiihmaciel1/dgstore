{{-- Modal: Nova Entrada com Foto IA --}}
<div id="consignment-entry-modal" 
     style="display: none; position: fixed; inset: 0; z-index: 50; align-items: flex-start; justify-content: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); overflow-y: auto; padding: 2rem 1rem;"
     onclick="if(event.target===this) closeConsignmentEntry()">
    
    <div style="background: #141414; border-radius: 1rem; box-shadow: 0 25px 50px rgba(0,0,0,0.5); width: 100%; max-width: 56rem; border: 1px solid rgba(255,255,255,0.08); margin: auto;">
        
        {{-- Header --}}
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; background: rgba(16,185,129,0.15); display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 1.125rem; height: 1.125rem; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #e3e3e3;">Nova Entrada</h3>
                    <p style="font-size: 0.75rem; color: #666;">Tire foto da traseira ou preencha manualmente</p>
                </div>
            </div>
            <button type="button" onclick="closeConsignmentEntry()"
                    style="padding: 0.375rem; color: #666; background: none; border: none; cursor: pointer; border-radius: 0.375rem;"
                    onmouseover="this.style.color='#e3e3e3';this.style.background='rgba(255,255,255,0.06)'" 
                    onmouseout="this.style.color='#666';this.style.background='none'">
                <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="consignment-entry-form">
            @csrf

            {{-- Seção Foto IA --}}
            <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.04); background: rgba(255,255,255,0.02);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                    <svg style="width: 1rem; height: 1rem; color: #818181;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span style="font-size: 0.8125rem; font-weight: 600; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.05em;">Identificação por Foto</span>
                    <span style="font-size: 0.6875rem; color: #555; font-style: italic;">(recomendado)</span>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: stretch;">
                    <button type="button" onclick="openConsignmentCamera()"
                            style="flex: 1; padding: 1rem; background: rgba(16,185,129,0.06); color: #10b981; border: 1px dashed rgba(16,185,129,0.3); border-radius: 0.5rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.75rem; font-size: 0.875rem; font-weight: 500;"
                            onmouseover="this.style.background='rgba(16,185,129,0.1)'" onmouseout="this.style.background='rgba(16,185,129,0.06)'">
                        <svg style="width: 1.5rem; height: 1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Tirar Foto da Traseira
                    </button>
                    <input type="file" id="consignment-camera-input" accept="image/*" capture="environment" style="display: none;" onchange="onConsignmentPhotoSelected(this)">
                </div>

                {{-- Indicador de análise IA --}}
                <div id="cs-ai-analyzing" style="display: none; margin-top: 0.75rem; padding: 0.875rem; background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15); border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.625rem;">
                        <div style="width: 1.25rem; height: 1.25rem; border: 2px solid rgba(99,102,241,0.3); border-top-color: #818cf8; border-radius: 50%; animation: cs-spin 0.6s linear infinite;"></div>
                        <div>
                            <p style="font-size: 0.8125rem; font-weight: 500; color: #818cf8;">Analisando com IA...</p>
                            <p style="font-size: 0.6875rem; color: #666; margin-top: 0.125rem;">Extraindo IMEI, modelo, cor, armazenamento e serial</p>
                        </div>
                    </div>
                </div>

                {{-- Resultado da análise IA --}}
                <div id="cs-ai-result" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: rgba(99,102,241,0.06); border: 1px solid rgba(99,102,241,0.15); border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #818cf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 600; color: #818cf8;">Dados extraídos pela IA</span>
                    </div>
                    <div id="cs-ai-result-details" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.25rem 1rem;"></div>
                </div>

                {{-- Alerta de duplicata --}}
                <div id="cs-duplicate-alert" style="display: none; margin-top: 0.75rem; padding: 0.75rem; background: rgba(251,191,36,0.08); border: 1px solid rgba(251,191,36,0.25); border-radius: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width: 1rem; height: 1rem; color: #fbbf24; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p style="font-size: 0.8125rem; font-weight: 600; color: #fbbf24;" id="cs-duplicate-text"></p>
                            <p style="font-size: 0.6875rem; color: #a4a4a4; margin-top: 0.125rem;">Ao cadastrar, será adicionada +1 unidade ao item existente.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulário de dados --}}
            <div style="padding: 1.25rem 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.875rem;" id="cs-form-grid">
                    
                    {{-- Fornecedor --}}
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Fornecedor <span style="color: #fca5a5;">*</span>
                        </label>
                        @php $modalSuppliers = \App\Domain\Supplier\Models\Supplier::active()->orderBy('name')->get(); @endphp
                        <select name="supplier_id" id="cs-supplier" required
                                style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem; background: #141414;">
                            @foreach($modalSuppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $modalSuppliers->count() === 1 ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Condição --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Condição <span style="color: #fca5a5;">*</span>
                        </label>
                        <select name="condition" id="cs-condition" required
                                style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem; background: #141414;">
                            <option value="new" selected>Novo</option>
                            <option value="used">Seminovo</option>
                        </select>
                    </div>

                    {{-- Quantidade --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Qtd <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="number" name="quantity" id="cs-quantity" value="1" min="1" max="999" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Nome do Produto --}}
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Nome do Produto <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="name" id="cs-name" required placeholder="Ex: Apple iPhone 17 Pro Max"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                               oninput="checkConsignmentDuplicate()">
                    </div>

                    {{-- Modelo --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Modelo</label>
                        <input type="text" name="model" id="cs-model" placeholder="Ex: 17 Pro Max"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                               oninput="checkConsignmentDuplicate()">
                    </div>

                    {{-- Armazenamento --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Armazenamento <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="storage" id="cs-storage" placeholder="Ex: 256GB" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                               oninput="checkConsignmentDuplicate()">
                    </div>

                    {{-- Cor --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Cor <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="text" name="color" id="cs-color" placeholder="Ex: Deep Blue" required
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;"
                               oninput="checkConsignmentDuplicate()">
                    </div>

                    {{-- Identificadores do Dispositivo --}}
                    <div class="cs-identifiers-section">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.625rem; padding-bottom: 0.375rem; border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <svg style="width: 0.875rem; height: 0.875rem; color: #555;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span style="font-size: 0.6875rem; font-weight: 500; color: #555; text-transform: uppercase; letter-spacing: 0.05em;">Identificadores do Dispositivo</span>
                        </div>
                        <div class="cs-identifiers-grid">
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">IMEI</label>
                                <input type="text" name="imei" id="cs-imei" maxlength="20" placeholder="IMEI principal"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">IMEI 2</label>
                                <input type="text" name="imei2" id="cs-imei2" maxlength="20" placeholder="Segundo IMEI"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Nº de Série</label>
                                <input type="text" name="serial_number" id="cs-serial" maxlength="30" placeholder="Serial Number"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Model No.</label>
                                <input type="text" name="model_number" id="cs-model-number" maxlength="20" placeholder="Ex: A3257"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Part Number</label>
                                <input type="text" name="part_number" id="cs-part-number" maxlength="30" placeholder="Ex: MFXJ4LL/A"
                                       style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.8125rem;">
                            </div>
                        </div>
                    </div>

                    {{-- Separador: Preços --}}
                    <div class="cs-section-separator">
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding-bottom: 0.375rem; border-bottom: 1px solid rgba(255,255,255,0.04);">
                            <svg style="width: 0.875rem; height: 0.875rem; color: #555;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span style="font-size: 0.6875rem; font-weight: 500; color: #555; text-transform: uppercase; letter-spacing: 0.05em;">Valores</span>
                        </div>
                    </div>

                    {{-- Custo Fornecedor --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">
                            Custo Forn. <span style="color: #fca5a5;">*</span>
                        </label>
                        <input type="number" step="0.01" min="0" name="supplier_cost" id="cs-cost" required value="0.00"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Preço Sugerido --}}
                    <div>
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Preço Sugerido</label>
                        <input type="number" step="0.01" min="0" name="suggested_price" id="cs-suggested-price" placeholder="0,00"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>

                    {{-- Observações --}}
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;">Observações</label>
                        <input type="text" name="notes" id="cs-notes"
                               style="width: 100%; padding: 0.5rem 0.625rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="padding: 1rem 1.5rem; background: #1a1a1a; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem; border-radius: 0 0 1rem 1rem;">
                <div id="cs-submit-status" style="display: none; font-size: 0.75rem; color: #22c55e; margin-right: 0.5rem;"></div>
                <button type="button" onclick="closeConsignmentEntry()"
                        style="padding: 0.5rem 1rem; background: #141414; color: #a4a4a4; font-weight: 500; font-size: 0.875rem; border-radius: 0.375rem; border: 1px solid rgba(255,255,255,0.08); cursor: pointer;">
                    Cancelar
                </button>
                <button type="submit" id="cs-submit-btn"
                        style="padding: 0.5rem 1.25rem; background: linear-gradient(to right, #10b981, #059669); color: white; font-weight: 600; font-size: 0.875rem; border-radius: 0.375rem; border: none; cursor: pointer;">
                    Cadastrar Entrada
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let csDuplicateTimeout = null;

    function openConsignmentEntry() {
        document.getElementById('consignment-entry-modal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        document.getElementById('consignment-entry-form').reset();
        hideConsignmentFeedback();
    }

    function closeConsignmentEntry() {
        document.getElementById('consignment-entry-modal').style.display = 'none';
        document.body.style.overflow = '';
    }

    function hideConsignmentFeedback() {
        ['cs-ai-analyzing', 'cs-ai-result', 'cs-duplicate-alert', 'cs-submit-status'].forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
    }

    // --- Foto IA ---
    function openConsignmentCamera() {
        document.getElementById('consignment-camera-input').click();
    }

    function onConsignmentPhotoSelected(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        compressConsignmentImage(file).then(compressed => {
            analyzeConsignmentPhoto(compressed);
        }).catch(() => {
            const reader = new FileReader();
            reader.onload = e => analyzeConsignmentPhoto(e.target.result);
            reader.readAsDataURL(file);
        });
        input.value = '';
    }

    function compressConsignmentImage(file) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = function() {
                const maxSize = 1024;
                let w = img.width, h = img.height;
                if (w > maxSize || h > maxSize) {
                    if (w > h) { h = Math.round(h * maxSize / w); w = maxSize; }
                    else { w = Math.round(w * maxSize / h); h = maxSize; }
                }
                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                canvas.getContext('2d').drawImage(img, 0, 0, w, h);
                resolve(canvas.toDataURL('image/jpeg', 0.7));
            };
            img.onerror = reject;
            img.src = URL.createObjectURL(file);
        });
    }

    function analyzeConsignmentPhoto(base64DataUrl) {
        document.getElementById('cs-ai-analyzing').style.display = 'block';
        document.getElementById('cs-ai-result').style.display = 'none';

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
            document.getElementById('cs-ai-analyzing').style.display = 'none';

            if (!data.success) {
                showCsAiError(data.error || 'Erro ao analisar imagem.');
                return;
            }

            const ext = data.extracted || {};
            showConsignmentAiResult(ext);

            if (ext.product_name) {
                csSetField('cs-name', 'Apple ' + ext.product_name);
                const modelOnly = ext.product_name.replace(/^iPhone\s*/i, '').trim();
                csSetField('cs-model', modelOnly || ext.product_name);
            }
            if (ext.color)        csSetField('cs-color', ext.color);
            if (ext.storage)      csSetField('cs-storage', ext.storage);
            if (ext.imei)         csSetField('cs-imei', ext.imei);
            if (ext.imei2)        csSetField('cs-imei2', ext.imei2);
            if (ext.serial)       csSetField('cs-serial', ext.serial);
            if (ext.model_number) csSetField('cs-model-number', ext.model_number);
            if (ext.part_number)  csSetField('cs-part-number', ext.part_number);

            checkConsignmentDuplicate();
        })
        .catch(err => {
            document.getElementById('cs-ai-analyzing').style.display = 'none';
            console.error('Erro na análise IA:', err);
            showCsAiError('Falha na conexão. Preencha os dados manualmente.');
        });
    }

    function csSetField(id, value) {
        const el = document.getElementById(id);
        if (el && value) el.value = value;
    }

    function showCsAiError(message) {
        const el = document.getElementById('cs-ai-result');
        el.style.display = 'block';
        document.getElementById('cs-ai-result-details').innerHTML =
            '<div style="padding:10px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#991b1b;font-size:12px;">'
            + '<strong>⚠ Erro na IA:</strong> ' + message
            + '<br><small style="color:#6b7280;margin-top:4px;display:block;">💡 Preencha os campos manualmente.</small>'
            + '</div>';
    }

    function showConsignmentAiResult(extracted) {
        const container = document.getElementById('cs-ai-result-details');
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
        document.getElementById('cs-ai-result').style.display = html ? 'block' : 'none';
    }

    // --- Verificação de Duplicata ---
    function checkConsignmentDuplicate() {
        clearTimeout(csDuplicateTimeout);
        csDuplicateTimeout = setTimeout(() => {
            const name = document.getElementById('cs-name').value.trim();
            const storage = document.getElementById('cs-storage').value.trim();
            const color = document.getElementById('cs-color').value.trim();
            const supplierId = document.getElementById('cs-supplier').value;

            if (!name || !storage || !color || !supplierId) {
                document.getElementById('cs-duplicate-alert').style.display = 'none';
                return;
            }

            const params = new URLSearchParams({ name, storage, color, supplier_id: supplierId });
            fetch(`{{ route('stock.consignment.check-duplicate') }}?${params}`, {
                headers: { 'Accept': 'application/json' },
            })
            .then(r => r.json())
            .then(data => {
                const alert = document.getElementById('cs-duplicate-alert');
                if (data.exists) {
                    document.getElementById('cs-duplicate-text').textContent = 
                        `Produto já existe: ${data.item_name} (${data.available_qty} un. disponíveis)`;
                    alert.style.display = 'block';
                } else {
                    alert.style.display = 'none';
                }
            })
            .catch(() => {});
        }, 500);
    }

    // --- Submit ---
    document.getElementById('consignment-entry-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('cs-submit-btn');
        const status = document.getElementById('cs-submit-status');
        btn.disabled = true;
        btn.textContent = 'Salvando...';

        fetch('{{ route("stock.consignment.smart-store") }}', {
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
            status.textContent = `✓ ${data.message}`;
            status.style.display = 'inline';
            btn.textContent = 'Cadastrar Entrada';
            btn.disabled = false;
            setTimeout(() => { closeConsignmentEntry(); window.location.reload(); }, 800);
        })
        .catch(err => {
            btn.textContent = 'Cadastrar Entrada';
            btn.disabled = false;
            if (err.errors) {
                alert('Erros de validação:\n' + Object.values(err.errors).flat().join('\n'));
            } else {
                alert(err.message || 'Erro ao registrar entrada.');
            }
        });
    });
</script>

<style>
    @keyframes cs-spin {
        to { transform: rotate(360deg); }
    }
    .cs-identifiers-section {
        grid-column: span 4;
        margin-top: 0.25rem;
    }
    .cs-identifiers-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.875rem;
    }
    .cs-section-separator {
        grid-column: span 4;
        margin-top: 0.25rem;
    }
    @media (max-width: 768px) {
        #cs-form-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        #cs-form-grid > div[style*="grid-column: span 2"] {
            grid-column: span 2 !important;
        }
        .cs-identifiers-section {
            grid-column: span 2 !important;
        }
        .cs-identifiers-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .cs-section-separator {
            grid-column: span 2 !important;
        }
    }
</style>
