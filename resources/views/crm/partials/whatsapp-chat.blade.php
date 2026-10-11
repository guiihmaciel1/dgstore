{{-- WhatsApp Chat Panel --}}
<div id="wa-chat-panel" style="background: #0d0d0d; border: 1px solid #222; border-radius: 0.75rem; overflow: hidden;">
    {{-- Header --}}
    <div style="padding: 0.75rem 1rem; background: #111; border-bottom: 1px solid #222; display: flex; align-items: center; gap: 0.75rem;">
        <svg style="width: 22px; height: 22px; color: #25D366; flex-shrink: 0;" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <div style="flex: 1;">
            <div style="font-weight: 600; font-size: 0.85rem; color: #e3e3e3;">
                {{ $deal->customer?->name ?? $deal->title }}
            </div>
            <div style="font-size: 0.7rem; color: #666;">{{ $deal->phone }}</div>
        </div>
        @if($deal->customer?->wa_is_business)
            <span style="font-size: 0.6rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; background: rgba(37,211,102,0.15); color: #25D366;">
                Business
            </span>
        @endif
    </div>

    {{-- Messages Area --}}
    <div id="wa-messages" style="height: 400px; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem; background: #0a0a0a;">
        @forelse($deal->whatsappMessages->sortBy('created_at') as $msg)
            @php
                $isOutbound = $msg->direction === 'outbound' || ($msg->raw_payload['fromMe'] ?? false);
                $time = $msg->created_at->setTimezone('America/Sao_Paulo')->format('H:i');
            @endphp
            <div style="display: flex; justify-content: {{ $isOutbound ? 'flex-end' : 'flex-start' }}; max-width: 100%;">
                <div style="max-width: 75%; padding: 0.5rem 0.75rem; border-radius: {{ $isOutbound ? '12px 12px 0 12px' : '12px 12px 12px 0' }};
                    background: {{ $isOutbound ? '#005c4b' : '#1a1a1a' }};
                    border: 1px solid {{ $isOutbound ? '#00694d' : '#2a2a2a' }};">

                    @if(!$isOutbound && $msg->from_name)
                        <div style="font-size: 0.65rem; color: #25D366; font-weight: 600; margin-bottom: 0.15rem;">{{ $msg->from_name }}</div>
                    @endif

                    @if(in_array($msg->message_type, ['image', 'imageMessage']))
                        <div style="margin-bottom: 0.25rem;">
                            @if($msg->local_media_path)
                                <img src="{{ asset('storage/' . $msg->local_media_path) }}" alt="Imagem" style="max-width: 100%; border-radius: 8px; cursor: pointer;" onclick="window.open(this.src)">
                            @else
                                <div style="padding: 1rem; text-align: center; color: #666; background: #111; border-radius: 8px;">
                                    <svg style="width: 24px; height: 24px; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <div style="font-size: 0.7rem; margin-top: 0.25rem;">Imagem</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(in_array($msg->message_type, ['audio', 'audioMessage', 'ptt', 'pttMessage']))
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0; min-width: 180px;">
                            <svg style="width: 16px; height: 16px; color: #25D366; flex-shrink: 0;" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5z"/>
                                <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                            </svg>
                            <div style="flex: 1; height: 4px; background: #333; border-radius: 2px;">
                                <div style="width: 60%; height: 100%; background: #25D366; border-radius: 2px;"></div>
                            </div>
                        </div>
                        @if($msg->transcription)
                            <div style="margin-top: 0.25rem; padding: 0.375rem 0.5rem; background: {{ $isOutbound ? '#004a3d' : '#111' }}; border-radius: 6px; font-size: 0.7rem; color: #999; font-style: italic;">
                                <span style="color: #666; font-size: 0.6rem;">🎙️ Transcrição:</span><br>
                                {{ $msg->transcription }}
                            </div>
                        @endif
                    @endif

                    @if(in_array($msg->message_type, ['document', 'documentMessage']))
                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0;">
                            <svg style="width: 20px; height: 20px; color: #999; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span style="font-size: 0.75rem; color: #aaa;">{{ $msg->raw_payload['message']['documentMessage']['fileName'] ?? 'Documento' }}</span>
                        </div>
                    @endif

                    @if($msg->message_body && !in_array($msg->message_type, ['audio', 'audioMessage', 'ptt', 'pttMessage']))
                        <div style="font-size: 0.8rem; color: #e3e3e3; white-space: pre-wrap; word-break: break-word; line-height: 1.4;">{{ $msg->message_body }}</div>
                    @endif

                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; margin-top: 0.15rem;">
                        <span style="font-size: 0.6rem; color: #666;">{{ $time }}</span>
                        @if($isOutbound)
                            @php $status = $msg->delivery_status ?? 'sent'; @endphp
                            @if($status === 'read')
                                <svg style="width: 14px; height: 14px; color: #53bdeb;" viewBox="0 0 16 15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88 5.59 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>
                            @elseif($status === 'delivered')
                                <svg style="width: 14px; height: 14px; color: #999;" viewBox="0 0 16 15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88 5.59 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>
                            @else
                                <svg style="width: 12px; height: 12px; color: #999;" viewBox="0 0 16 15" fill="currentColor"><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div style="flex: 1; display: flex; align-items: center; justify-content: center; color: #444; font-size: 0.8rem;">
                <div style="text-align: center;">
                    <svg style="width: 40px; height: 40px; margin: 0 auto 0.5rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Nenhuma mensagem ainda
                </div>
            </div>
        @endforelse
    </div>

    {{-- Input Area --}}
    @if($deal->phone && !$deal->stage?->is_won && !$deal->stage?->is_lost)
        <div style="padding: 0.75rem; background: #111; border-top: 1px solid #222;">
            <form id="wa-send-form" style="display: flex; gap: 0.5rem; align-items: flex-end;">
                @csrf
                <div x-data="{ showMenuForm: false }" style="position: relative;">
                    <button type="button" @click="showMenuForm = !showMenuForm" style="padding: 0.5rem; border-radius: 8px; background: #1a1a1a; border: 1px solid #333; cursor: pointer; display: flex; align-items: center;" title="Menu interativo">
                        <svg style="width: 18px; height: 18px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div x-show="showMenuForm" x-cloak @click.away="showMenuForm = false"
                         style="position: absolute; bottom: 100%; left: 0; margin-bottom: 0.5rem; background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 0.75rem; width: 300px; z-index: 20;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #e3e3e3; margin-bottom: 0.5rem;">Menu Interativo</div>
                        <input type="text" id="menu-title" placeholder="Título" style="width: 100%; padding: 0.375rem; background: #111; border: 1px solid #333; border-radius: 6px; color: #e3e3e3; font-size: 0.75rem; margin-bottom: 0.375rem;">
                        <textarea id="menu-body" placeholder="Mensagem" rows="2" style="width: 100%; padding: 0.375rem; background: #111; border: 1px solid #333; border-radius: 6px; color: #e3e3e3; font-size: 0.75rem; resize: none; margin-bottom: 0.375rem;"></textarea>
                        <div style="display: flex; flex-direction: column; gap: 0.25rem; margin-bottom: 0.375rem;">
                            <input type="text" id="menu-btn-1" placeholder="Botão 1" style="width: 100%; padding: 0.3rem; background: #111; border: 1px solid #333; border-radius: 4px; color: #e3e3e3; font-size: 0.7rem;">
                            <input type="text" id="menu-btn-2" placeholder="Botão 2 (opcional)" style="width: 100%; padding: 0.3rem; background: #111; border: 1px solid #333; border-radius: 4px; color: #e3e3e3; font-size: 0.7rem;">
                            <input type="text" id="menu-btn-3" placeholder="Botão 3 (opcional)" style="width: 100%; padding: 0.3rem; background: #111; border: 1px solid #333; border-radius: 4px; color: #e3e3e3; font-size: 0.7rem;">
                        </div>
                        <button type="button" onclick="sendInteractiveMenu()" style="width: 100%; padding: 0.375rem; background: #25D366; color: #000; font-weight: 600; font-size: 0.7rem; border: none; border-radius: 6px; cursor: pointer;">
                            Enviar Menu
                        </button>
                    </div>
                </div>
                <label style="cursor: pointer; padding: 0.5rem; border-radius: 8px; background: #1a1a1a; border: 1px solid #333; display: flex; align-items: center;" title="Enviar arquivo">
                    <input type="file" id="wa-media-input" accept="image/*,audio/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx" style="display: none;" onchange="handleMediaSelect(this)">
                    <svg style="width: 18px; height: 18px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                </label>
                <div style="flex: 1; position: relative;">
                    <div id="wa-media-preview" style="display: none; padding: 0.5rem; background: #1a1a1a; border: 1px solid #333; border-radius: 8px 8px 0 0; margin-bottom: -1px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span id="wa-media-name" style="font-size: 0.7rem; color: #999;"></span>
                            <button type="button" onclick="clearMediaPreview()" style="background: none; border: none; color: #666; cursor: pointer; font-size: 0.8rem;">✕</button>
                        </div>
                    </div>
                    <textarea id="wa-message-input" rows="1" placeholder="Digite uma mensagem..."
                        style="width: 100%; resize: none; background: #1a1a1a; border: 1px solid #333; border-radius: 8px; padding: 0.5rem 0.75rem; color: #e3e3e3; font-size: 0.8rem; outline: none; font-family: inherit; max-height: 100px; overflow-y: auto;"
                        onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault(); sendWaMessage();}"></textarea>
                </div>
                <button type="button" onclick="sendWaMessage()" id="wa-send-btn"
                    style="padding: 0.5rem 0.75rem; border-radius: 8px; background: #25D366; color: #000; font-weight: 600; font-size: 0.8rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.25rem; white-space: nowrap;">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Enviar
                </button>
            </form>
        </div>
    @endif
