@php
    $dollarRate = \App\Domain\System\Models\SystemSetting::get('dollar_rate');
    $isAdmin = auth()->user()->isAdminGeral();
@endphp

{{-- Import Calculator trigger --}}
@if($isAdmin && $dollarRate)
<button @click="$dispatch('open-import-calc')" class="flex items-center justify-center w-8 h-8 rounded-lg text-dg-300 hover:text-blue-400 hover:bg-blue-500/10 transition" title="Calculadora de Importação">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
</button>
@endif

{{-- Stone Calculator trigger --}}
<button @click="$dispatch('open-stone-calc')" class="flex items-center justify-center w-8 h-8 rounded-lg text-dg-300 hover:text-purple-400 hover:bg-purple-500/10 transition" title="Calculadora Stone">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25v-.008zm0 2.25h.008v.008H8.25v-.008zm2.25-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm2.25-2.25h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zM6.75 19.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm4.5-15v3.75"/>
    </svg>
</button>
