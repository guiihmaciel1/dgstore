{{--
    Conferência de caixa (banco + espécie). Abre sozinha em horário comercial quando
    a última conferência passou do intervalo, inclusive com a home aberta.
    Espera: $cashStatus (DashboardController::buildCashStatus).
--}}
@php
    $latest = $cashStatus['latest'];
    $fmt = fn ($v) => $v === null ? '' : number_format((float) $v, 2, ',', '.');
    $hasErrors = $errors->cashCheck->any();
    $stockNew = $cashStatus['stock']['groups']['iphone_new']['cost'];
    $stockUsed = $cashStatus['stock']['groups']['iphone_used']['cost'];
    $inTransit = (float) ($cashStatus['in_transit'] ?? 0);
    $check = config('store.cash_check');
@endphp

<div x-data="cashCheck({
        dueNow: @js($cashStatus['is_due']),
        reopen: @js($hasErrors),
        nextCheckAt: @js($cashStatus['next_check_at']?->toIso8601String()),
        days: @js($check['days']),
        startHour: @js($check['start_hour']),
        endHour: @js($check['end_hour']),
        bank: @js(old('bank_amount', $fmt($cashStatus['bank']))),
        cash: @js(old('cash_amount', $fmt($cashStatus['cash']))),
        stock: @js($stockNew + $stockUsed + $inTransit),
     })"
     x-on:open-cash-check.window="openNow()"
     x-on:keydown.escape.window="if (open) snooze()">
    <div x-show="open" x-cloak @click.self="snooze()"
         class="fixed inset-0 z-[9998] flex items-end justify-center bg-black/70 p-0 backdrop-blur-[2px] sm:items-center sm:p-4"
         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <div role="dialog" aria-modal="true" aria-labelledby="cash-check-title"
             class="w-full max-w-md rounded-t-2xl border border-border-strong bg-surface-raised shadow-2xl shadow-black/60 sm:rounded-2xl">
            <form method="POST" action="{{ route('cash-snapshots.store') }}" @submit="saving = true" class="p-5 sm:p-6">
                @csrf
                <h2 id="cash-check-title" class="text-lg font-semibold text-dg-50">Conferir caixa</h2>
                <p class="mt-1 text-sm text-dg-400">
                    @if($latest)
                        Confira os valores. Se nada mudou, é só confirmar.
                    @else
                        Informe quanto a loja tem hoje no banco e em espécie.
                    @endif
                </p>

                <div class="mt-5 space-y-4">
                    <div>
                        <label for="cash-bank" class="text-sm font-medium text-dg-200">Banco</label>
                        <div class="mt-1.5 flex items-center rounded-lg border bg-surface-overlay focus-within:border-indigo-400 {{ $errors->cashCheck->has('bank_amount') ? 'border-red-400' : 'border-border-strong' }}">
                            <span class="pl-3 text-sm text-dg-400">R$</span>
                            <input id="cash-bank" name="bank_amount" x-ref="bank" x-model="bank" @blur="bank = format(bank)"
                                   type="text" inputmode="decimal" autocomplete="off" required placeholder="0,00"
                                   class="w-full border-0 !bg-transparent py-2.5 pl-2 pr-3 text-right text-base font-semibold tabular-nums text-dg-50 placeholder:text-dg-600 focus:ring-0">
                        </div>
                        @error('bank_amount', 'cashCheck')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="cash-cash" class="text-sm font-medium text-dg-200">Espécie</label>
                        <div class="mt-1.5 flex items-center rounded-lg border bg-surface-overlay focus-within:border-indigo-400 {{ $errors->cashCheck->has('cash_amount') ? 'border-red-400' : 'border-border-strong' }}">
                            <span class="pl-3 text-sm text-dg-400">R$</span>
                            <input id="cash-cash" name="cash_amount" x-model="cash" @blur="cash = format(cash)"
                                   type="text" inputmode="decimal" autocomplete="off" required placeholder="0,00"
                                   class="w-full border-0 !bg-transparent py-2.5 pl-2 pr-3 text-right text-base font-semibold tabular-nums text-dg-50 placeholder:text-dg-600 focus:ring-0">
                        </div>
                        @error('cash_amount', 'cashCheck')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                    </div>
                </div>

                <dl class="mt-5 space-y-1.5 rounded-lg bg-surface-overlay px-3.5 py-3 text-xs">
                    <div class="flex justify-between gap-3"><dt class="text-dg-400">iPhones novos (custo)</dt><dd class="tabular-nums text-dg-200">R$ {{ number_format($stockNew, 2, ',', '.') }}</dd></div>
                    <div class="flex justify-between gap-3"><dt class="text-dg-400">iPhones seminovos (custo)</dt><dd class="tabular-nums text-dg-200">R$ {{ number_format($stockUsed, 2, ',', '.') }}</dd></div>
                    @if($inTransit > 0)
                        <div class="flex justify-between gap-3"><dt class="text-dg-400">Compras em trânsito</dt><dd class="tabular-nums text-dg-200">R$ {{ number_format($inTransit, 2, ',', '.') }}</dd></div>
                    @endif
                    <div class="flex justify-between gap-3 border-t border-border pt-1.5 text-sm">
                        <dt class="font-medium text-dg-200">Caixa real</dt>
                        <dd class="font-bold tabular-nums text-dg-50" x-text="'R$ ' + money(total())"></dd>
                    </div>
                </dl>

                @if($latest)
                    <p class="mt-3 text-[0.6875rem] text-dg-400">
                        Última conferência: {{ $latest->created_at->isToday() ? 'hoje' : $latest->created_at->format('d/m') }} às {{ $latest->created_at->format('H:i') }}@if($latest->user) por {{ $latest->user->name }}@endif.
                    </p>
                @endif

                <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" @click="snooze()"
                            class="rounded-lg px-4 py-2.5 text-sm font-medium text-dg-300 transition-colors hover:bg-surface-elevated hover:text-dg-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-indigo-400">
                        Lembrar em 30 min
                    </button>
                    <button type="submit" :disabled="saving"
                            class="rounded-lg bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-300 disabled:cursor-wait disabled:opacity-60"
                            x-text="saving ? 'Salvando…' : 'Confirmar'">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cashCheck(cfg) {
        const SNOOZE_KEY = 'dg_cash_check_snooze_until';
        const parse = (v) => {
            v = String(v ?? '').trim().replace(/[^\d,.\-]/g, '');
            if (v === '') return 0;
            if (v.includes(',')) v = v.replace(/\./g, '').replace(',', '.');
            else if (/^-?\d{1,3}(\.\d{3})+$/.test(v)) v = v.replace(/\./g, '');
            const n = parseFloat(v);
            return isNaN(n) ? 0 : n;
        };
        const money = (n) => Number(n).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const snoozedUntil = () => { try { return Number(localStorage.getItem(SNOOZE_KEY) || 0); } catch (e) { return 0; } };

        return {
            open: false,
            saving: false,
            bank: cfg.bank,
            cash: cfg.cash,
            money,
            format(v) { return String(v ?? '').trim() === '' ? '' : money(parse(v)); },
            total() { return parse(this.bank) + parse(this.cash) + cfg.stock; },
            inBusinessHours(d = new Date()) {
                const isoDay = d.getDay() === 0 ? 7 : d.getDay();
                return cfg.days.includes(isoDay) && d.getHours() >= cfg.startHour && d.getHours() < cfg.endHour;
            },
            isDue() {
                if (!this.inBusinessHours()) return false;
                if (Date.now() < snoozedUntil()) return false;
                return cfg.nextCheckAt === null || Date.now() >= new Date(cfg.nextCheckAt).getTime();
            },
            openNow() {
                this.open = true;
                this.$nextTick(() => { this.$refs.bank?.focus(); this.$refs.bank?.select(); });
            },
            snooze() {
                if (this.saving) return;
                try { localStorage.setItem(SNOOZE_KEY, String(Date.now() + 30 * 60 * 1000)); } catch (e) {}
                this.open = false;
            },
            init() {
                if (cfg.reopen || (cfg.dueNow && Date.now() >= snoozedUntil())) this.openNow();
                // Home aberta por horas: confere de minuto em minuto
                setInterval(() => { if (!this.open && this.isDue()) this.openNow(); }, 60 * 1000);
            },
        };
    }
</script>
