<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Passwort" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Passwort bestätigen" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-5 rounded-2xl border border-stone-200 bg-stone-50 px-4 py-3">
            <label for="privacy_acknowledged" class="flex items-start gap-3 text-sm text-stone-700">
                <input
                    id="privacy_acknowledged"
                    name="privacy_acknowledged"
                    type="checkbox"
                    value="1"
                    class="mt-0.5 h-4 w-4 rounded border-stone-300 text-amber-600 focus:ring-amber-500"
                    {{ old('privacy_acknowledged') ? 'checked' : '' }}
                    required
                >
                <span>
                    Ich habe die
                    <a href="{{ route('legal.privacy') }}" target="_blank" rel="noopener" class="font-medium text-amber-700 underline underline-offset-4 hover:text-amber-800">Datenschutzerklärung</a>
                    und das
                    <a href="{{ route('legal.imprint') }}" target="_blank" rel="noopener" class="font-medium text-amber-700 underline underline-offset-4 hover:text-amber-800">Impressum</a>
                    zur Kenntnis genommen.
                </span>
            </label>
            <x-input-error :messages="$errors->get('privacy_acknowledged')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                Bereits registriert?
            </a>

            <x-primary-button class="ms-4">
                Registrieren
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
