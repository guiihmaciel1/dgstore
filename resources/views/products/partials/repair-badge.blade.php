{{-- Badge "Na assistência": recebe $repair (ProductRepair aberto) --}}
@php
    $repairDays = $repair->days_out;
    $repairLate = $repairDays >= \App\Domain\Product\Services\ProductRepairService::LATE_AFTER_DAYS;
@endphp
<span title="{{ $repair->provider ? 'Assistência: ' . $repair->provider : 'Na assistência' }}{{ $repair->reason ? ' — ' . $repair->reason : '' }}"
      style="display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 0.25rem; padding: 0.25rem 0.625rem; background: {{ $repairLate ? 'rgba(239,68,68,0.12)' : 'rgba(249,115,22,0.12)' }}; color: {{ $repairLate ? '#f87171' : '#fb923c' }}; font-size: 0.7rem; font-weight: 600; border-radius: 9999px; white-space: nowrap;">
    <svg style="width: 0.75rem; height: 0.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085"/></svg>
    Assistência · {{ $repairDays }}d
</span>
