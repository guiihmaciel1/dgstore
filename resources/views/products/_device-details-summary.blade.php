@php
    $summaryKeys = [
        'SerialNumber' => 'Número de Série',
        'InternationalMobileEquipmentIdentity' => 'IMEI',
        'InternationalMobileEquipmentIdentity2' => 'IMEI 2',
        'ProductType' => 'Tipo do Dispositivo',
        'ModelNumber' => 'Número do Modelo',
        'ProductVersion' => 'Versão iOS',
        'RegionInfo' => 'Região',
        'DeviceName' => 'Nome do Dispositivo',
        'ActivationState' => 'Estado de Ativação',
        'BluetoothAddress' => 'Bluetooth MAC',
        'WiFiAddress' => 'WiFi MAC',
        'PhoneNumber' => 'Telefone',
    ];
@endphp

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.375rem 1rem;">
    @foreach($summaryKeys as $key => $label)
        @if(!empty($details[$key]))
            <div style="display: flex; justify-content: space-between; padding: 0.25rem 0; border-bottom: 1px solid #fef3c7;">
                <span style="font-size: 0.6875rem; font-weight: 500; color: #92400e;">{{ $label }}</span>
                <span style="font-size: 0.6875rem; color: #78350f; font-family: monospace;">{{ $details[$key] }}</span>
            </div>
        @endif
    @endforeach
</div>
<p style="font-size: 0.6875rem; color: #92400e; margin-top: 0.5rem; font-style: italic;">{{ count($details) }} propriedades importadas do dispositivo.</p>
