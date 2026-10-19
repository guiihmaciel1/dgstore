{{-- Idle Monitor — detecção de inatividade para interns --}}
{{-- Popup estilo Netflix "Você ainda está aí?" --}}
@auth
@if(auth()->user()->isIntern() && Route::has('idle-monitor.dismiss'))

<div id="idle-overlay" style="
    display: none;
    position: fixed; inset: 0; z-index: 99999;
    background: rgba(0,0,0,0.92);
    backdrop-filter: blur(8px);
    justify-content: center; align-items: center;
">
    <div style="
        background: #1a1a1a;
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 1rem;
        padding: 2.5rem;
        max-width: 420px;
        width: 90%;
        text-align: center;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        animation: idlePopIn 0.3s ease-out;
    ">
        {{-- Animated pulsing icon --}}
        <div style="
            width: 80px; height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            animation: idlePulse 2s infinite;
        " id="idle-icon-container">
            <svg style="width: 40px; height: 40px; color: #e3e3e3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">
            Você ainda está aí?
        </h2>
        <p style="font-size: 0.9rem; color: #818181; margin-bottom: 0.25rem;">
            Detectamos que você está inativa há
        </p>
        <div id="idle-timer" style="
            font-size: 2rem; font-weight: 800; color: #ef4444;
            font-variant-numeric: tabular-nums;
            margin-bottom: 0.5rem;
        ">00:00</div>

        {{-- Cumulative warning (shown when >= 30min total) --}}
        <div id="idle-cumulative-warning" style="
            display: none;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 0.5rem;
            padding: 0.625rem 1rem;
            margin-bottom: 1rem;
        ">
            <p style="font-size: 0.75rem; color: #ef4444; font-weight: 600;">
                ⚠️ Acumulado hoje: <span id="idle-cumulative-display">0</span> min
            </p>
            <p style="font-size: 0.7rem; color: #a16207; margin-top: 0.25rem;">
                A gestão será notificada automaticamente.
            </p>
        </div>

        <button id="idle-dismiss-btn" onclick="dismissIdle()" style="
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            border: none;
            border-radius: 0.625rem;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 15px rgba(37,99,235,0.3);
        " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Estou aqui!
        </button>
    </div>
</div>

<style>
@keyframes idlePopIn {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
@keyframes idlePulse {
    0%, 100% { background: rgba(239,68,68,0.15); }
    50% { background: rgba(239,68,68,0.3); }
}
</style>

<script>
(function() {
    const IDLE_THRESHOLD_MS = 5 * 60 * 1000; // 5 minutes
    const TICK_INTERVAL_MS  = 1000;
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

    let lastActivity = Date.now();
    let idleStart = null;
    let isIdle = false;
    let tickTimer = null;
    let cumulativeToday = 0;

    const overlay   = document.getElementById('idle-overlay');
    const timerEl   = document.getElementById('idle-timer');
    const cumWarn   = document.getElementById('idle-cumulative-warning');
    const cumDisp   = document.getElementById('idle-cumulative-display');
    const iconCont  = document.getElementById('idle-icon-container');

    function resetActivity() {
        lastActivity = Date.now();
        if (!isIdle) return;
    }

    ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'].forEach(evt => {
        document.addEventListener(evt, resetActivity, { passive: true });
    });

    function formatTime(ms) {
        const totalSec = Math.floor(ms / 1000);
        const m = Math.floor(totalSec / 60);
        const s = totalSec % 60;
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function showPopup() {
        if (isIdle) return;
        isIdle = true;
        idleStart = new Date(Date.now() - IDLE_THRESHOLD_MS);
        overlay.style.display = 'flex';
        document.title = '⏸ Inativa — DG Store';

        fetchCumulative();

        tickTimer = setInterval(() => {
            const elapsed = Date.now() - idleStart.getTime();
            timerEl.textContent = formatTime(elapsed);

            const totalMin = Math.floor(elapsed / 60000);
            if (totalMin >= 10) {
                timerEl.style.color = '#dc2626';
                iconCont.style.animationDuration = '0.5s';
            } else if (totalMin >= 5) {
                timerEl.style.color = '#ef4444';
                iconCont.style.animationDuration = '1s';
            }
        }, TICK_INTERVAL_MS);
    }

    function fetchCumulative() {
        fetch('{{ route("idle-monitor.alert") }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            cumulativeToday = data.cumulative_minutes || 0;
            if (cumulativeToday >= 20) {
                cumWarn.style.display = 'block';
                cumDisp.textContent = cumulativeToday;
            }
        })
        .catch(() => {});
    }

    window.dismissIdle = function() {
        if (!isIdle) return;

        const idleMinutes = Math.max(1, Math.round((Date.now() - idleStart.getTime()) / 60000));

        overlay.style.display = 'none';
        document.title = document.title.replace('⏸ Inativa — ', '');
        clearInterval(tickTimer);

        timerEl.textContent = '00:00';
        timerEl.style.color = '#ef4444';
        iconCont.style.animationDuration = '2s';
        cumWarn.style.display = 'none';
        isIdle = false;
        lastActivity = Date.now();

        fetch('{{ route("idle-monitor.dismiss") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                idle_minutes: idleMinutes,
                started_at: idleStart.toISOString(),
            }),
        }).catch(() => {});
    };

    setInterval(() => {
        if (isIdle) return;
        if (Date.now() - lastActivity >= IDLE_THRESHOLD_MS) {
            showPopup();
        }
    }, 10000);
})();
</script>

@endif
@endauth
