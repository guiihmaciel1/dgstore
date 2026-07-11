{{-- Header com filtros e ações --}}
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
        {{-- Filtro por status --}}
        <div style="display: flex; gap: 0.25rem; background: #f3f4f6; border-radius: 0.5rem; padding: 0.125rem;">
            <template x-for="s in [{v:'all',l:'Todas'},{v:'idea',l:'Ideias'},{v:'production',l:'Em Produção'},{v:'published',l:'Publicados'}]" :key="s.v">
                <button type="button" @click="contentFilterStatus = s.v"
                        :style="contentFilterStatus === s.v
                            ? 'padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 600; border: none; border-radius: 0.375rem; cursor: pointer; background: white; color: #111827; box-shadow: 0 1px 2px rgba(0,0,0,0.05);'
                            : 'padding: 0.375rem 0.75rem; font-size: 0.75rem; font-weight: 500; border: none; border-radius: 0.375rem; cursor: pointer; background: transparent; color: #6b7280;'"
                        x-text="s.l"></button>
            </template>
        </div>

        {{-- Filtro por tipo --}}
        <select x-model="contentFilterType"
                style="padding: 0.375rem 0.5rem; font-size: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; outline: none; color: #374151; background: white;">
            <option value="all">Todos os tipos</option>
            <option value="reels">Reels</option>
            <option value="stories">Stories</option>
            <option value="post">Post</option>
            <option value="carousel">Carrossel</option>
        </select>
    </div>

    <div style="display: flex; gap: 0.5rem;">
        <button type="button" @click="aiModal.open = true; aiSuggestions = []; aiTopic = '';"
                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; white-space: nowrap;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            Gerar Ideias com IA
        </button>
        <button type="button" @click="openNewContent()"
                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; white-space: nowrap;"
                onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
            <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nova Ideia
        </button>
    </div>
</div>

{{-- Estado vazio --}}
<div x-show="filteredContents.length === 0" style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 3rem 2rem; text-align: center;">
    <svg style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
    </svg>
    <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem;">Nenhuma ideia de conteúdo ainda</p>
    <p style="color: #9ca3af; font-size: 0.8rem;">Clique em "Nova Ideia" ou use o gerador com IA para começar</p>
</div>

