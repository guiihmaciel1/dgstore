<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Comparativo Apple — DG Store</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logodg.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Figtree', system-ui, sans-serif; background: #f8fafc; color: #111827; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }

        .container { max-width: 720px; margin: 0 auto; padding: 1.5rem 1rem 3rem; }

        /* Header / Branding */
        .brand-header {
            text-align: center;
            padding: 1.5rem 1rem 1rem;
        }
        .brand-header img { height: 40px; margin-bottom: 0.5rem; }
        .brand-header h1 {
            font-size: 1.25rem; font-weight: 700; color: #111827;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .brand-header p { font-size: 0.8125rem; color: #6b7280; margin-top: 0.25rem; }

        /* Empty state */
        .empty-state {
            text-align: center; padding: 4rem 1rem; color: #9ca3af;
        }
        .empty-state svg { width: 3rem; height: 3rem; margin: 0 auto 0.75rem; opacity: 0.3; }
        .empty-state p { font-size: 0.875rem; }

        /* Table card */
        .compare-card {
            background: white; border: 1px solid #e5e7eb; border-radius: 1rem; overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .header-row { display: grid; }
        .header-row.dual { grid-template-columns: minmax(110px, 0.7fr) 1fr 1fr; }
        .header-row.single { grid-template-columns: minmax(110px, 0.7fr) 1fr; }

        .header-label {
            padding: 1rem 1.25rem; background: #f9fafb;
            border-bottom: 2px solid #e5e7eb; border-right: 1px solid #e5e7eb;
            font-size: 0.6875rem; font-weight: 600; color: #9ca3af;
            text-transform: uppercase; letter-spacing: 0.05em;
        }
        .header-m1 {
            padding: 1rem 1.25rem; background: #111827; border-bottom: 2px solid #111827;
        }
        .header-m2 {
            padding: 1rem 1.25rem; background: #374151; border-bottom: 2px solid #374151;
            border-left: 1px solid #4b5563;
        }
        .header-m1 .name, .header-m2 .name { font-size: 1rem; font-weight: 700; color: white; }
        .header-m1 .year, .header-m2 .year { font-size: 0.75rem; color: #9ca3af; }

        /* Section header */
        .section-title {
            padding: 0.5rem 1.25rem; background: #f3f4f6;
            border-bottom: 1px solid #e5e7eb; border-top: 1px solid #e5e7eb;
            font-size: 0.6875rem; font-weight: 700; color: #6b7280;
            text-transform: uppercase; letter-spacing: 0.05em;
        }

        /* Spec rows */
        .spec-row { display: grid; border-bottom: 1px solid #f3f4f6; }
        .spec-row.dual { grid-template-columns: minmax(110px, 0.7fr) 1fr 1fr; }
        .spec-row.single { grid-template-columns: minmax(110px, 0.7fr) 1fr; }

        .spec-label {
            padding: 0.625rem 1.25rem; font-size: 0.8125rem; color: #6b7280;
            border-right: 1px solid #f3f4f6; display: flex; align-items: center;
        }
        .spec-val {
            padding: 0.625rem 1.25rem; font-size: 0.8125rem; font-weight: 500;
            display: flex; align-items: center; gap: 0.375rem;
        }
        .spec-val.m2 { border-left: 1px solid #f3f4f6; }

        .better { background: #f0fdf4; color: #166534; font-weight: 600; }
        .worse  { background: #fef2f2; color: #991b1b; }
        .diff   { background: #fefce8; color: #92400e; }
        .equal  { color: #111827; }

        .check-icon { width: 0.875rem; height: 0.875rem; flex-shrink: 0; color: #16a34a; }

        /* Legend */
        .legend {
            display: flex; gap: 1.25rem; justify-content: center; flex-wrap: wrap;
            margin-top: 0.75rem; font-size: 0.75rem; color: #6b7280;
        }
        .legend span { display: flex; align-items: center; gap: 0.25rem; }
        .legend-dot {
            width: 0.75rem; height: 0.75rem; border-radius: 0.25rem;
            border: 1px solid;
        }

        /* Footer */
        .footer {
            text-align: center; margin-top: 2rem; padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }
        .footer-logo { height: 28px; opacity: 0.5; margin-bottom: 0.5rem; }
        .footer p { font-size: 0.75rem; color: #9ca3af; }
        .footer a {
            display: inline-block; margin-top: 0.75rem; padding: 0.5rem 1.25rem;
            background: #16a34a; color: white; font-size: 0.8125rem; font-weight: 600;
            border-radius: 0.5rem; text-decoration: none; transition: background 0.15s;
        }
        .footer a:hover { background: #15803d; }

        /* Mobile tweaks */
        @media (max-width: 480px) {
            .header-row.dual, .spec-row.dual {
                grid-template-columns: minmax(80px, 0.6fr) 1fr 1fr;
            }
            .spec-label, .spec-val, .header-label, .header-m1, .header-m2 {
                padding: 0.5rem 0.75rem; font-size: 0.75rem;
            }
            .header-m1 .name, .header-m2 .name { font-size: 0.875rem; }
        }
    </style>
</head>
<body>
    <div class="container" x-data="publicCompare()" x-cloak>

        <!-- Branding -->
        <div class="brand-header">
            <h1>
                <svg style="width:1.5rem;height:1.5rem;" viewBox="0 0 24 24" fill="#111827"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/></svg>
                Comparativo Apple
            </h1>
            <p>Especificações técnicas lado a lado</p>
        </div>

        <!-- Empty state -->
        <template x-if="!model1">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p>Link inválido ou modelos não encontrados.</p>
            </div>
        </template>

        <!-- Comparison table -->
        <template x-if="model1">
            <div>
                <div class="compare-card">
                    <!-- Table header -->
                    <div class="header-row" :class="model2 ? 'dual' : 'single'">
                        <div class="header-label">Especificação</div>
                        <div class="header-m1">
                            <div class="name" x-text="model1.name"></div>
                            <div class="year" x-text="model1.year"></div>
                        </div>
                        <template x-if="model2">
                            <div class="header-m2">
                                <div class="name" x-text="model2.name"></div>
                                <div class="year" x-text="model2.year"></div>
                            </div>
                        </template>
                    </div>

                    <!-- Sections -->
                    <template x-for="(section, sIdx) in specSections" :key="sIdx">
                        <div>
                            <div class="section-title" x-text="section.label"></div>
                            <template x-for="(field, fIdx) in section.fields" :key="field.key">
                                <div class="spec-row" :class="model2 ? 'dual' : 'single'">
                                    <div class="spec-label" x-text="field.label"></div>
                                    <div class="spec-val" :class="cellClass(field.key, 1)">
                                        <span x-text="model1[field.key] || '-'"></span>
                                        <template x-if="model2 && getComparison(field.key) === 1">
                                            <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        </template>
                                    </div>
                                    <template x-if="model2">
                                        <div class="spec-val m2" :class="cellClass(field.key, 2)">
                                            <span x-text="model2[field.key] || '-'"></span>
                                            <template x-if="getComparison(field.key) === -1">
                                                <svg class="check-icon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Legend (only for dual compare) -->
                <template x-if="model2">
                    <div class="legend">
                        <span><span class="legend-dot" style="background:#dcfce7;border-color:#bbf7d0;"></span> Melhor</span>
                        <span><span class="legend-dot" style="background:#fee2e2;border-color:#fecaca;"></span> Inferior</span>
                        <span><span class="legend-dot" style="background:#fefce8;border-color:#fef08a;"></span> Diferente</span>
                        <span><span class="legend-dot" style="background:white;border-color:#e5e7eb;"></span> Igual</span>
                    </div>
                </template>
            </div>
        </template>

        <!-- Footer with CTA -->
        <div class="footer">
            @if(file_exists(public_path('images/logodg.png')))
                <img src="{{ asset('images/logodg.png') }}" alt="DG Store" class="footer-logo">
            @endif
            <p>Comparativo gerado pela <strong>DG Store</strong></p>
            <a href="https://wa.me/5567999999999" target="_blank">
                <svg style="width:1rem;height:1rem;vertical-align:middle;margin-right:0.25rem;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Fale conosco
            </a>
        </div>
    </div>

    <script src="//cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js" defer></script>
    <script src="{{ asset('js/apple-specs-data.js') }}"></script>
    <script>
    function publicCompare() {
        const params = new URLSearchParams(window.location.search);
        const m1Name = params.get('m1') || '';
        const m2Name = params.get('m2') || '';
        const allModels = specsModels();

        return {
            model1: allModels.find(m => m.name === m1Name) || null,
            model2: allModels.find(m => m.name === m2Name) || null,

            specSections: [
                { label: 'Tela', fields: [
                    { key: 'screen', label: 'Tela' },
                    { key: 'brightness', label: 'Brilho' },
                    { key: 'refresh', label: 'Taxa atualiz.' },
                ]},
                { label: 'Desempenho', fields: [
                    { key: 'chip', label: 'Chip' },
                    { key: 'ram', label: 'RAM' },
                    { key: 'storage', label: 'Armazenamento' },
                ]},
                { label: 'Câmeras', fields: [
                    { key: 'mainCam', label: 'Cam. traseira' },
                    { key: 'frontCam', label: 'Cam. frontal' },
                    { key: 'video', label: 'Vídeo' },
                ]},
                { label: 'Bateria e Energia', fields: [
                    { key: 'batteryCap', label: 'Capacidade' },
                    { key: 'battery', label: 'Duração' },
                    { key: 'charging', label: 'Carregamento' },
                ]},
                { label: 'Conectividade', fields: [
                    { key: 'sim', label: 'SIM' },
                    { key: 'connectivity', label: 'Conexões' },
                    { key: 'water', label: 'Resist. água' },
                ]},
                { label: 'Físico', fields: [
                    { key: 'dimensions', label: 'Dimensões' },
                    { key: 'weight', label: 'Peso' },
                    { key: 'material', label: 'Material' },
                    { key: 'biometrics', label: 'Biometria' },
                ]},
                { label: 'Destaque', fields: [
                    { key: 'highlight', label: 'Recursos' },
                ]},
            ],

            getComparison(key) {
                if (!this.model1 || !this.model2) return 0;
                const v1 = this.model1[key], v2 = this.model2[key];
                if (!v1 || !v2 || v1 === '-' || v2 === '-' || v1 === v2) return 0;

                const noRank = ['storage','charging','connectivity','material','highlight','sim','biometrics','dimensions','video'];
                if (noRank.includes(key)) return 2;

                if (key === 'chip') {
                    const r1 = this.chipRank(v1), r2 = this.chipRank(v2);
                    return r1 === r2 ? 2 : (r1 > r2 ? 1 : -1);
                }
                if (key === 'water') {
                    const r1 = this.waterRank(v1), r2 = this.waterRank(v2);
                    return r1 === r2 ? 2 : (r1 > r2 ? 1 : -1);
                }

                const n1 = this.extractNum(v1), n2 = this.extractNum(v2);
                if (n1 === null || n2 === null) return 2;
                if (n1 === n2) return 2;
                if (key === 'weight') return n1 < n2 ? 1 : -1;
                return n1 > n2 ? 1 : -1;
            },

            cellClass(key, modelNum) {
                if (!this.model2) return 'equal';
                const cmp = this.getComparison(key);
                if (cmp === 0) return 'equal';
                if (cmp === 2) return 'diff';
                if (modelNum === 1) return cmp === 1 ? 'better' : 'worse';
                return cmp === -1 ? 'better' : 'worse';
            },

            extractNum(str) {
                if (!str) return null;
                const m = str.match(/([\d.]+)/);
                return m ? parseFloat(m[1]) : null;
            },

            chipRank(chip) {
                const ranks = {
                    'A19 Pro': 100, 'A19': 95,
                    'A18 Pro': 90, 'A18 + Apple C1': 86, 'A18': 85,
                    'A17 Pro': 80, 'A16 Bionic': 75, 'A15 Bionic': 70, 'A14 Bionic': 65, 'A13 Bionic': 60,
                    'Apple M4 Pro': 120, 'Apple M4': 115,
                    'Apple M3 Max': 114, 'Apple M3 Pro': 112, 'Apple M3': 110,
                    'Apple M2 Pro': 107, 'Apple M2': 105,
                    'Apple M1 Pro': 102, 'Apple M1': 100,
                    'S10 SiP': 50, 'S9 SiP': 45, 'S8 SiP': 40, 'S5 SiP': 35,
                    'H3': 30, 'H2': 25, 'H1': 20,
                };
                return ranks[chip] || 0;
            },

            waterRank(val) {
                if (!val || val === '-') return 0;
                const u = val.toUpperCase();
                if (u.includes('IP68') && u.includes('6M')) return 68.6;
                if (u.includes('IP68')) return 68;
                if (u.includes('IP54')) return 54;
                if (u.includes('IPX4')) return 4;
                if (u.includes('WR100') || u.includes('EN13319')) return 100;
                if (u.includes('WR50')) return 50;
                return 0;
            },
        };
    }
    </script>
</body>
</html>
