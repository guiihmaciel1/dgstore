<div x-data="dailyChecklist()" x-init="init()" class="mb-6">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
        {{-- Header com progresso --}}
        <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="text-lg">📋</span>
                    <h3 class="text-sm font-extrabold text-gray-900 m-0">Checklist do Dia</h3>
                </div>
                <span class="text-xs font-bold" :class="progressPercent === 100 ? 'text-emerald-600' : 'text-gray-500'"
                      x-text="totalDone + '/' + totalItems + ' (' + Math.round(progressPercent) + '%)'"></span>
            </div>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500 ease-out"
                     :style="'width: ' + progressPercent + '%'"
                     :class="{
                         'bg-red-400': progressPercent < 30,
                         'bg-amber-400': progressPercent >= 30 && progressPercent < 60,
                         'bg-emerald-400': progressPercent >= 60 && progressPercent < 100,
                         'bg-emerald-500': progressPercent === 100,
                     }"></div>
            </div>
            <template x-if="progressPercent === 100">
                <p class="text-[0.7rem] font-bold text-emerald-600 mt-1.5 mb-0 text-center">Tudo feito! Excelente trabalho hoje!</p>
            </template>
        </div>

        {{-- Categorias --}}
        <div class="divide-y divide-gray-100">
            <template x-for="(cat, catIdx) in categories" :key="catIdx">
                <div>
                    <button @click="cat.open = !cat.open" type="button"
                            class="w-full flex items-center justify-between px-4 py-2.5 bg-transparent border-none cursor-pointer hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200"
                                 :class="cat.open ? 'rotate-90' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span class="text-sm" x-text="cat.icon"></span>
                            <span class="text-xs font-bold text-gray-700" x-text="cat.name"></span>
                        </div>
                        <span class="text-[0.65rem] font-bold px-2 py-0.5 rounded-full"
                              :class="catDone(catIdx) === cat.items.length ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500'"
                              x-text="catDone(catIdx) + '/' + cat.items.length"></span>
                    </button>
                    <div x-show="cat.open" x-collapse>
                        <div class="px-4 pb-3 space-y-1">
                            <template x-for="(item, itemIdx) in cat.items" :key="itemIdx">
                                <label class="flex items-start gap-2.5 px-2.5 py-2 rounded-lg cursor-pointer transition-colors"
                                       :class="item.done ? 'bg-emerald-50/60' : 'hover:bg-gray-50'">
                                    <input type="checkbox" :checked="item.done"
                                           @change="toggle(catIdx, itemIdx)"
                                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer shrink-0">
                                    <span class="text-xs leading-relaxed transition-all duration-200"
                                          :class="item.done ? 'line-through text-gray-400' : 'text-gray-700 font-medium'"
                                          x-text="item.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
function dailyChecklist() {
    const today = new Date().toISOString().slice(0, 10);
    const STORAGE_KEY = 'sophia-checklist-' + today;

    return {
        categories: [
            {
                name: 'Abertura da Loja',
                icon: '🏪',
                open: true,
                items: [
                    { label: 'Ligar ar-condicionado e iluminação/vitrine', done: false },
                    { label: 'Bater tapete / varrer entrada da loja', done: false },
                    { label: 'Limpar balcão e vitrines (pano úmido)', done: false },
                    { label: 'Verificar se aparelhos de mostruário estão ligados e funcionando', done: false },
                    { label: 'Conferir Wi-Fi da loja funcionando', done: false },
                ],
            },
            {
                name: 'Estoque e Produtos',
                icon: '📦',
                open: true,
                items: [
                    { label: 'Colocar todos os seminovos para carregar', done: false },
                    { label: 'Conferir se seminovos estão na tela de reset', done: false },
                    { label: 'Organizar caixinhas nas prateleiras', done: false },
                    { label: 'Conferir se valores das etiquetas batem com o sistema', done: false },
                ],
            },
            {
                name: 'Marketing e Digital',
                icon: '📣',
                open: true,
                items: [
                    { label: 'Conferir se tem story dos seminovos ativos (Instagram)', done: false },
                    { label: 'Olhar preço do concorrente GV_cell e enviar no grupo', done: false },
                    { label: 'Verificar se storys de clientes foram publicados no feed e adicionados aos destaques', done: false },
                    { label: 'Verificar se algum cliente ficou sem resposta no WhatsApp', done: false },
                    { label: 'Ler conversas do dia anterior para saber o que está sendo negociado', done: false },
                    { label: 'Verificar em Vendas o que foi concluído e excluir anúncios do marketplace se necessário', done: false },
                    { label: 'Acionar clientes que sumiram sem responder (ver Ferramentas > Mensagem WhatsApp)', done: false },
                ],
            },
            {
                name: 'Limpeza e Organização',
                icon: '🧹',
                open: true,
                items: [
                    { label: 'Trocar/colocar saco de lixo (sala e banheiro)', done: false },
                    { label: 'Verificar geladeira — descartar itens vencidos/abertos, repor o que necessário', done: false },
                    { label: 'Regar plantas / jogar água aberta nas plantas', done: false },
                    { label: 'Lavar suporte Dolce Gusto onde pinga café, ver se ficou cápsula dentro, encher água', done: false },
                    { label: 'Organizar mesas de atendimento', done: false },
                ],
            },
        ],

        init() {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const state = JSON.parse(saved);
                    state.forEach((catState, ci) => {
                        if (this.categories[ci]) {
                            catState.forEach((done, ii) => {
                                if (this.categories[ci].items[ii]) {
                                    this.categories[ci].items[ii].done = done;
                                }
                            });
                        }
                    });
                } catch (e) {}
            }
            this._cleanOldKeys();
        },

        toggle(catIdx, itemIdx) {
            this.categories[catIdx].items[itemIdx].done = !this.categories[catIdx].items[itemIdx].done;
            this._save();
        },

        catDone(catIdx) {
            return this.categories[catIdx].items.filter(i => i.done).length;
        },

        get totalDone() {
            return this.categories.reduce((sum, cat) => sum + cat.items.filter(i => i.done).length, 0);
        },

        get totalItems() {
            return this.categories.reduce((sum, cat) => sum + cat.items.length, 0);
        },

        get progressPercent() {
            if (this.totalItems === 0) return 0;
            return (this.totalDone / this.totalItems) * 100;
        },

        _save() {
            const state = this.categories.map(cat => cat.items.map(i => i.done));
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        },

        _cleanOldKeys() {
            for (let i = localStorage.length - 1; i >= 0; i--) {
                const key = localStorage.key(i);
                if (key && key.startsWith('sophia-checklist-') && key !== STORAGE_KEY) {
                    localStorage.removeItem(key);
                }
            }
        },
    };
}
</script>
