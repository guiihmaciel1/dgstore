<div x-data="idleMode()" x-cloak style="margin-bottom: 1rem;">
    {{-- BOTÃO PRINCIPAL --}}
    <div @click="toggle()" style="cursor: pointer;">
        <div style="background: linear-gradient(135deg, #312e81 0%, #4f46e5 50%, #6366f1 100%); border-radius: 0.75rem; padding: 1rem 1.25rem; position: relative; overflow: hidden; transition: box-shadow 0.2s;"
             onmouseover="this.style.boxShadow='0 8px 25px rgba(79,70,229,0.3)'" onmouseout="this.style.boxShadow='none'">
            <div style="position: absolute; top: -30%; right: -8%; width: 160px; height: 160px; background: rgba(255,255,255,0.06); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -40%; left: 10%; width: 120px; height: 120px; background: rgba(255,255,255,0.04); border-radius: 50%;"></div>

            <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 2.25rem; height: 2.25rem; background: rgba(255,255,255,0.15); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg style="width: 1.25rem; height: 1.25rem; color: #fbbf24;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 0.9375rem; font-weight: 800; color: white; margin: 0; line-height: 1.2;">O que fazer agora?</p>
                        <p style="font-size: 0.75rem; color: rgba(255,255,255,0.7); margin: 0.125rem 0 0 0;"
                           x-text="count > 0 ? count + ' sugestão(ões) para você' : 'Verifique suas pendências'"></p>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 0.625rem;">
                    <template x-if="count > 0 && !open">
                        <span style="background: #fbbf24; color: #78350f; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.625rem; border-radius: 9999px; min-width: 1.5rem; text-align: center; box-shadow: 0 2px 8px rgba(251,191,36,0.4);"
                              x-text="count"></span>
                    </template>
                    <div style="width: 1.75rem; height: 1.75rem; background: rgba(255,255,255,0.15); border-radius: 0.375rem; display: flex; align-items: center; justify-content: center;">
                        <svg :style="open ? 'transform: rotate(180deg)' : ''" style="width: 1rem; height: 1rem; color: white; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- PAINEL DE SUGESTÕES --}}
    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         style="margin-top: 0.5rem;">

        {{-- Loading --}}
        <template x-if="loading">
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1.25rem;">
                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    <template x-for="i in 3" :key="i">
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            <div style="width: 2.25rem; height: 2.25rem; background: #222222; border-radius: 0.5rem; animation: idlePulse 1.5s ease-in-out infinite;"></div>
                            <div style="flex: 1;">
                                <div style="height: 0.75rem; background: #222222; border-radius: 0.25rem; width: 35%; margin-bottom: 0.375rem; animation: idlePulse 1.5s ease-in-out infinite;"></div>
                                <div style="height: 0.625rem; background: #222222; border-radius: 0.25rem; width: 65%; animation: idlePulse 1.5s ease-in-out infinite;"></div>
                            </div>
                            <div style="width: 4.5rem; height: 1.75rem; background: #222222; border-radius: 0.375rem; animation: idlePulse 1.5s ease-in-out infinite;"></div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Estado vazio --}}
        <template x-if="!loading && suggestions.length === 0">
            <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border: 1px solid #a7f3d0; border-radius: 0.75rem; padding: 1.5rem; text-align: center;">
                <div style="width: 2.5rem; height: 2.5rem; background: #141414; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.625rem; box-shadow: 0 2px 8px rgba(5,150,105,0.15);">
                    <svg style="width: 1.25rem; height: 1.25rem; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <p style="font-weight: 700; color: #6ee7b7; font-size: 0.875rem; margin: 0 0 0.25rem 0;">Tudo em dia!</p>
                <p style="font-size: 0.8rem; color: #6ee7b7; margin: 0;">Nenhuma pendencia encontrada. Revise o checklist abaixo ou organize a loja.</p>
            </div>
        </template>

        {{-- Lista de sugestões --}}
        <template x-if="!loading && suggestions.length > 0">
            <div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                {{-- Header com resumo --}}
                <div style="padding: 0.625rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.375rem;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: #a4a4a4;">
                        <span x-text="suggestions.length"></span> sugestoes encontradas
                    </span>
                    <div style="display: flex; gap: 0.375rem; flex-wrap: wrap;">
                        <span x-show="suggestions.filter(s => s.priority === 'high').length > 0"
                              style="font-size: 0.625rem; padding: 0.125rem 0.4375rem; border-radius: 9999px; font-weight: 700; background: rgba(239,68,68,0.1); color: #fca5a5; border: 1px solid #fecaca;"
                              x-text="suggestions.filter(s => s.priority === 'high').length + ' urgente(s)'"></span>
                        <span x-show="suggestions.filter(s => s.priority === 'medium').length > 0"
                              style="font-size: 0.625rem; padding: 0.125rem 0.4375rem; border-radius: 9999px; font-weight: 700; background: #141414beb; color: #d97706; border: 1px solid #fde68a;"
                              x-text="suggestions.filter(s => s.priority === 'medium').length + ' atencao'"></span>
                        <span x-show="suggestions.filter(s => s.priority === 'low').length > 0"
                              style="font-size: 0.625rem; padding: 0.125rem 0.4375rem; border-radius: 9999px; font-weight: 700; background: rgba(59,130,246,0.1); color: #2563eb; border: 1px solid #bfdbfe;"
                              x-text="suggestions.filter(s => s.priority === 'low').length + ' dica(s)'"></span>
                    </div>
                </div>

                {{-- Cards --}}
                <div style="max-height: 22rem; overflow-y: auto;">
                    <template x-for="(item, idx) in suggestions" :key="idx">
                        <div style="display: flex; align-items: stretch;"
                             :style="idx < suggestions.length - 1 ? 'border-bottom: 1px solid rgba(255,255,255,0.04);' : ''">

                            <div style="width: 3px; flex-shrink: 0; border-radius: 0 0 0 0;"
                                 :style="item.priority === 'high' ? 'background: #ef4444;' : (item.priority === 'medium' ? 'background: #f59e0b;' : 'background: #3b82f6;')"></div>

                            <div style="flex: 1; padding: 0.75rem 0.875rem; display: flex; align-items: center; gap: 0.625rem; min-width: 0;">
                                <div style="width: 2rem; height: 2rem; border-radius: 0.4375rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.9375rem;"
                                     :style="getCategoryStyle(item.category)">
                                    <span x-text="getCategoryEmoji(item.icon)"></span>
                                </div>

                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 0.3125rem; flex-wrap: wrap;">
                                        <span style="font-size: 0.775rem; font-weight: 700; color: #e3e3e3;" x-text="item.title"></span>
                                        <span style="font-size: 0.5625rem; padding: 0.0625rem 0.3125rem; border-radius: 0.1875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;"
                                              :style="item.priority === 'high' ? 'background: rgba(239,68,68,0.1); color: #fca5a5;' : (item.priority === 'medium' ? 'background: #141414beb; color: #d97706;' : 'background: rgba(59,130,246,0.1); color: #2563eb;')"
                                              x-text="item.priority === 'high' ? 'urgente' : (item.priority === 'medium' ? 'atencao' : 'dica')"></span>
                                    </div>
                                    <p style="font-size: 0.725rem; color: #818181; margin: 0.125rem 0 0 0; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" x-text="item.message"></p>
                                </div>

                                <div style="display: flex; gap: 0.3125rem; flex-shrink: 0; align-items: center;">
                                    <template x-if="item.whatsapp_url">
                                        <a :href="item.whatsapp_url" target="_blank" rel="noopener"
                                           style="width: 1.875rem; height: 1.875rem; display: flex; align-items: center; justify-content: center; background: rgba(16,185,129,0.15); border-radius: 0.375rem; text-decoration: none; transition: background 0.15s; border: 1px solid #bbf7d0;"
                                           onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'"
                                           title="Enviar WhatsApp">
                                            <svg style="width: 0.875rem; height: 0.875rem; color: #16a34a;" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                    </template>
                                    <a :href="item.action_url"
                                       style="padding: 0.3125rem 0.5rem; background: #111827; color: white; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; text-decoration: none; white-space: nowrap; display: flex; align-items: center; transition: background 0.15s;"
                                       onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'"
                                       x-text="item.action_label"></a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Rodapé --}}
                <div style="padding: 0.4375rem 1rem; background: #1a1a1a; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.6875rem; color: #666666;" x-text="'Atualizado ' + lastRefreshLabel"></span>
                    <button @click.stop="refresh()"
                            style="font-size: 0.6875rem; color: #4f46e5; font-weight: 600; background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem; display: flex; align-items: center; gap: 0.25rem;"
                            onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='transparent'"
                            :disabled="loading">
                        <svg x-show="!loading" style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg x-show="loading" style="width: 0.75rem; height: 0.75rem; animation: idleSpin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" style="opacity: 0.25;"></circle>
                            <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" style="opacity: 0.75;"></path>
                        </svg>
                        <span x-text="loading ? 'Atualizando...' : 'Atualizar'"></span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<style>
