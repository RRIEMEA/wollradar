@php
    $manifestPath = public_path('build/manifest.json');
    $legacyManifest = is_file($manifestPath)
        ? json_decode((string) file_get_contents($manifestPath), true)
        : null;

    $legacyEntry = $legacyManifest['resources/js/app-legacy.js']['file'] ?? null;
    $legacyPolyfills = $legacyManifest['vite/legacy-polyfills-legacy']['file'] ?? null;
@endphp

@if($legacyEntry && $legacyPolyfills)
    <script>
        !function(){var e=document,t=e.createElement("script");if(!("noModule"in t)&&"onbeforeload"in t){var n=!1;e.addEventListener("beforeload",function(o){if(o.target===t)n=!0;else if(!o.target.hasAttribute("nomodule")||!n)return;o.preventDefault()},!0),t.type="module",t.src=".",e.head.appendChild(t),t.remove()}}();
    </script>
    <script nomodule id="vite-legacy-polyfill" src="{{ asset('build/' . $legacyPolyfills) }}"></script>
    <script nomodule id="vite-legacy-entry" data-src="{{ asset('build/' . $legacyEntry) }}">
        System.import(document.getElementById('vite-legacy-entry').getAttribute('data-src'));
    </script>
@endif
