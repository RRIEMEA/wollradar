<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Impressum | {{ config('app.name', 'Wollradar') }}</title>
        @include('layouts.vite-assets')
    </head>
    <body class="min-h-screen bg-stone-50 text-stone-900">
        <main class="mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="mb-6 flex items-center justify-between gap-4">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="inline-flex items-center rounded-full border border-stone-200 bg-white px-4 py-2 text-sm font-medium text-stone-700 transition hover:bg-stone-100 hover:text-stone-900">
                    Zurück
                </a>
                <x-application-wordmark class="block h-10 w-auto max-w-[12rem]" />
            </div>

            <div class="rounded-[32px] border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-700">Rechtliches</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Impressum</h1>

                <div class="mt-6 rounded-[24px] border border-amber-200 bg-amber-50 px-5 py-4 text-stone-800">
                    <p class="font-semibold text-stone-950">Privates Hobbyprojekt</p>
                    <p class="mt-2 leading-7">
                        Wollradar ist ein privates, nicht-kommerzielles Hobbyprojekt. Das Angebot wird ohne finanzielle Interessen betrieben.
                        Es werden keine Waren oder Dienstleistungen verkauft. Soweit gesetzlich zulässig, erfolgt die Nutzung ohne Gewähr,
                        Garantie oder Zusicherung einer bestimmten Verfügbarkeit, Fehlerfreiheit oder Eignung für einen bestimmten Zweck.
                    </p>
                </div>

                <section class="mt-8 space-y-6 leading-7 text-stone-700">
                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Angaben gemäß § 5 DDG</h2>
                        <div class="mt-3 rounded-2xl bg-stone-50 px-4 py-4">
                            <p>[Vorname Nachname]</p>
                            <p>[Straße Hausnummer]</p>
                            <p>[PLZ Ort]</p>
                            <p>Deutschland</p>
                            <p class="mt-3">E-Mail: [deine-e-mail@domain.de]</p>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Verantwortlich für den Inhalt</h2>
                        <p>
                            Verantwortlich für dieses Angebot ist die oben genannte Person.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Inhalte dieses Angebots</h2>
                        <p>
                            Die Inhalte dieser Website wurden mit Sorgfalt erstellt. Dennoch wird, soweit gesetzlich zulässig, keine Gewähr,
                            Garantie oder Haftung für Richtigkeit, Vollständigkeit, Aktualität oder Verfügbarkeit übernommen. Unberührt bleiben
                            Ansprüche aus Vorsatz, grober Fahrlässigkeit sowie aus der Verletzung von Leben, Körper oder Gesundheit und sonstige
                            zwingende gesetzliche Ansprüche.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Haftung für externe Links</h2>
                        <p>
                            Diese Website kann Links zu externen Angeboten Dritter enthalten. Auf deren Inhalte besteht kein Einfluss.
                            Für die Inhalte verlinkter Seiten ist stets der jeweilige Anbieter oder Betreiber verantwortlich.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Urheberrecht</h2>
                        <p>
                            Soweit nicht anders gekennzeichnet, unterliegen die auf dieser Website veröffentlichten Inhalte und Werke dem
                            geltenden Urheberrecht. Jede Nutzung außerhalb der gesetzlich zulässigen Grenzen bedarf der vorherigen Zustimmung.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-semibold text-stone-950">Hinweis bei Beanstandungen</h2>
                        <p>
                            Sollte dieses Angebot Rechte Dritter oder gesetzliche Vorschriften verletzen, wird um eine entsprechende Nachricht
                            gebeten. Berechtigte Beanstandungen werden geprüft und gegebenenfalls zügig angepasst.
                        </p>
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
