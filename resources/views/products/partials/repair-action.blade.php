{{-- Botão de ação no grid: enviar para / retornar da assistência --}}
@if($product->openRepair)
    <button type="button"
            onclick="openRepairReturnModal(@js($product->id), @js($product->full_name))"
            style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.625rem; background: rgba(16,185,129,0.1); color: #34d399; border: 1px solid rgba(16,185,129,0.3); border-radius: 0.375rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; white-space: nowrap;"
            title="Aparelho voltou da assistência">
        Disponível
    </button>
@elseif($product->canGoToRepair() && $product->stock_quantity > 0)
    <button type="button"
            onclick="openRepairSendModal(@js($product->id), @js($product->full_name))"
            style="color: #818181; background: none; border: none; cursor: pointer; padding: 0;"
            onmouseover="this.style.color='#fb923c'" onmouseout="this.style.color='#818181'"
            title="Enviar para assistência">
        <svg style="width: 1rem; height: 1rem; display: inline;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085"/></svg>
    </button>
@endif
