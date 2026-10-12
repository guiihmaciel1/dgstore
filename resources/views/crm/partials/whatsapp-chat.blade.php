{{-- WhatsApp Chat Panel (full-height flex) with real-time polling --}}
<div id="wa-chat-panel" style="display: flex; flex-direction: column; height: 100%; background: #0a0a0a;">

    {{-- Presence indicator bar --}}
    <div id="wa-presence-bar" style="display: none; padding: 0.15rem 0.75rem; background: #111; border-bottom: 1px solid #1a1a1a; font-size: 0.65rem; color: #25D366; font-style: italic;">
        <span id="wa-presence-text"></span>
    </div>

    {{-- Reply preview bar --}}
    <div id="wa-reply-bar" style="display: none; padding: 0.35rem 0.625rem; background: #111; border-bottom: 1px solid #252525;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <div style="flex: 1; padding: 0.25rem 0.5rem; background: #1a1a1a; border-left: 3px solid #25D366; border-radius: 0 4px 4px 0; overflow: hidden;">
                <div id="wa-reply-name" style="font-size: 0.6rem; color: #25D366; font-weight: 600;"></div>
                <div id="wa-reply-text" style="font-size: 0.7rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 300px;"></div>
            </div>
            <button type="button" onclick="cancelReply()" style="background: none; border: none; color: #555; cursor: pointer; font-size: 0.85rem; padding: 0.2rem;">✕</button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div id="wa-messages" style="flex: 1; overflow-y: auto; padding: 0.75rem; display: flex; flex-direction: column; gap: 0.375rem;" class="custom-scroll">
        @forelse($deal->whatsappMessages->sortBy('created_at') as $msg)
            @php
                $isOutbound = $msg->direction === 'outbound' || ($msg->raw_payload['fromMe'] ?? false);
                $time = $msg->created_at->setTimezone('America/Sao_Paulo')->format('H:i');
            @endphp
            <div class="wa-msg-row" data-wa-id="{{ $msg->wa_message_id }}" data-direction="{{ $isOutbound ? 'outbound' : 'inbound' }}"
                 style="display: flex; justify-content: {{ $isOutbound ? 'flex-end' : 'flex-start' }}; max-width: 100%;"
                 onmouseenter="this.querySelector('.wa-hover-actions')?.style.setProperty('display','flex')"
                 onmouseleave="this.querySelector('.wa-hover-actions')?.style.setProperty('display','none')">

                <div style="max-width: 80%; position: relative;">
                    {{-- Hover action buttons — inside bubble, top-right --}}
                    <div class="wa-hover-actions" style="display: none; position: absolute; top: -10px; {{ $isOutbound ? 'left: -2px' : 'right: -2px' }}; gap: 2px; z-index: 5;">
                        @if(!$isOutbound)
                        <button class="wa-reply-btn" type="button"
                            data-wa-id="{{ $msg->wa_message_id }}"
                            data-name="{{ e($msg->from_name ?? 'Cliente') }}"
                            data-text="{{ e(mb_substr($msg->message_body ?? '', 0, 100)) }}"
                            onclick="setReply(this.dataset.waId, this.dataset.name, this.dataset.text)"
                            style="background: #252525; border: 1px solid #333; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                            title="Responder">
                            <svg style="width: 11px; height: 11px; color: #999;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                        </button>
                        @endif
                        <button type="button" onclick="showEmojiPicker('{{ $msg->wa_message_id }}', this)"
                            style="background: #252525; border: 1px solid #333; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                            title="Reagir">
                            <span style="font-size: 10px;">😊</span>
                        </button>
                        @if($isOutbound && $msg->message_type === 'text')
                        <button type="button" onclick="startEditMessage('{{ $msg->wa_message_id }}')"
                            style="background: #252525; border: 1px solid #333; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                            title="Editar">
                            <svg style="width: 10px; height: 10px; color: #999;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <button type="button" onclick="confirmDeleteMessage('{{ $msg->wa_message_id }}')"
                            style="background: #252525; border: 1px solid #333; border-radius: 50%; width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer;"
                            title="Apagar para todos">
                            <svg style="width: 10px; height: 10px; color: #999;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        @endif
                    </div>

                    <div style="padding: 0.4rem 0.625rem; border-radius: {{ $isOutbound ? '10px 10px 0 10px' : '10px 10px 10px 0' }};
                    background: {{ $isOutbound ? '#005c4b' : '#1a1a1a' }};
                    border: 1px solid {{ $isOutbound ? '#00694d' : '#252525' }};">

                    @if(!$isOutbound && $msg->from_name)
                        <div style="font-size: 0.6rem; color: #25D366; font-weight: 600; margin-bottom: 0.1rem;">{{ $msg->from_name }}</div>
                    @endif

                    {{-- Quoted message (reply) --}}
                    @if($msg->reply_to_body)
                        <div style="padding: 0.2rem 0.4rem; margin-bottom: 0.25rem; background: {{ $isOutbound ? '#004a3d' : '#111' }}; border-left: 2px solid #25D366; border-radius: 0 4px 4px 0;">
                            <div style="font-size: 0.6rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">{{ $msg->reply_to_body }}</div>
                        </div>
                    @endif

                    @if(in_array($msg->message_type, ['image', 'imageMessage']))
                        <div style="margin-bottom: 0.2rem;">
                            @if($msg->local_media_path)
                                <img src="{{ asset('storage/' . $msg->local_media_path) }}" alt="Imagem" style="max-width: 100%; max-height: 240px; border-radius: 6px; cursor: pointer; object-fit: cover;" onclick="window.open(this.src)">
                            @else
                                <div style="padding: 0.75rem; text-align: center; color: #555; background: #111; border-radius: 6px;">
                                    <svg style="width: 20px; height: 20px; margin: 0 auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <div style="font-size: 0.65rem; margin-top: 0.15rem;">Imagem</div>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(in_array($msg->message_type, ['audio', 'audioMessage', 'ptt', 'pttMessage']))
                        <div style="min-width: 200px; max-width: 280px;">
                            @if($msg->local_media_path)
                                <audio controls preload="none" style="width: 100%; height: 36px; border-radius: 18px;">
                                    <source src="{{ asset('storage/' . $msg->local_media_path) }}" type="{{ $msg->media_mime_type ?? 'audio/ogg' }}">
                                </audio>
                            @else
                                <div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0;">
                                    <svg style="width: 14px; height: 14px; color: #25D366; flex-shrink: 0;" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5z"/>
                                        <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                                    </svg>
                                    <span style="font-size: 0.7rem; color: #555;">Audio indisponivel</span>
                                </div>
                            @endif
                        </div>
                        @if($msg->transcription)
                            <div style="margin-top: 0.2rem; padding: 0.25rem 0.375rem; background: {{ $isOutbound ? '#004a3d' : '#111' }}; border-radius: 4px; font-size: 0.65rem; color: #888; font-style: italic;">
                                <span style="color: #555; font-size: 0.55rem;">🎙 Transcrição:</span><br>
                                {{ $msg->transcription }}
                            </div>
                        @endif
                    @endif

                    @if(in_array($msg->message_type, ['document', 'documentMessage']))
                        <div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.15rem 0;">
                            <svg style="width: 16px; height: 16px; color: #888; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <span style="font-size: 0.7rem; color: #999;">{{ $msg->raw_payload['message']['documentMessage']['fileName'] ?? 'Documento' }}</span>
                        </div>
                    @endif

                    @if($msg->message_body && !in_array($msg->message_type, ['audio', 'audioMessage', 'ptt', 'pttMessage']))
                        <div style="font-size: 0.8rem; color: #e3e3e3; white-space: pre-wrap; word-break: break-word; line-height: 1.4;">{{ $msg->message_body }}</div>
                    @endif

                    <div class="wa-status-row" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.2rem; margin-top: 0.1rem;">
                        <span style="font-size: 0.55rem; color: #555;">{{ $time }}</span>
                        @if($isOutbound)
                            @php $status = $msg->delivery_status ?? 'sent'; @endphp
                            <span class="wa-delivery-icon" data-status="{{ $status }}">
                                @if($status === 'read')
                                    <svg style="width: 13px; height: 13px; color: #53bdeb;" viewBox="0 0 16 15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88 5.59 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>
                                @elseif($status === 'delivered')
                                    <svg style="width: 13px; height: 13px; color: #888;" viewBox="0 0 16 15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88 5.59 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>
                                @else
                                    <svg style="width: 11px; height: 11px; color: #888;" viewBox="0 0 16 15" fill="currentColor"><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>
                                @endif
                            </span>
                        @endif
                    </div>
                    </div>{{-- /bubble --}}
                </div>{{-- /position:relative wrapper --}}
            </div>{{-- /msg-row --}}
        @empty
            <div id="wa-empty-state" style="flex: 1; display: flex; align-items: center; justify-content: center; color: #333;">
                <div style="text-align: center;">
                    <svg style="width: 36px; height: 36px; margin: 0 auto 0.5rem; opacity: 0.3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p style="font-size: 0.8rem;">Nenhuma mensagem ainda</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Input Area --}}
    @if($deal->phone && !$deal->stage?->is_won && !$deal->stage?->is_lost)
        <div style="flex-shrink: 0; padding: 0.5rem 0.75rem; background: #111; border-top: 1px solid rgba(255,255,255,0.06);">
            <form id="wa-send-form" style="display: flex; gap: 0.5rem; align-items: flex-end;">
                @csrf

                {{-- Botões de ação (menu + anexo) --}}
                <div style="display: flex; gap: 0.25rem; flex-shrink: 0; padding-bottom: 1px;">
                    {{-- Menu Interativo --}}
                    <div x-data="{ showMenuForm: false }" style="position: relative;">
                        <button type="button" @click="showMenuForm = !showMenuForm"
                                style="width: 34px; height: 34px; border-radius: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: border-color 0.15s;"
                                onmouseover="this.style.borderColor='rgba(255,255,255,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'"
                                title="Menu interativo">
                            <svg style="width: 16px; height: 16px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div x-show="showMenuForm" x-cloak @click.away="showMenuForm = false"
                             style="position: absolute; bottom: 100%; left: 0; margin-bottom: 0.5rem; background: #141414; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; padding: 0.75rem; width: 280px; z-index: 20; box-shadow: 0 -4px 16px rgba(0,0,0,0.4);">
                            <div style="font-size: 0.7rem; font-weight: 600; color: #e3e3e3; margin-bottom: 0.375rem;">Menu Interativo</div>
                            <input type="text" id="menu-title" placeholder="Titulo" style="width: 100%; padding: 0.3rem 0.5rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; color: #e3e3e3; font-size: 0.7rem; margin-bottom: 0.25rem;">
                            <textarea id="menu-body" placeholder="Mensagem" rows="2" style="width: 100%; padding: 0.3rem 0.5rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; color: #e3e3e3; font-size: 0.7rem; resize: none; margin-bottom: 0.25rem;"></textarea>
                            <div style="display: flex; flex-direction: column; gap: 0.2rem; margin-bottom: 0.375rem;">
                                <input type="text" id="menu-btn-1" placeholder="Botao 1" style="width: 100%; padding: 0.25rem 0.5rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.25rem; color: #e3e3e3; font-size: 0.65rem;">
                                <input type="text" id="menu-btn-2" placeholder="Botao 2 (opcional)" style="width: 100%; padding: 0.25rem 0.5rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.25rem; color: #e3e3e3; font-size: 0.65rem;">
                                <input type="text" id="menu-btn-3" placeholder="Botao 3 (opcional)" style="width: 100%; padding: 0.25rem 0.5rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.25rem; color: #e3e3e3; font-size: 0.65rem;">
                            </div>
                            <button type="button" onclick="sendInteractiveMenu()" style="width: 100%; padding: 0.3rem; background: #25D366; color: #000; font-weight: 600; font-size: 0.65rem; border: none; border-radius: 0.375rem; cursor: pointer;">
                                Enviar Menu
                            </button>
                        </div>
                    </div>

                    {{-- Anexar Arquivo --}}
                    <label style="cursor: pointer; width: 34px; height: 34px; border-radius: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; transition: border-color 0.15s;"
                           onmouseover="this.style.borderColor='rgba(255,255,255,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'"
                           title="Enviar arquivo">
                        <input type="file" id="wa-media-input" accept="image/*,audio/*,video/*,application/pdf,.doc,.docx,.xls,.xlsx" style="display: none;" onchange="handleMediaSelect(this)">
                        <svg style="width: 16px; height: 16px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </label>

                    {{-- Enviar Localização --}}
                    <button type="button" onclick="sendStoreLocation()"
                        style="width: 34px; height: 34px; border-radius: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: border-color 0.15s;"
                        onmouseover="this.style.borderColor='rgba(255,255,255,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'"
                        title="Enviar localização da loja">
                        <svg style="width: 16px; height: 16px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>

                    {{-- Enviar PIX --}}
                    <button type="button" onclick="sendPixButton()"
                        style="width: 34px; height: 34px; border-radius: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: border-color 0.15s;"
                        onmouseover="this.style.borderColor='rgba(255,255,255,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'"
                        title="Enviar chave PIX">
                        <span style="font-size: 14px; color: #666; font-weight: 700;">₱</span>
                    </button>

                    {{-- Respostas Rápidas --}}
                    <div x-data="{ showQuickReplies: false }" style="position: relative;">
                        <button type="button" @click="showQuickReplies = !showQuickReplies"
                            style="width: 34px; height: 34px; border-radius: 0.375rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: border-color 0.15s;"
                            onmouseover="this.style.borderColor='rgba(255,255,255,0.15)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)'"
                            title="Respostas rápidas">
                            <svg style="width: 16px; height: 16px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </button>
                        <div x-show="showQuickReplies" x-cloak @click.away="showQuickReplies = false"
                             style="position: absolute; bottom: 100%; left: 0; margin-bottom: 0.5rem; background: #141414; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; padding: 0.5rem; width: 300px; max-height: 320px; z-index: 20; box-shadow: 0 -4px 16px rgba(0,0,0,0.4); display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.375rem;">
                                <span style="font-size: 0.7rem; font-weight: 600; color: #e3e3e3;">Respostas Rápidas</span>
                                <button type="button" onclick="toggleQuickReplyForm()" style="background: #25D366; border: none; border-radius: 4px; color: #000; font-size: 0.6rem; font-weight: 600; padding: 0.15rem 0.4rem; cursor: pointer;">+ Nova</button>
                            </div>
                            <input type="text" id="qr-search" placeholder="Buscar atalho..." oninput="filterQuickReplies(this.value)"
                                style="width: 100%; padding: 0.25rem 0.4rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.25rem; color: #e3e3e3; font-size: 0.7rem; margin-bottom: 0.375rem; box-sizing: border-box;">
                            <div id="qr-list" style="flex: 1; overflow-y: auto; max-height: 200px;">
                                <div style="text-align: center; padding: 1rem; color: #555; font-size: 0.7rem;">Carregando...</div>
                            </div>
                            <div id="qr-form" style="display: none; border-top: 1px solid #252525; padding-top: 0.375rem; margin-top: 0.375rem;">
                                <input type="text" id="qr-new-shortcut" placeholder="Atalho (ex: endereco)" style="width: 100%; padding: 0.25rem 0.4rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.25rem; color: #e3e3e3; font-size: 0.65rem; margin-bottom: 0.2rem; box-sizing: border-box;">
                                <textarea id="qr-new-text" placeholder="Texto da resposta" rows="2" style="width: 100%; padding: 0.25rem 0.4rem; background: #0a0a0a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.25rem; color: #e3e3e3; font-size: 0.65rem; resize: none; margin-bottom: 0.2rem; box-sizing: border-box;"></textarea>
                                <div style="display: flex; gap: 0.25rem;">
                                    <button type="button" onclick="saveQuickReply()" style="flex: 1; padding: 0.2rem; background: #25D366; color: #000; font-weight: 600; font-size: 0.6rem; border: none; border-radius: 0.25rem; cursor: pointer;">Salvar</button>
                                    <button type="button" onclick="toggleQuickReplyForm()" style="padding: 0.2rem 0.4rem; background: #1a1a1a; color: #888; font-size: 0.6rem; border: 1px solid #252525; border-radius: 0.25rem; cursor: pointer;">Cancelar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Input de Mensagem --}}
                <div style="flex: 1; min-width: 0;">
                    <div id="wa-media-preview" style="display: none; padding: 0.3rem 0.5rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem 0.375rem 0 0; border-bottom: none;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span id="wa-media-name" style="font-size: 0.65rem; color: #888;"></span>
                            <button type="button" onclick="clearMediaPreview()" style="background: none; border: none; color: #555; cursor: pointer; font-size: 0.75rem; line-height: 1; padding: 0;">✕</button>
                        </div>
                    </div>
                    <textarea id="wa-message-input" rows="1" placeholder="Digite uma mensagem..."
                        style="width: 100%; resize: none; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.08); border-radius: 0.375rem; padding: 0.5rem 0.75rem; color: #e3e3e3; font-size: 0.8rem; outline: none; font-family: inherit; max-height: 80px; overflow-y: auto; line-height: 1.4; box-sizing: border-box;"
                        onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault(); sendWaMessage();}"></textarea>
                </div>

                {{-- Botao Enviar --}}
                <div style="flex-shrink: 0; padding-bottom: 1px;">
                    <button type="button" onclick="sendWaMessage()" id="wa-send-btn"
                        style="height: 34px; padding: 0 0.75rem; border-radius: 0.375rem; background: #25D366; color: #000; font-weight: 600; font-size: 0.75rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.3rem; white-space: nowrap; transition: opacity 0.15s;"
                        onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
