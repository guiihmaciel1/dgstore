<x-app-layout>
    <x-slot name="title">Campanhas WhatsApp</x-slot>

    <div style="max-width: 64rem; margin: 0 auto; padding: 1.5rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <h1 style="font-size: 1.25rem; font-weight: 700; color: #e3e3e3;">📢 Campanhas WhatsApp</h1>
            <a href="{{ route('campaigns.create') }}"
               style="padding: 0.5rem 1rem; background: #25D366; color: #000; font-weight: 600; font-size: 0.8rem; border-radius: 0.5rem; text-decoration: none;">
                + Nova Campanha
            </a>
        </div>

        @if(session('success'))
            <div style="padding: 0.75rem; background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); border-radius: 0.5rem; color: #22c55e; font-size: 0.8rem; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @forelse($campaigns as $campaign)
                <a href="{{ route('campaigns.show', $campaign) }}" style="text-decoration: none; color: inherit;">
                    <div style="background: #141414; border: 1px solid #222; border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 1rem; transition: border-color 0.2s;"
                         onmouseover="this.style.borderColor='#444'" onmouseout="this.style.borderColor='#222'">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem;">
                                <span style="font-weight: 600; font-size: 0.9rem; color: #e3e3e3;">{{ $campaign->name }}</span>
                                @php
                                    $statusColors = ['draft' => '#6b7280', 'sending' => '#f59e0b', 'completed' => '#22c55e', 'scheduled' => '#3b82f6'];
                                    $statusLabels = ['draft' => 'Rascunho', 'sending' => 'Enviando', 'completed' => 'Concluída', 'scheduled' => 'Agendada'];
                                @endphp
                                <span style="font-size: 0.6rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; background: {{ $statusColors[$campaign->status] ?? '#6b7280' }}20; color: {{ $statusColors[$campaign->status] ?? '#6b7280' }};">
                                    {{ $statusLabels[$campaign->status] ?? $campaign->status }}
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: #818181; display: flex; gap: 1rem;">
                                <span>📩 {{ $campaign->sent_count }}/{{ $campaign->total_recipients }}</span>
                                @if($campaign->failed_count > 0)
                                    <span style="color: #ef4444;">❌ {{ $campaign->failed_count }} falhas</span>
                                @endif
                                <span>{{ $campaign->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($campaign->tags->isNotEmpty())
                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-top: 0.375rem;">
                                    @foreach($campaign->tags as $tag)
                                        <span style="font-size: 0.6rem; font-weight: 600; padding: 1px 5px; border-radius: 4px; background: {{ $tag->color }}20; color: {{ $tag->color }};">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <svg style="width: 16px; height: 16px; color: #666;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @empty
                <div style="text-align: center; padding: 3rem; color: #666;">
                    <p style="font-size: 0.9rem;">Nenhuma campanha criada ainda.</p>
                    <p style="font-size: 0.75rem; margin-top: 0.5rem;">Crie sua primeira campanha para alcançar seus clientes.</p>
                </div>
            @endforelse
        </div>

        <div style="margin-top: 1rem;">
            {{ $campaigns->links() }}
        </div>
    </div>
</x-app-layout>
