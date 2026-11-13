{{-- Mensagens da sessão (success / error) e erros de validação, no padrão x-alert. --}}
<div {{ $attributes->merge(['class' => 'space-y-3']) }} @if(! session('success') && ! session('error') && ! $errors->any()) hidden @endif>
    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif
    @if($errors->any())
        <div class="rounded-xl border-l-4 border-red-500/30 bg-red-500/10 p-4 text-sm text-red-300" role="alert">
            <ul class="list-disc space-y-0.5 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