(function() {
    const msgContainer = document.getElementById('wa-messages');
    if (msgContainer) msgContainer.scrollTop = msgContainer.scrollHeight;

    let selectedFile = null;
    let replyToId = null;
    let replyToName = null;
    let replyToText = null;
    let lastPollTime = '{{ now()->toISOString() }}';
    let pollInterval = null;
    let knownMessageIds = new Set();

    document.querySelectorAll('.wa-msg-row[data-wa-id]').forEach(el => {
        knownMessageIds.add(el.dataset.waId);
    });

    // ─── Reply ──────────────────────────────────────────────

    window.setReply = function(waId, name, text) {
        replyToId = waId;
        replyToName = name;
        replyToText = text;

        document.getElementById('wa-reply-bar').style.display = 'block';
        document.getElementById('wa-reply-name').textContent = name;
        document.getElementById('wa-reply-text').textContent = text || '...';
        document.getElementById('wa-message-input')?.focus();
    };

    window.cancelReply = function() {
        replyToId = null;
        replyToName = null;
        replyToText = null;
        document.getElementById('wa-reply-bar').style.display = 'none';
    };

    // ─── Media ──────────────────────────────────────────────

    window.handleMediaSelect = function(input) {
        if (input.files.length > 0) {
            selectedFile = input.files[0];
            document.getElementById('wa-media-name').textContent = `📎 ${selectedFile.name} (${(selectedFile.size/1024).toFixed(1)} KB)`;
            document.getElementById('wa-media-preview').style.display = 'block';
        }
    };

    window.clearMediaPreview = function() {
        selectedFile = null;
        document.getElementById('wa-media-input').value = '';
        document.getElementById('wa-media-preview').style.display = 'none';
    };

    // ─── Send Message ───────────────────────────────────────

    window.sendWaMessage = async function() {
        const input = document.getElementById('wa-message-input');
        const btn = document.getElementById('wa-send-btn');
        const message = input?.value?.trim();

        if (!message && !selectedFile) return;

        btn.disabled = true;
        btn.style.opacity = '0.5';
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span style="display: inline-block; width: 14px; height: 14px; border: 2px solid #000; border-top-color: transparent; border-radius: 50%; animation: spin 0.6s linear infinite;"></span>';

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
                const payload = { message };
                if (replyToId) payload.reply_to = replyToId;

                res = await fetch('{{ route("crm.deals.wa.send", $deal) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });
            }

            const data = await res.json();
            if (data.success) {
                const sentBody = message || '';
                const now = new Date();
                const timeStr = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                const tempId = 'local_' + Date.now();

                if (sentBody && !selectedFile) {
                    appendPollMessage({
                        wa_message_id: tempId,
                        direction: 'outbound',
                        from_name: '{{ addslashes(auth()->user()->name) }} DG Store',
                        message_type: 'text',
                        message_body: sentBody,
                        delivery_status: 'sent',
                        time: timeStr,
                        reply_to_body: replyToId ? (document.querySelector('.reply-preview-text')?.textContent || null) : null,
                    });
                    knownMessageIds.add(tempId);
                    const mc = document.getElementById('wa-messages');
                    if (mc) mc.scrollTop = mc.scrollHeight;
                }

                input.value = '';
                window.clearMediaPreview();
                window.cancelReply();
                setTimeout(pollMessages, 1500);
            } else {
                showWaError(data.error || 'Erro ao enviar mensagem');
            }
        } catch(e) {
            showWaError('Erro de conexão ao enviar mensagem');
        } finally {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.innerHTML = originalText;
        }
    };

    // ─── Interactive Menu ───────────────────────────────────

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
                const menuText = title + '\n' + body;
                const tempId = 'menu_' + Date.now();
                const now = new Date();
                appendPollMessage({
                    wa_message_id: tempId,
                    direction: 'outbound',
                    from_name: '{{ addslashes(auth()->user()->name) }} DG Store',
                    message_type: 'text',
                    message_body: menuText,
                    delivery_status: 'sent',
                    time: now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' }),
                });
                knownMessageIds.add(tempId);

                document.getElementById('menu-title').value = '';
                document.getElementById('menu-body').value = '';
                document.getElementById('menu-btn-1').value = '';
                document.getElementById('menu-btn-2').value = '';
                document.getElementById('menu-btn-3').value = '';
                setTimeout(pollMessages, 1500);
            } else {
                showWaError(data.error || 'Erro ao enviar menu');
            }
        } catch(e) {
            showWaError('Erro de conexão ao enviar menu');
        }
    };

    // ─── Polling (Real-time) ────────────────────────────────

    function startPolling() {
        pollMessages();
        pollInterval = setInterval(pollMessages, 5000);
    }

    async function pollMessages() {
        try {
            const url = `{{ route('crm.deals.wa.poll', $deal) }}?since=${encodeURIComponent(lastPollTime)}`;
            const res = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });

            if (!res.ok) return;
            const data = await res.json();

            if (data.messages?.length) {
                data.messages.forEach(msg => {
                    if (!knownMessageIds.has(msg.wa_message_id)) {
                        knownMessageIds.add(msg.wa_message_id);
                        appendPollMessage(msg);
                    }
                });
            }

            if (data.status_updates?.length) {
                data.status_updates.forEach(upd => {
                    updateDeliveryStatus(upd.wa_message_id, upd.delivery_status);
                });
            }

            if (data.presence) {
                showPresence(data.presence);
            } else {
                hidePresence();
            }

            if (data.server_time) {
                lastPollTime = data.server_time;
            }
        } catch(e) {
            // silent fail
        }
    }

    function appendPollMessage(msg) {
        const container = document.getElementById('wa-messages');
        const emptyState = document.getElementById('wa-empty-state');
        if (emptyState) emptyState.remove();

        const isOutbound = msg.direction === 'outbound';
        const time = msg.time || new Date(msg.created_at).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

        const row = document.createElement('div');
        row.className = 'wa-msg-row';
        row.dataset.waId = msg.wa_message_id;
        row.dataset.direction = msg.direction;
        row.style.cssText = `display: flex; justify-content: ${isOutbound ? 'flex-end' : 'flex-start'}; max-width: 100%;`;

        row.onmouseenter = function() { this.querySelector('.wa-hover-actions')?.style.setProperty('display','flex'); };
        row.onmouseleave = function() { this.querySelector('.wa-hover-actions')?.style.setProperty('display','none'); };

        const hoverPos = isOutbound ? 'left: -2px' : 'right: -2px';
        const btnStyle = 'background:#252525;border:1px solid #333;border-radius:50%;width:22px;height:22px;display:flex;align-items:center;justify-content:center;cursor:pointer;';

        let hoverBtns = '';
        if (!isOutbound) {
            hoverBtns += `<button class="wa-reply-btn" type="button" data-wa-id="${msg.wa_message_id}" data-name="${escHtml(msg.from_name || 'Cliente')}" data-text="${escHtml((msg.message_body || '').substring(0, 100))}" onclick="setReply(this.dataset.waId, this.dataset.name, this.dataset.text)" style="${btnStyle}" title="Responder"><svg style="width:11px;height:11px;color:#999;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg></button>`;
        }
        hoverBtns += `<button type="button" onclick="showEmojiPicker('${msg.wa_message_id}', this)" style="${btnStyle}" title="Reagir"><span style="font-size:10px;">😊</span></button>`;
        if (isOutbound && (msg.message_type === 'text' || !msg.message_type)) {
            hoverBtns += `<button type="button" onclick="startEditMessage('${msg.wa_message_id}')" style="${btnStyle}" title="Editar"><svg style="width:10px;height:10px;color:#999;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>`;
            hoverBtns += `<button type="button" onclick="confirmDeleteMessage('${msg.wa_message_id}')" style="${btnStyle}" title="Apagar"><svg style="width:10px;height:10px;color:#999;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>`;
        }
        const actionsHtml = `<div class="wa-hover-actions" style="display:none;position:absolute;top:-10px;${hoverPos};gap:2px;z-index:5;">${hoverBtns}</div>`;

        let quoteHtml = '';
        if (msg.reply_to_body) {
            const quoteBg = isOutbound ? '#004a3d' : '#111';
            quoteHtml = `<div style="padding: 0.2rem 0.4rem; margin-bottom: 0.25rem; background: ${quoteBg}; border-left: 2px solid #25D366; border-radius: 0 4px 4px 0;"><div style="font-size: 0.6rem; color: #888; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 250px;">${escHtml(msg.reply_to_body)}</div></div>`;
        }

        let bodyHtml = '';
        const bodyText = msg.message_body || '';
        const mType = msg.message_type || 'text';

        if (['image', 'imageMessage'].includes(mType) && msg.local_media_path) {
            bodyHtml += `<div style="margin-bottom: 0.2rem;"><img src="/storage/${msg.local_media_path}" style="max-width: 100%; max-height: 240px; border-radius: 6px; cursor: pointer; object-fit: cover;" onclick="window.open(this.src)"></div>`;
        }

        if (['audio', 'audioMessage', 'ptt', 'pttMessage'].includes(mType)) {
            if (msg.local_media_path) {
                bodyHtml += `<div style="min-width: 200px; max-width: 280px;"><audio controls preload="none" style="width: 100%; height: 36px; border-radius: 18px;"><source src="/storage/${msg.local_media_path}" type="audio/ogg"></audio></div>`;
            } else {
                bodyHtml += `<div style="display: flex; align-items: center; gap: 0.375rem; padding: 0.25rem 0; min-width: 150px;"><svg style="width: 14px; height: 14px; color: #25D366; flex-shrink: 0;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm-1-9c0-.55.45-1 1-1s1 .45 1 1v6c0 .55-.45 1-1 1s-1-.45-1-1V5z"/><path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/></svg><span style="font-size: 0.7rem; color: #555;">Audio indisponivel</span></div>`;
            }
            if (msg.transcription) {
                const tBg = isOutbound ? '#004a3d' : '#111';
                bodyHtml += `<div style="margin-top: 0.2rem; padding: 0.25rem 0.375rem; background: ${tBg}; border-radius: 4px; font-size: 0.65rem; color: #888; font-style: italic;"><span style="color: #555; font-size: 0.55rem;">🎙 Transcrição:</span><br>${escHtml(msg.transcription)}</div>`;
            }
        } else if (bodyText) {
            bodyHtml += `<div style="font-size: 0.8rem; color: #e3e3e3; white-space: pre-wrap; word-break: break-word; line-height: 1.4;">${escHtml(bodyText)}</div>`;
        }

        const deliveryIcon = isOutbound ? getDeliveryIcon(msg.delivery_status || 'sent') : '';

        const senderHtml = !isOutbound && msg.from_name
            ? `<div style="font-size: 0.6rem; color: #25D366; font-weight: 600; margin-bottom: 0.1rem;">${escHtml(msg.from_name)}</div>`
            : '';

        row.innerHTML = `
            <div style="max-width: 80%; position: relative;">
                ${actionsHtml}
                <div style="padding: 0.4rem 0.625rem; border-radius: ${isOutbound ? '10px 10px 0 10px' : '10px 10px 10px 0'};
                    background: ${isOutbound ? '#005c4b' : '#1a1a1a'};
                    border: 1px solid ${isOutbound ? '#00694d' : '#252525'};">
                    ${senderHtml}
                    ${quoteHtml}
                    ${bodyHtml}
                    <div class="wa-status-row" style="display: flex; align-items: center; justify-content: flex-end; gap: 0.2rem; margin-top: 0.1rem;">
                        <span style="font-size: 0.55rem; color: #555;">${time}</span>
                        ${deliveryIcon ? `<span class="wa-delivery-icon" data-status="${msg.delivery_status || 'sent'}">${deliveryIcon}</span>` : ''}
                    </div>
                </div>
            </div>`;

        container.appendChild(row);

        const isNearBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 120;
        if (isNearBottom) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function updateDeliveryStatus(waMessageId, status) {
        const row = document.querySelector(`.wa-msg-row[data-wa-id="${waMessageId}"]`);
        if (!row) return;

        const icon = row.querySelector('.wa-delivery-icon');
        if (icon) {
            const currentStatus = icon.dataset.status;
            const rank = { sent: 1, delivered: 2, read: 3 };
            if ((rank[status] || 0) <= (rank[currentStatus] || 0)) return;

            icon.dataset.status = status;
            icon.innerHTML = getDeliveryIcon(status);
        }
    }

    function getDeliveryIcon(status) {
        if (status === 'read') {
            return '<svg style="width: 13px; height: 13px; color: #53bdeb;" viewBox="0 0 16 15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88 5.59 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>';
        } else if (status === 'delivered') {
            return '<svg style="width: 13px; height: 13px; color: #888;" viewBox="0 0 16 15" fill="currentColor"><path d="M15.01 3.316l-.478-.372a.365.365 0 00-.51.063L8.666 9.88 5.59 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>';
        }
        return '<svg style="width: 11px; height: 11px; color: #888;" viewBox="0 0 16 15" fill="currentColor"><path d="M10.91 3.316l-.478-.372a.365.365 0 00-.51.063L4.566 9.88 1.49 6.222a.366.366 0 00-.516-.063l-.478.372a.365.365 0 00-.063.516l3.75 4.584a.366.366 0 00.516.063l6.148-7.862a.365.365 0 00.063-.516z"/></svg>';
    }

    // ─── Presence ───────────────────────────────────────────

    function showPresence(type) {
        const bar = document.getElementById('wa-presence-bar');
        const text = document.getElementById('wa-presence-text');
        if (!bar || !text) return;

        const labels = {
            composing: '✍️ digitando...',
            recording: '🎙️ gravando áudio...',
            available: null,
            unavailable: null,
            paused: null,
        };

        const label = labels[type] ?? null;
        if (label) {
            text.textContent = label;
            bar.style.display = 'block';
        } else {
            bar.style.display = 'none';
        }
    }

    function hidePresence() {
        const bar = document.getElementById('wa-presence-bar');
        if (bar) bar.style.display = 'none';
    }

    // ─── Helpers ────────────────────────────────────────────

    function escHtml(s) {
        if (!s) return '';
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    // Auto-resize textarea
    const autoTextarea = document.getElementById('wa-message-input');
    if (autoTextarea) {
        autoTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        });
    }

    // Start polling when chat tab is active
    startPolling();

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            if (pollInterval) { clearInterval(pollInterval); pollInterval = null; }
        } else {
            if (!pollInterval) startPolling();
        }
    });
    // ─── Error Modal ────────────────────────────────────────

    window.showWaError = function(errorMsg) {
        const isPhoneError = /not on whatsapp|número.*inválido|invalid.*number|not.*registered/i.test(errorMsg);
        const phone = '{{ $deal->phone }}';

        const overlay = document.createElement('div');
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;display:flex;align-items:center;justify-content:center;';

        const title = isPhoneError ? 'Número não encontrado no WhatsApp' : 'Erro ao enviar mensagem';
        const desc = isPhoneError
            ? `O número <strong>${phone}</strong> não está registrado no WhatsApp. Verifique se o telefone do cliente está correto.`
            : errorMsg;

        let editBtn = '';
        if (isPhoneError) {
            editBtn = `
                <div style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid rgba(255,255,255,0.06);">
                    <label style="font-size:0.7rem; color:#818181; display:block; margin-bottom:4px;">Corrigir telefone:</label>
                    <div style="display:flex; gap:0.375rem;">
                        <input type="text" id="wa-fix-phone" value="${phone}" maxlength="15"
                               oninput="this.value=this.value.replace(/\\D/g,'').replace(/^(\\d{2})(\\d)/,'($1) $2').replace(/(\\d{5})(\\d)/,'$1-$2').substring(0,15)"
                               style="flex:1; padding:0.5rem; background:#0d0d0d; border:1px solid rgba(255,255,255,0.1); border-radius:0.375rem; color:#e3e3e3; font-size:0.85rem;">
                        <button type="button" onclick="saveFixedPhone()" id="wa-fix-btn"
                                style="padding:0.5rem 1rem; background:#059669; color:white; border:none; border-radius:0.375rem; font-size:0.8rem; font-weight:600; cursor:pointer;">
                            Salvar
                        </button>
                    </div>
                </div>`;
        }

        overlay.innerHTML = `
            <div style="background:#141414; border:1px solid rgba(255,255,255,0.08); border-radius:0.75rem; padding:1.5rem; max-width:380px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.4);">
                <div style="display:flex; align-items:center; gap:0.625rem; margin-bottom:0.75rem;">
                    <div style="width:2.25rem; height:2.25rem; border-radius:50%; background:${isPhoneError ? 'rgba(245,158,11,0.15)' : 'rgba(239,68,68,0.15)'}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        ${isPhoneError
                            ? '<svg style="width:1.125rem;height:1.125rem;color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                            : '<svg style="width:1.125rem;height:1.125rem;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
                        }
                    </div>
                    <h3 style="font-size:0.9rem; font-weight:700; color:#e3e3e3; margin:0;">${title}</h3>
                </div>
                <p style="font-size:0.8rem; color:#a4a4a4; margin:0; line-height:1.5;">${desc}</p>
                ${editBtn}
                <div style="display:flex; justify-content:flex-end; margin-top:1rem;">
                    <button type="button" onclick="this.closest('[style*=position]').remove()"
                            style="padding:0.4rem 1rem; background:#222; color:#a4a4a4; border:none; border-radius:0.375rem; font-size:0.8rem; font-weight:600; cursor:pointer;">
                        Fechar
                    </button>
                </div>
            </div>`;

        document.body.appendChild(overlay);
        overlay.addEventListener('click', function(e) { if (e.target === overlay) overlay.remove(); });

        const fixInput = overlay.querySelector('#wa-fix-phone');
        if (fixInput) fixInput.focus();
    };

    window.saveFixedPhone = async function() {
        const input = document.getElementById('wa-fix-phone');
        const btn = document.getElementById('wa-fix-btn');
        const raw = input.value.replace(/\D/g, '');

        if (raw.length < 10) {
            input.style.borderColor = '#fca5a5';
            return;
        }

        btn.disabled = true;
        btn.textContent = '...';

        try {
            const res = await fetch('{{ route("crm.deals.phone", $deal) }}', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ phone: input.value }),
            });

            if (res.ok || res.status === 302) {
                window.location.reload();
            } else {
                btn.textContent = 'Erro';
                setTimeout(() => { btn.textContent = 'Salvar'; btn.disabled = false; }, 2000);
            }
        } catch(e) {
            btn.textContent = 'Erro';
            setTimeout(() => { btn.textContent = 'Salvar'; btn.disabled = false; }, 2000);
        }
    };

    // ─── Emoji Reactions ────────────────────────────────────

    const QUICK_EMOJIS = ['👍', '❤️', '😂', '😮', '😢', '🙏'];

    window.showEmojiPicker = function(waMessageId, btnEl) {
        document.querySelectorAll('.wa-emoji-picker').forEach(el => el.remove());

        const picker = document.createElement('div');
        picker.className = 'wa-emoji-picker';
        picker.style.cssText = 'position:absolute; bottom:100%; left:50%; transform:translateX(-50%); margin-bottom:4px; background:#1a1a1a; border:1px solid #333; border-radius:20px; padding:4px 6px; display:flex; gap:2px; z-index:30; box-shadow:0 4px 12px rgba(0,0,0,0.5);';

        QUICK_EMOJIS.forEach(emoji => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = emoji;
            btn.style.cssText = 'background:none; border:none; font-size:18px; cursor:pointer; padding:2px 4px; border-radius:6px; transition:background 0.1s;';
            btn.onmouseenter = () => btn.style.background = '#333';
            btn.onmouseleave = () => btn.style.background = 'none';
            btn.onclick = () => { sendReaction(waMessageId, emoji); picker.remove(); };
            picker.appendChild(btn);
        });

        btnEl.parentElement.style.position = 'relative';
        btnEl.parentElement.appendChild(picker);

        const autoClose = (e) => {
            if (!picker.contains(e.target)) { picker.remove(); document.removeEventListener('click', autoClose); }
        };
        setTimeout(() => document.addEventListener('click', autoClose), 50);
    };

    async function sendReaction(waMessageId, emoji) {
        try {
            const res = await fetch('{{ route("crm.deals.wa.react", $deal) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]')?.value || document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ message_id: waMessageId, emoji }),
            });
            const data = await res.json();
            if (data.success) {
                const row = document.querySelector(`.wa-msg-row[data-wa-id="${waMessageId}"]`);
                if (row) {
                    let reactEl = row.querySelector('.wa-reaction');
                    if (!reactEl) {
                        reactEl = document.createElement('div');
                        reactEl.className = 'wa-reaction';
                        reactEl.style.cssText = 'position:absolute; bottom:-8px; left:8px; background:#1a1a1a; border:1px solid #333; border-radius:10px; padding:1px 5px; font-size:14px; z-index:2;';
                        const bubble = row.querySelector('div[style*="max-width: 80%"]');
                        if (bubble) { bubble.style.position = 'relative'; bubble.appendChild(reactEl); }
                    }
                    reactEl.textContent = emoji;
                }
            }
        } catch(e) {}
    }

    // ─── Edit Message ───────────────────────────────────────

    window.startEditMessage = function(waMessageId) {
        const row = document.querySelector(`.wa-msg-row[data-wa-id="${waMessageId}"]`);
        if (!row) return;

        const textEl = row.querySelector('div[style*="white-space: pre-wrap"]');
        if (!textEl) return;

        const currentText = textEl.textContent.trim();
        const originalHtml = textEl.innerHTML;

        textEl.innerHTML = `
            <div style="display:flex; flex-direction:column; gap:4px;">
                <textarea class="wa-edit-input" style="width:100%; min-height:40px; background:#003d33; border:1px solid #25D366; border-radius:4px; color:#e3e3e3; font-size:0.8rem; padding:4px 6px; resize:none; font-family:inherit;">${escHtml(currentText)}</textarea>
                <div style="display:flex; gap:4px; justify-content:flex-end;">
                    <button type="button" class="wa-edit-cancel" style="padding:2px 8px; background:#333; color:#888; border:none; border-radius:4px; font-size:0.65rem; cursor:pointer;">Cancelar</button>
                    <button type="button" class="wa-edit-save" style="padding:2px 8px; background:#25D366; color:#000; border:none; border-radius:4px; font-size:0.65rem; font-weight:600; cursor:pointer;">Salvar</button>
                </div>
            </div>`;

        const input = textEl.querySelector('.wa-edit-input');
        input.focus();
        input.setSelectionRange(input.value.length, input.value.length);

        textEl.querySelector('.wa-edit-cancel').onclick = () => { textEl.innerHTML = originalHtml; };
        textEl.querySelector('.wa-edit-save').onclick = async () => {
            const newText = input.value.trim();
            if (!newText || newText === currentText) { textEl.innerHTML = originalHtml; return; }

            try {
                const res = await fetch('{{ route("crm.deals.wa.edit", $deal) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]')?.value || document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message_id: waMessageId, text: newText }),
                });
                const data = await res.json();
                if (data.success) {
                    textEl.innerHTML = '';
                    textEl.textContent = newText;
                } else {
                    showWaError(data.error || 'Erro ao editar mensagem');
                    textEl.innerHTML = originalHtml;
                }
            } catch(e) {
                showWaError('Erro de conexão');
                textEl.innerHTML = originalHtml;
            }
        };
    };

    // ─── Delete Message ─────────────────────────────────────

    window.confirmDeleteMessage = function(waMessageId) {
        if (!confirm('Apagar esta mensagem para todos?')) return;

        fetch('{{ route("crm.deals.wa.delete", $deal) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]')?.value || document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ message_id: waMessageId }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`.wa-msg-row[data-wa-id="${waMessageId}"]`);
                if (row) {
                    const textEl = row.querySelector('div[style*="white-space: pre-wrap"]');
                    if (textEl) {
                        textEl.textContent = '🚫 Mensagem apagada';
                        textEl.style.fontStyle = 'italic';
                        textEl.style.color = '#666';
                    }
                    row.querySelector('.wa-hover-actions')?.remove();
                }
            } else {
                showWaError(data.error || 'Erro ao apagar');
            }
        })
        .catch(() => showWaError('Erro de conexão'));
    };

    // ─── Send Location ──────────────────────────────────────

    window.sendStoreLocation = function() {
        useQuickReply("📍 Endereço:\nRua Rahme Trad Bechara Hage, 2061\nSala 45, 4º andar\nCentro Empresarial Anthurium\nHigienopolis - S.J. do Rio Preto\n(Atendimento com horário marcado)");
    };

    window.sendPixButton = function() {
        useQuickReply("💳 FORMA DE PAGAMENTO – DG STORE\n\n🚨ATENÇÃO SEGUE CHAVE PIX 👇🏽\nBanco: Stone\nNome: Valdinei Vinha\n\nChave pix telefone\n17996498338\n\n⚠️ IMPORTANTE:\n• Após o pagamento, enviar o comprovante neste número\n• Pedido só é reservado mediante pagamento\n\n🤝 Qualquer dúvida, só chamar!");
    };

    // ─── Quick Replies ──────────────────────────────────────

    let quickRepliesData = [];
    let quickRepliesLoaded = false;

    window.toggleQuickReplyForm = function() {
        const form = document.getElementById('qr-form');
        if (form) form.style.display = form.style.display === 'none' ? 'block' : 'none';
    };

    window.filterQuickReplies = function(query) {
        if (!quickRepliesLoaded) { loadQuickReplies(); return; }
        const q = query.toLowerCase();
        renderQuickReplies(quickRepliesData.filter(r =>
            r.shortCut?.toLowerCase().includes(q) ||
            r.message?.text?.toLowerCase().includes(q)
        ));
    };

    async function loadQuickReplies() {
        try {
            const res = await fetch('{{ route("crm.quick-replies.list") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            });
            const data = await res.json();
            quickRepliesData = data.data || data.quickReplies || data || [];
            if (!Array.isArray(quickRepliesData)) quickRepliesData = [];
            quickRepliesLoaded = true;
            renderQuickReplies(quickRepliesData);
        } catch(e) {
            document.getElementById('qr-list').innerHTML = '<div style="text-align:center;padding:0.5rem;color:#f87171;font-size:0.7rem;">Erro ao carregar</div>';
        }
    }

    const _qrTexts = {};

    function renderQuickReplies(items) {
        const list = document.getElementById('qr-list');
        if (!items.length) {
            list.innerHTML = '<div style="text-align:center;padding:0.75rem;color:#555;font-size:0.7rem;">Nenhuma resposta rápida</div>';
            return;
        }
        list.innerHTML = items.map((r, i) => {
            const text = r.message?.text || r.message?.name || '(sem texto)';
            const preview = text.length > 60 ? text.substring(0, 60) + '...' : text;
            const key = 'qr_' + i;
            _qrTexts[key] = text;
            return `<div class="qr-item" data-qr-key="${key}" style="padding:0.35rem 0.4rem;border-radius:0.25rem;cursor:pointer;transition:background 0.1s;border-bottom:1px solid #1a1a1a;" onmouseenter="this.style.background='#1a1a1a'" onmouseleave="this.style.background='none'">
                <div style="font-size:0.65rem;color:#25D366;font-weight:600;">/${escHtml(r.shortCut || '')}</div>
                <div style="font-size:0.7rem;color:#999;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escHtml(preview)}</div>
            </div>`;
        }).join('');

        list.querySelectorAll('.qr-item[data-qr-key]').forEach(el => {
            el.onclick = () => useQuickReply(_qrTexts[el.dataset.qrKey]);
        });
    }

    window.useQuickReply = function(text) {
        const input = document.getElementById('wa-message-input');
        if (input) {
            input.value = text;
            input.focus();
            input.style.height = 'auto';
            input.style.height = Math.min(input.scrollHeight, 80) + 'px';
        }
    };

    window.saveQuickReply = async function() {
        const shortcut = document.getElementById('qr-new-shortcut')?.value?.trim();
        const text = document.getElementById('qr-new-text')?.value?.trim();
        if (!shortcut || !text) { alert('Preencha atalho e texto'); return; }

        try {
            const res = await fetch('{{ route("crm.quick-replies.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('#wa-send-form input[name=_token]')?.value || document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ shortcut, text }),
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('qr-new-shortcut').value = '';
                document.getElementById('qr-new-text').value = '';
                toggleQuickReplyForm();
                quickRepliesLoaded = false;
                loadQuickReplies();
            } else {
                alert('Erro ao salvar: ' + (data.error || 'desconhecido'));
            }
        } catch(e) {
            alert('Erro de conexão');
        }
    };

    // Load quick replies on first open
    const qrObserver = new MutationObserver(() => {
        const qrPanel = document.querySelector('[x-show="showQuickReplies"]');
        if (qrPanel && qrPanel.style.display !== 'none' && !quickRepliesLoaded) {
            loadQuickReplies();
        }
    });
    const qrContainer = document.querySelector('[x-data*="showQuickReplies"]');
    if (qrContainer) qrObserver.observe(qrContainer, { attributes: true, subtree: true });

})();
</script>
<style>
@keyframes spin { to { transform: rotate(360deg); } }
#wa-messages { scrollbar-width: none; -ms-overflow-style: none; }
#wa-messages::-webkit-scrollbar { display: none; }
#wa-chat-panel audio {
    filter: invert(1) hue-rotate(180deg);
    opacity: 0.85;
}
#wa-chat-panel audio::-webkit-media-controls-panel {
    background: transparent;
}
</style>
