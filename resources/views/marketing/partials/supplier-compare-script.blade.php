<script>
/**
 * Comparação da tabela de Novos com a cotação do fornecedor Santa Fé.
 * Só altera os valores na tela; a gravação continua sendo o "Salvar Tabela".
 * Apenas métodos (sem getters): o objeto é espalhado com "..." dentro de marketingApp().
 */
function supplierCompareMixin(data) {
    const MARGIN_STORAGE_KEY = 'marketing.supplierCompareMargin';
    const BULK_TYPES = ['color', 'no_color'];
    const toNumber = (value) => {
        const n = parseFloat(value);
        return Number.isFinite(n) ? n : null;
    };

    return {
        compareData: data,
        compareMode: false,
        compareMargin: localStorage.getItem(MARGIN_STORAGE_KEY) || '',
        compareShowMissing: false,
        compareUndoStack: [],
        compareOriginals: {},
        compareFeedback: '',

        toggleCompareMode() {
            this.compareMode = !this.compareMode;
            if (this.compareMode && !['', 'iphone'].includes(this.priceCategoryFilter)) {
                this.priceCategoryFilter = 'iphone';
            }
        },

        compareInfo(row) {
            if (!this.compareData || !row.id) return null;
            return this.compareData.by_price_id[row.id] || null;
        },

        compareCostOf(row) {
            const info = this.compareInfo(row);
            if (!info) return null;
            return info.supplier_cost ?? info.model_min ?? null;
        },

        compareStatus(row) {
            if (!row.id) return 'new';
            const info = this.compareInfo(row);
            if (!info) return 'skip';
            if (info.type === 'none') return 'none';
            if (info.type === 'color_missing') return 'color_missing';
            return toNumber(row.cost_price) === info.supplier_cost ? 'match' : 'diff';
        },

        compareRealMargin(row) {
            const cost = this.compareCostOf(row);
            const price = toNumber(row.price);
            if (cost === null || price === null) return null;
            return price - cost;
        },

        compareSummary() {
            const summary = { match: 0, diff: 0, color_missing: 0, none: 0 };
            this.prices.filter(row => row.active).forEach(row => {
                const status = this.compareStatus(row);
                if (status in summary) summary[status]++;
            });
            return summary;
        },

        compareQuoteAgeDays() {
            if (!this.compareData) return null;
            const quoted = new Date(this.compareData.quoted_at + 'T00:00:00');
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return Math.round((today - quoted) / 86400000);
        },

        compareFormatDate(isoDate) {
            if (!isoDate) return '';
            const [y, m, d] = isoDate.split('-');
            return `${d}/${m}/${y}`;
        },

        compareFmt(value) {
            const n = toNumber(value);
            return n === null ? '—' : n.toLocaleString('pt-BR', { maximumFractionDigits: 0 });
        },

        compareMarginValue() {
            const n = toNumber(this.compareMargin);
            return n !== null && n >= 0 ? n : null;
        },

        // Só linhas visíveis: o formulário envia apenas as linhas renderizadas.
        compareEligibleRows() {
            return this.filteredPrices.filter(row => {
                const info = this.compareInfo(row);
                return row.active && info && BULK_TYPES.includes(info.type) && info.supplier_cost !== null;
            });
        },

        compareApply(withMargin) {
            const margin = this.compareMarginValue();
            if (withMargin && margin === null) {
                alert('Informe uma margem válida em R$ (ex.: 499).');
                return;
            }

            const rows = this.compareEligibleRows();
            if (rows.length === 0) {
                alert('Nenhum item ativo com cotação do fornecedor na lista atual.');
                return;
            }

            const action = withMargin
                ? `aplicar custo Santa Fé + R$ ${this.compareFmt(margin)} na venda de ${rows.length} item(ns)`
                : `alinhar o custo de ${rows.length} item(ns) com o Santa Fé`;
            if (!confirm(`Deseja ${action}?\n\nNada é gravado até você clicar em "Salvar Tabela".`)) {
                return;
            }

            this._compareSetValues(rows, withMargin ? margin : null);
            if (withMargin) {
                localStorage.setItem(MARGIN_STORAGE_KEY, String(margin));
            }
        },

        compareApplyRow(row) {
            const cost = this.compareCostOf(row);
            if (cost === null) return;
            this._compareSetValues([row], this.compareMarginValue(), cost);
        },

        _compareSetValues(rows, margin, forcedCost = null) {
            this.compareUndoStack.push(rows.map(row => ({
                key: row._key, cost_price: row.cost_price, price: row.price,
            })));

            rows.forEach(row => {
                if (!(row._key in this.compareOriginals)) {
                    this.compareOriginals[row._key] = { cost_price: row.cost_price, price: row.price };
                }
                const cost = forcedCost ?? this.compareInfo(row).supplier_cost;
                row.cost_price = cost.toFixed(2);
                if (margin !== null) {
                    row.price = (cost + margin).toFixed(2);
                }
            });

            this.compareFeedback = `${rows.length} item(ns) atualizado(s) na tela.`;
            setTimeout(() => this.compareFeedback = '', 4000);
        },

        compareUndo() {
            const snapshot = this.compareUndoStack.pop();
            if (!snapshot) return;
            snapshot.forEach(saved => {
                const row = this.prices.find(p => p._key === saved.key);
                if (row) {
                    row.cost_price = saved.cost_price;
                    row.price = saved.price;
                }
            });
        },

        compareIsChanged(row) {
            const original = this.compareOriginals[row._key];
            if (!original) return false;
            return toNumber(original.cost_price) !== toNumber(row.cost_price)
                || toNumber(original.price) !== toNumber(row.price);
        },

        compareChangedCount() {
            return this.prices.filter(row => this.compareIsChanged(row)).length;
        },

        compareChangedHiddenCount() {
            const visible = new Set(this.filteredPrices.map(row => row._key));
            return this.prices.filter(row => this.compareIsChanged(row) && !visible.has(row._key)).length;
        },

        compareAddMissing(item) {
            const margin = this.compareMarginValue();
            if (margin === null) {
                alert('Informe a margem em R$ antes de adicionar, para calcular o preço de venda.');
                return;
            }
            this.addPrice();
            const row = this.prices[this.prices.length - 1];
            Object.assign(row, {
                name: item.name,
                category: 'iphone',
                storage: item.storage,
                color: item.color,
                color_hex: item.color_hex,
                cost_price: item.price.toFixed(2),
                price: (item.price + margin).toFixed(2),
            });
            item._added = true;
        },
    };
}
</script>