{{-- Grid de cards --}}
<div x-show="filteredContents.length > 0"
     style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;"
     class="contents-grid">
    <template x-for="c in filteredContents" :key="c.id">
        <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden; display: flex; flex-direction: column; transition: box-shadow 0.15s;"
             onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='none'">

            {{-- Imagem de referência --}}
            <template x-if="c.image_url">
                <div style="height: 140px; overflow: hidden; background: #f9fafb;">
                    <img :src="c.image_url" style="width: 100%; height: 100%; object-fit: cover;" alt="">
                </div>
            </template>

            {{-- Corpo do card --}}
            <div style="padding: 1rem; flex: 1; display: flex; flex-direction: column;">
                {{-- Badges --}}
                <div style="display: flex; gap: 0.375rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                    <span :style="'font-size:0.625rem;font-weight:700;padding:2px 8px;border-radius:9999px;text-transform:uppercase;letter-spacing:0.025em;'
                        + (c.type === 'reels' ? 'background:#fce7f3;color:#be185d;'
                         : c.type === 'stories' ? 'background:#e0e7ff;color:#3730a3;'
                         : c.type === 'carousel' ? 'background:#fef3c7;color:#92400e;'
                         : 'background:#ecfdf5;color:#065f46;')"
                        x-text="c.type_label"></span>
                    <span style="font-size:0.625rem;font-weight:600;padding:2px 8px;border-radius:9999px;background:#f3f4f6;color:#6b7280;"
                          x-text="c.platform_label"></span>
                    <template x-if="c.ai_generated">
                        <span style="font-size:0.625rem;font-weight:700;padding:2px 8px;border-radius:9999px;background:linear-gradient(135deg,#ede9fe,#ddd6fe);color:#7c3aed;">IA</span>
                    </template>
                </div>

                {{-- Título --}}
                <h3 style="font-size: 0.875rem; font-weight: 700; color: #111827; margin-bottom: 0.375rem; line-height: 1.3;" x-text="c.title"></h3>

                {{-- Descrição truncada --}}
                <p style="font-size: 0.75rem; color: #6b7280; line-height: 1.5; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"
                   x-text="c.description || '—'"></p>

                {{-- Data prevista --}}
                <template x-if="c.scheduled_at_formatted">
                    <div style="display: flex; align-items: center; gap: 0.25rem; margin-top: 0.5rem;">
                        <svg style="width: 0.75rem; height: 0.75rem; color: #9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span style="font-size: 0.6875rem; color: #9ca3af;" x-text="c.scheduled_at_formatted"></span>
                    </div>
                </template>

                {{-- Footer --}}
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #f3f4f6;">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span :style="'font-size:0.625rem;font-weight:700;padding:2px 8px;border-radius:9999px;'
                            + (c.status === 'idea' ? 'background:#fef3c7;color:#92400e;'
                             : c.status === 'production' ? 'background:#dbeafe;color:#1e40af;'
                             : 'background:#d1fae5;color:#065f46;')"
                            x-text="c.status_label"></span>
                        <span style="font-size: 0.625rem; color: #d1d5db;" x-text="c.user_name"></span>
                    </div>
                    <div style="display: flex; gap: 0.25rem;">
                        <button type="button" @click="openEditContent(c)"
                                style="padding: 0.25rem; background: none; border: none; cursor: pointer; color: #9ca3af; border-radius: 0.25rem;"
                                onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#9ca3af'" title="Editar">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <button type="button" @click="deleteContent(c.id)"
                                :style="contentDeleting === c.id ? 'padding:0.25rem;background:none;border:none;cursor:default;color:#ef4444;opacity:0.5;' : 'padding:0.25rem;background:none;border:none;cursor:pointer;color:#9ca3af;border-radius:0.25rem;'"
                                :disabled="contentDeleting === c.id"
                                onmouseover="if(!this.disabled)this.style.color='#ef4444'" onmouseout="if(!this.disabled)this.style.color='#9ca3af'" title="Excluir">
                            <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Nova/Editar Ideia --}}
{{-- ============================================================ --}}
<div x-show="contentModal.open" x-cloak
     style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem;"
     @keydown.escape.window="contentModal.open = false">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.4);" @click="contentModal.open = false"></div>
    <div style="background: white; border-radius: 0.75rem; width: 100%; max-width: 540px; position: relative; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb;">
            <h3 style="font-size: 1rem; font-weight: 700; color: #111827;" x-text="contentModal.editing ? 'Editar Conteúdo' : 'Nova Ideia de Conteúdo'"></h3>
        </div>
        <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
            <div>
                <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Título *</label>
                <input type="text" x-model="contentForm.title" required
                       style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                       placeholder="Ex: 5 recursos escondidos do iPhone 17">
            </div>
            <div>
                <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Descrição / Roteiro</label>
                <textarea x-model="contentForm.description" rows="4"
                          style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; resize: vertical; box-sizing: border-box;"
                          onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'"
                          placeholder="Descreva o conteúdo, roteiro, pontos principais..."></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Tipo</label>
                    <select x-model="contentForm.type"
                            style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; box-sizing: border-box;">
                        <option value="reels">Reels</option>
                        <option value="stories">Stories</option>
                        <option value="post">Post</option>
                        <option value="carousel">Carrossel</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Plataforma</label>
                    <select x-model="contentForm.platform"
                            style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; box-sizing: border-box;">
                        <option value="instagram">Instagram</option>
                        <option value="tiktok">TikTok</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="all">Todas</option>
                    </select>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Status</label>
                    <select x-model="contentForm.status"
                            style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; box-sizing: border-box;">
                        <option value="idea">Ideia</option>
                        <option value="production">Em Produção</option>
                        <option value="published">Publicado</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Data Prevista</label>
                    <input type="date" x-model="contentForm.scheduled_at"
                           style="width: 100%; padding: 0.5rem 0.625rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 0.875rem; outline: none; box-sizing: border-box;">
                </div>
            </div>
            <template x-if="!contentModal.editing">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #374151; display: block; margin-bottom: 0.25rem;">Imagem de Referência</label>
                    <input type="file" id="content-image-input" accept="image/*"
                           style="width: 100%; font-size: 0.8rem; color: #6b7280;">
                </div>
            </template>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end; gap: 0.5rem;">
            <button type="button" @click="contentModal.open = false"
                    style="padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                Cancelar
            </button>
            <button type="button" @click="saveContent()" :disabled="contentModal.saving || !contentForm.title.trim()"
                    :style="contentModal.saving
                        ? 'padding: 0.5rem 1.25rem; background: #9ca3af; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: default;'
                        : 'padding: 0.5rem 1.25rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;'">
                <span x-text="contentModal.saving ? 'Salvando...' : (contentModal.editing ? 'Atualizar' : 'Salvar')"></span>
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Gerar Ideias com IA --}}
{{-- ============================================================ --}}
<div x-show="aiModal.open" x-cloak
     style="position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem;"
     @keydown.escape.window="aiModal.open = false">
    <div style="position: fixed; inset: 0; background: rgba(0,0,0,0.4);" @click="aiModal.open = false"></div>
    <div style="background: white; border-radius: 0.75rem; width: 100%; max-width: 680px; position: relative; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem;">
            <div style="width: 2rem; height: 2rem; border-radius: 0.5rem; background: linear-gradient(135deg, #7c3aed, #6d28d9); display: flex; align-items: center; justify-content: center;">
                <svg style="width: 1rem; height: 1rem; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: #111827;">Gerar Ideias com IA</h3>
                <p style="font-size: 0.75rem; color: #9ca3af;">Tendências, curiosidades e novidades Apple</p>
            </div>
        </div>
        <div style="padding: 1.5rem;">
            {{-- Campo de tema --}}
            <div style="display: flex; gap: 0.5rem; margin-bottom: 1.25rem;">
                <input type="text" x-model="aiTopic"
                       style="flex: 1; padding: 0.625rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.875rem; outline: none; box-sizing: border-box;"
                       onfocus="this.style.borderColor='#7c3aed'" onblur="this.style.borderColor='#e5e7eb'"
                       placeholder="Tema opcional: ex. lançamento iPhone 17, dicas de bateria..."
                       @keydown.enter="generateIdeas()">
                <button type="button" @click="generateIdeas()" :disabled="aiLoading"
                        :style="aiLoading
                            ? 'padding: 0.625rem 1.25rem; background: #a78bfa; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: default; white-space: nowrap;'
                            : 'padding: 0.625rem 1.25rem; background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer; white-space: nowrap;'">
                    <span x-text="aiLoading ? 'Gerando...' : 'Gerar 5 Ideias'"></span>
                </button>
            </div>

            {{-- Loading --}}
            <div x-show="aiLoading" style="text-align: center; padding: 2rem;">
                <div style="display: inline-block; width: 2rem; height: 2rem; border: 3px solid #e5e7eb; border-top-color: #7c3aed; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                <p style="color: #6b7280; font-size: 0.8rem; margin-top: 0.75rem;">Consultando tendências e gerando ideias...</p>
            </div>

            {{-- Sugestões da IA --}}
            <div x-show="aiSuggestions.length > 0 && !aiLoading" style="display: flex; flex-direction: column; gap: 0.75rem;">
                <template x-for="(idea, idx) in aiSuggestions" :key="idx">
                    <div :style="idea.saved
                        ? 'border: 1px solid #d1fae5; border-radius: 0.75rem; padding: 1rem; background: #f0fdf4;'
                        : 'border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem; background: white; transition: border-color 0.15s;'"
                         onmouseover="if(!this.dataset.saved)this.style.borderColor='#a78bfa'" onmouseout="if(!this.dataset.saved)this.style.borderColor='#e5e7eb'"
                         :data-saved="idea.saved">
                        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 0.75rem;">
                            <div style="flex: 1;">
                                <div style="display: flex; gap: 0.375rem; margin-bottom: 0.375rem; flex-wrap: wrap;">
                                    <span :style="'font-size:0.6rem;font-weight:700;padding:2px 6px;border-radius:9999px;text-transform:uppercase;'
                                        + (idea.type === 'reels' ? 'background:#fce7f3;color:#be185d;'
                                         : idea.type === 'stories' ? 'background:#e0e7ff;color:#3730a3;'
                                         : idea.type === 'carousel' ? 'background:#fef3c7;color:#92400e;'
                                         : 'background:#ecfdf5;color:#065f46;')"
                                        x-text="idea.type === 'carousel' ? 'Carrossel' : (idea.type === 'reels' ? 'Reels' : (idea.type === 'stories' ? 'Stories' : 'Post'))"></span>
                                    <span style="font-size:0.6rem;font-weight:600;padding:2px 6px;border-radius:9999px;background:#f3f4f6;color:#6b7280;"
                                          x-text="idea.platform === 'all' ? 'Todas' : (idea.platform === 'instagram' ? 'Instagram' : (idea.platform === 'tiktok' ? 'TikTok' : 'WhatsApp'))"></span>
                                </div>
                                <h4 style="font-size: 0.8125rem; font-weight: 700; color: #111827; margin-bottom: 0.25rem;" x-text="idea.title"></h4>
                                <p style="font-size: 0.75rem; color: #6b7280; line-height: 1.5;" x-text="idea.description"></p>
                            </div>
                            <div style="flex-shrink: 0;">
                                <template x-if="idea.saved">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: #059669; display: flex; align-items: center; gap: 0.25rem;">
                                        <svg style="width: 0.875rem; height: 0.875rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Salvo
                                    </span>
                                </template>
                                <template x-if="!idea.saved">
                                    <button type="button" @click="saveIdeaFromAi(idea, idx)" :disabled="idea.saving"
                                            :style="idea.saving
                                                ? 'padding: 0.375rem 0.75rem; background: #d1d5db; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: default; white-space: nowrap;'
                                                : 'padding: 0.375rem 0.75rem; background: #111827; color: white; border: none; border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; white-space: nowrap;'"
                                            onmouseover="if(!this.disabled)this.style.background='#374151'" onmouseout="if(!this.disabled)this.style.background='#111827'">
                                        <span x-text="idea.saving ? 'Salvando...' : 'Salvar'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Botão gerar novamente --}}
                <div style="text-align: center; padding-top: 0.5rem;">
                    <button type="button" @click="generateIdeas()" :disabled="aiLoading"
                            style="padding: 0.5rem 1rem; background: transparent; color: #7c3aed; border: 1px solid #7c3aed; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;"
                            onmouseover="this.style.background='#f5f3ff'" onmouseout="this.style.background='transparent'">
                        Gerar Novamente
                    </button>
                </div>
            </div>

            {{-- Dica inicial --}}
            <div x-show="aiSuggestions.length === 0 && !aiLoading" style="text-align: center; padding: 1.5rem;">
                <p style="color: #9ca3af; font-size: 0.8rem;">Digite um tema ou clique em "Gerar 5 Ideias" para receber sugestões da IA sobre tendências Apple, curiosidades iPhone, lançamentos e mais.</p>
            </div>
        </div>
        <div style="padding: 0.75rem 1.5rem; border-top: 1px solid #e5e7eb; display: flex; justify-content: flex-end;">
            <button type="button" @click="aiModal.open = false"
                    style="padding: 0.5rem 1rem; background: #f3f4f6; color: #374151; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                Fechar
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes spin { to { transform: rotate(360deg); } }
    @media (max-width: 1024px) { .contents-grid { grid-template-columns: repeat(2, 1fr) !important; } }
    @media (max-width: 640px) { .contents-grid { grid-template-columns: 1fr !important; } }
</style>
