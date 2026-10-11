<x-app-layout>
    <x-slot name="title">Nova Campanha</x-slot>

    <div style="max-width: 40rem; margin: 0 auto; padding: 1.5rem;">
        <h1 style="font-size: 1.25rem; font-weight: 700; color: #e3e3e3; margin-bottom: 1.5rem;">📢 Nova Campanha</h1>

        <form method="POST" action="{{ route('campaigns.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 4px;">Nome da Campanha *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex: Promoção iPhone 16"
                           style="width: 100%; padding: 0.5rem; background: #141414; border: 1px solid #333; border-radius: 0.375rem; color: #e3e3e3; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 4px;">Mensagem *</label>
                    <textarea name="message" required rows="5" placeholder="Sua mensagem para os clientes..."
                              style="width: 100%; padding: 0.5rem; background: #141414; border: 1px solid #333; border-radius: 0.375rem; color: #e3e3e3; font-size: 0.875rem; resize: vertical;">{{ old('message') }}</textarea>
                    <p style="font-size: 0.65rem; color: #666; margin-top: 2px;">Máximo 4096 caracteres</p>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 4px;">URL da Mídia (opcional)</label>
                    <input type="url" name="media_url" value="{{ old('media_url') }}" placeholder="https://..."
                           style="width: 100%; padding: 0.5rem; background: #141414; border: 1px solid #333; border-radius: 0.375rem; color: #e3e3e3; font-size: 0.875rem;">
                    <p style="font-size: 0.65rem; color: #666; margin-top: 2px;">Imagem que será enviada junto com a mensagem</p>
                </div>

                <div>
                    <label style="font-size: 0.75rem; font-weight: 600; color: #818181; display: block; margin-bottom: 4px;">Tags de Destinatários *</label>
                    <p style="font-size: 0.65rem; color: #666; margin-bottom: 8px;">Selecione as tags dos clientes que receberão a campanha</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($tags as $tag)
                            <label style="display: flex; align-items: center; gap: 0.375rem; padding: 0.375rem 0.625rem; background: #141414; border: 1px solid #333; border-radius: 0.5rem; cursor: pointer; font-size: 0.8rem; color: #e3e3e3; transition: border-color 0.2s;"
                                   onmouseover="this.style.borderColor='{{ $tag->color }}'" onmouseout="this.style.borderColor=this.querySelector('input').checked?'{{ $tag->color }}':'#333'">
                                <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tag_ids', [])) ? 'checked' : '' }}
                                       onchange="this.closest('label').style.borderColor=this.checked?'{{ $tag->color }}':'#333'"
                                       style="accent-color: {{ $tag->color }};">
                                <span style="width: 8px; height: 8px; border-radius: 50%; background: {{ $tag->color }}; flex-shrink: 0;"></span>
                                {{ $tag->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('tag_ids')
                        <p style="font-size: 0.7rem; color: #fca5a5; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                @if($errors->any())
                    <div style="padding: 0.75rem; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); border-radius: 0.5rem; color: #ef4444; font-size: 0.75rem;">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                    <a href="{{ route('campaigns.index') }}"
                       style="padding: 0.5rem 1rem; font-size: 0.8rem; border: 1px solid #333; border-radius: 0.375rem; background: #141414; color: #818181; text-decoration: none;">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="padding: 0.5rem 1rem; font-size: 0.8rem; background: #25D366; color: #000; border-radius: 0.375rem; border: none; cursor: pointer; font-weight: 600;">
                        Criar Campanha
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
