{{-- Módulo 7: Quiz --}}
<div style="background: #141414; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden;">
    <button @click="toggleModule('quiz')" type="button"
            style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: #141414; border: none; cursor: pointer; text-align: left;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 1.5rem;">🧠</span>
            <div>
                <span style="font-size: 1rem; font-weight: 700; color: #e3e3e3;">Quiz — Testa teu Conhecimento!</span>
                <div style="font-size: 0.75rem; color: #666666;">Veja se você tá pronta pra arrasar nas vendas</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span x-show="readModules.includes('quiz')" style="font-size: 0.6875rem; font-weight: 600; color: #059669; background: rgba(16,185,129,0.1); padding: 2px 8px; border-radius: 9999px;">✓ feito</span>
            <svg width="16" height="16" :style="openModule === 'quiz' ? 'transform:rotate(180deg);' : ''" style="flex-shrink:0; color: #666666; transition: transform 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </button>

    <div x-show="openModule === 'quiz'" x-transition style="border-top: 1px solid #f3f4f6; padding: 1.25rem;">

        {{-- Quiz not started --}}
        <div x-show="!quizStarted && !quizFinished" style="text-align: center; padding: 1.5rem 0;">
            <div style="font-size: 3rem; margin-bottom: 0.75rem;">🏆</div>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #e3e3e3; margin-bottom: 0.5rem;">Bora testar o que você aprendeu?</h3>
            <p style="font-size: 0.875rem; color: #818181; margin-bottom: 1rem;">São <span x-text="quizQuestions.length"></span> perguntas sobre tudo que vimos nos módulos.</p>
            <button @click="startQuiz()" type="button"
                    style="padding: 0.625rem 2rem; background: linear-gradient(135deg, #7c3aed, #6366f1); color: white; border: none; border-radius: 0.5rem; font-size: 0.9375rem; font-weight: 700; cursor: pointer;">
                Começar Quiz 🚀
            </button>
        </div>

        {{-- Quiz in progress --}}
        <div x-show="quizStarted && !quizFinished">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #818181;" x-text="'Pergunta ' + (quizCurrent + 1) + ' de ' + quizQuestions.length"></span>
                <span style="font-size: 0.8rem; font-weight: 700; color: #c4b5fd;" x-text="quizScore + ' acerto(s)'"></span>
            </div>
            <div style="width: 100%; height: 4px; background: #222222; border-radius: 9999px; overflow: hidden; margin-bottom: 1rem;">
                <div :style="'height: 100%; background: #7c3aed; border-radius: 9999px; transition: width 0.3s; width:' + ((quizCurrent / quizQuestions.length) * 100) + '%'"></div>
            </div>

            <div style="margin-bottom: 1rem;">
                <p style="font-size: 1rem; font-weight: 700; color: #e3e3e3; line-height: 1.5; margin-bottom: 1rem;" x-text="currentQuestion.q"></p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <template x-for="(opt, optIdx) in currentQuestion.opts" :key="optIdx">
                        <button @click="answerQuiz(optIdx)" type="button"
                                :disabled="quizAnswered !== null"
                                :style="quizAnswered === null
                                    ? 'width: 100%; text-align: left; padding: 0.75rem 1rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; color: #a4a4a4; background: #141414; cursor: pointer;'
                                    : (optIdx === currentQuestion.correct
                                        ? 'width: 100%; text-align: left; padding: 0.75rem 1rem; border: 2px solid #059669; border-radius: 0.5rem; font-size: 0.875rem; color: #6ee7b7; background: rgba(16,185,129,0.1); cursor: default; font-weight: 600;'
                                        : (quizAnswered === optIdx
                                            ? 'width: 100%; text-align: left; padding: 0.75rem 1rem; border: 2px solid #dc2626; border-radius: 0.5rem; font-size: 0.875rem; color: #fca5a5; background: rgba(239,68,68,0.1); cursor: default;'
                                            : 'width: 100%; text-align: left; padding: 0.75rem 1rem; border: 1px solid rgba(255,255,255,0.06); border-radius: 0.5rem; font-size: 0.875rem; color: #666666; background: #1a1a1a; cursor: default;'))"
                                x-text="opt">
                        </button>
                    </template>
                </div>
            </div>

            <div x-show="quizAnswered !== null" x-transition style="margin-bottom: 1rem;">
                <div :style="quizAnswered === currentQuestion.correct
                    ? 'background: rgba(16,185,129,0.1); border: 1px solid #a7f3d0; border-radius: 0.5rem; padding: 0.75rem 1rem;'
                    : 'background: rgba(239,68,68,0.1); border: 1px solid #fecaca; border-radius: 0.5rem; padding: 0.75rem 1rem;'">
                    <p :style="quizAnswered === currentQuestion.correct ? 'font-size: 0.8rem; color: #6ee7b7; font-weight: 600;' : 'font-size: 0.8rem; color: #fca5a5; font-weight: 600;'"
                       x-text="quizAnswered === currentQuestion.correct ? '✅ Certíssimo! Mandou bem!' : '❌ Errou, mas bora aprender!'"></p>
                    <p style="font-size: 0.8rem; color: #a4a4a4; margin-top: 0.25rem;" x-text="currentQuestion.explanation"></p>
                </div>
                <div style="margin-top: 0.75rem; text-align: right;">
                    <button @click="nextQuestion()" type="button"
                            style="padding: 0.5rem 1.25rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.8rem; font-weight: 600; cursor: pointer;"
                            x-text="quizCurrent < quizQuestions.length - 1 ? 'Próxima →' : 'Ver Resultado'">
                    </button>
                </div>
            </div>
        </div>

        {{-- Quiz finished --}}
        <div x-show="quizFinished" style="text-align: center; padding: 1.5rem 0;">
            <div style="font-size: 3.5rem; margin-bottom: 0.5rem;" x-text="quizEmoji"></div>
            <h3 style="font-size: 1.25rem; font-weight: 800; color: #e3e3e3; margin-bottom: 0.375rem;" x-text="quizTitle"></h3>
            <p style="font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem;" :style="'color:' + (quizPercent >= 70 ? '#059669' : quizPercent >= 50 ? '#d97706' : '#dc2626')" x-text="quizScore + '/' + quizQuestions.length"></p>
            <p style="font-size: 0.875rem; color: #818181; margin-bottom: 1.25rem;" x-text="quizMessage"></p>
            <button @click="resetQuiz()" type="button"
                    style="padding: 0.625rem 2rem; background: #111827; color: white; border: none; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer;">
                Tentar de Novo 🔄
            </button>
        </div>
    </div>
</div>
