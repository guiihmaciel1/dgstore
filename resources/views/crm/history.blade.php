<x-app-layout>
    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div style="margin-bottom: 1rem; font-size: 0.8rem; color: #6b7280;">
                <a href="{{ route('crm.board') }}" style="color: #3b82f6; text-decoration: none;">Pipeline</a>
                <span style="margin: 0 0.375rem;">/</span>
                <span>Histórico</span>
            </div>

            {{-- Header --}}
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Histórico de Negócios</h1>

                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    @if($isAdmin)
                        <select onchange="window.location.href='{{ route('crm.history') }}?tab={{ $tab }}&user_id=' + this.value"
                                style="padding: 0.4rem 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.5rem; font-size: 0.8rem; background: white;">
                            <option value="">Todos</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ $filterUserId === $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            {{-- Tabs --}}
            <div style="display: flex; gap: 0.375rem; margin-bottom: 1rem;">
                <a href="{{ route('crm.history', ['tab' => 'won', 'user_id' => $filterUserId]) }}"
                   style="padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 500; border-radius: 9999px; border: 1px solid {{ $tab === 'won' ? 'transparent' : '#e5e7eb' }}; background: {{ $tab === 'won' ? '#059669' : 'white' }}; color: {{ $tab === 'won' ? 'white' : '#6b7280' }}; text-decoration: none;">
                    Ganhos
                </a>
                <a href="{{ route('crm.history', ['tab' => 'lost', 'user_id' => $filterUserId]) }}"
                   style="padding: 0.375rem 0.875rem; font-size: 0.8rem; font-weight: 500; border-radius: 9999px; border: 1px solid {{ $tab === 'lost' ? 'transparent' : '#e5e7eb' }}; background: {{ $tab === 'lost' ? '#dc2626' : 'white' }}; color: {{ $tab === 'lost' ? 'white' : '#6b7280' }}; text-decoration: none;">
                    Perdidos
                </a>
            </div>

            {{-- Lista --}}
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                @forelse($deals as $deal)
                    <a href="{{ route('crm.show', $deal) }}" style="display: block; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 0.875rem 1.25rem; text-decoration: none; transition: box-shadow 0.15s;"
                       onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)'" onmouseout="this.style.boxShadow='none'">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                    @if($deal->product_interest)
                                        <span style="font-size: 0.65rem; font-weight: 600; color: #374151; background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">
                                            {{ $deal->product_interest }}
                                        </span>
                                    @endif
                                    <span style="font-size: 0.65rem; font-weight: 600; padding: 2px 8px; border-radius: 9999px; {{ $tab === 'won' ? 'background: #dcfce7; color: #166534;' : 'background: #fef2f2; color: #991b1b;' }}">
                                        {{ $tab === 'won' ? 'GANHO' : 'PERDIDO' }}
                                    </span>
                                </div>
                                <div style="font-size: 0.875rem; font-weight: 600; color: #111827; margin-top: 0.25rem;">{{ $deal->title }}</div>
                                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem; display: flex; gap: 0.75rem;">
                                    @if($deal->customer)
                                        <span>{{ $deal->customer->name }}</span>
                                    @endif
                                    <span>{{ $tab === 'won' ? $deal->won_at->format('d/m/Y') : $deal->lost_at->format('d/m/Y') }}</span>
                                    @if($isAdmin && $deal->user)
                                        <span>{{ $deal->user->name }}</span>
                                    @endif
                                    @if($deal->lost_reason)
                                        <span style="color: #dc2626;">{{ $deal->lost_reason }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($deal->value)
                                <span style="font-size: 0.9rem; font-weight: 700; color: {{ $tab === 'won' ? '#059669' : '#dc2626' }}; white-space: nowrap; margin-left: 1rem;">
                                    R$ {{ number_format((float)$deal->value, 2, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div style="text-align: center; padding: 2.5rem; color: #9ca3af; font-size: 0.875rem;">
                        Nenhum negócio {{ $tab === 'won' ? 'ganho' : 'perdido' }} encontrado.
                    </div>
                @endforelse
            </div>

            @if($deals->hasPages())
                <div style="margin-top: 1rem;">
                    {{ $deals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
