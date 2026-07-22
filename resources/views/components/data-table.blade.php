@props([
    'headers' => [],
    'empty' => 'Nenhum registro encontrado.',
])

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-border">
        <thead class="bg-surface-overlay">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-dg-400 uppercase tracking-wider">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-surface-raised divide-y divide-border">
            {{ $slot }}
        </tbody>
    </table>
    
    @if($slot->isEmpty())
        <div class="text-center py-8 text-dg-500">
            {{ $empty }}
        </div>
    @endif
</div>
