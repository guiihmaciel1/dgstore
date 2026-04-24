<x-app-layout>
    <x-slot name="title">Checklist: {{ $checklist->name }}</x-slot>
    <div class="py-4">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <a href="{{ route('checklists.index') }}" style="font-size: 0.75rem; color: #6b7280; text-decoration: none;">
                        &larr; Voltar para listagem
                    </a>
                    <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-top: 0.25rem;">{{ $checklist->name }}</h1>
                    <p style="font-size: 0.8rem; color: #6b7280;">
                        Salvo em {{ $checklist->created_at->format('d/m/Y H:i') }} por {{ $checklist->user?->name ?? '-' }}
                    </p>
                </div>
                <div>
                    @php
                        $badgeBg = match($checklist->status) { 'approved' => '#dcfce7', 'failed' => '#fef2f2', default => '#fefce8' };
                        $badgeColor = match($checklist->status) { 'approved' => '#16a34a', 'failed' => '#dc2626', default => '#d97706' };
                    @endphp
                    <span style="padding: 0.25rem 0.75rem; font-size: 0.8rem; font-weight: 600; border-radius: 9999px; background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                        {{ $checklist->status_label }} &middot; {{ $checklist->summary_label }}
                    </span>
                </div>
            </div>

            @if($checklist->product || $checklist->tradeIn)
                <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 0.75rem; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.8rem; color: #4338ca;">
                    Vinculado a:
                    @if($checklist->product)
                        <strong>Produto — {{ $checklist->product->name }}</strong>
                    @else
                        <strong>Trade-in — {{ $checklist->tradeIn->device_name }}</strong>
                    @endif
                </div>
            @endif

            @php $deviceInfo = $checklist->device_info; @endphp
            @if($deviceInfo)
                <div style="background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%); border: 1px solid #c7d2fe; border-radius: 0.75rem; overflow: hidden; margin-bottom: 1.25rem;">
                    <div style="padding: 0.875rem 1.25rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; border-bottom: 1px solid rgba(199,210,254,0.5);">
                        <span style="font-size: 1rem;">📱</span>
                        <span style="font-size: 0.8rem; font-weight: 700; color: #312e81;">{{ $deviceInfo['modelName'] ?? 'Dispositivo' }}</span>
                        @if(!empty($deviceInfo['capacity']))
                            <span style="font-size: 0.65rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #c7d2fe; color: #4338ca;">{{ $deviceInfo['capacity'] }}</span>
                        @endif
                        @if(!empty($deviceInfo['color']))
                            <span style="font-size: 0.65rem; font-weight: 600; padding: 0.125rem 0.5rem; border-radius: 9999px; background: #e0e7ff; color: #4338ca;">{{ $deviceInfo['color'] }}</span>
                        @endif
                    </div>

                    @php
                        $fieldGroups = [
                            'Identificacao' => [
                                'iosVersion' => 'iOS', 'serialNumber' => 'Serial', 'imei' => 'IMEI',
                                'imei2' => 'IMEI 2', 'region' => 'Regiao', 'modelNumber' => 'Modelo',
                                'regulatoryModel' => 'Homologacao', 'batteryLife' => 'Bateria',
                            ],
                            'Status' => [
                                'activation' => 'Ativacao', 'jailbreak' => 'Jailbreak',
                                'snMatch' => 'SN Match', 'fiveCodeMatch' => '5-Code',
                                'simLock' => 'SIM Lock', 'idLock' => 'ID Lock',
                                'mfgDate' => 'Fabricacao', 'warrantyPeriod' => 'Garantia',
                            ],
                            'Rede' => [
                                'phoneNumber' => 'Telefone', 'simStatus' => 'SIM Status',
                                'simTrayStatus' => 'Bandeja SIM',
                                'bluetoothAddress' => 'Bluetooth', 'wifiAddress' => 'Wi-Fi',
                                'cellularAddress' => 'Cellular',
                                'iccid' => 'ICCID', 'iccid2' => 'ICCID 2',
                            ],
                            'Hardware' => [
                                'hardwareModel' => 'Hardware', 'cpuArchitecture' => 'CPU',
                                'basebandVersion' => 'Baseband', 'firmwareVersion' => 'Firmware',
                                'mlbSerialNumber' => 'MLB Serial', 'wirelessBoardSN' => 'Wireless Board',
                                'udid' => 'UDID', 'deviceName' => 'Nome Dispositivo', 'timeZone' => 'Fuso Horario',
                            ],
                            'Componentes' => [
                                'batterySN' => 'Bateria SN', 'frontCameraSN' => 'Camera Frontal SN',
                                'rearCameraSN' => 'Camera Traseira SN', 'lidarSN' => 'LiDAR SN',
                                'screenSN' => 'Tela SN',
                            ],
                            'Sensores' => [
                                'faceId' => 'Face ID', 'infraredCamera' => 'Infrared Camera',
                                'dotProjector' => 'Dot Projector', 'distanceSensor' => 'Distance Sensor',
                            ],
                        ];
                        $monoFields = ['serialNumber','imei','imei2','iccid','iccid2','bluetoothAddress','wifiAddress','cellularAddress','mlbSerialNumber','wirelessBoardSN','udid','batterySN','frontCameraSN','rearCameraSN','lidarSN','screenSN'];
                        $statusFields = ['snMatch','fiveCodeMatch','activation','jailbreak','simLock','idLock','faceId','infraredCamera','dotProjector','distanceSensor'];
                        $statusColorFn = function($key, $val) {
                            if (in_array($key, ['snMatch','fiveCodeMatch'])) return $val === 'Yes' ? '#059669' : '#dc2626';
                            if ($key === 'jailbreak') return $val === 'No' ? '#059669' : '#dc2626';
                            $low = strtolower($val);
                            if (in_array($low, ['normal','yes','no','off','unlocked','activated'])) return '#059669';
                            if (in_array($low, ['on','locked','abnormal'])) return '#dc2626';
                            if (in_array($low, ['unknown','unknow','pending inspection','no testing'])) return '#d97706';
                            return '#111827';
                        };
                    @endphp

                    @foreach($fieldGroups as $groupName => $fields)
                        @php $hasAny = collect($fields)->keys()->contains(fn($k) => !empty($deviceInfo[$k])); @endphp
                        @if($hasAny)
                            <div style="padding: 0.75rem 1.25rem; display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 0.5rem 1rem; {{ !$loop->first ? 'border-top: 1px solid rgba(199,210,254,0.3);' : '' }}">
                                @foreach($fields as $key => $label)
                                    @if(!empty($deviceInfo[$key]))
                                        <div>
                                            <span style="font-size: 0.6rem; font-weight: 600; color: #6b7280; text-transform: uppercase;">{{ $label }}</span>
                                            @php
                                                $val = $deviceInfo[$key];
                                                $textColor = '#111827';
                                                $isMono = in_array($key, $monoFields);
                                                if ($key === 'iosVersion' && !empty($deviceInfo['buildVersion'])) {
                                                    $val .= ' (' . $deviceInfo['buildVersion'] . ')';
                                                }
                                                if ($key === 'batteryLife') {
                                                    $val .= '%' . (!empty($deviceInfo['chargeCycles']) ? ' / ' . $deviceInfo['chargeCycles'] . ' ciclos' : '');
                                                    $textColor = intval($deviceInfo['batteryLife']) >= 80 ? '#059669' : '#dc2626';
                                                }
                                                if ($key === 'modelNumber') {
                                                    if (!empty($deviceInfo['productType'])) $val .= ' (' . $deviceInfo['productType'] . ')';
                                                }
                                                if (in_array($key, $statusFields)) {
                                                    $textColor = $statusColorFn($key, $val);
                                                }
                                            @endphp
                                            <p style="font-size: {{ $isMono ? '0.65rem' : '0.75rem' }}; font-weight: 600; color: {{ $textColor }}; margin: 0; {{ $isMono ? 'font-family: monospace; word-break: break-all;' : '' }}">{{ $val }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span style="font-size: 0.8rem; font-weight: 600; color: #374151;">Progresso</span>
                    <span style="font-size: 0.8rem; font-weight: 700; color: #111827;">{{ $checklist->passed_items + $checklist->failed_items }} / {{ $checklist->total_items }}</span>
                </div>
                @php $pct = $checklist->total_items > 0 ? round((($checklist->passed_items + $checklist->failed_items) / $checklist->total_items) * 100) : 0; @endphp
                <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 9999px; overflow: hidden;">
                    <div style="height: 100%; border-radius: 9999px; transition: width 0.3s; background: {{ $pct === 100 ? '#059669' : '#111827' }}; width: {{ $pct }}%;"></div>
                </div>
            </div>

            @php $sections = $checklist->sections ?? []; @endphp
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($sections as $section)
                    <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem; overflow: hidden;">
                        <div style="padding: 0.875rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px solid #f3f4f6;">
                            <span style="font-size: 1.25rem;">{{ $section['icon'] ?? '' }}</span>
                            <span style="font-size: 0.9375rem; font-weight: 700; color: #111827;">{{ $section['title'] ?? '' }}</span>
                        </div>
                        @foreach($section['subs'] ?? [] as $sub)
                            @if(!empty($sub['label']))
                                <div style="padding: 0.5rem 1.25rem 0.25rem; font-size: 0.7rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">{{ $sub['label'] }}</div>
                            @endif
                            @foreach($sub['items'] ?? [] as $item)
                                @php
                                    $status = $item['status'] ?? '';
                                    $iconBg = match($status) { 'ok' => '#dcfce7', 'fail' => '#fef2f2', default => '#f3f4f6' };
                                    $iconColor = match($status) { 'ok' => '#16a34a', 'fail' => '#dc2626', default => '#d1d5db' };
                                @endphp
                                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 1.25rem; border-bottom: 1px solid #fafafa;">
                                    <div style="width: 24px; height: 24px; border-radius: 6px; background: {{ $iconBg }}; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        @if($status === 'ok')
                                            <svg style="width: 14px; height: 14px; color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($status === 'fail')
                                            <svg style="width: 14px; height: 14px; color: {{ $iconColor }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        @else
                                            <div style="width: 8px; height: 8px; border-radius: 9999px; background: #d1d5db;"></div>
                                        @endif
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <span style="font-size: 0.875rem; {{ $status === 'ok' ? 'color: #374151; text-decoration: line-through; opacity: 0.5;' : ($status === 'fail' ? 'color: #dc2626; font-weight: 500;' : 'color: #374151;') }}">
                                            {{ $item['label'] ?? '' }}
                                        </span>
                                        @if(!empty($item['hint']))
                                            <span style="display: block; font-size: 0.7rem; color: #9ca3af; margin-top: 1px;">{{ $item['hint'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
