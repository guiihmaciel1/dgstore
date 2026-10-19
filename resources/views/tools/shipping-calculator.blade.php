<x-app-layout>
    <x-slot name="title">Calculadora de Frete</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8" x-data="shippingCalc()" x-init="init()">

            {{-- Header --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h1 style="font-size: 1.375rem; font-weight: 700; color: #e3e3e3; display: flex; align-items: center; gap: 0.5rem;">
                            <svg style="width:1.5rem;height:1.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                            </svg>
                            Calculadora de Frete
                        </h1>
                        <p style="font-size: 0.8125rem; color: #818181;">Entregas na regi&atilde;o de S&atilde;o Jos&eacute; do Rio Preto</p>
                    </div>
                    <button x-show="selected" @click="reset()" type="button"
                            style="padding: 0.5rem 0.875rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.06); background: transparent; cursor: pointer; color: #818181; font-size: 0.8125rem; display: flex; align-items: center; gap: 0.375rem;"
                            x-cloak>
                        <svg style="width:1rem;height:1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.992 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.185M21.036 4.356v4.992"/>
                        </svg>
                        Limpar
                    </button>
                </div>
            </div>

            {{-- Seletor de Cidade --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: #a4a4a4; margin-bottom: 0.5rem; display: block;">Cidade de destino</label>
                <div style="position: relative;">
                    <div style="position: relative;">
                        <svg style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); width: 1.125rem; height: 1.125rem; color: #555; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-model="search"
                               @focus="dropdownOpen = true"
                               @click="dropdownOpen = true"
                               @input="dropdownOpen = true; selected = null"
                               @keydown.escape="dropdownOpen = false"
                               @keydown.arrow-down.prevent="moveHighlight(1)"
                               @keydown.arrow-up.prevent="moveHighlight(-1)"
                               @keydown.enter.prevent="selectHighlighted()"
                               placeholder="Digite o nome da cidade..."
                               style="width: 100%; padding: 0.75rem 0.75rem 0.75rem 2.75rem; border-radius: 0.75rem; border: 1px solid rgba(255,255,255,0.08); background: #0d0d0d; color: #e3e3e3; font-size: 0.9375rem; outline: none; transition: border-color 0.15s;"
                               onfocus="this.style.borderColor='rgba(59,130,246,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'"
                        />
                    </div>

                    {{-- Dropdown --}}
                    <div x-show="dropdownOpen && filteredCities.length > 0"
                         @click.outside="dropdownOpen = false"
                         x-cloak
                         style="position: absolute; z-index: 30; width: 100%; margin-top: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.75rem; max-height: 18rem; overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.4);">
                        <template x-for="(city, idx) in filteredCities" :key="city.name">
                            <button type="button"
                                    @click="selectCity(city)"
                                    @mouseenter="highlightIdx = idx"
                                    :style="highlightIdx === idx
                                        ? 'width: 100%; padding: 0.625rem 1rem; display: flex; align-items: center; justify-content: space-between; cursor: pointer; background: rgba(59,130,246,0.1); border: none; text-align: left; color: #e3e3e3;'
                                        : 'width: 100%; padding: 0.625rem 1rem; display: flex; align-items: center; justify-content: space-between; cursor: pointer; background: transparent; border: none; text-align: left; color: #a4a4a4;'"
                            >
                                <span style="display: flex; align-items: center; gap: 0.5rem;">
                                    <svg style="width:0.875rem;height:0.875rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span x-text="city.name" style="font-size: 0.875rem; font-weight: 500;"></span>
                                </span>
                                <span style="display: flex; align-items: center; gap: 0.625rem; font-size: 0.75rem;">
                                    <span x-text="city.distance + 'km'" style="color: #666;"></span>
                                    <span x-show="city.toll > 0" style="background: rgba(234,179,8,0.15); color: #eab308; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.6875rem; font-weight: 600;" x-cloak>Ped&aacute;gio</span>
                                </span>
                            </button>
                        </template>
                    </div>

                    {{-- Nenhum resultado --}}
                    <div x-show="dropdownOpen && search.length >= 2 && filteredCities.length === 0"
                         x-cloak
                         style="position: absolute; z-index: 30; width: 100%; margin-top: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.75rem; padding: 1rem; text-align: center; color: #666; font-size: 0.8125rem;">
                        Nenhuma cidade encontrada neste raio
                    </div>
                </div>
            </div>

            {{-- Resultado --}}
            <div x-show="selected" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="margin-bottom: 1.25rem;">

                {{-- Cards de valores --}}
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; margin-bottom: 1rem;">

                    {{-- Distancia --}}
                    <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem 1.25rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Dist&acirc;ncia</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;" x-text="selected ? selected.distance + ' km' : ''"></div>
                        <div style="font-size: 0.75rem; color: #555; margin-top: 0.125rem;" x-text="selected ? 'Faixa: at\u00e9 ' + getBracketLabel() : ''"></div>
                    </div>

                    {{-- Frete --}}
                    <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem 1.25rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Frete</div>
                        <div style="font-size: 1.5rem; font-weight: 700;" :style="getShippingCost() === 0 ? 'color: #22c55e;' : 'color: #e3e3e3;'" x-text="formatCurrency(getShippingCost())"></div>
                        <div x-show="getShippingCost() === 0" style="font-size: 0.75rem; color: #22c55e; margin-top: 0.125rem;">Entrega gr&aacute;tis!</div>
                    </div>

                    {{-- Pedagio --}}
                    <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1rem 1.25rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Ped&aacute;gio</div>
                        <div style="font-size: 1.5rem; font-weight: 700;"
                             :style="selected && selected.toll > 0 ? 'color: #eab308;' : 'color: #555;'"
                             x-text="selected && selected.toll > 0 ? formatCurrency(selected.toll) : 'Sem ped\u00e1gio'"></div>
                        <div x-show="selected && selected.toll > 0" style="font-size: 0.75rem; color: #a3842a; margin-top: 0.125rem;" x-text="selected ? selected.tollName : ''" x-cloak></div>
                        <div x-show="selected && selected.toll > 0" style="font-size: 0.6875rem; color: #666; margin-top: 0.25rem;" x-text="selected ? 'Com TAG: ' + formatCurrency(selected.tollTag) : ''" x-cloak></div>
                    </div>

                    {{-- Total --}}
                    <div style="background: linear-gradient(135deg, rgba(59,130,246,0.08), rgba(59,130,246,0.02)); border: 1px solid rgba(59,130,246,0.2); border-radius: 0.75rem; padding: 1rem 1.25rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem;">Custo Total</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: #e3e3e3;" x-text="formatCurrency(getTotalCost())"></div>
                        <div x-show="selected && selected.toll > 0" style="font-size: 0.75rem; color: #555; margin-top: 0.125rem;" x-text="'Com TAG: ' + formatCurrency(getTotalCostTag())" x-cloak></div>
                    </div>
                </div>

                {{-- Observacao sobre ida e volta do pedagio --}}
                <div x-show="selected && selected.toll > 0"
                     x-cloak
                     style="background: rgba(234,179,8,0.06); border: 1px solid rgba(234,179,8,0.15); border-radius: 0.75rem; padding: 0.75rem 1rem; display: flex; align-items: flex-start; gap: 0.625rem;">
                    <svg style="width:1rem;height:1rem;flex-shrink:0;margin-top:0.125rem;color:#eab308;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div style="font-size: 0.8125rem; color: #a3842a;">
                        <strong style="color: #eab308;">Aten&ccedil;&atilde;o:</strong> O valor do ped&aacute;gio refere-se a um trecho (ida). Para ida e volta, considere o dobro.
                    </div>
                </div>
            </div>

            {{-- Tabela de Faixas de Frete --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem;">
                <h2 style="font-size: 1rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width:1.125rem;height:1.125rem;color:#555;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Tabela de Frete Progressivo
                </h2>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Faixa</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="bracket in brackets" :key="bracket.label">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);"
                                    :style="selected && getBracketLabel() === bracket.label ? 'background: rgba(59,130,246,0.06);' : ''">
                                    <td style="padding: 0.625rem 0.75rem; font-size: 0.875rem; color: #a4a4a4;" x-text="bracket.label"></td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.875rem; font-weight: 600;"
                                        :style="bracket.cost === 0 ? 'color: #22c55e;' : 'color: #e3e3e3;'"
                                        x-text="bracket.cost === 0 ? 'Gr\u00e1tis' : formatCurrency(bracket.cost)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tabela de Cidades --}}
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 1rem; padding: 1.25rem 1.5rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <h2 style="font-size: 1rem; font-weight: 700; color: #e3e3e3; display: flex; align-items: center; gap: 0.5rem;">
                        <svg style="width:1.125rem;height:1.125rem;color:#555;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Cidades Atendidas
                    </h2>
                    <span style="font-size: 0.75rem; color: #555;" x-text="cities.length + ' cidades atendidas'"></span>
                </div>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th style="padding: 0.625rem 0.75rem; text-align: left; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Cidade</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Dist&acirc;ncia</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Frete</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Ped&aacute;gio</th>
                                <th style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.6875rem; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: 0.05em;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="city in citiesSorted" :key="city.name">
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.03); cursor: pointer;"
                                    @click="selectCity(city)"
                                    :style="selected && selected.name === city.name
                                        ? 'background: rgba(59,130,246,0.06); border-bottom: 1px solid rgba(255,255,255,0.03);'
                                        : 'border-bottom: 1px solid rgba(255,255,255,0.03);'"
                                    onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                                    <td style="padding: 0.625rem 0.75rem;">
                                        <span style="font-size: 0.875rem; font-weight: 500; color: #e3e3e3;" x-text="city.name"></span>
                                        <span x-show="city.isDistrict" style="font-size: 0.6875rem; color: #555; margin-left: 0.375rem;" x-cloak>(distrito)</span>
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: center; font-size: 0.875rem; color: #a4a4a4;" x-text="city.distance + ' km'"></td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.875rem; font-weight: 600;"
                                        :style="calcShippingForCity(city) === 0 ? 'color: #22c55e;' : 'color: #e3e3e3;'"
                                        x-text="calcShippingForCity(city) === 0 ? 'Gr\u00e1tis' : formatCurrency(calcShippingForCity(city))"></td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: center;">
                                        <span x-show="city.toll > 0"
                                              style="display: inline-flex; align-items: center; gap: 0.25rem; background: rgba(234,179,8,0.12); color: #eab308; padding: 0.1875rem 0.5rem; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600;"
                                              x-cloak>
                                            <span x-text="formatCurrency(city.toll)"></span>
                                        </span>
                                        <span x-show="city.toll === 0" style="font-size: 0.75rem; color: #444;">&mdash;</span>
                                    </td>
                                    <td style="padding: 0.625rem 0.75rem; text-align: right; font-size: 0.875rem; font-weight: 700; color: #e3e3e3;"
                                        x-text="formatCurrency(calcShippingForCity(city) + city.toll)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function shippingCalc() {
            return {
                search: '',
                selected: null,
                dropdownOpen: false,
                highlightIdx: 0,

                brackets: [
                    { min: 0,  max: 0,  cost: 0,     label: 'Dentro da cidade (Rio Preto)' },
                    { min: 1,  max: 10, cost: 30,    label: 'At\u00e9 10km' },
                    { min: 11, max: 20, cost: 50,    label: 'At\u00e9 20km' },
                    { min: 21, max: 30, cost: 60,    label: 'At\u00e9 30km' },
                    { min: 31, max: 40, cost: 80,    label: 'At\u00e9 40km' },
                    { min: 41, max: 50, cost: 100,   label: 'At\u00e9 50km' },
                    { min: 51, max: 60, cost: 120,   label: 'At\u00e9 60km' },
                ],

                cities: [
                    { name: 'S\u00e3o Jos\u00e9 do Rio Preto', distance: 0, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Engenheiro Schmitt', distance: 12, toll: 0, tollTag: 0, tollName: null, isDistrict: true, fixedCost: 30 },
                    { name: 'Bady Bassitt', distance: 15, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: 30 },
                    { name: 'Mirassol', distance: 16, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: 30 },
                    { name: 'Cedral', distance: 19, toll: 19.80, tollTag: 18.81, tollName: 'Catigu\u00e1 \u2013 SP-310', isDistrict: false, fixedCost: null },
                    { name: 'Ipigua', distance: 19, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Guapia\u00e7u', distance: 20, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: 30 },
                    { name: 'Jaci', distance: 27, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'B\u00e1lsamo', distance: 28, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Mirassol\u00e2ndia', distance: 28, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Nova Alian\u00e7a', distance: 28, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Onda Verde', distance: 28, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Neves Paulista', distance: 34, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Potirendaba', distance: 35, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Nova Granada', distance: 35, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Uchoa', distance: 35, toll: 19.80, tollTag: 18.81, tollName: 'Catigu\u00e1 \u2013 SP-310', isDistrict: false, fixedCost: null },
                    { name: 'Monte Apraz\u00edvel', distance: 38, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Tanabi', distance: 41, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Ibir\u00e1', distance: 41, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Jos\u00e9 Bonif\u00e1cio', distance: 47, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Mendon\u00e7a', distance: 50, toll: 0, tollTag: 0, tollName: null, isDistrict: false, fixedCost: null },
                    { name: 'Ol\u00edmpia', distance: 54, toll: 19.80, tollTag: 18.81, tollName: 'Catigu\u00e1 \u2013 SP-310', isDistrict: false, fixedCost: null },
                    { name: 'Catanduva', distance: 60, toll: 19.80, tollTag: 18.81, tollName: 'Catigu\u00e1 \u2013 SP-310', isDistrict: false, fixedCost: null },
                ],

                init() {},

                get filteredCities() {
                    if (!this.search || this.search.length < 1) return this.cities;
                    const q = this.normalizeStr(this.search);
                    return this.cities.filter(c => this.normalizeStr(c.name).includes(q));
                },

                get citiesSorted() {
                    return [...this.cities].sort((a, b) => a.distance - b.distance);
                },

                normalizeStr(str) {
                    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
                },

                selectCity(city) {
                    this.selected = city;
                    this.search = city.name;
                    this.dropdownOpen = false;
                },

                selectHighlighted() {
                    const list = this.filteredCities;
                    if (list.length > 0 && this.highlightIdx >= 0 && this.highlightIdx < list.length) {
                        this.selectCity(list[this.highlightIdx]);
                    }
                },

                moveHighlight(dir) {
                    const list = this.filteredCities;
                    if (list.length === 0) return;
                    this.highlightIdx = Math.max(0, Math.min(list.length - 1, this.highlightIdx + dir));
                },

                reset() {
                    this.selected = null;
                    this.search = '';
                    this.highlightIdx = 0;
                },

                calcShippingForCity(city) {
                    if (city.fixedCost !== null) return city.fixedCost;
                    return this.calcShipping(city.distance);
                },

                calcShipping(distance) {
                    if (distance === 0) return 0;
                    for (const b of this.brackets) {
                        if (distance >= b.min && distance <= b.max) return b.cost;
                    }
                    return 100;
                },

                getShippingCost() {
                    if (!this.selected) return 0;
                    return this.calcShippingForCity(this.selected);
                },

                getBracketLabel() {
                    if (!this.selected) return '';
                    if (this.selected.fixedCost !== null) return 'Valor fixo';
                    const d = this.selected.distance;
                    for (const b of this.brackets) {
                        if (d >= b.min && d <= b.max) return b.label;
                    }
                    return 'At\u00e9 60km';
                },

                getTotalCost() {
                    if (!this.selected) return 0;
                    return this.getShippingCost() + (this.selected.toll || 0);
                },

                getTotalCostTag() {
                    if (!this.selected) return 0;
                    return this.getShippingCost() + (this.selected.tollTag || 0);
                },

                formatCurrency(value) {
                    return 'R$ ' + value.toFixed(2).replace('.', ',');
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
