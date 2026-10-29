{{-- Modais de assistência: enviar e marcar como disponível --}}
@php
    $repairOverlay = 'display: none; position: fixed; inset: 0; z-index: 60; align-items: center; justify-content: center; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); padding: 1rem;';
    $repairCard = 'background: #141414; border-radius: 1rem; box-shadow: 0 25px 50px rgba(0,0,0,0.5); width: 100%; max-width: 26rem; border: 1px solid rgba(255,255,255,0.08);';
    $repairLabel = 'display: block; font-size: 0.75rem; font-weight: 500; color: #818181; margin-bottom: 0.25rem;';
    $repairInput = 'width: 100%; padding: 0.5rem 0.75rem; background: #1a1a1a; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; color: #e3e3e3; font-size: 0.875rem;';
    $repairBtnGhost = 'padding: 0.5rem 1rem; background: transparent; color: #a4a4a4; border: 1px solid rgba(255,255,255,0.1); border-radius: 0.5rem; font-size: 0.875rem; cursor: pointer;';
@endphp

@if($errors->hasAny(['provider', 'reason', 'cost', 'notes']))
    <div style="position: fixed; top: 1rem; right: 1rem; z-index: 70; max-width: 24rem; padding: 0.75rem 1rem; background: #1a1a1a; border: 1px solid rgba(239,68,68,0.4); border-radius: 0.5rem; color: #fca5a5; font-size: 0.85rem;"
         onclick="this.remove()">
        {{ $errors->first('cost') ?: $errors->first('provider') ?: $errors->first('reason') ?: $errors->first('notes') }}
    </div>
@endif

<div id="repair-send-modal" style="{{ $repairOverlay }}" onclick="if(event.target===this) closeRepairModals()">
    <form method="POST" data-action-template="{{ route('products.repair.send', '__ID__') }}" style="{{ $repairCard }}">
        @csrf
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <h3 style="font-size: 1.05rem; font-weight: 600; color: #e3e3e3;">Enviar para assistência</h3>
            <p data-repair-name style="font-size: 0.8rem; color: #818181; margin-top: 0.125rem;"></p>
        </div>
        <div style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 0.875rem;">
            <div>
                <label style="{{ $repairLabel }}">Assistência (opcional)</label>
                <input type="text" name="provider" maxlength="255" placeholder="Ex: Assistência do João" style="{{ $repairInput }}">
            </div>
            <div>
                <label style="{{ $repairLabel }}">Motivo (opcional)</label>
                <input type="text" name="reason" maxlength="500" placeholder="Ex: Revisão geral, troca de bateria" style="{{ $repairInput }}">
            </div>
            <p style="font-size: 0.75rem; color: #666;">Enquanto estiver na assistência, o aparelho fica bloqueado para venda, marketing e simulador.</p>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: flex-end; gap: 0.5rem;">
            <button type="button" onclick="closeRepairModals()" style="{{ $repairBtnGhost }}">Cancelar</button>
            <button type="submit" style="padding: 0.5rem 1rem; background: #ea580c; color: #fff; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">Enviar</button>
        </div>
    </form>
</div>

<div id="repair-return-modal" style="{{ $repairOverlay }}" onclick="if(event.target===this) closeRepairModals()">
    <form method="POST" data-action-template="{{ route('products.repair.return', '__ID__') }}" style="{{ $repairCard }}">
        @csrf
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.06);">
            <h3 style="font-size: 1.05rem; font-weight: 600; color: #e3e3e3;">Voltou da assistência</h3>
            <p data-repair-name style="font-size: 0.8rem; color: #818181; margin-top: 0.125rem;"></p>
        </div>
        <div style="padding: 1.25rem 1.5rem; display: flex; flex-direction: column; gap: 0.875rem;">
            <div>
                <label style="{{ $repairLabel }}">Valor da assistência (R$)</label>
                <input type="number" name="cost" min="0" step="0.01" required placeholder="0,00" style="{{ $repairInput }}">
                <p style="font-size: 0.7rem; color: #666; margin-top: 0.25rem;">Somado ao custo do aparelho para calcular lucro e comissão. Informe 0 se não houve custo.</p>
            </div>
            <div>
                <label style="{{ $repairLabel }}">Observação (opcional)</label>
                <input type="text" name="notes" maxlength="1000" placeholder="Ex: Trocada a bateria" style="{{ $repairInput }}">
            </div>
        </div>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: flex-end; gap: 0.5rem;">
            <button type="button" onclick="closeRepairModals()" style="{{ $repairBtnGhost }}">Cancelar</button>
            <button type="submit" style="padding: 0.5rem 1rem; background: #059669; color: #fff; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">Marcar disponível</button>
        </div>
    </form>
</div>

<script>
    function openRepairModal(modalId, productId, productName) {
        const modal = document.getElementById(modalId);
        const form = modal.querySelector('form');
        form.reset();
        form.action = form.dataset.actionTemplate.replace('__ID__', productId);
        modal.querySelector('[data-repair-name]').textContent = productName;
        modal.style.display = 'flex';
        form.querySelector('input:not([type=hidden])')?.focus();
    }

    function openRepairSendModal(productId, productName) {
        openRepairModal('repair-send-modal', productId, productName);
    }

    function openRepairReturnModal(productId, productName) {
        openRepairModal('repair-return-modal', productId, productName);
    }

    function closeRepairModals() {
        document.getElementById('repair-send-modal').style.display = 'none';
        document.getElementById('repair-return-modal').style.display = 'none';
    }

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeRepairModals(); });
</script>
