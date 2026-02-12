<x-app-layout>
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div style="margin-bottom: 1.5rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Consulta IMEI</h1>
                <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">Verifique a situação do aparelho em sites oficiais</p>
            </div>

            <!-- Campo de IMEI -->
            <div x-data="{ imei: '' }" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #6b7280; margin-bottom: 0.375rem; text-transform: uppercase; letter-spacing: 0.05em;">
                    Digite o IMEI do aparelho
                </label>
                <div style="position: relative;">
                    <svg style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #9ca3af; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <input type="text"
                           x-model="imei"
                           placeholder="000000000000000"
                           maxlength="20"
                           autocomplete="off"
                           style="width: 100%; padding: 0.875rem 1rem 0.875rem 2.75rem; font-size: 1.125rem; font-family: ui-monospace, monospace; letter-spacing: 0.1em; border: 2px solid #e5e7eb; border-radius: 0.75rem; background: white; outline: none; transition: border-color 0.15s;"
                           onfocus="this.style.borderColor='#111827'" onblur="this.style.borderColor='#e5e7eb'">
                </div>
                <p style="margin-top: 0.375rem; font-size: 0.75rem; color: #9ca3af;">
                    Disque <strong>*#06#</strong> no aparelho para ver o IMEI
                </p>

                <!-- Links de consulta -->
                <div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.75rem;">

                    <!-- Apple - Check Coverage -->
                    <a :href="'https://checkcoverage.apple.com/?imei=' + encodeURIComponent(imei)"
                       target="_blank" rel="noopener noreferrer"
                       style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;"
                       onmouseover="this.style.borderColor='#111827'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';"
                       onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #111827; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 20px; height: 20px;" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                            </svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Apple - Check Coverage</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Garantia Apple, AppleCare e cobertura de serviço</div>
                        </div>
                        <svg style="width: 18px; height: 18px; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>

                    <!-- IMEI.info -->
                    <a :href="'https://www.imei.info/check/?imei=' + encodeURIComponent(imei)"
                       target="_blank" rel="noopener noreferrer"
                       style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;"
                       onmouseover="this.style.borderColor='#2563eb'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';"
                       onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #2563eb; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.9375rem; font-weight: 600; color: #111827;">IMEI.info</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Modelo, marca, especificações e status de bloqueio</div>
                        </div>
                        <svg style="width: 18px; height: 18px; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>

                    <!-- Anatel - Celular Legal -->
                    <a href="https://www.gov.br/anatel/pt-br/assuntos/celular-legal/consulte-sua-situacao"
                       target="_blank" rel="noopener noreferrer"
                       style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;"
                       onmouseover="this.style.borderColor='#059669'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';"
                       onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #059669; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Anatel - Celular Legal</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Homologação, situação regular e restrição por roubo/furto</div>
                        </div>
                        <svg style="width: 18px; height: 18px; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>

                    <!-- Swappa ESN Check -->
                    <a :href="'https://swappa.com/esn?imei=' + encodeURIComponent(imei)"
                       target="_blank" rel="noopener noreferrer"
                       style="display: flex; align-items: center; gap: 1rem; padding: 1rem 1.25rem; background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; text-decoration: none; transition: all 0.15s;"
                       onmouseover="this.style.borderColor='#7c3aed'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.08)';"
                       onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: #7c3aed; color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 0.9375rem; font-weight: 600; color: #111827;">Swappa ESN Check</div>
                            <div style="font-size: 0.75rem; color: #6b7280;">Verificação de bloqueio em operadoras (blacklist)</div>
                        </div>
                        <svg style="width: 18px; height: 18px; color: #9ca3af; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
