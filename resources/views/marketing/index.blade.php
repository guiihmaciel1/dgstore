<x-app-layout>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="marketingApp()">

            @if(session('success'))
                <div style="margin-bottom: 1rem;">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif
            @if(session('error'))
                <div style="margin-bottom: 1rem;">
                    <x-alert type="error">{{ session('error') }}</x-alert>
                </div>
            @endif

            <!-- Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Marketing</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Precos, criativos e seminovos para o dia a dia</p>
                </div>
            </div>

            <!-- Tabs -->
            <div style="display: flex; gap: 0.25rem; margin-bottom: 1.5rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0;">
                <button @click="tab = 'prices'" type="button"
                        :style="tab === 'prices'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #111827; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Tabela de Precos
                </button>
                <button @click="tab = 'creatives'" type="button"
                        :style="tab === 'creatives'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #111827; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Criativos do Dia
                </button>
                <button @click="tab = 'used'" type="button"
                        :style="tab === 'used'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #111827; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Seminovos
                </button>
                <button @click="tab = 'resale'" type="button"
                        :style="tab === 'resale'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #111827; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Repasses
                </button>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 1: TABELA DE PRECOS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'prices'" x-cloak>
                <form method="POST" action="{{ route('marketing.prices.store') }}">
                    @csrf

                    <!-- Busca + Copiar WhatsApp -->
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                        <input type="text" x-model="priceSearch" placeholder="Buscar por nome, storage ou cor..."
                               style="width: 100%; max-width: 360px; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                        <button type="button" @click="copyPriceListToClipboard()"
                                :style="priceCopied
                                    ? 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#059669;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:default;white-space:nowrap;'
                                    : 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap;'"
                                onmouseover="if(!this.__vue_app__)this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                            <svg style="width: 1rem; height: 1rem;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span x-text="priceCopied ? 'Copiado!' : 'Copiar p/ WhatsApp'"></span>
                        </button>
                    </div>

                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 30px;">#</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Modelo</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 100px;">Storage</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 100px;">Cor</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 120px;">Preco</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 150px;">Obs</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 50px;">Ativo</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, idx) in filteredPrices" :key="row._key">
                                        <tr style="border-bottom: 1px solid #f3f4f6;">
                                            <td style="padding: 0.375rem 0.75rem; font-size: 0.75rem; color: #9ca3af;" x-text="idx + 1"></td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="hidden" :name="'prices[' + row._origIdx + '][id]'" :value="row.id || ''">
                                                <input type="text" :name="'prices[' + row._origIdx + '][name]'" x-model="row.name" required
                                                       style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                                       placeholder="Ex: iPhone 16 Pro">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="text" :name="'prices[' + row._origIdx + '][storage]'" x-model="row.storage"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                                       placeholder="128GB">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="text" :name="'prices[' + row._origIdx + '][color]'" x-model="row.color"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                                       placeholder="Preto">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="number" step="0.01" :name="'prices[' + row._origIdx + '][price]'" x-model="row.price" required
                                                       style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                                       placeholder="0.00">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="text" :name="'prices[' + row._origIdx + '][notes]'" x-model="row.notes"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                                                       placeholder="Obs...">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                                <input type="checkbox" :name="'prices[' + row._origIdx + '][active]'" x-model="row.active"
                                                       style="width: 1rem; height: 1rem; cursor: pointer; accent-color: #111827;">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                                <button type="button" @click="removePrice(row._origIdx)"
                                                        style="padding: 0.25rem; color: #dc2626; background: none; border: none; cursor: pointer; border-radius: 0.25rem;"
                                                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'"
                                                        title="Remover">
                                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div x-show="filteredPrices.length === 0" style="padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">
                            Nenhum item na tabela de precos
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                        <button type="button" @click="addPrice()"
                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 500; color: #374151; cursor: pointer;"
                                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Linha
                        </button>
                        <button type="submit"
                                style="padding: 0.5rem 1.5rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;"
                                onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                            Salvar Tabela
                        </button>
                    </div>
                </form>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 2: CRIATIVOS DO DIA --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'creatives'" x-cloak>
                <!-- Filtro de data + botao novo -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <label style="font-size: 0.8rem; font-weight: 500; color: #374151;">Data:</label>
                        <input type="date" x-model="creativeDate" @change="loadCreativesByDate()"
                               style="padding: 0.375rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                    </div>
                    <button type="button" @click="showCreativeForm = !showCreativeForm"
                            style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;"
                            onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Novo Criativo
                    </button>
                </div>

                <!-- Form novo criativo -->
                <div x-show="showCreativeForm" x-transition style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1rem;">
                    <form method="POST" action="{{ route('marketing.creatives.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="date" :value="creativeDate">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Titulo</label>
                                <input type="text" name="title" required placeholder="Ex: Promo iPhone 16"
                                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none;"
                                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div>
                                <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Imagem</label>
                                <input type="file" name="image" accept="image/*"
                                       style="width: 100%; padding: 0.375rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem;">
                            </div>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Texto para copiar</label>
                            <textarea name="description" rows="4" placeholder="Cole aqui o texto pronto para WhatsApp..."
                                      style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; resize: vertical;"
                                      onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                        </div>
                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                            <button type="button" @click="showCreativeForm = false"
                                    style="padding: 0.5rem 1rem; background: #f3f4f6; color: #6b7280; border: none; border-radius: 0.375rem; font-size: 0.8rem; cursor: pointer;">
                                Cancelar
                            </button>
                            <button type="submit"
                                    style="padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;"
                                    onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                                Salvar Criativo
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de criativos -->
                @if($creatives->isEmpty())
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 3rem; text-align: center;">
                        <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">Nenhum criativo para esta data</p>
                    </div>
                @else
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                        @foreach($creatives as $creative)
                            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                                @if($creative->image_path)
                                    <div style="position: relative; height: 200px; background: #f3f4f6;">
                                        <img src="{{ route('marketing.creatives.image', $creative) }}" alt="{{ $creative->title }}"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                        <a href="{{ route('marketing.creatives.download', $creative) }}"
                                           style="position: absolute; top: 0.5rem; right: 0.5rem; padding: 0.375rem; background: rgba(0,0,0,0.6); color: white; border-radius: 0.375rem; text-decoration: none;"
                                           title="Baixar imagem">
                                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                                <div style="padding: 1rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                        <h3 style="font-size: 0.9375rem; font-weight: 600; color: #111827;">{{ $creative->title }}</h3>
                                        <form method="POST" action="{{ route('marketing.creatives.destroy', $creative) }}"
                                              onsubmit="return confirm('Remover este criativo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 0.25rem; color: #dc2626; background: none; border: none; cursor: pointer;" title="Remover">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    @if($creative->description)
                                        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 0.75rem; margin-bottom: 0.75rem; font-size: 0.8rem; color: #374151; white-space: pre-wrap; max-height: 150px; overflow-y: auto; line-height: 1.5;">{{ $creative->description }}</div>
                                        <button type="button" onclick="copyText(this, {{ json_encode($creative->description) }})"
                                                style="width: 100%; padding: 0.5rem; background: #111827; color: white; border: none; border-radius: 0.375rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.375rem;"
                                                onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                            </svg>
                                            <span>Copiar Texto</span>
                                        </button>
                                    @endif
                                    <div style="margin-top: 0.5rem; font-size: 0.7rem; color: #9ca3af;">
                                        Por {{ $creative->user?->name ?? 'Sistema' }} em {{ $creative->created_at->format('d/m H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 4: REPASSES --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'resale'" x-cloak>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827;">Lista de Repasse</h2>
                        <p style="font-size: 0.8rem; color: #6b7280;">Selecione os itens e copie a lista formatada para WhatsApp</p>
                    </div>
                    <button type="button" @click="copyResaleToWhatsApp()"
                            :style="resaleCopied
                                ? 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#059669;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:default;white-space:nowrap;'
                                : 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#16a34a;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap;'">
                        <svg style="width: 1rem; height: 1rem;" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        <span x-text="resaleCopied ? 'Copiado!' : 'Copiar p/ WhatsApp'"></span>
                    </button>
                </div>

                {{-- Novos Lacrados --}}
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; background: #dbeafe; border-radius: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </span>
                        Novos Lacrados
                        <span style="font-size: 0.7rem; font-weight: 500; color: #6b7280;" x-text="'(' + resaleConsignment.length + ' itens)'"></span>
                    </h3>

                    <div x-show="resaleConsignment.length === 0" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">
                        Nenhum item consignado disponivel
                    </div>

                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;" x-show="resaleConsignment.length > 0">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 50px;">Exibir</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Modelo</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 80px;">Storage</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 100px;">Cor</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 130px;">Preco Repasse</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #6b7280; text-transform: uppercase; width: 60px;">Salvar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="item in resaleConsignment" :key="item.id">
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.375rem 0.75rem; text-align: center;">
                                            <input type="checkbox" x-model="item.resale.visible"
                                                   style="width: 0.9rem; height: 0.9rem; accent-color: #111827; cursor: pointer;">
                                        </td>
                                        <td style="padding: 0.375rem 0.75rem; font-size: 0.8rem; font-weight: 500; color: #111827;" x-text="item.name"></td>
                                        <td style="padding: 0.375rem 0.75rem; font-size: 0.8rem; color: #6b7280;" x-text="item.storage || '-'"></td>
                                        <td style="padding: 0.375rem 0.75rem; font-size: 0.8rem; color: #6b7280;">
                                            <div style="display: flex; align-items: center; gap: 0.375rem;">
                                                <span x-show="item._colorHex" :style="'display:inline-block;width:12px;height:12px;border-radius:50%;border:1px solid #d1d5db;background:' + item._colorHex"></span>
                                                <span x-text="item.color || '-'"></span>
                                            </div>
                                        </td>
                                        <td style="padding: 0.375rem 0.5rem;">
                                            <input type="number" step="0.01" x-model="item.resale.resale_price" placeholder="0.00"
                                                   style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right;"
                                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        </td>
                                        <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                            <button type="button" @click="saveResaleItem(item)"
                                                    :style="item._saving
                                                        ? 'padding:0.25rem 0.625rem;background:#059669;color:white;border:none;border-radius:0.375rem;font-size:0.75rem;font-weight:600;cursor:default;'
                                                        : 'padding:0.25rem 0.625rem;background:#111827;color:white;border:none;border-radius:0.375rem;font-size:0.75rem;font-weight:600;cursor:pointer;'"
                                                    :disabled="item._saving">
                                                <span x-text="item._saving ? '✓' : 'Salvar'"></span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Seminovos --}}
                <div>
                    <h3 style="font-size: 0.9375rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; background: #fef3c7; border-radius: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </span>
                        Semi Novos
                        <span style="font-size: 0.7rem; font-weight: 500; color: #6b7280;" x-text="'(' + resaleUsed.length + ' itens)'"></span>
                    </h3>

                    <div x-show="resaleUsed.length === 0" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 2rem; text-align: center; color: #9ca3af; font-size: 0.875rem;">
                        Nenhum seminovo disponivel em estoque
                    </div>

                    <div style="display: flex; gap: 0.5rem; margin-bottom: 0.75rem;" x-show="resaleUsed.length > 0">
                        <button type="button" @click="resaleUsed.forEach(i => i._collapsed = false)"
                                style="padding: 0.25rem 0.75rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.7rem; font-weight: 500; color: #374151; cursor: pointer;"
                                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            Expandir Todos
                        </button>
                        <button type="button" @click="resaleUsed.forEach(i => i._collapsed = true)"
                                style="padding: 0.25rem 0.75rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.7rem; font-weight: 500; color: #374151; cursor: pointer;"
                                onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                            Minimizar Todos
                        </button>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1rem;" x-show="resaleUsed.length > 0">
                        <template x-for="item in resaleUsed" :key="item.id">
                            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                                <div @click="item._collapsed = !item._collapsed"
                                     style="padding: 0.625rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; cursor: pointer; user-select: none;"
                                     :style="item._collapsed ? '' : 'border-bottom: 1px solid #f3f4f6;'">
                                    <svg :style="item._collapsed ? 'width:0.875rem;height:0.875rem;color:#9ca3af;flex-shrink:0;transition:transform 0.2s;transform:rotate(0deg);' : 'width:0.875rem;height:0.875rem;color:#9ca3af;flex-shrink:0;transition:transform 0.2s;transform:rotate(90deg);'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    <div style="min-width: 0; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.8125rem; font-weight: 600; color: #111827;" x-text="item.name"></div>
                                    <template x-if="item._collapsed && item.resale.resale_price">
                                        <span style="font-size: 0.75rem; color: #059669; font-weight: 600; flex-shrink: 0; white-space: nowrap;">R$ <span x-text="parseFloat(item.resale.resale_price).toLocaleString('pt-BR')"></span></span>
                                    </template>
                                    <label @click.stop style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.7rem; color: #374151; cursor: pointer; flex-shrink: 0; margin-left: auto; padding-left: 0.5rem;">
                                        <input type="checkbox" x-model="item.resale.visible"
                                               style="width: 0.875rem; height: 0.875rem; accent-color: #111827; cursor: pointer;">
                                        Exibir
                                    </label>
                                </div>

                                <div x-show="!item._collapsed" x-transition.duration.200ms style="padding: 0.75rem 1rem;">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.625rem;">
                                        <div>
                                            <label style="font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px;">Preco Repasse</label>
                                            <input type="number" step="0.01" x-model="item.resale.resale_price" placeholder="0.00"
                                                   style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px;">Bateria %</label>
                                            <input type="number" min="0" max="100" x-model="item.resale.battery_health" placeholder="0"
                                                   style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        </div>
                                        <div>
                                            <label style="font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px;">Garantia ate</label>
                                            <input type="date" x-model="item.resale.warranty_until"
                                                   style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                   onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                        </div>
                                        <div style="display: flex; align-items: flex-end; gap: 0.75rem; padding-bottom: 0.25rem;">
                                            <label style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: #374151; cursor: pointer;">
                                                <input type="checkbox" x-model="item.resale.has_box" style="accent-color: #111827; cursor: pointer;">
                                                Caixa
                                            </label>
                                            <label style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: #374151; cursor: pointer;">
                                                <input type="checkbox" x-model="item.resale.has_cable" style="accent-color: #111827; cursor: pointer;">
                                                Cabo
                                            </label>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 0.625rem;">
                                        <textarea x-model="item.resale.notes" rows="2" placeholder="Observacoes..."
                                                  style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none; resize: vertical;"
                                                  onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                                    </div>

                                    <button type="button" @click="saveResaleItem(item)"
                                            :style="item._saving
                                                ? 'width:100%;padding:0.5rem;background:#059669;color:white;border:none;border-radius:0.375rem;font-size:0.8rem;font-weight:600;cursor:default;'
                                                : 'width:100%;padding:0.5rem;background:#111827;color:white;border:none;border-radius:0.375rem;font-size:0.8rem;font-weight:600;cursor:pointer;'"
                                            :disabled="item._saving">
                                        <span x-text="item._saving ? 'Salvo!' : 'Salvar'"></span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 3: SEMINOVOS DISPONIVEIS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'used'" x-cloak>
                <!-- Busca -->
                <div style="margin-bottom: 1rem;">
                    <input type="text" x-model="usedSearch" placeholder="Buscar seminovo por nome..."
                           style="width: 100%; max-width: 360px; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                </div>

                <div x-show="filteredUsed.length === 0" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 3rem; text-align: center;">
                    <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p style="margin-top: 0.75rem; color: #6b7280; font-size: 0.875rem;">Nenhum seminovo disponivel em estoque</p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1rem;">
                    <template x-for="item in filteredUsed" :key="item.id">
                        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                            <!-- Header do card -->
                            <div style="padding: 0.875rem 1rem; border-bottom: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="min-width: 0; flex: 1;">
                                    <div style="font-size: 0.9375rem; font-weight: 600; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="item.name"></div>
                                    <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 2px;">
                                        <span x-text="item.model || ''"></span>
                                        <span x-show="item.storage"> · <span x-text="item.storage"></span></span>
                                        <span x-show="item.color"> · <span x-text="item.color"></span></span>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                                    <span :style="item.condition === 'used'
                                        ? 'font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#fef3c7;color:#92400e;'
                                        : 'font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#dbeafe;color:#1e40af;'"
                                          x-text="item.condition === 'used' ? 'Usado' : 'Recond.'"></span>
                                    <span style="font-size:0.7rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#dcfce7;color:#166534;"
                                          x-text="'Est: ' + item.stock"></span>
                                </div>
                            </div>

                            <!-- Campos editaveis -->
                            <div style="padding: 0.875rem 1rem;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem;">
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px;">Custo</label>
                                        <input type="number" step="0.01" x-model="item.listing.cost_price" placeholder="0.00"
                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px;">Entrada</label>
                                        <input type="number" step="0.01" x-model="item.listing.trade_in_price" placeholder="0.00"
                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 2px;">Repasse</label>
                                        <input type="number" step="0.01" x-model="item.listing.resale_price" placeholder="0.00"
                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                    <div>
                                        <label style="font-size: 0.65rem; font-weight: 600; color: #7c3aed; text-transform: uppercase; display: block; margin-bottom: 2px;">Final</label>
                                        <input type="number" step="0.01" x-model="item.listing.final_price" placeholder="0.00"
                                               style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none; font-weight: 600;"
                                               onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'">
                                    </div>
                                </div>

                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                    <label style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; color: #374151; cursor: pointer;">
                                        <input type="checkbox" x-model="item.listing.has_box"
                                               style="width: 0.9rem; height: 0.9rem; accent-color: #111827; cursor: pointer;">
                                        Caixa
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 0.375rem; font-size: 0.8rem; color: #374151; cursor: pointer;">
                                        <input type="checkbox" x-model="item.listing.has_cable"
                                               style="width: 0.9rem; height: 0.9rem; accent-color: #111827; cursor: pointer;">
                                        Cabo
                                    </label>
                                </div>

                                <div style="margin-bottom: 0.75rem;">
                                    <textarea x-model="item.listing.notes" rows="2" placeholder="Observacoes..."
                                              style="width: 100%; padding: 0.375rem 0.5rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.8rem; outline: none; resize: vertical;"
                                              onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"></textarea>
                                </div>

                                <button type="button" @click="saveUsedListing(item)"
                                        :style="item._saving
                                            ? 'width:100%;padding:0.5rem;background:#059669;color:white;border:none;border-radius:0.375rem;font-size:0.8rem;font-weight:600;cursor:default;'
                                            : 'width:100%;padding:0.5rem;background:#111827;color:white;border:none;border-radius:0.375rem;font-size:0.8rem;font-weight:600;cursor:pointer;'"
                                        :disabled="item._saving">
                                    <span x-text="item._saving ? 'Salvo!' : 'Salvar'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

    <script>
    function copyText(btn, text) {
        navigator.clipboard.writeText(text).then(() => {
            const original = btn.querySelector('span');
            const originalText = original.textContent;
            original.textContent = 'Copiado!';
            btn.style.background = '#059669';
            setTimeout(() => {
                original.textContent = originalText;
                btn.style.background = '#111827';
            }, 1500);
        });
    }

    function marketingApp() {
        const initialPrices = @json($pricesJson);

        const usedProducts = @json($usedProductsJson);

        const usedListings = @json($usedListings);

        const consignmentForResale = @json($consignmentResaleJson);
        const usedForResale = @json($usedResaleJson);
        const resaleItemsMap = @json($resaleItems);

        const colorMap = {
            'preto': '#000000', 'black': '#000000', 'midnight': '#1c1c1e', 'meia-noite': '#1c1c1e',
            'branco': '#f5f5f7', 'white': '#f5f5f7', 'starlight': '#f9f3ee', 'estelar': '#f9f3ee',
            'azul': '#0071e3', 'blue': '#0071e3', 'ultramarine': '#3634a3', 'ultramarino': '#3634a3',
            'verde': '#4caf50', 'green': '#4caf50',
            'roxo': '#bf5af2', 'purple': '#bf5af2',
            'rosa': '#f472b6', 'pink': '#f472b6',
            'vermelho': '#dc2626', 'red': '#dc2626', 'product red': '#dc2626',
            'dourado': '#f5d08e', 'gold': '#f5d08e',
            'prateado': '#c0c0c0', 'silver': '#c0c0c0', 'prata': '#c0c0c0',
            'grafite': '#5c5c5e', 'graphite': '#5c5c5e',
            'natural titanium': '#b0a898', 'titanio natural': '#b0a898', 'titânio natural': '#b0a898',
            'desert': '#c2a97c', 'desert titanium': '#c2a97c', 'deserto': '#c2a97c',
            'white titanium': '#f0ede8', 'titanio branco': '#f0ede8', 'titânio branco': '#f0ede8',
            'black titanium': '#3c3c3d', 'titanio preto': '#3c3c3d', 'titânio preto': '#3c3c3d',
            'teal': '#5ac8d8',
        };

        function getColorHex(colorName) {
            if (!colorName) return '';
            return colorMap[colorName.toLowerCase().trim()] || '';
        }

        function getColorEmoji(colorName) {
            if (!colorName) return '';
            const lower = colorName.toLowerCase().trim();
            const emojiMap = {
                'preto': '⚫', 'black': '⚫', 'midnight': '⚫', 'meia-noite': '⚫',
                'branco': '⚪', 'white': '⚪', 'starlight': '⚪', 'estelar': '⚪',
                'azul': '🔵', 'blue': '🔵', 'ultramarine': '🔵', 'ultramarino': '🔵',
                'verde': '🟢', 'green': '🟢',
                'roxo': '🟣', 'purple': '🟣',
                'rosa': '🩷', 'pink': '🩷',
                'vermelho': '🔴', 'red': '🔴', 'product red': '🔴',
                'dourado': '🟡', 'gold': '🟡',
                'prateado': '⚪', 'silver': '⚪', 'prata': '⚪',
                'grafite': '⚫', 'graphite': '⚫',
                'natural titanium': '🟤', 'titanio natural': '🟤', 'titânio natural': '🟤',
                'desert': '🟠', 'desert titanium': '🟠', 'deserto': '🟠',
                'white titanium': '⚪', 'titanio branco': '⚪', 'titânio branco': '⚪',
                'black titanium': '⚫', 'titanio preto': '⚫', 'titânio preto': '⚫',
                'teal': '🔵',
            };
            return emojiMap[lower] || '🔘';
        }

        function buildResaleData(item) {
            const key = item.morph_type + '_' + item.id;
            const existing = resaleItemsMap[key];
            return {
                resale_price: existing ? existing.resale_price : item.suggested_price || null,
                battery_health: existing ? existing.battery_health : null,
                warranty_until: existing ? existing.warranty_until : null,
                has_box: existing ? existing.has_box : false,
                has_cable: existing ? existing.has_cable : false,
                notes: existing ? existing.notes : '',
                visible: existing ? existing.visible : true,
            };
        }

        const urlParams = new URLSearchParams(window.location.search);

        return {
            tab: urlParams.get('tab') || 'prices',
            priceSearch: '',
            priceCopied: false,
            resaleCopied: false,
            showCreativeForm: false,
            creativeDate: @json($creativeDate),
            usedSearch: '',

            prices: initialPrices.map((p, i) => ({ ...p, _key: 'existing_' + i, _origIdx: i })),
            _priceCounter: initialPrices.length,

            usedItems: usedProducts.map(p => ({
                ...p,
                listing: usedListings[p.id] ? {
                    cost_price: usedListings[p.id].cost_price,
                    trade_in_price: usedListings[p.id].trade_in_price,
                    resale_price: usedListings[p.id].resale_price,
                    final_price: usedListings[p.id].final_price,
                    has_box: usedListings[p.id].has_box,
                    has_cable: usedListings[p.id].has_cable,
                    notes: usedListings[p.id].notes,
                } : {
                    cost_price: null,
                    trade_in_price: null,
                    resale_price: null,
                    final_price: null,
                    has_box: false,
                    has_cable: false,
                    notes: '',
                },
                _saving: false,
            })),

            resaleConsignment: consignmentForResale.map(c => ({
                ...c,
                resale: buildResaleData(c),
                _colorHex: getColorHex(c.color),
                _saving: false,
            })),

            resaleUsed: usedForResale.map(p => ({
                ...p,
                resale: buildResaleData(p),
                _collapsed: true,
                _saving: false,
            })),

            get filteredPrices() {
                if (!this.priceSearch) return this.prices;
                const s = this.priceSearch.toLowerCase();
                return this.prices.filter(p =>
                    (p.name || '').toLowerCase().includes(s) ||
                    (p.storage || '').toLowerCase().includes(s) ||
                    (p.color || '').toLowerCase().includes(s)
                );
            },

            get filteredUsed() {
                if (!this.usedSearch) return this.usedItems;
                const s = this.usedSearch.toLowerCase();
                return this.usedItems.filter(p =>
                    p.name.toLowerCase().includes(s) ||
                    (p.model || '').toLowerCase().includes(s)
                );
            },

            addPrice() {
                this.prices.push({
                    id: '',
                    name: '',
                    storage: '',
                    color: '',
                    price: '',
                    notes: '',
                    active: true,
                    _key: 'new_' + (++this._priceCounter),
                    _origIdx: this.prices.length,
                });
            },

            removePrice(origIdx) {
                this.prices = this.prices.filter(p => p._origIdx !== origIdx);
                this.prices.forEach((p, i) => p._origIdx = i);
            },

            copyPriceListToClipboard() {
                const activePrices = this.prices.filter(p => p.active && p.name);

                if (activePrices.length === 0) {
                    alert('Nenhum item ativo na tabela.');
                    return;
                }

                const grouped = {};
                activePrices.forEach(p => {
                    const key = (p.name || '').trim();
                    if (!grouped[key]) grouped[key] = [];
                    grouped[key].push(p);
                });

                let lines = [];
                lines.push('📱 *TABELA DE PREÇOS*');
                lines.push('━━━━━━━━━━━━━━━━━━━');
                lines.push('');

                Object.keys(grouped).forEach(model => {
                    const items = grouped[model];
                    lines.push(`*${model}*`);
                    items.forEach(p => {
                        const storage = p.storage ? ` ${p.storage}` : '';
                        const color = p.color && p.color !== 'Todas' ? ` (${p.color})` : '';
                        const price = parseFloat(p.price || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                        const obs = p.notes ? ` - _${p.notes}_` : '';
                        lines.push(`▸${storage}${color} → ${price}${obs}`);
                    });
                    lines.push('');
                });

                lines.push('📲 Consulte condições e disponibilidade!');

                const text = lines.join('\n');

                navigator.clipboard.writeText(text).then(() => {
                    this.priceCopied = true;
                    setTimeout(() => { this.priceCopied = false; }, 2500);
                }).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    this.priceCopied = true;
                    setTimeout(() => { this.priceCopied = false; }, 2500);
                });
            },

            loadCreativesByDate() {
                window.location.href = '{{ route("marketing.index") }}?tab=creatives&date=' + this.creativeDate;
            },

            async saveUsedListing(item) {
                item._saving = true;
                try {
                    const res = await fetch('{{ route("marketing.used-listings.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: item.id,
                            cost_price: item.listing.cost_price || null,
                            trade_in_price: item.listing.trade_in_price || null,
                            resale_price: item.listing.resale_price || null,
                            final_price: item.listing.final_price || null,
                            has_box: item.listing.has_box ? 1 : 0,
                            has_cable: item.listing.has_cable ? 1 : 0,
                            notes: item.listing.notes || null,
                        }),
                    });
                    if (!res.ok) throw new Error('Erro ao salvar');
                    setTimeout(() => { item._saving = false; }, 1200);
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                    item._saving = false;
                }
            },

            async saveResaleItem(item) {
                item._saving = true;
                try {
                    const res = await fetch('{{ route("marketing.resale-items.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            resaleable_type: item.morph_type,
                            resaleable_id: item.id,
                            resale_price: item.resale.resale_price || null,
                            battery_health: item.resale.battery_health || null,
                            warranty_until: item.resale.warranty_until || null,
                            has_box: item.resale.has_box ? 1 : 0,
                            has_cable: item.resale.has_cable ? 1 : 0,
                            notes: item.resale.notes || null,
                            visible: item.resale.visible ? 1 : 0,
                        }),
                    });
                    if (!res.ok) throw new Error('Erro ao salvar');
                    setTimeout(() => { item._saving = false; }, 1200);
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                    item._saving = false;
                }
            },

            copyResaleToWhatsApp() {
                const visibleConsignment = this.resaleConsignment.filter(c => c.resale.visible && c.resale.resale_price);
                const visibleUsed = this.resaleUsed.filter(u => u.resale.visible && u.resale.resale_price);

                if (visibleConsignment.length === 0 && visibleUsed.length === 0) {
                    alert('Nenhum item marcado como visivel com preco.');
                    return;
                }

                let lines = [];
                lines.push('LISTA DE REPASSE DG STORE');
                lines.push('');

                if (visibleConsignment.length > 0) {
                    lines.push('*NOVOS LACRADOS*');
                    lines.push('');

                    const grouped = {};
                    visibleConsignment.forEach(c => {
                        const key = (c.name || '').trim() + (c.storage ? ' ' + c.storage : '');
                        if (!grouped[key]) grouped[key] = [];
                        grouped[key].push(c);
                    });

                    Object.keys(grouped).forEach(modelKey => {
                        const items = grouped[modelKey];
                        if (items.length === 1) {
                            const c = items[0];
                            const colorEmoji = c.color ? getColorEmoji(c.color) : '';
                            const price = parseFloat(c.resale.resale_price).toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                            lines.push(`${modelKey}`);
                            lines.push(`${colorEmoji} R$ ${price} 🔥🔥`);
                        } else {
                            lines.push(`${modelKey}`);
                            items.forEach(c => {
                                const colorEmoji = c.color ? getColorEmoji(c.color) : '🔘';
                                const price = parseFloat(c.resale.resale_price).toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                                lines.push(`${colorEmoji} R$ ${price} 🔥🔥`);
                            });
                        }
                        lines.push('');
                    });
                }

                if (visibleUsed.length > 0) {
                    lines.push('*SEMI NOVOS*');
                    lines.push('');

                    visibleUsed.forEach(u => {
                        const namePart = (u.name || '') + (u.storage ? ' ' + u.storage : '');
                        const color = u.color || '';
                        const battery = u.resale.battery_health ? `🔋${u.resale.battery_health}%` : '';

                        let accessories = '';
                        if (u.resale.has_box && u.resale.has_cable) {
                            accessories = '📦 Caixa e cabo';
                        } else if (u.resale.has_box) {
                            accessories = '📦 Caixa';
                        } else if (u.resale.has_cable) {
                            accessories = '✅Cabo';
                        } else {
                            accessories = '❌Caixa ❌Cabo';
                        }

                        let warranty = '';
                        if (u.resale.warranty_until) {
                            const d = new Date(u.resale.warranty_until);
                            const months = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
                            warranty = `🛡️ Garantia até ${months[d.getMonth()]}/${d.getFullYear()}`;
                        }

                        const price = parseFloat(u.resale.resale_price).toLocaleString('pt-BR', { minimumFractionDigits: 0 });

                        let parts = [`${namePart} ${color}`.trim()];
                        if (battery) parts.push(battery);
                        if (accessories) parts.push(accessories);
                        if (warranty) parts.push(warranty);
                        if (u.resale.notes) parts.push(u.resale.notes);
                        parts.push(`💰R$ ${price}`);

                        lines.push(parts.join(' - '));
                    });
                }

                const text = lines.join('\n');

                navigator.clipboard.writeText(text).then(() => {
                    this.resaleCopied = true;
                    setTimeout(() => { this.resaleCopied = false; }, 2500);
                }).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    this.resaleCopied = true;
                    setTimeout(() => { this.resaleCopied = false; }, 2500);
                });
            },
        };
    }
    </script>

    <style>
        @media (max-width: 640px) {
            div[style*="grid-template-columns: repeat(auto-fill"] {
                grid-template-columns: 1fr !important;
            }
            div[style*="grid-template-columns: 1fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-app-layout>
