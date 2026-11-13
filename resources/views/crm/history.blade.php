<x-app-layout>
    <x-slot name="title">CRM - Histórico</x-slot>
    <div class="py-6">
        <div class="px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="mb-4 text-[0.8rem] text-dg-400">
                <a class="text-blue-500 no-underline" href="{{ route('crm.board') }}">Pipeline</a>
                <span class="my-0 mx-1.5">/</span>
                <span>Histórico</span>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                <h1 class="text-2xl font-bold text-dg-100">Histórico de Negócios</h1>

                <div class="flex gap-2 items-center">
                    <select class="py-[0.4rem] px-3 border border-border rounded-lg text-[0.8rem] bg-surface-raised" onchange="window.location.href='{{ route('crm.history') }}?tab={{ $tab }}&user_id=' + this.value">
                        <option value="">Todos</option>
                        @foreach($sellers as $seller)
                            <option value="{{ $seller->id }}" {{ $filterUserId === $seller->id ? 'selected' : '' }}>
                                {{ $seller->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="flex gap-1.5 mb-4">
                <a class="py-1.5 px-3.5 text-[0.8rem] font-medium rounded-full" href="{{ route('crm.history', ['tab' => 'won', 'user_id' => $filterUserId]) }}"
                   style="border: 1px solid {{ $tab === 'won' ? 'transparent' : 'rgba(255,255,255,0.08)' }}; background: {{ $tab === 'won' ? '#059669' : '#141414' }}; color: {{ $tab === 'won' ? 'white' : '#6b7280' }}; text-decoration: none;">
                    Ganhos
                </a>
                <a class="py-1.5 px-3.5 text-[0.8rem] font-medium rounded-full" href="{{ route('crm.history', ['tab' => 'lost', 'user_id' => $filterUserId]) }}"
                   style="border: 1px solid {{ $tab === 'lost' ? 'transparent' : 'rgba(255,255,255,0.08)' }}; background: {{ $tab === 'lost' ? '#dc2626' : '#141414' }}; color: {{ $tab === 'lost' ? 'white' : '#6b7280' }}; text-decoration: none;">
                    Perdidos
                </a>
            </div>

            {{-- Lista --}}
            <div class="flex flex-col gap-2">
                @forelse($deals as $deal)
                    <a class="block bg-surface-raised border border-border rounded-xl py-3.5 px-5 no-underline" href="{{ route('crm.show', $deal) }}" style="transition: box-shadow 0.15s;"
                       onmouseover="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)'" onmouseout="this.style.boxShadow='none'">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($deal->product_interest)
                                        <span class="text-[0.65rem] font-semibold text-dg-300 bg-surface-elevated py-[2px] px-[6px] rounded-[4px]">
                                            {{ $deal->product_interest }}
                                        </span>
                                    @endif
                                    <span class="text-[0.65rem] font-semibold py-[2px] px-[8px] rounded-full" style="{{ $tab === 'won' ? 'background: rgba(16,185,129,0.15); color: #6ee7b7;' : 'background: rgba(239,68,68,0.1); color: #fca5a5;' }}">
                                        {{ $tab === 'won' ? 'GANHO' : 'PERDIDO' }}
                                    </span>
                                </div>
                                <div class="text-sm font-semibold text-dg-100 mt-1">{{ $deal->title }}</div>
                                <div class="text-xs text-dg-500 mt-1 flex gap-3">
                                    @if($deal->customer)
                                        <span>{{ $deal->customer->name }}</span>
                                    @endif
                                    <span>{{ $tab === 'won' ? $deal->won_at->format('d/m/Y') : $deal->lost_at->format('d/m/Y') }}</span>
                                    @if($deal->user)
                                        <span>{{ $deal->user->name }}</span>
                                    @endif
                                    @if($deal->lost_reason)
                                        <span class="text-red-300">{{ $deal->lost_reason }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($deal->value)
                                <span class="text-[0.9rem] font-bold" style="color: {{ $tab === 'won' ? '#059669' : '#dc2626' }}; white-space: nowrap; margin-left: 1rem;">
                                    R$ {{ number_format((float)$deal->value, 2, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="text-center p-10 text-dg-500 text-sm">
                        Nenhum negócio {{ $tab === 'won' ? 'ganho' : 'perdido' }} encontrado.
                    </div>
                @endforelse
            </div>

            @if($deals->hasPages())
                <div class="mt-4">
                    {{ $deals->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