@keyframes idlePulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
}
@keyframes idleSpin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<script>
function idleMode() {
    return {
        open: false,
        loading: false,
        suggestions: [],
        count: 0,
        lastRefresh: null,
        refreshInterval: null,

        get lastRefreshLabel() {
            if (!this.lastRefresh) return '';
            const diff = Math.floor((Date.now() - this.lastRefresh) / 1000);
            if (diff < 60) return 'agora';
            if (diff < 3600) return 'ha ' + Math.floor(diff / 60) + ' min';
            return 'ha ' + Math.floor(diff / 3600) + 'h';
        },

        toggle() {
            this.open = !this.open;
            if (this.open && this.suggestions.length === 0) {
                this.fetchSuggestions();
            }
            if (this.open) {
                this.startAutoRefresh();
            } else {
                this.stopAutoRefresh();
            }
        },

        async refresh() {
            await this.fetchSuggestions();
        },

        async fetchSuggestions() {
            this.loading = true;
            try {
                const res = await fetch('{{ route("idle-mode.suggestions") }}');
                const data = await res.json();
                this.suggestions = data.suggestions;
                this.count = data.count;
                this.lastRefresh = Date.now();
            } catch (e) {
                console.error('Erro ao buscar sugestoes:', e);
            }
            this.loading = false;
        },

        startAutoRefresh() {
            this.stopAutoRefresh();
            this.refreshInterval = setInterval(() => {
                if (this.open && !this.loading) {
                    this.fetchSuggestions();
                }
            }, 5 * 60 * 1000);
        },

        stopAutoRefresh() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }
        },

        getCategoryStyle(category) {
            const styles = {
                crm: 'background: rgba(99,102,241,0.1); color: #4f46e5;',
                sales: 'background: rgba(16,185,129,0.1); color: #16a34a;',
                customers: 'background: #fef3c7; color: #d97706;',
                stock: 'background: rgba(168,85,247,0.1); color: #c4b5fd;',
                marketing: 'background: rgba(236,72,153,0.1); color: #db2777;',
                schedule: 'background: rgba(16,185,129,0.1); color: #059669;',
            };
            return styles[category] || 'background: #222222; color: #818181;';
        },

        getCategoryEmoji(icon) {
            const emojis = {
                'phone-missed': '\u{1F4DE}',
                'clock-alert': '\u{23F0}',
                'user-plus': '\u{1F464}',
                'message-circle': '\u{1F4AC}',
                'alert-circle': '\u{26A0}\u{FE0F}',
                'cake': '\u{1F382}',
                'hourglass': '\u{23F3}',
                'package': '\u{1F4E6}',
                'camera': '\u{1F4F7}',
                'calendar': '\u{1F4C5}',
            };
            return emojis[icon] || '\u{1F4CB}';
        },

        init() {
            this.fetchSuggestions();
        }
    };
}
</script>
