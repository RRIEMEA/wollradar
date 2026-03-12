<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Passwort vergessen? Kein Problem. Teile uns einfach deine E-Mail-Adresse mit. Wir senden dir dann einen Link zum Zurücksetzen des Passworts.
    </div>

    @if (app()->environment('local') && config('mail.default') === 'log')
        <div class="mb-4 rounded-3xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Lokaler Testmodus: E-Mails werden aktuell nicht wirklich versendet.
            Der Reset-Link wird in <code class="font-mono">storage/logs/laravel.log</code> geschrieben, weil <code class="font-mono">MAIL_MAILER=log</code> aktiv ist.
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Link zum Zurücksetzen senden
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
