<div x-data="idleMode()" x-cloak>
    {{-- BOTÃO PRINCIPAL --}}
    <button @click="toggle()"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.875rem 1.25rem; margin-bottom: 1rem; border: 2px solid; border-radius: 0.75rem; cursor: pointer; font-size: 0.875rem; font-weight: 700; transition: all 0.2s;"
            :style="open
                ? 'background: #312e81; border-color: #312e81; color: white;'
                : 'background: linear-gradient(135deg, #4f46e5 0%, #312e81 100%); border-color: #4338ca; color: white;'"
            onmouseover="this.style.opacity='0.92'" onmouseout="this.style.opacity='1'">
        <div style="display: flex; align-items: center; gap: 0.625rem;">
            <svg style="width: 1.25rem; height: 1.25rem; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <span>O que fazer agora?</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <template x-if="count > 0 && !open">
                <span style="background: #fbbf24; color: #78350f; font-size: 0.6875rem; font-weight: 800; padding: 0.125rem 0.5rem; border-radius: 9999px; min-width: 1.25rem; text-align: center;"
                      x-text="count"></span>
            </template>
            <svg :style="open ? 'transform: rotate(180deg)' : ''" style="width: 1rem; height: 1rem; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    {{-- PAINEL DE SUGESTÕES --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0"
         style="margin-bottom: 1rem;">

        {{-- Loading --}}
        <template x-if="loading">
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem;">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <template x-for="i in 3" :key="i">
                        <div style="display: flex; gap: 0.75rem; align-items: center;">
                            <div style="width: 2.25rem; height: 2.25rem; background: #f3f4f6; border-radius: 0.5rem; animation: pulse 1.5s ease-in-out infinite;"></div>
                            <div style="flex: 1;">
                                <div style="height: 0.75rem; background: #f3f4f6; border-radius: 0.25rem; width: 40%; margin-bottom: 0.375rem; animation: pulse 1.5s ease-in-out infinite;"></div>
                                <div style="height: 0.625rem; background: #f3f4f6; border-radius: 0.25rem; width: 70%; animation: pulse 1.5s ease-in-out infinite;"></div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Estado vazio --}}
        <template x-if="!loading && suggestions.length === 0">
            <div style="background: white; border: 1px solid #d1fae5; border-radius: 0.75rem; padding: 1.5rem; text-align: center;">
                <div style="font-size: 1.75rem; margin-bottom: 0.5rem;">&#10024;</div>
                <p style="font-weight: 700; color: #065f46; font-size: 0.875rem; margin: 0 0 0.25rem 0;">Tudo em dia!</p>
                <p style="font-size: 0.8rem; color: #6b7280; margin: 0;">Nenhuma pendência encontrada. Revise o checklist abaixo ou organize a loja.</p>
            </div>
        </template>

        {{-- Lista de sugestões --}}
        <template x-if="!loading && suggestions.length > 0">
            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                {{-- Header do painel --}}
                <div style="padding: 0.625rem 1rem; background: #f9fafb; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; font-weight: 600; color: #6b7280;">
                        <span x-text="suggestions.length"></span> sugestões encontradas
                    </span>
                    <div style="display: flex; gap: 0.375rem;">
                        <span style="font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-weight: 600;"
                              :style="'background: #fef2f2; color: #991b1b;'"
                              x-show="suggestions.filter(s => s.priority === 'high').length > 0"
                              x-text="suggestions.filter(s => s.priority === 'high').length + ' urgente(s)'"></span>
                        <span style="font-size: 0.625rem; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-weight: 600;"
                              :style="'background: #fffbeb; color: #92400e;'"
                              x-show="suggestions.filter(s => s.priority === 'medium').length > 0"
                              x-text="suggestions.filter(s => s.priority === 'medium').length + ' atenção'"></span>
                    </div>
                </div>

                {{-- Cards de sugestão --}}
                <div style="max-height: 25rem; overflow-y: auto;">
                    <template x-for="(item, idx) in suggestions" :key="idx">
                        <div style="display: flex; align-items: stretch; border-bottom: 1px solid #f3f4f6;"
                             :style="idx === suggestions.length - 1 ? 'border-bottom: none;' : ''">
                            {{-- Barra de prioridade --}}
                            <div style="width: 0.25rem; flex-shrink: 0;"
                                 :style="item.priority === 'high' ? 'background: #ef4444;' : (item.priority === 'medium' ? 'background: #f59e0b;' : 'background: #3b82f6;')"></div>

                            <div style="flex: 1; padding: 0.75rem 1rem; display: flex; align-items: center; gap: 0.75rem;">
                                {{-- Ícone --}}
                                <div style="width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem;"
                                     :style="getCategoryStyle(item.category)">
                                    <span x-text="getCategoryEmoji(item.icon)"></span>
                                </div>

                                {{-- Conteúdo --}}
                                <div style="flex: 1; min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 0.375rem; margin-bottom: 0.125rem; flex-wrap: wrap;">
                                        <span style="font-size: 0.8rem; font-weight: 700; color: #111827;" x-text="item.title"></span>
                                        <span style="font-size: 0.5625rem; padding: 0.0625rem 0.3125rem; border-radius: 0.25rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em;"
                                              :style="item.priority === 'high' ? 'background: #fef2f2; color: #dc2626;' : (item.priority === 'medium' ? 'background: #fffbeb; color: #d97706;' : 'background: #eff6ff; color: #2563eb;')"
                                              x-text="item.priority === 'high' ? 'urgente' : (item.priority === 'medium' ? 'atenção' : 'dica')"></span>
                                    </div>
                                    <p style="font-size: 0.75rem; color: #6b7280; margin: 0; line-height: 1.3;" x-text="item.message"></p>
                                </div>

                                {{-- Ações --}}
                                <div style="display: flex; gap: 0.375rem; flex-shrink: 0;">
                                    <template x-if="item.whatsapp_url">
                                        <a :href="item.whatsapp_url" target="_blank" rel="noopener"
                                           style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; background: #dcfce7; border-radius: 0.375rem; text-decoration: none; transition: background 0.15s;"
                                           onmouseover="this.style.background='#bbf7d0'" onmouseout="this.style.background='#dcfce7'"
                                           title="Enviar WhatsApp">
                                            <svg style="width: 1rem; height: 1rem; color: #16a34a;" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        </a>
                                    </template>
                                    <a :href="item.action_url"
                                       style="padding: 0.375rem 0.625rem; background: #111827; color: white; border-radius: 0.375rem; font-size: 0.6875rem; font-weight: 600; text-decoration: none; white-space: nowrap; display: flex; align-items: center; transition: background 0.15s;"
                                       onmouseover="this.style.background='#374151'" onmouseout="this.style.background='#111827'"
                                       x-text="item.action_label"></a>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Rodapé --}}
                <div style="padding: 0.5rem 1rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.6875rem; color: #9ca3af;" x-text="'Atualizado ' + lastRefreshLabel"></span>
                    <button @click="refresh()"
                            style="font-size: 0.6875rem; color: #4f46e5; font-weight: 600; background: none; border: none; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem;"
                            onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='none'"
                            :disabled="loading">
                        <span x-show="!loading">Atualizar</span>
                        <span x-show="loading">Atualizando...</span>
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
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
            if (diff < 3600) return `há ${Math.floor(diff / 60)} min`;
            return `há ${Math.floor(diff / 3600)}h`;
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
                console.error('Erro ao buscar sugestões:', e);
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
                crm: 'background: #eef2ff; color: #4f46e5;',
                sales: 'background: #f0fdf4; color: #16a34a;',
                customers: 'background: #fef3c7; color: #d97706;',
                followup: 'background: #fef2f2; color: #dc2626;',
                stock: 'background: #f5f3ff; color: #7c3aed;',
                marketing: 'background: #fdf2f8; color: #db2777;',
                schedule: 'background: #ecfdf5; color: #059669;',
            };
            return styles[category] || 'background: #f3f4f6; color: #6b7280;';
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
