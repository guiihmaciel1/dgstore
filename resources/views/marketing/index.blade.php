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
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 1: TABELA DE PRECOS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'prices'" x-cloak>
                <form method="POST" action="{{ route('marketing.prices.store') }}">
                    @csrf

                    <!-- Busca rapida -->
                    <div style="margin-bottom: 1rem;">
                        <input type="text" x-model="priceSearch" placeholder="Buscar por nome, storage ou cor..."
                               style="width: 100%; max-width: 360px; padding: 0.5rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
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
                                        <img src="{{ $creative->image_url }}" alt="{{ $creative->title }}"
                                             style="width: 100%; height: 100%; object-fit: cover;">
                                        <a href="{{ $creative->image_url }}" download
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

        const urlParams = new URLSearchParams(window.location.search);

        return {
            tab: urlParams.get('tab') || 'prices',
            priceSearch: '',
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
