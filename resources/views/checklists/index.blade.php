<x-app-layout>
    <x-slot name="title">Checklists</x-slot>
    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4">
                    <x-alert type="success">{{ session('success') }}</x-alert>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4">
                    <x-alert type="danger">{{ session('error') }}</x-alert>
                </div>
            @endif

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Checklists Salvos</h1>
                    <p style="font-size: 0.875rem; color: #6b7280;">Historico de avaliacoes tecnicas de seminovos</p>
                </div>
                <a href="{{ route('tools.checklist') }}"
                   style="padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: white; background: #4f46e5; border-radius: 0.5rem; text-decoration: none;"
                   onmouseover="this.style.background='#4338ca'" onmouseout="this.style.background='#4f46e5'">
                    + Novo Checklist
                </a>
            </div>

            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; padding: 1rem; margin-bottom: 1rem;">
                <form method="GET" x-data x-ref="filterForm" style="display: flex; gap: 0.75rem; align-items: flex-end;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 500; color: #6b7280; margin-bottom: 0.25rem;">Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome do checklist..."
                               style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem; font-size: 0.875rem;"
                               x-on:input.debounce.400ms="$refs.filterForm.submit()">
                    </div>
                    @if(request('search'))
                        <a href="{{ route('checklists.index') }}" style="padding: 0.5rem 1rem; color: #6b7280; font-size: 0.875rem; text-decoration: none;">Limpar</a>
                    @endif
                </form>
            </div>

            <div style="background: white; border-radius: 0.75rem; border: 1px solid #e5e7eb; overflow: hidden;">
                @if($checklists->isEmpty())
                    <div style="padding: 3rem; text-align: center;">
                        <svg style="margin: 0 auto; width: 3rem; height: 3rem; color: #d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <p style="margin-top: 1rem; color: #6b7280;">Nenhum checklist salvo ainda.</p>
                        <a href="{{ route('tools.checklist') }}" style="display: inline-block; margin-top: 0.75rem; padding: 0.5rem 1rem; font-size: 0.8rem; font-weight: 600; color: white; background: #4f46e5; border-radius: 0.5rem; text-decoration: none;">
                            Criar primeiro checklist
                        </a>
                    </div>
                @else
                    {{-- Mobile cards --}}
                    <div class="block sm:hidden">
                        @foreach($checklists as $checklist)
                            <div style="padding: 1rem; border-bottom: 1px solid #f3f4f6;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <div style="flex: 1; min-width: 0;">
                                        <a href="{{ route('checklists.show', $checklist) }}" style="font-weight: 600; color: #111827; text-decoration: none; font-size: 0.875rem;">
                                            {{ $checklist->name }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">{{ $checklist->device_name }}</div>
                                    </div>
                                    @php
                                        $badgeBg = match($checklist->status) { 'approved' => '#dcfce7', 'failed' => '#fef2f2', default => '#fefce8' };
                                        $badgeColor = match($checklist->status) { 'approved' => '#16a34a', 'failed' => '#dc2626', default => '#d97706' };
                                    @endphp
                                    <span style="padding: 0.125rem 0.5rem; font-size: 0.7rem; font-weight: 600; border-radius: 9999px; background: {{ $badgeBg }}; color: {{ $badgeColor }}; flex-shrink: 0;">
                                        {{ $checklist->status_label }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div style="font-size: 0.75rem; color: #6b7280;">
                                        {{ $checklist->summary_label }} &middot; {{ $checklist->created_at->format('d/m/Y H:i') }}
                                    </div>
                                    <div style="display: flex; gap: 0.375rem;">
                                        <a href="{{ route('checklists.show', $checklist) }}" style="padding: 0.25rem 0.5rem; background: #f3f4f6; color: #374151; font-size: 0.7rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;">Ver</a>
                                        @unless($checklist->isLinked())
                                            <form method="POST" action="{{ route('checklists.destroy', $checklist) }}" onsubmit="return confirm('Excluir este checklist?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" style="padding: 0.25rem 0.5rem; background: #fef2f2; color: #dc2626; font-size: 0.7rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;">Excluir</button>
                                            </form>
                                        @endunless
                                    </div>
                                </div>
                                @if($checklist->product || $checklist->tradeIn)
                                    <div style="margin-top: 0.375rem; font-size: 0.7rem; color: #4f46e5;">
                                        Vinculado: {{ $checklist->product ? 'Produto #' . Str::limit($checklist->product->name, 30) : 'Trade-in #' . Str::limit($checklist->tradeIn->device_name, 30) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop table --}}
                    <div class="hidden sm:block" style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Nome</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Status</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Resultado</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Vinculo</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Criado por</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Data</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">Acoes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($checklists as $checklist)
                                    <tr style="border-bottom: 1px solid #f3f4f6;">
                                        <td style="padding: 0.75rem 1rem;">
                                            <a href="{{ route('checklists.show', $checklist) }}" style="font-weight: 500; color: #111827; text-decoration: none;">{{ $checklist->name }}</a>
                                            <div style="font-size: 0.75rem; color: #9ca3af;">{{ $checklist->device_name !== $checklist->name ? $checklist->device_name : '' }}</div>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center;">
                                            @php
                                                $badgeBg = match($checklist->status) { 'approved' => '#dcfce7', 'failed' => '#fef2f2', default => '#fefce8' };
                                                $badgeColor = match($checklist->status) { 'approved' => '#16a34a', 'failed' => '#dc2626', default => '#d97706' };
                                            @endphp
                                            <span style="padding: 0.125rem 0.5rem; font-size: 0.7rem; font-weight: 600; border-radius: 9999px; background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                                {{ $checklist->status_label }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.875rem; color: #374151;">
                                            {{ $checklist->summary_label }}
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.75rem;">
                                            @if($checklist->product)
                                                <span style="color: #4f46e5;">{{ Str::limit($checklist->product->name, 25) }}</span>
                                            @elseif($checklist->tradeIn)
                                                <span style="color: #4f46e5;">Trade-in: {{ Str::limit($checklist->tradeIn->device_name, 25) }}</span>
                                            @else
                                                <span style="color: #9ca3af;">-</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.8rem; color: #6b7280;">
                                            {{ $checklist->user?->name ?? '-' }}
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: center; font-size: 0.8rem; color: #6b7280;">
                                            {{ $checklist->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <div style="display: flex; justify-content: flex-end; gap: 0.375rem;">
                                                <a href="{{ route('checklists.show', $checklist) }}"
                                                   style="padding: 0.375rem 0.75rem; background: #f3f4f6; color: #374151; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; text-decoration: none;"
                                                   onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                                                    Ver
                                                </a>
                                                @unless($checklist->isLinked())
                                                    <form method="POST" action="{{ route('checklists.destroy', $checklist) }}" onsubmit="return confirm('Excluir este checklist?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                style="padding: 0.375rem 0.75rem; background: #fef2f2; color: #dc2626; font-size: 0.75rem; font-weight: 500; border-radius: 0.375rem; border: none; cursor: pointer;"
                                                                onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                                            Excluir
                                                        </button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($checklists->hasPages())
                        <div style="padding: 1rem; border-top: 1px solid #e5e7eb;">
                            {{ $checklists->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