</div>

<script>
(function() {
    const msgContainer = document.getElementById('wa-messages');
    if (msgContainer) {
        msgContainer.scrollTop = msgContainer.scrollHeight;
    }

    let selectedFile = null;

    window.handleMediaSelect = function(input) {
        if (input.files.length > 0) {
            selectedFile = input.files[0];
            const preview = document.getElementById('wa-media-preview');
            const nameEl = document.getElementById('wa-media-name');
            nameEl.textContent = `📎 ${selectedFile.name} (${(selectedFile.size/1024).toFixed(1)} KB)`;
            preview.style.display = 'block';
        }
    };

    window.clearMediaPreview = function() {
        selectedFile = null;
        document.getElementById('wa-media-input').value = '';
        document.getElementById('wa-media-preview').style.display = 'none';
    };

    window.sendWaMessage = async function() {
        const input = document.getElementById('wa-message-input');
        const btn = document.getElementById('wa-send-btn');
        const message = input?.value?.trim();

        if (!message && !selectedFile) return;

        btn.disabled = true;
        btn.style.opacity = '0.5';
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span style="display: inline-block; width: 16px; height: 16px; border: 2px solid #000; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite;"></span> Enviando';

        try {
            let res;
            if (selectedFile) {
                const formData = new FormData();
                formData.append('media', selectedFile);
                formData.append('caption', message || '');
                formData.append('_token', document.querySelector('#wa-send-form input[name=_token]').value);

                res = await fetch('{{ route("crm.deals.wa.send-media", $deal) }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
            } else {
                res = await fetch('{{ route("crm.deals.wa.send", $deal) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ message })
                });
            }

            const data = await res.json();
            if (data.success) {
                input.value = '';
                window.clearMediaPreview();
                appendMessage(message || '📎 ' + (selectedFile?.name ?? 'Arquivo'), true);
            } else {
                alert(data.error || 'Erro ao enviar mensagem');
            }
        } catch(e) {
            alert('Erro de conexão ao enviar mensagem');
        } finally {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalText;
        }
    };

    function appendMessage(text, isOutbound) {
        const container = document.getElementById('wa-messages');
        const now = new Date();
        const time = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

        const div = document.createElement('div');
        div.style.cssText = `display: flex; justify-content: ${isOutbound ? 'flex-end' : 'flex-start'}; max-width: 100%;`;
        div.innerHTML = `
            <div style="max-width: 75%; padding: 0.5rem 0.75rem; border-radius: ${isOutbound ? '12px 12px 0 12px' : '12px 12px 12px 0'};
                background: ${isOutbound ? '#005c4b' : '#1a1a1a'};
                border: 1px solid ${isOutbound ? '#00694d' : '#2a2a2a'};">
                <div style="font-size: 0.8rem; color: #e3e3e3; white-space: pre-wrap; word-break: break-word; line-height: 1.4;">${text}</div>
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.25rem; margin-top: 0.15rem;">
                    <span style="font-size: 0.6rem; color: #666;">${time}</span>
                    ${isOutbound ? '<svg style="width: 12px; height: 12px; color: #999;" viewBox="0 0 16 15" fill="currentColor"><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>' : ''}
                </div>
            </div>
        `;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }

    window.sendInteractiveMenu = async function() {
        const title = document.getElementById('menu-title')?.value?.trim();
        const body = document.getElementById('menu-body')?.value?.trim();
        const btn1 = document.getElementById('menu-btn-1')?.value?.trim();
        const btn2 = document.getElementById('menu-btn-2')?.value?.trim();
        const btn3 = document.getElementById('menu-btn-3')?.value?.trim();

        if (!title || !body || !btn1) {
            alert('Preencha título, mensagem e pelo menos 1 botão');
            return;
        }

        const buttons = [{ text: btn1 }];
        if (btn2) buttons.push({ text: btn2 });
        if (btn3) buttons.push({ text: btn3 });

        try {
            const res = await fetch('{{ route("crm.deals.wa.send-menu", $deal) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ title, body, type: 'buttons', buttons })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('menu-title').value = '';
                document.getElementById('menu-body').value = '';
                document.getElementById('menu-btn-1').value = '';
                document.getElementById('menu-btn-2').value = '';
                document.getElementById('menu-btn-3').value = '';
                appendMessage(`📋 ${title}\n${body}`, true);
            } else {
                alert(data.error || 'Erro ao enviar menu');
            }
        } catch(e) {
            alert('Erro de conexão');
        }
    };

    const autoTextarea = document.getElementById('wa-message-input');
    if (autoTextarea) {
        autoTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        });
    }
})();
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
