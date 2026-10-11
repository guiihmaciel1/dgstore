<x-app-layout>
    <x-slot name="title">{{ $campaign->name }}</x-slot>

    <div style="max-width: 64rem; margin: 0 auto; padding: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <a href="{{ route('campaigns.index') }}" style="color: #666; text-decoration: none;">← Campanhas</a>
        </div>

        @if(session('success'))
            <div style="padding: 0.75rem; background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); border-radius: 0.5rem; color: #22c55e; font-size: 0.8rem; margin-bottom: 1rem;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
            {{-- Info da Campanha --}}
            <div style="background: #141414; border: 1px solid #222; border-radius: 0.75rem; padding: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <h2 style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">{{ $campaign->name }}</h2>
                    @php
                        $statusColors = ['draft' => '#6b7280', 'sending' => '#f59e0b', 'completed' => '#22c55e'];
                        $statusLabels = ['draft' => 'Rascunho', 'sending' => 'Enviando', 'completed' => 'Concluída'];
                    @endphp
                    <span style="font-size: 0.65rem; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: {{ $statusColors[$campaign->status] ?? '#6b7280' }}20; color: {{ $statusColors[$campaign->status] ?? '#6b7280' }};">
                        {{ $statusLabels[$campaign->status] ?? $campaign->status }}
                    </span>
                </div>

                <div style="background: #0d0d0d; border: 1px solid #1a1a1a; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                    <p style="font-size: 0.8rem; color: #c4c4c4; white-space: pre-wrap; line-height: 1.5;">{{ $campaign->message }}</p>
                </div>

                @if($campaign->tags->isNotEmpty())
                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                        @foreach($campaign->tags as $tag)
                            <span style="font-size: 0.6rem; font-weight: 600; padding: 2px 6px; border-radius: 4px; background: {{ $tag->color }}20; color: {{ $tag->color }};">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <div style="font-size: 0.7rem; color: #666;">
                    Criada por {{ $campaign->creator->name }} em {{ $campaign->created_at->format('d/m/Y H:i') }}
                </div>
            </div>

            {{-- Stats --}}
            <div style="background: #141414; border: 1px solid #222; border-radius: 0.75rem; padding: 1rem;">
                <h3 style="font-size: 0.8rem; font-weight: 700; color: #818181; margin-bottom: 0.75rem;">PROGRESSO</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem;">
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $stats['pending'] }}</div>
                        <div style="font-size: 0.65rem; color: #666;">Pendentes</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #22c55e;">{{ $stats['sent'] }}</div>
                        <div style="font-size: 0.65rem; color: #666;">Enviados</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #ef4444;">{{ $stats['failed'] }}</div>
                        <div style="font-size: 0.65rem; color: #666;">Falhas</div>
                    </div>
                </div>

                @if($campaign->total_recipients > 0)
                    @php $pct = round(($stats['sent'] / $campaign->total_recipients) * 100); @endphp
                    <div style="background: #222; border-radius: 999px; height: 8px; overflow: hidden; margin-bottom: 0.75rem;">
                        <div style="width: {{ $pct }}%; height: 100%; background: #25D366; border-radius: 999px; transition: width 0.3s;"></div>
                    </div>
                    <div style="font-size: 0.7rem; color: #666; text-align: center;">{{ $pct }}% concluído</div>
                @endif

                @if($campaign->status !== 'completed')
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                        <button onclick="startSending(false)" id="send-campaign-btn"
                                style="flex: 1; padding: 0.625rem; background: #25D366; color: #000; font-weight: 700; font-size: 0.8rem; border: none; border-radius: 8px; cursor: pointer;">
                            {{ $campaign->status === 'sending' ? '▶ Continuar' : '🚀 Enviar' }}
                        </button>
                        <button onclick="startSending(true)" id="queue-campaign-btn"
                                style="padding: 0.625rem 0.75rem; background: #1a1a1a; color: #e3e3e3; font-weight: 600; font-size: 0.75rem; border: 1px solid #333; border-radius: 8px; cursor: pointer;" title="Enviar via fila (background)">
                            📥 Fila
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Lista de Destinatários --}}
        <div style="background: #141414; border: 1px solid #222; border-radius: 0.75rem; padding: 1rem;">
            <h3 style="font-size: 0.8rem; font-weight: 700; color: #818181; margin-bottom: 0.75rem;">DESTINATÁRIOS ({{ $campaign->total_recipients }})</h3>
            <div style="max-height: 400px; overflow-y: auto;">
                <table style="width: 100%; font-size: 0.8rem;">
                    <thead>
                        <tr style="color: #666; border-bottom: 1px solid #222;">
                            <th style="padding: 0.375rem; text-align: left;">Cliente</th>
                            <th style="padding: 0.375rem; text-align: left;">Telefone</th>
                            <th style="padding: 0.375rem; text-align: center;">Status</th>
                            <th style="padding: 0.375rem; text-align: right;">Enviado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->recipients as $recipient)
                            <tr style="border-bottom: 1px solid #1a1a1a;">
                                <td style="padding: 0.375rem; color: #e3e3e3;">{{ $recipient->customer?->name ?? '-' }}</td>
                                <td style="padding: 0.375rem; color: #818181;">{{ $recipient->phone }}</td>
                                <td style="padding: 0.375rem; text-align: center;">
                                    @php
                                        $rc = ['pending' => '#6b7280', 'sent' => '#22c55e', 'failed' => '#ef4444'];
                                        $rl = ['pending' => '⏳', 'sent' => '✓', 'failed' => '✗'];
                                    @endphp
                                    <span style="color: {{ $rc[$recipient->status] ?? '#666' }}; font-weight: 700;">
                                        {{ $rl[$recipient->status] ?? $recipient->status }}
                                    </span>
                                </td>
                                <td style="padding: 0.375rem; text-align: right; color: #666;">
                                    {{ $recipient->sent_at?->format('H:i:s') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    async function startSending(useQueue) {
        const btn = document.getElementById('send-campaign-btn');
        const queueBtn = document.getElementById('queue-campaign-btn');
        btn.disabled = true;
        if (queueBtn) queueBtn.disabled = true;
        btn.textContent = '⏳ Enviando...';
        btn.style.opacity = '0.6';

        try {
            const res = await fetch('{{ route("campaigns.send", $campaign) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ queue: useQueue })
            });
            const data = await res.json();

            if (data.queued) {
                btn.textContent = '📥 Na fila!';
                btn.style.background = '#3b82f6';
                setTimeout(() => window.location.reload(), 2000);
                return;
            }

            if (data.remaining > 0) {
                btn.textContent = `▶ Continuar (${data.remaining} restantes)`;
                btn.disabled = false;
                if (queueBtn) queueBtn.disabled = false;
                btn.style.opacity = '1';
            } else {
                btn.textContent = '✓ Concluída!';
                btn.style.background = '#22c55e';
            }

            setTimeout(() => window.location.reload(), 1500);
        } catch(e) {
            btn.textContent = 'Erro - Tentar novamente';
            btn.disabled = false;
            if (queueBtn) queueBtn.disabled = false;
            btn.style.opacity = '1';
            btn.style.background = '#ef4444';
        }
    }
    </script>
</x-app-layout>
