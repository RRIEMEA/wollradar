<button {{ $attributes->merge(['type' => 'submit', 'class' => 'app-button']) }}>
    {{ $slot }}
</button>
