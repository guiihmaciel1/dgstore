<x-app-layout>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="salesTraining()">

            {{-- Header --}}
            <div style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
                <div style="position: absolute; bottom: -30px; right: 60px; width: 80px; height: 80px; background: rgba(255,255,255,0.03); border-radius: 50%;"></div>
                <div style="position: relative; z-index: 1;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.75rem;">🎓</span>
                        <div>
                            <h1 style="font-size: 1.5rem; font-weight: 800; color: white; letter-spacing: -0.025em;">Treinamento de Vendas</h1>
                            <p style="font-size: 0.8125rem; color: rgba(255,255,255,0.6);">Sua jornada pra virar expert Apple começa aqui ✨</p>
                        </div>
                    </div>
                    <div style="margin-top: 1rem; background: rgba(255,255,255,0.1); border-radius: 0.75rem; padding: 0.75rem 1rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.375rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: rgba(255,255,255,0.8);">Seu progresso</span>
                            <span style="font-size: 0.75rem; font-weight: 700; color: white;" x-text="modulesRead + '/' + totalModules + ' módulos'"></span>
                        </div>
                        <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.15); border-radius: 9999px; overflow: hidden;">
                            <div :style="'height: 100%; border-radius: 9999px; transition: width 0.5s ease; background: linear-gradient(90deg, #a78bfa, #818cf8); width:' + progressPercent + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Módulos --}}
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">

                @include('tools.training.module-lineup')
                @include('tools.training.module-seminovos')
                @include('tools.training.module-batalha')
                @include('tools.training.module-ai')
                @include('tools.training.module-vendas')
                @include('tools.training.module-trends')
                @include('tools.training.module-quiz')

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/sales-training-data.js') }}"></script>
    <script>
    function salesTraining() {
        var data = salesTrainingData();
        return {
            openModule: null,
            readModules: JSON.parse(localStorage.getItem('st_read') || '[]'),
            totalModules: 7,

            get modulesRead() { return this.readModules.length; },
            get progressPercent() { return Math.round((this.readModules.length / this.totalModules) * 100); },

            toggleModule(key) {
                this.openModule = this.openModule === key ? null : key;
                if (this.openModule === key && !this.readModules.includes(key)) {
                    this.readModules.push(key);
                    localStorage.setItem('st_read', JSON.stringify(this.readModules));
                }
            },

            ...data,

            // Quiz state
            quizStarted: false,
            quizFinished: false,
            quizCurrent: 0,
            quizScore: 0,
            quizAnswered: null,

            get currentQuestion() { return this.quizQuestions[this.quizCurrent]; },
            get quizPercent() { return Math.round((this.quizScore / this.quizQuestions.length) * 100); },
            get quizEmoji() {
                if (this.quizPercent >= 90) return '🏆';
                if (this.quizPercent >= 70) return '🎉';
                if (this.quizPercent >= 50) return '💪';
                return '📚';
            },
            get quizTitle() {
                if (this.quizPercent >= 90) return 'Expert Apple! Arrasou! 🔥';
                if (this.quizPercent >= 70) return 'Muito bem! Quase lá!';
                if (this.quizPercent >= 50) return 'Bom começo! Continue estudando!';
                return 'Bora revisar os módulos!';
            },
            get quizMessage() {
                if (this.quizPercent >= 90) return 'Você tá mais que pronta pra vender. Manda ver na loja!';
                if (this.quizPercent >= 70) return 'Sabe bastante! Revisa os pontos que errou e vai ficar expert.';
                if (this.quizPercent >= 50) return 'Tá no caminho certo. Dá uma relida nos módulos e tenta de novo!';
                return 'Sem estresse! Lê os módulos com calma e depois volta pro quiz.';
            },

            startQuiz() {
                this.quizStarted = true;
                this.quizFinished = false;
                this.quizCurrent = 0;
                this.quizScore = 0;
                this.quizAnswered = null;
            },
            answerQuiz(idx) {
                if (this.quizAnswered !== null) return;
                this.quizAnswered = idx;
                if (idx === this.currentQuestion.correct) this.quizScore++;
            },
            nextQuestion() {
                if (this.quizCurrent < this.quizQuestions.length - 1) {
                    this.quizCurrent++;
                    this.quizAnswered = null;
                } else {
                    this.quizFinished = true;
                    this.quizStarted = false;
                    if (!this.readModules.includes('quiz')) {
                        this.readModules.push('quiz');
                        localStorage.setItem('st_read', JSON.stringify(this.readModules));
                    }
                }
            },
            resetQuiz() {
                this.quizStarted = false;
                this.quizFinished = false;
                this.quizCurrent = 0;
                this.quizScore = 0;
                this.quizAnswered = null;
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
