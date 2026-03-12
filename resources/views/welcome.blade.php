<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#fbbf24">

        <title>{{ config('app.name', 'Wollradar') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            body {
                margin: 0;
                min-height: 100vh;
                font-family: Figtree, sans-serif;
                background:
                    radial-gradient(circle at top left, rgba(251, 191, 36, 0.22), transparent 28rem),
                    linear-gradient(180deg, #fffdf7 0%, #f7f2e8 100%);
                color: #1c1917;
            }

            .welcome-shell {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1.25rem;
            }

            .welcome-card {
                width: min(100%, 70rem);
                display: grid;
                gap: 1.5rem;
                grid-template-columns: 1.15fr 0.85fr;
                background: rgba(255, 255, 255, 0.92);
                border: 1px solid rgba(255, 255, 255, 0.85);
                border-radius: 2rem;
                box-shadow: 0 28px 80px -42px rgba(28, 25, 23, 0.42);
                overflow: hidden;
                backdrop-filter: blur(14px);
            }

            .welcome-copy {
                padding: 2rem;
            }

            .welcome-side {
                padding: 2rem;
                background: linear-gradient(160deg, #fef3c7 0%, #fde68a 42%, #f5e3b2 100%);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                gap: 1.25rem;
            }

            .eyebrow {
                margin: 0;
                font-size: 0.8rem;
                font-weight: 700;
                letter-spacing: 0.22em;
                text-transform: uppercase;
                color: #b45309;
            }

            h1 {
                margin: 0.8rem 0 0;
                font-size: clamp(2.2rem, 4vw, 4rem);
                line-height: 1.05;
            }

            .lead {
                margin: 1rem 0 0;
                max-width: 42rem;
                font-size: 1.05rem;
                line-height: 1.75;
                color: #57534e;
            }

            .feature-list {
                margin: 1.75rem 0 0;
                padding: 0;
                list-style: none;
                display: grid;
                gap: 0.85rem;
            }

            .feature-list li {
                display: flex;
                gap: 0.8rem;
                align-items: flex-start;
                color: #44403c;
            }

            .feature-dot {
                width: 0.8rem;
                height: 0.8rem;
                margin-top: 0.38rem;
                border-radius: 999px;
                background: #d97706;
                box-shadow: 0 0 0 0.32rem rgba(251, 191, 36, 0.22);
                flex: 0 0 auto;
            }

            .cta-row {
                margin-top: 2rem;
                display: flex;
                flex-wrap: wrap;
                gap: 0.85rem;
            }

            .button {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 3rem;
                padding: 0.75rem 1.25rem;
                border-radius: 999px;
                border: 1px solid #1c1917;
                text-decoration: none;
                font-weight: 600;
                transition: transform 140ms ease, box-shadow 140ms ease, background 140ms ease;
            }

            .button:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 24px -18px rgba(28, 25, 23, 0.45);
            }

            .button-primary {
                background: #1c1917;
                color: #fff;
            }

            .button-secondary {
                background: rgba(255, 255, 255, 0.7);
                color: #1c1917;
            }

            .brand-lockup {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .brand-icon {
                width: 4rem;
                height: 4rem;
                border-radius: 1.25rem;
                background: rgba(255, 255, 255, 0.85);
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: inset 0 0 0 1px rgba(28, 25, 23, 0.08);
            }

            .brand-name {
                margin: 0;
                font-size: 1.5rem;
                font-weight: 700;
            }

            .brand-subtitle {
                margin: 0.3rem 0 0;
                color: #57534e;
                line-height: 1.6;
            }

            .side-card {
                border-radius: 1.5rem;
                background: rgba(255, 255, 255, 0.72);
                padding: 1.1rem 1.15rem;
                box-shadow: inset 0 0 0 1px rgba(28, 25, 23, 0.08);
            }

            .side-card h2 {
                margin: 0 0 0.45rem;
                font-size: 1rem;
            }

            .side-card p {
                margin: 0;
                color: #57534e;
                line-height: 1.65;
            }

            @media (max-width: 860px) {
                .welcome-card {
                    grid-template-columns: 1fr;
                }

                .welcome-copy,
                .welcome-side {
                    padding: 1.5rem;
                }
            }
        </style>
    </head>
    <body>
        <main class="welcome-shell">
            <section class="welcome-card">
                <div class="welcome-copy">
                    <p class="eyebrow">Wollradar</p>
                    <h1>Wollvorräte mobil erfassen, finden und abschließen.</h1>
                    <p class="lead">
                        Wollradar ist auf kleine Displays ausgelegt: Garne mit Foto erfassen, Projekte verknüpfen,
                        Mengen direkt anpassen und auch unterwegs sauber weiterarbeiten.
                    </p>

                    <ul class="feature-list">
                        <li>
                            <span class="feature-dot"></span>
                            <span>Schnelle Erfassung von Garn, Farbe, Material, Marke, Ort und Projekt direkt im Flow.</span>
                        </li>
                        <li>
                            <span class="feature-dot"></span>
                            <span>Mobile Kartenansichten, große Touch-Ziele, PWA-Installierbarkeit und Offline-Hinweise.</span>
                        </li>
                        <li>
                            <span class="feature-dot"></span>
                            <span>Lokale Formular-Entwürfe, Bildvorschau und kompakter Bestandsüberblick.</span>
                        </li>
                    </ul>

                    <div class="cta-row">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="button button-primary">Zum Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="button button-primary">Anmelden</a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="button button-secondary">Registrieren</a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>

                <aside class="welcome-side">
                    <div class="brand-lockup">
                        <div class="brand-icon">
                            <x-application-logo class="h-8 w-8 fill-current text-amber-600" />
                        </div>
                        <div>
                            <p class="brand-name">{{ config('app.name', 'Wollradar') }}</p>
                            <p class="brand-subtitle">Bestand, Projekte und Fotos in einer mobilen WebApp.</p>
                        </div>
                    </div>

                    <div class="side-card">
                        <h2>Für den Alltag gedacht</h2>
                        <p>
                            Statt Tabellenchaos auf dem Handy bekommst du fokussierte Eingaben, Suchfilter und klare
                            Aktionen direkt dort, wo du arbeitest.
                        </p>
                    </div>

                    <div class="side-card">
                        <h2>Auch später konsistent</h2>
                        <p>
                            Die Oberfläche, Formularlabels, Statusmeldungen und Standard-Validierungen laufen jetzt
                            durchgehend auf Deutsch.
                        </p>
                    </div>
                </aside>
            </section>
        </main>
    </body>
</html>
