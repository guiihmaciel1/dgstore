<x-app-layout>
    <x-slot name="title">Marketing</x-slot>
    <div class="py-4">
        <div class="px-6 lg:px-8" x-data="marketingApp()">

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
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;">Marketing</h1>
                    <p style="font-size: 0.875rem; color: #818181;">Precos, criativos e seminovos para o dia a dia</p>
                </div>
            </div>

            <!-- Tabs -->
            <div style="display: flex; gap: 0.25rem; margin-bottom: 1.5rem; border-bottom: 2px solid rgba(255,255,255,0.06); padding-bottom: 0;">
                <button @click="tab = 'prices'" type="button"
                        :style="tab === 'prices'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #e3e3e3; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #818181; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Novos
                </button>
                <button @click="tab = 'used'" type="button"
                        :style="tab === 'used'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #e3e3e3; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #818181; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Seminovos
                </button>
                <button @click="tab = 'resale'" type="button"
                        :style="tab === 'resale'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #e3e3e3; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #818181; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Repasses
                </button>
                <button @click="tab = 'contents'" type="button"
                        class="hidden sm:inline-block"
                        :style="tab === 'contents'
                            ? 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; background: transparent; color: #e3e3e3; border-bottom: 2px solid #111827; margin-bottom: -2px;'
                            : 'padding: 0.625rem 1.25rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; background: transparent; color: #818181; border-bottom: 2px solid transparent; margin-bottom: -2px;'">
                    Conteúdos
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
                               style="width: 100%; max-width: 360px; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                               onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
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

                    {{-- MOBILE: Cards Novos --}}
                    <div class="sm:hidden" style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.5rem; background: rgba(22,163,106,0.08); border: 1px solid rgba(22,163,106,0.15); border-radius: 0.5rem;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #16a34a; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                        <span style="font-size: 0.75rem; color: #16a34a; font-weight: 500;">Toque em um item para copiar para WhatsApp</span>
                    </div>
                    <div class="sm:hidden" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem;">
                        <template x-for="(row, idx) in filteredPrices" :key="'pm_' + row._key">
                            <div x-data="{ tapped: false }"
                                 x-show="row.active"
                                 @click="if ($event.target.closest('input, button, label')) return; copySingleNewPrice(row); tapped = true; setTimeout(() => tapped = false, 1500);"
                                 :class="tapped ? 'used-card-tapped' : ''"
                                 class="used-card-mobile">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.9rem; font-weight: 700; color: #e3e3e3;" x-text="row.name"></div>
                                        <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                            <span x-show="row.storage" style="font-size: 0.7rem; padding: 1px 6px; background: #222; color: #a4a4a4; border-radius: 4px;" x-text="row.storage"></span>
                                            <span x-show="row.color" style="font-size: 0.7rem; padding: 1px 6px; background: #222; color: #a4a4a4; border-radius: 4px;" x-text="row.color"></span>
                                            <span style="font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(16,163,106,0.15);color:#6ee7b7;">Novo</span>
                                        </div>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        <div style="font-size: 1rem; font-weight: 800; color: #e3e3e3;" x-text="row.price ? 'R$ ' + parseFloat(row.price).toLocaleString('pt-BR') : '—'"></div>
                                        @if(auth()->user()->role->isAdminGeral())
                                        <div style="font-size: 0.7rem; color: #666;" x-text="row.cost_price ? 'Custo: ' + parseFloat(row.cost_price).toLocaleString('pt-BR') : ''"></div>
                                        @endif
                                    </div>
                                </div>
                                <template x-if="row.notes">
                                    <div style="font-size: 0.7rem; color: #818181; margin-top: 0.375rem;" x-text="'📝 ' + row.notes"></div>
                                </template>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.375rem;">
                                    <span x-show="(row.images || []).length > 0" style="font-size: 0.65rem; padding: 1px 5px; background: rgba(16,185,129,0.15); color: #6ee7b7; border-radius: 4px;" x-text="(row.images || []).length + ' foto(s)'"></span>
                                </div>
                                <div x-show="tapped" x-transition.opacity style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(5,150,105,0.9); border-radius: 0.75rem; pointer-events: none;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: white; font-weight: 700; font-size: 0.875rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Copiado!
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- DESKTOP: Tabela Novos --}}
                    <div class="hidden sm:block" style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                        <th style="padding: 0.625rem 0.25rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; width: 24px;" x-show="!priceSearch"></th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 30px;">#</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase;">Modelo</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 100px;">Storage</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 100px;">Cor</th>
                                        @if(auth()->user()->role->isAdminGeral())
                                        <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 100px;">Custo</th>
                                        @endif
                                        <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 100px;">Venda</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 150px;">Obs</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 50px;">Ativo</th>
                                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 50px;">Fotos</th>
                                        @if(auth()->user()->role->isAdminGeral())
                                        <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.7rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 40px;"></th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, idx) in filteredPrices" :key="row._key">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04);"
                                            :style="dragOverIdx === idx ? 'border-top: 2px solid #6d28d9;' : ''"
                                            draggable="true"
                                            x-show-drag="!priceSearch"
                                            @dragstart="if(priceSearch) return; dragIdx = idx; $event.dataTransfer.effectAllowed = 'move'; $el.style.opacity = '0.4';"
                                            @dragend="$el.style.opacity = '1'; dragIdx = null; dragOverIdx = null;"
                                            @dragover.prevent="if(priceSearch) return; dragOverIdx = idx; $event.dataTransfer.dropEffect = 'move';"
                                            @dragleave="if(dragOverIdx === idx) dragOverIdx = null;"
                                            @drop.prevent="if(priceSearch || dragIdx === null || dragIdx === idx) { dragOverIdx = null; return; } movePrice(dragIdx, idx); dragIdx = null; dragOverIdx = null;">
                                            <td x-show="!priceSearch" style="padding: 0.375rem 0.25rem; text-align: center; cursor: grab; color: #666666; user-select: none;" title="Arrastar para reordenar">
                                                <svg style="width: 1rem; height: 1rem; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/></svg>
                                            </td>
                                            <td style="padding: 0.375rem 0.75rem; font-size: 0.75rem; color: #666666;" x-text="idx + 1"></td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="hidden" :name="'prices[' + row._origIdx + '][id]'" :value="row.id || ''">
                                                <input type="text" :name="'prices[' + row._origIdx + '][name]'" x-model="row.name" required
                                                       style="width: 100%; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                                                       placeholder="Ex: iPhone 16 Pro">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="text" :name="'prices[' + row._origIdx + '][storage]'" x-model="row.storage"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                                                       placeholder="128GB">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="text" :name="'prices[' + row._origIdx + '][color]'" x-model="row.color"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                                                       placeholder="Preto">
                                            </td>
                                            @if(auth()->user()->role->isAdminGeral())
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="number" step="0.01" :name="'prices[' + row._origIdx + '][cost_price]'" x-model="row.cost_price"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right;"
                                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                                                       placeholder="0.00">
                                            </td>
                                            @else
                                            <input type="hidden" :name="'prices[' + row._origIdx + '][cost_price]'" x-model="row.cost_price">
                                            @endif
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="number" step="0.01" :name="'prices[' + row._origIdx + '][price]'" x-model="row.price" required
                                                       style="width: 100%; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right;"
                                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                                                       placeholder="0.00">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="text" :name="'prices[' + row._origIdx + '][notes]'" x-model="row.notes"
                                                       style="width: 100%; padding: 0.375rem 0.5rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none;"
                                                       onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'"
                                                       placeholder="Obs...">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                                <input type="checkbox" :name="'prices[' + row._origIdx + '][active]'" x-model="row.active"
                                                       style="width: 1rem; height: 1rem; cursor: pointer; accent-color: #e3e3e3;">
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                                <button type="button" @click="openImageModal(prices.indexOf(row))"
                                                        style="padding: 0.25rem; background: none; border: none; cursor: pointer; border-radius: 0.25rem; position: relative;"
                                                        :style="(row.images && row.images.length) ? 'color: #059669;' : 'color: #666666;'"
                                                        onmouseover="this.style.background='#222222'" onmouseout="this.style.background='transparent'"
                                                        :title="(row.images && row.images.length) ? row.images.length + ' foto(s)' : 'Adicionar fotos'">
                                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span x-show="row.images && row.images.length > 0"
                                                          style="position: absolute; top: -4px; right: -4px; background: #059669; color: white; font-size: 9px; font-weight: 700; width: 14px; height: 14px; border-radius: 50%; display: flex; align-items: center; justify-content: center;"
                                                          x-text="row.images.length"></span>
                                                </button>
                                            </td>
                                            @if(auth()->user()->role->isAdminGeral())
                                            <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                                <button type="button" @click="removePrice(row._origIdx)"
                                                        style="padding: 0.25rem; color: #fca5a5; background: none; border: none; cursor: pointer; border-radius: 0.25rem;"
                                                        onmouseover="this.style.background=rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'"
                                                        title="Remover">
                                                    <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </td>
                                            @endif
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <div x-show="filteredPrices.length === 0" style="padding: 2rem; text-align: center; color: #666666; font-size: 0.875rem;">
                            Nenhum item na tabela de precos
                        </div>
                    </div>

                    <div class="hidden sm:flex" style="justify-content: space-between; align-items: center; margin-top: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                        <button type="button" @click="addPrice()"
                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.8rem; font-weight: 500; color: #a4a4a4; cursor: pointer;"
                                onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Adicionar Linha
                        </button>
                        <button type="submit"
                                style="padding: 0.5rem 1.5rem; background: white; color: #0d0d0d; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;"
                                onmouseover="this.style.background='#1f2937'" onmouseout="this.style.background='#111827'">
                            Salvar Tabela
                        </button>
                    </div>
                </form>

                <!-- Modal de Imagens -->
                <div x-show="imgModal.open" x-cloak
                     style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center;"
                     @keydown.escape.window="closeImageModal()">
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4);" @click="closeImageModal()"></div>
                    <div style="position: relative; background: #141414; border-radius: 12px; padding: 1.5rem; width: 100%; max-width: 520px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                            <div>
                                <h3 style="font-size: 1rem; font-weight: 700; color: #e3e3e3;" x-text="imgModalPrice ? 'Fotos: ' + imgModalPrice.name + (imgModalPrice.storage ? ' ' + imgModalPrice.storage : '') : 'Fotos'"></h3>
                                <p style="font-size: 0.75rem; color: #666666;" x-text="imgModalPrice ? (imgModalPrice.images || []).length + '/5 imagens' : ''"></p>
                            </div>
                            <button type="button" @click="closeImageModal()"
                                    style="padding: 4px; background: none; border: none; cursor: pointer; color: #666666; border-radius: 6px;"
                                    onmouseover="this.style.background='#222222'; this.style.color='#e3e3e3'" onmouseout="this.style.background='transparent'; this.style.color='#666666'">
                                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Galeria -->
                        <div x-show="imgModalPrice && (imgModalPrice.images || []).length > 0" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 1rem;">
                            <template x-for="img in (imgModalPrice ? imgModalPrice.images : [])" :key="img.id">
                                <div style="position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; background: #222222; border: 1px solid rgba(255,255,255,0.06);">
                                    <img :src="img.url" :alt="img.original_name"
                                         style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;"
                                         @click="window.open(img.url, '_blank')">
                                    <button type="button" @click="deletePriceImage(img.id)"
                                            style="position: absolute; top: 4px; right: 4px; width: 22px; height: 22px; border-radius: 50%; background: rgba(0,0,0,0.6); color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;"
                                            onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='rgba(0,0,0,0.6)'"
                                            title="Remover">&times;</button>
                                </div>
                            </template>
                        </div>

                        <!-- Vazio -->
                        <div x-show="imgModalPrice && (imgModalPrice.images || []).length === 0"
                             style="padding: 2rem; text-align: center; color: #666666; border: 2px dashed rgba(255,255,255,0.08); border-radius: 8px; margin-bottom: 1rem;">
                            <svg style="width: 32px; height: 32px; margin: 0 auto 0.5rem; color: #515151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p style="font-size: 13px;">Nenhuma foto adicionada</p>
                        </div>

                        <!-- Upload -->
                        <div x-show="imgModalPrice && imgModalPrice.id && (imgModalPrice.images || []).length < 5">
                            <label style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border: 2px dashed rgba(255,255,255,0.08); border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #818181; transition: all 0.15s;"
                                   onmouseover="this.style.borderColor='#111827'; this.style.color='#111827'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.color='#6b7280'">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span x-text="imgModal.uploading ? 'Enviando...' : 'Adicionar foto'"></span>
                                <input type="file" accept="image/jpeg,image/png,image/webp" @change="uploadPriceImage($event)" style="display: none;" :disabled="imgModal.uploading">
                            </label>
                        </div>

                        <!-- Aviso para itens novos -->
                        <div x-show="imgModalPrice && !imgModalPrice.id"
                             style="padding: 12px; background: rgba(245,158,11,0.15); border-radius: 8px; font-size: 12px; color: #fbbf24; text-align: center;">
                            Salve a tabela primeiro para poder adicionar fotos a este item.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Imagens — Seminovos -->
            <div x-show="usedImgModal.open" x-cloak
                 style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center;"
                 @keydown.escape.window="closeUsedImageModal()">
                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4);" @click="closeUsedImageModal()"></div>
                <div style="position: relative; background: #141414; border-radius: 12px; padding: 1.25rem; width: 100%; max-width: 480px; max-height: 90vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.6);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: #e3e3e3;" x-text="usedImgModalItem ? 'Fotos: ' + usedImgModalItem.name + (usedImgModalItem.storage ? ' ' + usedImgModalItem.storage : '') : 'Fotos'"></h3>
                            <p style="font-size: 0.75rem; color: #666666;" x-text="usedImgModalItem ? (usedImgModalItem.images || []).length + '/5 imagens' : ''"></p>
                        </div>
                        <button type="button" @click="closeUsedImageModal()"
                                style="padding: 4px; background: none; border: none; cursor: pointer; color: #666666; border-radius: 4px;"
                                onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#9ca3af'">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Galeria -->
                    <div x-show="usedImgModalItem && (usedImgModalItem.images || []).length > 0" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 1rem;">
                        <template x-for="img in (usedImgModalItem ? usedImgModalItem.images : [])" :key="img.id">
                            <div style="position: relative; border-radius: 8px; overflow: hidden; aspect-ratio: 1; background: #222222; border: 1px solid rgba(255,255,255,0.06);">
                                <img :src="img.url" :alt="img.original_name"
                                     style="width: 100%; height: 100%; object-fit: cover; cursor: pointer;"
                                     @click="window.open(img.url, '_blank')">
                                <button type="button" @click="deleteUsedListingImage(img.id)"
                                        style="position: absolute; top: 4px; right: 4px; width: 20px; height: 20px; border-radius: 50%; background: rgba(0,0,0,0.6); border: none; color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; line-height: 1;"
                                        onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='rgba(0,0,0,0.6)'">&times;</button>
                            </div>
                        </template>
                    </div>

                    <!-- Vazio -->
                    <div x-show="usedImgModalItem && (usedImgModalItem.images || []).length === 0"
                         style="padding: 2rem; text-align: center; color: #666666; border: 2px dashed rgba(255,255,255,0.08); border-radius: 8px; margin-bottom: 1rem;">
                        <svg style="width: 32px; height: 32px; margin: 0 auto 0.5rem; color: #515151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p style="font-size: 0.8rem;">Nenhuma foto adicionada</p>
                    </div>

                    <!-- Upload -->
                    <div x-show="usedImgModalItem && usedImgModalItem.listing_id && (usedImgModalItem.images || []).length < 5">
                        <label style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px; border: 2px dashed rgba(255,255,255,0.08); border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; color: #818181; transition: all 0.15s;"
                               onmouseover="this.style.borderColor='#111827'; this.style.color='#111827'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.color='#6b7280'">
                            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span x-text="usedImgModal.uploading ? 'Enviando...' : 'Adicionar foto'"></span>
                            <input type="file" accept="image/jpeg,image/png,image/webp" @change="uploadUsedListingImage($event)" style="display: none;" :disabled="usedImgModal.uploading">
                        </label>
                    </div>

                    <!-- Aviso para itens não salvos -->
                    <div x-show="usedImgModalItem && !usedImgModalItem.listing_id"
                         style="padding: 12px; background: rgba(245,158,11,0.15); border-radius: 8px; font-size: 12px; color: #fbbf24; text-align: center;">
                        Salve os seminovos primeiro (clique em "Salvar Tudo") para poder adicionar fotos.
                    </div>
                </div>
            </div>


            {{-- ============================================================ --}}
            {{-- ABA 4: REPASSES --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'resale'" x-cloak>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h2 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3;">Lista de Repasse</h2>
                        <p class="hidden sm:block" style="font-size: 0.8rem; color: #818181;">Selecione os itens e copie a lista formatada para WhatsApp</p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" @click="copyResaleSeminovos()"
                                :style="resaleSeminovosCopied
                                    ? 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#059669;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:default;white-space:nowrap;'
                                    : 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.5rem 1rem;background:#d97706;color:white;border:none;border-radius:0.5rem;font-size:0.8rem;font-weight:600;cursor:pointer;white-space:nowrap;'">
                            <svg style="width: 1rem; height: 1rem;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span x-text="resaleSeminovosCopied ? 'Copiado!' : 'Copiar Seminovos'"></span>
                        </button>
                    </div>
                </div>

                {{-- Seminovos --}}
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h3 style="font-size: 0.9375rem; font-weight: 700; color: #e3e3e3; display: flex; align-items: center; gap: 0.5rem;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 1.5rem; height: 1.5rem; background: rgba(245,158,11,0.15); border-radius: 0.375rem;">
                                <svg style="width: 0.875rem; height: 0.875rem; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </span>
                            Seminovos
                            <span style="font-size: 0.7rem; font-weight: 500; color: #818181;" x-text="'(' + resaleUsed.length + ' itens)'"></span>
                        </h3>
                        <button type="button" @click="saveAllResaleUsed()" x-show="resaleUsed.length > 0"
                                :style="resaleUsedAllSaving
                                    ? 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 0.875rem;background:#059669;color:white;border:none;border-radius:0.375rem;font-size:0.75rem;font-weight:600;cursor:default;'
                                    : 'display:inline-flex;align-items:center;gap:0.375rem;padding:0.375rem 0.875rem;background:#111827;color:white;border:none;border-radius:0.375rem;font-size:0.75rem;font-weight:600;cursor:pointer;'"
                                :disabled="resaleUsedAllSaving">
                            <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="resaleUsedAllSaving ? 'Salvo!' : 'Salvar Tudo'"></span>
                        </button>
                    </div>

                    <div x-show="resaleUsed.length === 0" style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 2rem; text-align: center; color: #666666; font-size: 0.875rem;">
                        Nenhum seminovo disponivel em estoque
                    </div>

                    {{-- MOBILE: Cards Repasse --}}
                    <div x-show="resaleUsed.length > 0" class="sm:hidden" style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.5rem; background: rgba(217,119,6,0.08); border: 1px solid rgba(217,119,6,0.15); border-radius: 0.5rem;">
                        <svg style="width: 0.875rem; height: 0.875rem; color: #d97706; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                        <span style="font-size: 0.75rem; color: #d97706; font-weight: 500;">Toque em um item para copiar para WhatsApp</span>
                    </div>
                    <div x-show="resaleUsed.length > 0" class="sm:hidden" style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <template x-for="item in resaleUsed" :key="'rm_' + item.morph_type + '_' + item.id">
                            <div x-data="{ tapped: false }"
                                 @click="if ($event.target.closest('input, button, label')) return; if (!item.resale.resale_price) { alert('Defina o preço de repasse antes de copiar.'); return; } copySingleResale(item); tapped = true; setTimeout(() => tapped = false, 1500);"
                                 :class="tapped ? 'used-card-tapped' : ''"
                                 class="used-card-mobile">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-size: 0.9rem; font-weight: 700; color: #e3e3e3;" x-text="item.name"></div>
                                        <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                            <span x-show="item.storage" style="font-size: 0.7rem; padding: 1px 6px; background: #222; color: #a4a4a4; border-radius: 4px;" x-text="item.storage"></span>
                                            <span x-show="item.color" style="font-size: 0.7rem; padding: 1px 6px; background: #222; color: #a4a4a4; border-radius: 4px;" x-text="item.color"></span>
                                            <span :style="item.condition === 'used'
                                                ? 'font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(245,158,11,0.15);color:#fbbf24;'
                                                : 'font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(59,130,246,0.15);color:#93c5fd;'"
                                                  x-text="item.condition === 'used' ? 'Usado' : 'Recond.'"></span>
                                            <template x-if="item.morph_type && item.morph_type.includes('ConsignmentStockItem')">
                                                <span style="font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(139,92,246,0.15);color:#c4b5fd;">Consig.</span>
                                            </template>
                                        </div>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        <div style="font-size: 1rem; font-weight: 800; color: #d97706;" x-text="item.resale.resale_price ? 'R$ ' + parseFloat(item.resale.resale_price).toLocaleString('pt-BR') : '—'"></div>
                                        <div style="font-size: 0.7rem; color: #666;" x-text="item._usedListing.final_price ? 'Final: ' + parseFloat(item._usedListing.final_price).toLocaleString('pt-BR') : ''"></div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                    <span x-show="item._usedListing.battery_health" style="font-size: 0.75rem; color: #059669; font-weight: 600;" x-text="'🔋 ' + item._usedListing.battery_health + '%'"></span>
                                    <span style="font-size: 0.7rem;" :style="item._usedListing.has_box ? 'color:#059669;' : 'color:#555;'" x-text="item._usedListing.has_box ? '📦 Caixa' : '❌ Caixa'"></span>
                                </div>
                                <template x-if="item._usedListing.notes">
                                    <div style="font-size: 0.7rem; color: #818181; margin-top: 0.375rem;" x-text="'📝 ' + item._usedListing.notes"></div>
                                </template>
                                <div x-show="tapped" x-transition.opacity style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(5,150,105,0.9); border-radius: 0.75rem; pointer-events: none;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: white; font-weight: 700; font-size: 0.875rem;">
                                        <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        Copiado!
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- DESKTOP: Tabela Repasse --}}
                    <div x-show="resaleUsed.length > 0" class="hidden sm:block" style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse; min-width: 850px;">
                                <thead>
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                        <th style="padding: 0.5rem 0.75rem; text-align: center; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 50px;">Exibir</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase;">Produto</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: center; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 55px;">Bat.</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: center; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 40px;">Cx</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 120px;">Obs</th>
                                        @if(auth()->user()->role->isAdminGeral())
                                        <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 85px;">Custo</th>
                                        @endif
                                        <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 85px;">Final</th>
                                        <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.65rem; font-weight: 600; color: #d97706; text-transform: uppercase; width: 110px;">Repasse</th>
                                        <th style="padding: 0.5rem 0.5rem; text-align: center; font-size: 0.65rem; font-weight: 600; color: #818181; text-transform: uppercase; width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in resaleUsed" :key="item.morph_type + '_' + item.id">
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.04); transition: background 0.1s;"
                                            onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background='transparent'">
                                            <td style="padding: 0.375rem 0.75rem; text-align: center;">
                                                <input type="checkbox" x-model="item.resale.visible"
                                                       @change="saveResaleVisibility(item)"
                                                       style="width: 0.9rem; height: 0.9rem; accent-color: #e3e3e3; cursor: pointer;">
                                            </td>
                                            <td style="padding: 0.5rem 0.75rem;">
                                                <div style="font-size: 0.8125rem; font-weight: 600; color: #e3e3e3;" x-text="item.name"></div>
                                                <div style="font-size: 0.6875rem; color: #666666; display: flex; align-items: center; gap: 0.375rem; margin-top: 1px;">
                                                    <span :style="item.condition === 'used'
                                                        ? 'font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(245,158,11,0.15);color:#fbbf24;'
                                                        : 'font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(59,130,246,0.15);color:#93c5fd;'"
                                                          x-text="item.condition === 'used' ? 'Usado' : 'Recond.'"></span>
                                                    <template x-if="item.morph_type && item.morph_type.includes('ConsignmentStockItem')">
                                                        <span style="font-size:0.6rem;font-weight:600;padding:1px 5px;border-radius:3px;background:rgba(139,92,246,0.15);color:#c4b5fd;">Consig.</span>
                                                    </template>
                                                    <span x-show="item.storage" style="font-size:0.6rem;color:#818181;" x-text="item.storage"></span>
                                                    <span x-show="item.color" style="font-size:0.6rem;color:#818181;" x-text="item.color"></span>
                                                </div>
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem; text-align: center; font-size: 0.8rem; color: #a4a4a4;">
                                                <span x-text="item._usedListing.battery_health ? item._usedListing.battery_health + '%' : '-'" :style="item._usedListing.battery_health ? 'color:#059669;font-weight:600;' : 'color:#d1d5db;'"></span>
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                                <span :style="item._usedListing.has_box ? 'color:#059669;font-size:0.8rem;' : 'color:#d1d5db;font-size:0.8rem;'" x-text="item._usedListing.has_box ? '✓' : '—'"></span>
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <span style="font-size: 0.75rem; color: #818181;" x-text="item._usedListing.notes || '—'"></span>
                                            </td>
                                            @if(auth()->user()->role->isAdminGeral())
                                            <td style="padding: 0.375rem 0.75rem; text-align: right; font-size: 0.75rem; color: #818181;">
                                                <span x-text="item._usedListing.cost_price ? parseFloat(item._usedListing.cost_price).toLocaleString('pt-BR', {minimumFractionDigits:0}) : '-'"></span>
                                            </td>
                                            @endif
                                            <td style="padding: 0.375rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #a4a4a4;">
                                                <span x-text="item._usedListing.final_price ? parseFloat(item._usedListing.final_price).toLocaleString('pt-BR', {minimumFractionDigits:0}) : '-'"></span>
                                            </td>
                                            <td style="padding: 0.375rem 0.5rem;">
                                                <input type="number" step="0.01" x-model="item.resale.resale_price" placeholder="0,00"
                                                       style="width: 100%; padding: 0.3rem 0.375rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right; font-weight: 600;"
                                                       onfocus="this.style.borderColor='#d97706'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                                            </td>
                                            <td style="padding: 0.375rem 0.375rem; text-align: center;">
                                                <button type="button"
                                                        x-data="{ justCopied: false }"
                                                        @click="if (!item.resale.resale_price) { alert('Defina o preço de repasse antes de copiar.'); return; } copySingleResale(item); justCopied = true; setTimeout(() => justCopied = false, 2000);"
                                                        :title="justCopied ? 'Copiado!' : 'Copiar este item'"
                                                        class="resale-copy-btn"
                                                        :class="justCopied ? 'copied' : ''">
                                                    <svg x-show="!justCopied" style="width: 0.8rem; height: 0.8rem; color: #818181;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    <svg x-show="justCopied" x-cloak style="width: 0.8rem; height: 0.8rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 3: SEMINOVOS DISPONIVEIS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'used'" x-cloak>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; margin-bottom: 1rem; flex-wrap: wrap;">
                    <input type="text" x-model="usedSearch" placeholder="Buscar seminovo por nome..."
                           style="flex: 1; min-width: 200px; max-width: 360px; padding: 0.5rem 0.75rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; outline: none;"
                           onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button type="button" @click="saveAllUsedListings()"
                                :style="usedAllSaving
                                    ? 'padding: 0.5rem 1rem; background: #059669; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: default; display: flex; align-items: center; gap: 0.375rem;'
                                    : 'padding: 0.5rem 1rem; background: white; color: #0d0d0d; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;'"
                                :disabled="usedAllSaving">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span x-text="usedAllSaving ? 'Salvo!' : 'Salvar Tudo'"></span>
                        </button>
                        <button type="button" @click="printUsedLabels()"
                                class="hidden sm:flex"
                                style="padding: 0.5rem 1rem; background: #4b5563; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; align-items: center; gap: 0.375rem;">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Imprimir Etiquetas
                        </button>
                        <button type="button" @click="copyUsedListToWhatsApp()"
                                :style="usedListCopied
                                    ? 'padding: 0.5rem 1rem; background: #059669; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: default; display: flex; align-items: center; gap: 0.375rem;'
                                    : 'padding: 0.5rem 1rem; background: #25d366; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.375rem;'">
                            <svg style="width: 1rem; height: 1rem;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span x-text="usedListCopied ? 'Copiado!' : 'Copiar Lista'"></span>
                        </button>
                    </div>
                </div>

                <div x-show="filteredUsed.filter(i => (i.images || []).length === 0).length > 0"
                     style="display: flex; align-items: center; gap: 0.625rem; padding: 0.625rem 0.875rem; background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; margin-bottom: 0.75rem;">
                    <svg style="width: 1.125rem; height: 1.125rem; color: #fca5a5; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span style="font-size: 0.8125rem; color: #fca5a5; font-weight: 600;"
                          x-text="filteredUsed.filter(i => (i.images || []).length === 0).length + ' seminovo(s) sem fotos — adicione fotos a todos antes de divulgar'"></span>
                </div>

                <div x-show="filteredUsed.length === 0" style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 3rem; text-align: center;">
                    <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #515151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p style="margin-top: 0.75rem; color: #818181; font-size: 0.875rem;">Nenhum seminovo disponivel em estoque</p>
                </div>

                {{-- ===== MOBILE: Cards ===== --}}
                <div x-show="filteredUsed.length > 0" class="sm:hidden" style="margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.5rem; background: rgba(37,211,102,0.08); border: 1px solid rgba(37,211,102,0.15); border-radius: 0.5rem;">
                    <svg style="width: 0.875rem; height: 0.875rem; color: #25d366; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    <span style="font-size: 0.75rem; color: #25d366; font-weight: 500;">Toque em um item para copiar para WhatsApp</span>
                </div>
                <div x-show="filteredUsed.length > 0" class="sm:hidden" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <template x-for="(item, idx) in filteredUsed" :key="'m_' + item.morph_type + '_' + item.id">
                        <div x-data="{ tapped: false }"
                             @click="if ($event.target.closest('input, button, label, a')) return; copyUsedToWhatsApp(item); tapped = true; setTimeout(() => tapped = false, 1500);"
                             :class="tapped ? 'used-card-tapped' : ''"
                             class="used-card-mobile"
                             :style="(item.images || []).length === 0 ? 'border-left: 3px solid #dc2626; background: rgba(239,68,68,0.08);' : ''">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 0.9rem; font-weight: 700; color: #e3e3e3;" x-text="item.name"></div>
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-top: 0.25rem; flex-wrap: wrap;">
                                        <span x-show="item.storage" style="font-size: 0.7rem; padding: 1px 6px; background: #222; color: #a4a4a4; border-radius: 4px;" x-text="item.storage"></span>
                                        <span x-show="item.color" style="font-size: 0.7rem; padding: 1px 6px; background: #222; color: #a4a4a4; border-radius: 4px;" x-text="item.color"></span>
                                        <span :style="item.condition === 'used'
                                            ? 'font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(245,158,11,0.15);color:#fbbf24;'
                                            : 'font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(59,130,246,0.15);color:#93c5fd;'"
                                              x-text="item.condition === 'used' ? 'Usado' : 'Recond.'"></span>
                                        <template x-if="item.morph_type && item.morph_type.includes('ConsignmentStockItem')">
                                            <span style="font-size:0.65rem;font-weight:600;padding:1px 5px;border-radius:4px;background:rgba(139,92,246,0.15);color:#c4b5fd;">Consig.</span>
                                        </template>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <div style="font-size: 1rem; font-weight: 800; color: #e3e3e3;" x-text="item.listing.final_price ? 'R$ ' + parseFloat(item.listing.final_price).toLocaleString('pt-BR') : '—'"></div>
                                    @if(auth()->user()->role->isAdminGeral())
                                    <div style="font-size: 0.7rem; color: #666;" x-text="item.listing.cost_price ? 'Custo: ' + parseFloat(item.listing.cost_price).toLocaleString('pt-BR') : ''"></div>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap;">
                                <span x-show="item.listing.battery_health" style="font-size: 0.75rem; color: #059669; font-weight: 600;" x-text="'🔋 ' + item.listing.battery_health + '%'"></span>
                                <span style="font-size: 0.7rem;" :style="item.listing.has_box ? 'color:#059669;' : 'color:#555;'" x-text="item.listing.has_box ? '📦 Caixa' : '❌ Caixa'"></span>
                                <span style="font-size: 0.7rem;" :style="item.listing.has_cable ? 'color:#059669;' : 'color:#555;'" x-text="item.listing.has_cable ? '🔌 Cabo' : '❌ Cabo'"></span>
                                <span x-show="(item.images || []).length > 0" style="font-size: 0.65rem; padding: 1px 5px; background: rgba(16,185,129,0.15); color: #6ee7b7; border-radius: 4px;" x-text="(item.images || []).length + ' foto(s)'"></span>
                                <span x-show="(item.images || []).length === 0" style="font-size: 0.65rem; padding: 1px 5px; background: rgba(239,68,68,0.15); color: #fca5a5; border-radius: 4px;">Sem fotos</span>
                            </div>
                            <template x-if="item.listing.notes">
                                <div style="font-size: 0.7rem; color: #818181; margin-top: 0.375rem;" x-text="'📝 ' + item.listing.notes"></div>
                            </template>
                            <template x-if="item.origin_customer">
                                <div style="font-size: 0.65rem; color: #a78bfa; margin-top: 0.375rem;" x-text="'↩ ' + item.origin_customer + ' — ' + item.origin_date"></div>
                            </template>
                            <div x-show="tapped" x-transition.opacity style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(5,150,105,0.9); border-radius: 0.75rem; pointer-events: none;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: white; font-weight: 700; font-size: 0.875rem;">
                                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Copiado!
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- ===== DESKTOP: Tabela ===== --}}
                <div x-show="filteredUsed.length > 0" class="hidden sm:block" style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; min-width: 1350px; table-layout: fixed;">
                            <colgroup>
                                <col style="width: 46px;">
                                <col style="width: auto;">
                                <col style="width: 72px;">
                                <col style="width: 80px;">
                                @if(auth()->user()->role->isAdminGeral())
                                <col style="width: 100px;">
                                @endif
                                <col style="width: 100px;">
                                <col style="width: 62px;">
                                <col style="width: 40px;">
                                <col style="width: 46px;">
                                <col style="width: 150px;">
                                <col style="width: 150px;">
                                <col style="width: 36px;">
                                <col style="width: 40px;">
                            </colgroup>
                            <thead>
                                <tr style="background: #222222; border-bottom: 2px solid #d1d5db;">
                                    <th style="padding: 0.625rem 0.5rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Lista</th>
                                    <th style="padding: 0.625rem 0.5rem; text-align: left; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Produto</th>
                                    <th style="padding: 0.625rem 0.375rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Storage</th>
                                    <th style="padding: 0.625rem 0.375rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Cor</th>
                                    @if(auth()->user()->role->isAdminGeral())
                                    <th style="padding: 0.625rem 0.375rem; text-align: right; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Custo</th>
                                    @endif
                                    <th style="padding: 0.625rem 0.375rem; text-align: right; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Final</th>
                                    <th style="padding: 0.625rem 0.375rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Bat. %</th>
                                    <th style="padding: 0.625rem 0.25rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Cx</th>
                                    <th style="padding: 0.625rem 0.25rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Cabo</th>
                                    <th style="padding: 0.625rem 0.375rem; text-align: left; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Obs</th>
                                    <th style="padding: 0.625rem 0.375rem; text-align: left; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Origem</th>
                                    <th style="padding: 0.625rem 0.25rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;"></th>
                                    <th style="padding: 0.625rem 0.25rem; text-align: center; font-size: 0.65rem; font-weight: 700; color: #a4a4a4; text-transform: uppercase; letter-spacing: 0.025em;">Fotos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, idx) in filteredUsed" :key="item.morph_type + '_' + item.id">
                                    <tr :style="'border-bottom: 1px solid rgba(255,255,255,0.06); transition: background 0.15s;'
                                            + ((item.images || []).length === 0
                                                ? ' background: rgba(239,68,68,0.1); border-left: 3px solid #dc2626;'
                                                : (idx % 2 === 1 ? ' background: #1a1a1a;' : ' background: #141414;'))"
                                        :data-bg="(item.images || []).length === 0 ? 'rgba(239,68,68,0.08)' : (idx % 2 === 1 ? '#1a1a1a' : 'transparent')"
                                        onmouseover="this.style.background='rgba(255,255,255,0.03)'" onmouseout="this.style.background=this.dataset.bg">
                                        <td style="padding: 0.375rem 0.5rem; text-align: center;">
                                            <input type="checkbox" x-model="item.listing.visible"
                                                   @change="saveUsedVisibility(item)"
                                                   style="width: 0.9rem; height: 0.9rem; accent-color: #e3e3e3; cursor: pointer;">
                                        </td>
                                        <td style="padding: 0.375rem 0.5rem; overflow: hidden;">
                                            <div style="font-size: 0.8125rem; font-weight: 600; color: #e3e3e3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="item.name"></div>
                                            <div style="font-size: 0.6875rem; color: #666666; display: flex; align-items: center; gap: 0.25rem; margin-top: 1px; flex-wrap: wrap;">
                                                <span :style="item.condition === 'used'
                                                    ? 'font-size:0.6rem;font-weight:600;padding:1px 4px;border-radius:3px;background:rgba(245,158,11,0.15);color:#fbbf24;'
                                                    : 'font-size:0.6rem;font-weight:600;padding:1px 4px;border-radius:3px;background:rgba(59,130,246,0.15);color:#93c5fd;'"
                                                      x-text="item.condition === 'used' ? 'Usado' : 'Recond.'"></span>
                                                <span style="font-size:0.6rem;font-weight:600;padding:1px 4px;border-radius:3px;background:rgba(16,185,129,0.15);color:#6ee7b7;"
                                                      x-text="'Est: ' + item.stock"></span>
                                                <template x-if="item.morph_type && item.morph_type.includes('ConsignmentStockItem')">
                                                    <span style="font-size:0.6rem;font-weight:600;padding:1px 4px;border-radius:3px;background:rgba(139,92,246,0.15);color:#c4b5fd;">Consig.</span>
                                                </template>
                                            </div>
                                        </td>
                                        <td style="padding: 0.375rem 0.375rem; text-align: center;">
                                            <span style="font-size: 0.75rem; color: #a4a4a4;" x-text="item.storage || '—'"></span>
                                        </td>
                                        <td style="padding: 0.375rem 0.375rem; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <span style="font-size: 0.75rem; color: #a4a4a4;" x-text="item.color || '—'"></span>
                                        </td>
                                        @if(auth()->user()->role->isAdminGeral())
                                        <td style="padding: 0.375rem 0.375rem;">
                                            <input type="number" step="0.01" x-model="item.listing.cost_price" placeholder="0,00"
                                                   style="width: 100%; padding: 0.25rem 0.3rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right; box-sizing: border-box;"
                                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        </td>
                                        @endif
                                        <td style="padding: 0.375rem 0.375rem;">
                                            <input type="number" step="0.01" x-model="item.listing.final_price" placeholder="0,00"
                                                   style="width: 100%; padding: 0.25rem 0.3rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: right; font-weight: 600; box-sizing: border-box;"
                                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        </td>
                                        <td style="padding: 0.375rem 0.375rem;">
                                            <input type="number" min="0" max="100" x-model="item.listing.battery_health" placeholder="%"
                                                   style="width: 100%; padding: 0.25rem 0.2rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.8rem; outline: none; text-align: center; box-sizing: border-box;"
                                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        </td>
                                        <td style="padding: 0.375rem 0.25rem; text-align: center;">
                                            <input type="checkbox" x-model="item.listing.has_box"
                                                   style="width: 0.875rem; height: 0.875rem; accent-color: #e3e3e3; cursor: pointer;">
                                        </td>
                                        <td style="padding: 0.375rem 0.25rem; text-align: center;">
                                            <input type="checkbox" x-model="item.listing.has_cable"
                                                   style="width: 0.875rem; height: 0.875rem; accent-color: #e3e3e3; cursor: pointer;">
                                        </td>
                                        <td style="padding: 0.375rem 0.375rem;">
                                            <input type="text" x-model="item.listing.notes" placeholder="Obs..."
                                                   @input="syncUsedToResale(item)"
                                                   style="width: 100%; padding: 0.25rem 0.3rem; background: #1a1a1a; color: #e3e3e3; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.375rem; font-size: 0.75rem; outline: none; box-sizing: border-box;"
                                                   onfocus="this.style.borderColor='#666666'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        </td>
                                        <td style="padding: 0.375rem 0.375rem;">
                                            <template x-if="item.origin_customer">
                                                <div>
                                                    <div style="font-size: 0.7rem; font-weight: 600; color: #a78bfa; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" x-text="item.origin_customer" :title="item.origin_customer"></div>
                                                    <div style="font-size: 0.6rem; color: #666666;" x-text="item.origin_date"></div>
                                                </div>
                                            </template>
                                            <template x-if="!item.origin_customer">
                                                <span style="font-size: 0.7rem; color: #444;">—</span>
                                            </template>
                                        </td>
                                        <td style="padding: 0.375rem 0.25rem; text-align: center;">
                                            <button type="button" @click="copyUsedToWhatsApp(item)"
                                                    :style="item._copied
                                                        ? 'padding:0.25rem;background:none;border:none;cursor:default;color:#059669;'
                                                        : 'padding:0.25rem;background:none;border:none;cursor:pointer;color:#666666;border-radius:0.25rem;'"
                                                    onmouseover="if(!this.dataset.copied)this.style.color='#25d366'" onmouseout="if(!this.dataset.copied)this.style.color='#9ca3af'"
                                                    title="Copiar para WhatsApp">
                                                <svg x-show="!item._copied" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                                </svg>
                                                <svg x-show="item._copied" style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </td>
                                        <td style="padding: 0.375rem 0.25rem; text-align: center;">
                                            <button type="button" @click="openUsedImageModal(item)"
                                                    :style="(item.images || []).length > 0
                                                        ? 'position:relative;padding:0.25rem;background:none;border:none;cursor:pointer;color:#059669;'
                                                        : 'position:relative;padding:0.25rem;background:none;border:none;cursor:pointer;color:#dc2626;'"
                                                    onmouseover="this.style.color='#111827'" onmouseout="this.style.color=this.dataset.hasimg==='1'?'#059669':'#dc2626'"
                                                    :data-hasimg="(item.images || []).length > 0 ? '1' : '0'"
                                                    :title="(item.images || []).length > 0 ? 'Gerenciar fotos' : 'Sem fotos — clique para adicionar'">
                                                <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                                <span x-show="(item.images || []).length > 0"
                                                      style="position:absolute;top:-4px;right:-4px;width:14px;height:14px;background:#059669;color:white;font-size:0.55rem;font-weight:700;border-radius:50%;display:flex;align-items:center;justify-content:center;"
                                                      x-text="(item.images || []).length"></span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem 0.75rem; margin-top: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <span style="display: inline-block; width: 12px; height: 12px; background: rgba(239,68,68,0.1); border: 1px solid #fca5a5; border-left: 3px solid #dc2626; border-radius: 2px;"></span>
                            <span style="font-size: 0.6875rem; color: #818181;">Sem fotos</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <span style="display: inline-block; width: 12px; height: 12px; background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 2px;"></span>
                            <span style="font-size: 0.6875rem; color: #818181;">Com fotos</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================================ --}}
            {{-- ABA 5: CONTEÚDOS --}}
            {{-- ============================================================ --}}
            <div x-show="tab === 'contents'" x-cloak>
                @include('marketing.partials.contents-tab')
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
        const consignmentUsed = @json($consignmentUsedJson);

        const usedListingsRaw = @json($usedListings);

        const consignmentForResale = @json($consignmentResaleJson);
        const usedForResale = @json($usedResaleJson);
        const newProductsForResale = @json($newProductsResaleJson);
        const resaleItemsMap = @json($resaleItems);

        const colorMap = {
            'preto': '#000000', 'black': '#000000', 'midnight': '#1c1c1e', 'meia-noite': '#1c1c1e',
            'branco': '#f5f5f7', 'white': '#f5f5f7', 'starlight': '#f9f3ee', 'estelar': '#f9f3ee',
            'azul': '#0071e3', 'blue': '#0071e3', 'ultramarine': '#3634a3', 'ultramarino': '#3634a3',
            'verde': '#4caf50', 'green': '#4caf50',
            'roxo': '#bf5af2', 'purple': '#bf5af2',
            'rosa': '#f472b6', 'pink': '#f472b6',
            'vermelho': '#dc2626', 'red': '#dc2626', 'product red': '#dc2626',
            'laranja': '#f97316', 'orange': '#f97316',
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
                'laranja': '🟠', 'orange': '🟠',
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

        const initialContents = @json($contentsJson);

        return {
            tab: urlParams.get('tab') || 'prices',
            mobileEdit: false,
            priceSearch: '',
            dragIdx: null,
            dragOverIdx: null,
            priceCopied: false,
            resaleSeminovosCopied: false,
            showCreativeForm: false,
            creativeDate: @json($creativeDate),
            usedSearch: '',

            contents: initialContents,
            contentFilterStatus: 'all',
            contentFilterType: 'all',
            contentModal: { open: false, editing: null, saving: false },
            contentForm: { title: '', description: '', type: 'post', platform: 'instagram', status: 'idea', scheduled_at: '' },
            aiModal: { open: false },
            aiTopic: '',
            aiLoading: false,
            aiSuggestions: [],
            contentDeleting: null,

            get filteredContents() {
                return this.contents.filter(c => {
                    if (this.contentFilterStatus !== 'all' && c.status !== this.contentFilterStatus) return false;
                    if (this.contentFilterType !== 'all' && c.type !== this.contentFilterType) return false;
                    return true;
                });
            },

            openNewContent() {
                this.contentForm = { title: '', description: '', type: 'post', platform: 'instagram', status: 'idea', scheduled_at: '' };
                this.contentModal = { open: true, editing: null, saving: false };
            },

            openEditContent(c) {
                this.contentForm = {
                    title: c.title,
                    description: c.description || '',
                    type: c.type,
                    platform: c.platform,
                    status: c.status,
                    scheduled_at: c.scheduled_at || '',
                };
                this.contentModal = { open: true, editing: c.id, saving: false };
            },

            async saveContent() {
                this.contentModal.saving = true;
                try {
                    const isEdit = this.contentModal.editing;
                    const url = isEdit
                        ? '{{ url("marketing/contents") }}/' + isEdit
                        : '{{ route("marketing.contents.store") }}';

                    const formData = new FormData();
                    formData.append('title', this.contentForm.title);
                    formData.append('description', this.contentForm.description);
                    formData.append('type', this.contentForm.type);
                    formData.append('platform', this.contentForm.platform);
                    formData.append('status', this.contentForm.status);
                    if (this.contentForm.scheduled_at) formData.append('scheduled_at', this.contentForm.scheduled_at);

                    const imgInput = document.getElementById('content-image-input');
                    if (imgInput && imgInput.files[0]) formData.append('image', imgInput.files[0]);

                    if (isEdit) formData.append('_method', 'PUT');

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData,
                    });

                    if (!res.ok) throw new Error('Erro ao salvar');
                    const data = await res.json();

                    if (isEdit) {
                        const idx = this.contents.findIndex(c => c.id === isEdit);
                        if (idx !== -1) this.contents[idx] = data.content;
                    } else {
                        this.contents.unshift(data.content);
                    }

                    this.contentModal = { open: false, editing: null, saving: false };
                    if (imgInput) imgInput.value = '';
                } catch (e) {
                    alert('Erro ao salvar conteúdo: ' + e.message);
                    this.contentModal.saving = false;
                }
            },

            async deleteContent(id) {
                if (!confirm('Excluir esta ideia de conteúdo?')) return;
                this.contentDeleting = id;
                try {
                    const res = await fetch('{{ url("marketing/contents") }}/' + id, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    });
                    if (!res.ok) throw new Error('Erro ao excluir');
                    this.contents = this.contents.filter(c => c.id !== id);
                } catch (e) {
                    alert('Erro ao excluir: ' + e.message);
                } finally {
                    this.contentDeleting = null;
                }
            },

            async generateIdeas() {
                this.aiLoading = true;
                this.aiSuggestions = [];
                try {
                    const res = await fetch('{{ route("marketing.contents.generate-ideas") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ topic: this.aiTopic }),
                    });
                    if (!res.ok) {
                        const err = await res.json();
                        throw new Error(err.error || 'Erro ao gerar ideias');
                    }
                    const data = await res.json();
                    this.aiSuggestions = (data.ideas || []).map(i => ({ ...i, saving: false, saved: false }));
                } catch (e) {
                    alert(e.message);
                } finally {
                    this.aiLoading = false;
                }
            },

            async saveIdeaFromAi(idea, idx) {
                this.aiSuggestions[idx].saving = true;
                try {
                    const formData = new FormData();
                    formData.append('title', idea.title);
                    formData.append('description', idea.description);
                    formData.append('type', idea.type);
                    formData.append('platform', idea.platform);
                    formData.append('status', 'idea');
                    formData.append('ai_generated', '1');

                    const res = await fetch('{{ route("marketing.contents.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: formData,
                    });
                    if (!res.ok) throw new Error('Erro ao salvar');
                    const data = await res.json();
                    this.contents.unshift(data.content);
                    this.aiSuggestions[idx].saved = true;
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                } finally {
                    this.aiSuggestions[idx].saving = false;
                }
            },

            prices: initialPrices.map((p, i) => ({ ...p, images: p.images || [], _key: 'existing_' + i, _origIdx: i })),
            _priceCounter: initialPrices.length,

            imgModal: { open: false, priceIdx: null, uploading: false },

            usedImgModal: { open: false, itemKey: null, uploading: false },

            usedListCopied: false,
            usedAllSaving: false,

            usedItems: [...usedProducts, ...consignmentUsed].map(p => {
                const listingKey = p.morph_type + '_' + p.id;
                const existing = usedListingsRaw[listingKey];
                return {
                    ...p,
                    listing_id: existing ? existing.id : null,
                    listing: existing ? {
                        cost_price: existing.cost_price,
                        final_price: existing.final_price,
                        battery_health: existing.battery_health,
                        has_box: existing.has_box,
                        has_cable: existing.has_cable,
                        notes: existing.notes,
                        visible: existing.visible ?? true,
                    } : {
                        cost_price: p.supplier_cost || null,
                        final_price: p.suggested_price || null,
                        battery_health: p.battery_health || null,
                        has_box: p.has_box || false,
                        has_cable: p.has_cable || false,
                        notes: p.product_notes || '',
                        visible: true,
                    },
                    images: existing && existing.images ? existing.images.map(img => ({
                        id: img.id,
                        url: img.url,
                        original_name: img.original_name,
                    })) : [],
                    _saving: false,
                    _copied: false,
                };
            }).sort((a, b) => (parseFloat(a.listing.final_price) || 0) - (parseFloat(b.listing.final_price) || 0)),

            resaleConsignment: [
                ...consignmentForResale.filter(c => (c.condition || 'new') === 'new'),
                ...newProductsForResale,
            ].map(c => ({
                    ...c,
                    resale: buildResaleData(c),
                    _colorHex: getColorHex(c.color),
                    _saving: false,
                }))
                .sort((a, b) => (parseFloat(a.resale.resale_price) || 0) - (parseFloat(b.resale.resale_price) || 0)),

            resaleUsedAllSaving: false,

            resaleUsed: [
                ...usedForResale,
                ...consignmentForResale.filter(c => c.condition === 'used'),
            ].map(p => {
                const ulKey = (p.morph_type || 'App\\Domain\\Product\\Models\\Product') + '_' + p.id;
                const ul = usedListingsRaw[ulKey] || {};
                const resale = buildResaleData(p);
                const bat = ul.battery_health || p.battery_health || null;
                const box = !!ul.has_box || !!p.has_box;
                const cable = !!ul.has_cable || !!p.has_cable;
                if (!resale.battery_health && bat) resale.battery_health = bat;
                if (!resale.has_box && box) resale.has_box = box;
                if (!resale.has_cable && cable) resale.has_cable = cable;
                if (!resale.notes && ul.notes) resale.notes = ul.notes;
                return {
                    ...p,
                    resale,
                    _usedListing: {
                        cost_price: ul.cost_price || p.supplier_cost || null,
                        final_price: ul.final_price || p.suggested_price || null,
                        battery_health: bat,
                        has_box: box,
                        has_cable: cable,
                        notes: ul.notes || '',
                    },
                    _saving: false,
                };
            }).sort((a, b) => (parseFloat(a._usedListing.final_price) || 0) - (parseFloat(b._usedListing.final_price) || 0)),

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
                    (p.model || '').toLowerCase().includes(s) ||
                    (p.storage || '').toLowerCase().includes(s) ||
                    (p.color || '').toLowerCase().includes(s)
                );
            },

            addPrice() {
                this.prices.push({
                    id: '',
                    name: '',
                    storage: '',
                    color: '',
                    cost_price: '',
                    price: '',
                    notes: '',
                    active: true,
                    images: [],
                    _key: 'new_' + (++this._priceCounter),
                    _origIdx: this.prices.length,
                });
            },

            removePrice(origIdx) {
                this.prices = this.prices.filter(p => p._origIdx !== origIdx);
                this.prices.forEach((p, i) => p._origIdx = i);
            },

            movePrice(fromIdx, toIdx) {
                const item = this.prices.splice(fromIdx, 1)[0];
                this.prices.splice(toIdx, 0, item);
                this.prices.forEach((p, i) => p._origIdx = i);
            },

            copySingleNewPrice(row) {
                if (!row.price) return;
                const name = (row.name || '').trim();
                const storage = row.storage ? ` ${row.storage}` : '';
                const color = row.color ? ` ${row.color}` : '';
                const price = parseFloat(row.price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                const notes = row.notes ? ` - ${row.notes}` : '';

                const text = `📱 *${name}${storage}${color}*\n💰 ${price}${notes}\n\n📲 DG Store - Consulte disponibilidade!`;

                navigator.clipboard.writeText(text).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.cssText = 'position:fixed;left:-9999px;';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                });
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

            openImageModal(priceIdx) {
                this.imgModal = { open: true, priceIdx: priceIdx, uploading: false };
            },

            closeImageModal() {
                this.imgModal = { open: false, priceIdx: null, uploading: false };
            },

            get imgModalPrice() {
                if (this.imgModal.priceIdx === null) return null;
                return this.prices[this.imgModal.priceIdx] || null;
            },

            async uploadPriceImage(event) {
                const file = event.target.files[0];
                if (!file) return;

                const price = this.imgModalPrice;
                if (!price || !price.id) {
                    alert('Salve a tabela antes de adicionar imagens a novos itens.');
                    event.target.value = '';
                    return;
                }

                if ((price.images || []).length >= 5) {
                    alert('Limite de 5 imagens atingido.');
                    event.target.value = '';
                    return;
                }

                this.imgModal.uploading = true;
                const formData = new FormData();
                formData.append('marketing_price_id', price.id);
                formData.append('image', file);

                try {
                    const res = await fetch('{{ route("marketing.price-images.store") }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData,
                    });
                    const json = await res.json();
                    if (json.success) {
                        if (!price.images) price.images = [];
                        price.images.push(json.image);
                    } else {
                        alert(json.message || 'Erro ao enviar imagem.');
                    }
                } catch (e) {
                    alert('Erro de conexão ao enviar imagem.');
                } finally {
                    this.imgModal.uploading = false;
                    event.target.value = '';
                }
            },

            async deletePriceImage(imageId) {
                if (!confirm('Remover esta imagem?')) return;

                const price = this.imgModalPrice;
                try {
                    const res = await fetch('/marketing/price-images/' + imageId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const json = await res.json();
                    if (json.success && price) {
                        price.images = (price.images || []).filter(img => img.id !== imageId);
                    }
                } catch (e) {
                    alert('Erro ao remover imagem.');
                }
            },

            openUsedImageModal(item) {
                this.usedImgModal = { open: true, itemKey: item.morph_type + '_' + item.id, uploading: false };
            },

            closeUsedImageModal() {
                this.usedImgModal = { open: false, itemKey: null, uploading: false };
            },

            get usedImgModalItem() {
                if (!this.usedImgModal.itemKey) return null;
                return this.usedItems.find(i => i.morph_type + '_' + i.id === this.usedImgModal.itemKey) || null;
            },

            async uploadUsedListingImage(event) {
                const file = event.target.files[0];
                if (!file) return;

                const item = this.usedImgModalItem;
                if (!item) return;

                if (!item.listing_id) {
                    alert('Salve o seminovo primeiro (clique em "Salvar Tudo") antes de adicionar fotos.');
                    event.target.value = '';
                    return;
                }

                if ((item.images || []).length >= 5) {
                    alert('Limite de 5 imagens atingido.');
                    event.target.value = '';
                    return;
                }

                this.usedImgModal.uploading = true;
                const formData = new FormData();
                formData.append('marketing_used_listing_id', item.listing_id);
                formData.append('image', file);

                try {
                    const res = await fetch('{{ route("marketing.used-listing-images.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });
                    const json = await res.json();
                    if (json.success) {
                        if (!item.images) item.images = [];
                        item.images.push(json.image);
                    } else {
                        alert(json.message || 'Erro ao enviar imagem.');
                    }
                } catch (e) {
                    alert('Erro de conexão ao enviar imagem.');
                } finally {
                    this.usedImgModal.uploading = false;
                    event.target.value = '';
                }
            },

            async deleteUsedListingImage(imageId) {
                if (!confirm('Remover esta imagem?')) return;

                const item = this.usedImgModalItem;
                try {
                    const res = await fetch('/marketing/used-listing-images/' + imageId, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const json = await res.json();
                    if (json.success && item) {
                        item.images = (item.images || []).filter(img => img.id !== imageId);
                    }
                } catch (e) {
                    alert('Erro ao remover imagem.');
                }
            },

            loadCreativesByDate() {
                window.location.href = '{{ route("marketing.index") }}?tab=creatives&date=' + this.creativeDate;
            },

            syncUsedToResale(item) {
                const key = item.morph_type + '_' + item.id;
                const match = this.resaleUsed.find(r => r.morph_type + '_' + r.id === key);
                if (!match) return;
                match._usedListing.notes = item.listing.notes || '';
                match._usedListing.cost_price = item.listing.cost_price;
                match._usedListing.final_price = item.listing.final_price;
                match._usedListing.battery_health = item.listing.battery_health;
                match._usedListing.has_box = item.listing.has_box;
                match._usedListing.has_cable = item.listing.has_cable;
            },

            _buildUsedPayload(item) {
                return {
                    listable_type: item.morph_type,
                    listable_id: item.id,
                    cost_price: item.listing.cost_price || null,
                    final_price: item.listing.final_price || null,
                    battery_health: item.listing.battery_health || null,
                    has_box: item.listing.has_box ? 1 : 0,
                    has_cable: item.listing.has_cable ? 1 : 0,
                    notes: item.listing.notes || null,
                    visible: item.listing.visible ? 1 : 0,
                };
            },

            async saveUsedListing(item) {
                item._saving = true;
                this.syncUsedToResale(item);
                try {
                    const res = await fetch('{{ route("marketing.used-listings.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this._buildUsedPayload(item)),
                    });
                    if (!res.ok) throw new Error('Erro ao salvar');
                    const json = await res.json();
                    if (json.success && json.listing && json.listing.id) {
                        item.listing_id = json.listing.id;
                    }
                    setTimeout(() => { item._saving = false; }, 1200);
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                    item._saving = false;
                }
            },

            async saveUsedVisibility(item) {
                try {
                    const res = await fetch('{{ route("marketing.used-listings.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this._buildUsedPayload(item)),
                    });
                    const json = await res.json();
                    if (json.success && json.listing && json.listing.id) {
                        item.listing_id = json.listing.id;
                    }
                } catch (e) {
                    // silently fail
                }
            },

            async saveAllUsedListings() {
                this.usedAllSaving = true;
                try {
                    const promises = this.usedItems.map(async (item) => {
                        this.syncUsedToResale(item);
                        const res = await fetch('{{ route("marketing.used-listings.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this._buildUsedPayload(item)),
                        });
                        const json = await res.json();
                        if (json.success && json.listing && json.listing.id) {
                            item.listing_id = json.listing.id;
                        }
                    });
                    await Promise.all(promises);
                    setTimeout(() => { this.usedAllSaving = false; }, 1500);
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                    this.usedAllSaving = false;
                }
            },

            copyUsedListToWhatsApp() {
                const visibleItems = this.usedItems.filter(i => i.listing.visible && i.listing.final_price);

                if (visibleItems.length === 0) {
                    alert('Nenhum seminovo marcado como "Na Lista" com preço final.');
                    return;
                }

                let lines = [];
                lines.push('📱 *SEMINOVOS DG STORE*');
                lines.push('━━━━━━━━━━━━━━━━━━━');
                lines.push('');

                visibleItems.forEach(item => {
                    const name = (item.name || '').trim();
                    const storage = item.storage ? ` ${item.storage}` : '';
                    const color = item.color || '';
                    const battery = item.listing.battery_health ? `🔋${item.listing.battery_health}%` : '';
                    const price = parseFloat(item.listing.final_price).toLocaleString('pt-BR', { minimumFractionDigits: 0 });

                    let accessories = '';
                    if (item.listing.has_box && item.listing.has_cable) {
                        accessories = '📦 Caixa e cabo';
                    } else if (item.listing.has_box) {
                        accessories = '📦 Caixa';
                    } else if (item.listing.has_cable) {
                        accessories = '🔌 Cabo';
                    }

                    let parts = [`*${name}${storage}*${color ? ' ' + color : ''}`];
                    if (battery) parts.push(battery);
                    if (accessories) parts.push(accessories);
                    if (item.listing.notes) parts.push(item.listing.notes);
                    parts.push(`💰R$ ${price}`);

                    lines.push(parts.join(' - '));
                });

                lines.push('');
                lines.push('📲 Consulte disponibilidade!');

                const text = lines.join('\n');

                navigator.clipboard.writeText(text).then(() => {
                    this.usedListCopied = true;
                    setTimeout(() => { this.usedListCopied = false; }, 2500);
                }).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    this.usedListCopied = true;
                    setTimeout(() => { this.usedListCopied = false; }, 2500);
                });
            },

            copyUsedToWhatsApp(item) {
                const name = (item.name || '').trim();
                const storage = item.storage ? ` ${item.storage}` : '';
                const color = item.color ? ` ${item.color}` : '';
                const battery = item.listing.battery_health ? `🔋 Bateria: ${item.listing.battery_health}%` : '';
                const price = item.listing.final_price
                    ? parseFloat(item.listing.final_price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })
                    : '';

                let accessories = '';
                if (item.listing.has_box && item.listing.has_cable) {
                    accessories = '📦 Caixa e cabo inclusos';
                } else if (item.listing.has_box) {
                    accessories = '📦 Caixa inclusa';
                } else if (item.listing.has_cable) {
                    accessories = '🔌 Cabo incluso';
                }

                let lines = [];
                lines.push(`📱 *${name}${storage}${color}*`);
                lines.push('');
                if (battery) lines.push(battery);
                if (accessories) lines.push(accessories);
                if (item.listing.notes) lines.push(`📝 ${item.listing.notes}`);
                if (price) {
                    lines.push('');
                    lines.push(`💰 *${price}*`);
                }
                lines.push('');
                lines.push('📲 DG Store - Consulte disponibilidade!');

                const text = lines.join('\n');

                navigator.clipboard.writeText(text).then(() => {
                    item._copied = true;
                    setTimeout(() => { item._copied = false; }, 2500);
                }).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    item._copied = true;
                    setTimeout(() => { item._copied = false; }, 2500);
                });
            },

            printUsedLabels() {
                const visibleItems = this.usedItems.filter(i => i.listing.visible && i.listing.final_price);

                if (visibleItems.length === 0) {
                    alert('Nenhum seminovo marcado como "Na Lista" com preço final.');
                    return;
                }

                let labelsHtml = '';
                visibleItems.forEach(item => {
                    const name = (item.name || '').trim();
                    const condition = item.condition === 'used' ? 'Seminovo' : 'Recondicionado';
                    const battery = item.listing.battery_health ? item.listing.battery_health + '%' : '';
                    const price = parseFloat(item.listing.final_price).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });

                    let extras = [];
                    if (item.listing.has_box) extras.push('Caixa');
                    if (item.listing.has_cable) extras.push('Cabo');
                    const accessoriesText = extras.length > 0 ? extras.join(' + ') : '';

                    labelsHtml += `
                        <div class="label">
                            <div class="label-condition">${condition}</div>
                            <div class="label-name">${name}</div>
                            <div class="label-price">${price}</div>
                            <div class="label-details">
                                ${battery ? '<span class="label-battery">Bat: ' + battery + '</span>' : ''}
                                ${accessoriesText ? '<span class="label-accessories">' + accessoriesText + '</span>' : ''}
                            </div>
                            ${item.listing.notes ? '<div class="label-notes">' + item.listing.notes + '</div>' : ''}
                            <div class="label-logo">
                                <svg viewBox="0 0 170 170" fill="currentColor"><path d="M150.4 130.3c-2.4 5.5-5.2 10.6-8.4 15.2-4.4 6.3-8 10.7-10.8 13.1-4.3 4-8.9 6-13.9 6.1-3.6 0-7.9-1-13-3.1-5.1-2-9.8-3.1-14.1-3.1-4.5 0-9.4 1-14.5 3.1-5.2 2.1-9.3 3.1-12.5 3.2-4.8.2-9.5-1.9-14.2-6.2-3-2.6-6.7-7.1-11.2-13.5-4.8-6.9-8.8-14.8-11.8-23.9-3.2-9.8-4.9-19.3-4.9-28.5 0-10.5 2.3-19.6 6.8-27.2 3.5-6.1 8.2-10.9 14.1-14.5 5.9-3.5 12.2-5.3 19.1-5.4 3.8 0 8.8 1.2 15.1 3.5 6.2 2.4 10.2 3.5 12 3.5 1.3 0 5.7-1.3 13.2-4 7.1-2.5 13-3.5 17.9-3.1 13.2 1.1 23.1 6.3 29.7 15.7-11.8 7.2-17.7 17.2-17.5 30.1.1 10 3.8 18.4 10.8 25 3.2 3 6.8 5.4 10.8 7-0.9 2.5-1.8 4.9-2.7 7.2zM119.1 7.3c0 7.9-2.9 15.2-8.6 21.9-6.9 8-15.2 12.7-24.2 11.9-.1-1-.2-2-.2-3.1 0-7.5 3.3-15.6 9.1-22.2 2.9-3.3 6.6-6.1 11.1-8.3 4.5-2.2 8.7-3.4 12.7-3.6.1 1.1.1 2.3.1 3.4z"/></svg>
                                <span>DG Store</span>
                            </div>
                        </div>
                    `;
                });

                const printWindow = window.open('', '_blank');
                printWindow.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Etiquetas Seminovos - DG Store</title>
    <style>
        @page {
            margin: 5mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #141414;
            color: #111;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 3mm;
            padding: 1mm;
        }
        .label {
            border: 1.5px solid #111;
            border-radius: 2.5mm;
            padding: 2.5mm 3mm 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            page-break-inside: avoid;
            gap: 1mm;
            overflow: hidden;
        }
        .label-condition {
            font-size: 5.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #fff;
            background: #111;
            padding: 0.5mm 2mm;
            border-radius: 1.5mm;
        }
        .label-name {
            font-size: 7.5pt;
            font-weight: 700;
            line-height: 1.15;
            color: #111;
            word-break: break-word;
        }
        .label-price {
            font-size: 12pt;
            font-weight: 800;
            color: #111;
        }
        .label-details {
            display: flex;
            gap: 1.5mm;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            font-size: 5.5pt;
            color: #444;
        }
        .label-battery {
            background: #222222;
            padding: 0.3mm 1.5mm;
            border-radius: 1mm;
            font-weight: 600;
        }
        .label-accessories {
            background: #222222;
            padding: 0.3mm 1.5mm;
            border-radius: 1mm;
            font-weight: 600;
        }
        .label-notes {
            font-size: 5pt;
            color: #666;
            font-style: italic;
            max-width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .label-logo {
            display: flex;
            align-items: center;
            gap: 1mm;
            margin-top: 0.5mm;
            color: #bbb;
        }
        .label-logo svg {
            width: 7pt;
            height: 7pt;
        }
        .label-logo span {
            font-size: 5.5pt;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        @media print {
            body { background: #141414; }
            .no-print { display: none !important; }
            .grid { gap: 4mm; }
        }
        .toolbar {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 16px;
            background: #1a1a1a;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .toolbar button {
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-print {
            background: #111827;
            color: #fff;
        }
        .btn-close {
            background: #e5e7eb;
            color: #a4a4a4;
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <button class="btn-print" onclick="window.print()">Imprimir</button>
        <button class="btn-close" onclick="window.close()">Fechar</button>
    </div>
    <div class="grid">${labelsHtml}</div>
</body>
</html>`);
                printWindow.document.close();
            },

            async saveResaleItem(item) {
                item._saving = true;
                try {
                    const ul = item._usedListing || {};
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
                            battery_health: item.resale.battery_health || ul.battery_health || null,
                            warranty_until: item.resale.warranty_until || null,
                            has_box: (item.resale.has_box || ul.has_box) ? 1 : 0,
                            has_cable: (item.resale.has_cable || ul.has_cable) ? 1 : 0,
                            notes: item.resale.notes || ul.notes || null,
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

            async saveAllResaleUsed() {
                this.resaleUsedAllSaving = true;
                try {
                    const promises = this.resaleUsed.map(item => {
                        const ul = item._usedListing || {};
                        return fetch('{{ route("marketing.resale-items.store") }}', {
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
                                battery_health: item.resale.battery_health || ul.battery_health || null,
                                warranty_until: item.resale.warranty_until || null,
                                has_box: (item.resale.has_box || ul.has_box) ? 1 : 0,
                                has_cable: (item.resale.has_cable || ul.has_cable) ? 1 : 0,
                                notes: item.resale.notes || ul.notes || null,
                                visible: item.resale.visible ? 1 : 0,
                            }),
                        });
                    });
                    await Promise.all(promises);
                    setTimeout(() => { this.resaleUsedAllSaving = false; }, 1500);
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                    this.resaleUsedAllSaving = false;
                }
            },

            async saveResaleVisibility(item) {
                try {
                    await fetch('{{ route("marketing.resale-items.store") }}', {
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
                } catch (e) {
                    // silently fail
                }
            },

            _copyToClipboard(text, flagName) {
                navigator.clipboard.writeText(text).then(() => {
                    this[flagName] = true;
                    setTimeout(() => { this[flagName] = false; }, 2500);
                }).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    this[flagName] = true;
                    setTimeout(() => { this[flagName] = false; }, 2500);
                });
            },

            copySingleResale(item) {
                if (!item.resale.resale_price) {
                    alert('Defina o preço de repasse antes de copiar.');
                    return;
                }

                const ul = item._usedListing || {};
                const namePart = (item.name || '') + (item.storage ? ' ' + item.storage : '');
                const color = item.color || '';
                const bat = item.resale.battery_health || ul.battery_health;
                const battery = bat ? `🔋${bat}%` : '';
                const hasBox = item.resale.has_box || ul.has_box;

                let accessories = hasBox ? '📦 Caixa' : '❌Caixa';

                let warranty = '';
                if (item.resale.warranty_until) {
                    const d = new Date(item.resale.warranty_until);
                    const months = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
                    warranty = `🛡️ Garantia até ${months[d.getMonth()]}/${d.getFullYear()}`;
                }

                const price = parseFloat(item.resale.resale_price).toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                const notes = item.resale.notes || ul.notes || '';

                let parts = [`${namePart} ${color}`.trim()];
                if (battery) parts.push(battery);
                if (accessories) parts.push(accessories);
                if (warranty) parts.push(warranty);
                if (notes) parts.push(notes);
                parts.push(`💰R$ ${price}`);

                const text = parts.join(' - ');

                navigator.clipboard.writeText(text).catch(() => {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.cssText = 'position:fixed;left:-9999px;';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                });
            },

            copyResaleSeminovos() {
                const visibleUsed = this.resaleUsed.filter(u => u.resale.visible && u.resale.resale_price);

                if (visibleUsed.length === 0) {
                    alert('Nenhum seminovo marcado como visível com preço.');
                    return;
                }

                let lines = [];
                lines.push('LISTA DE REPASSE DG STORE');
                lines.push('');
                lines.push('*SEMINOVOS*');
                lines.push('');

                visibleUsed.forEach(u => {
                    const ul = u._usedListing || {};
                    const namePart = (u.name || '') + (u.storage ? ' ' + u.storage : '');
                    const color = u.color || '';
                    const bat = u.resale.battery_health || ul.battery_health;
                    const battery = bat ? `🔋${bat}%` : '';
                    const hasBox = u.resale.has_box || ul.has_box;

                    let accessories = hasBox ? '📦 Caixa' : '❌Caixa';

                    let warranty = '';
                    if (u.resale.warranty_until) {
                        const d = new Date(u.resale.warranty_until);
                        const months = ['jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez'];
                        warranty = `🛡️ Garantia até ${months[d.getMonth()]}/${d.getFullYear()}`;
                    }

                    const price = parseFloat(u.resale.resale_price).toLocaleString('pt-BR', { minimumFractionDigits: 0 });
                    const notes = u.resale.notes || ul.notes || '';

                    let parts = [`${namePart} ${color}`.trim()];
                    if (battery) parts.push(battery);
                    if (accessories) parts.push(accessories);
                    if (warranty) parts.push(warranty);
                    if (notes) parts.push(notes);
                    parts.push(`💰R$ ${price}`);

                    lines.push(parts.join(' - '));
                });

                this._copyToClipboard(lines.join('\n'), 'resaleSeminovosCopied');
            },
        };
    }
    </script>

    <style>
        .used-card-mobile {
            position: relative;
            background: #141414;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.75rem;
            padding: 0.875rem;
            cursor: pointer;
            transition: all 0.15s;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        .used-card-mobile:active:not(.used-card-tapped) {
            transform: scale(0.98);
            background: #1a1a1a;
        }
        .used-card-tapped {
            border-color: #059669 !important;
        }
        .resale-copy-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 1.75rem;
            height: 1.75rem;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .resale-copy-btn:hover:not(.copied) {
            background: rgba(217,119,6,0.15);
            border-color: rgba(217,119,6,0.3);
        }
        .resale-copy-btn.copied {
            background: #059669;
            border-color: #059669;
            cursor: default;
        }
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
