<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Datenschutzerklärung | {{ config('app.name', 'Wollradar') }}</title>
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
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-stone-950">Datenschutzerklärung</h1>

                <div class="mt-6 rounded-[24px] border border-amber-200 bg-amber-50 px-5 py-4 text-stone-800">
                    <p class="font-semibold text-stone-950">Privates Hobbyprojekt ohne kommerzielle Interessen</p>
                    <p class="mt-2 leading-7">
                        Wollradar wird als privates Hobbyprojekt betrieben. Es werden keine finanziellen Interessen verfolgt.
                        Personenbezogene Daten werden nur insoweit verarbeitet, wie dies für Registrierung, Anmeldung, Nutzung der App,
                        Sicherheit und technische Bereitstellung erforderlich ist.
                    </p>
                </div>

                <div class="mt-8 space-y-8 leading-7 text-stone-700">
                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">1. Verantwortlicher</h2>
                        <div class="mt-3 rounded-2xl bg-stone-50 px-4 py-4">
                            <p>[Vorname Nachname]</p>
                            <p>[Straße Hausnummer]</p>
                            <p>[PLZ Ort]</p>
                            <p>Deutschland</p>
                            <p class="mt-3">E-Mail: [deine-e-mail@domain.de]</p>
                        </div>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">2. Zweck des Angebots</h2>
                        <p>
                            Wollradar dient der privaten Organisation von Garnen, Projekten und zugehörigen Informationen. Nutzer können ein
                            Konto anlegen, eigene Inhalte speichern und diese innerhalb der Anwendung verwalten.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">3. Verarbeitung bei Aufruf der Website</h2>
                        <p>
                            Beim Besuch der Website werden technisch erforderliche Daten verarbeitet, insbesondere IP-Adresse, Datum und Uhrzeit,
                            Browser- und Systeminformationen, aufgerufene Seiten sowie Server-Logdaten. Diese Verarbeitung ist erforderlich, um
                            die Website sicher und stabil bereitzustellen.
                        </p>
                        <p class="mt-2"><span class="font-medium text-stone-950">Rechtsgrundlage:</span> Art. 6 Abs. 1 lit. f DSGVO</p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">4. Registrierung und Benutzerkonto</h2>
                        <p>
                            Bei der Registrierung werden Name, E-Mail-Adresse und Passwort verarbeitet. Passwörter werden nicht im Klartext
                            gespeichert, sondern nur in verschlüsselter Form. Zusätzlich wird ein Kontostatus verarbeitet, etwa ausstehend,
                            freigegeben, abgelehnt oder deaktiviert.
                        </p>
                        <p class="mt-2"><span class="font-medium text-stone-950">Rechtsgrundlage:</span> Art. 6 Abs. 1 lit. b DSGVO</p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">5. Nutzungsdaten innerhalb der App</h2>
                        <p>
                            Im Rahmen der Nutzung können insbesondere Garndaten, Projektdaten, Farben, Materialien, Marken, Orte, Notizen und
                            hochgeladene Bilder gespeichert werden. Diese Daten dienen ausschließlich der Bereitstellung der App-Funktionen.
                        </p>
                        <p class="mt-2"><span class="font-medium text-stone-950">Rechtsgrundlage:</span> Art. 6 Abs. 1 lit. b DSGVO</p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">6. E-Mails und systembezogene Benachrichtigungen</h2>
                        <p>
                            Für Registrierung, Freigabe, Ablehnung, Passwort-Zurücksetzung und ähnliche Systemvorgänge können E-Mails versendet
                            werden. Diese Benachrichtigungen dienen ausschließlich dem Betrieb und der Zugangskontrolle.
                        </p>
                        <p class="mt-2"><span class="font-medium text-stone-950">Rechtsgrundlage:</span> Art. 6 Abs. 1 lit. b DSGVO, hilfsweise Art. 6 Abs. 1 lit. f DSGVO</p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">7. Cookies, Session-Daten und lokale Speicherung</h2>
                        <p>
                            Die Anwendung verwendet technisch erforderliche Cookies und Session-Daten für Login, Sicherheit und Betrieb.
                            Zusätzlich können lokal auf dem Endgerät funktionale Daten gespeichert werden, etwa Formularentwürfe,
                            Installationshinweise oder Offline-Zwischenspeicher für die Web-App.
                        </p>
                        <p class="mt-2"><span class="font-medium text-stone-950">Rechtsgrundlage:</span> Art. 6 Abs. 1 lit. f DSGVO</p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">8. Hosting und technische Dienstleister</h2>
                        <p>
                            Die Website wird bei einem Hosting-Anbieter betrieben. Soweit für Betrieb oder Mailversand erforderlich, erhalten
                            technische Dienstleister Zugriff auf die dafür notwendigen Daten. Eine darüber hinausgehende Weitergabe an Dritte
                            erfolgt nicht, sofern keine gesetzliche Verpflichtung besteht.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">9. Speicherdauer</h2>
                        <p>
                            Personenbezogene Daten werden nur so lange gespeichert, wie dies für die Nutzung der Anwendung, die technische
                            Sicherheit oder gesetzliche Aufbewahrungspflichten erforderlich ist. Kontodaten und nutzerbezogene Inhalte bleiben
                            grundsätzlich gespeichert, solange das Benutzerkonto besteht.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">10. Rechte der betroffenen Personen</h2>
                        <p>Es bestehen insbesondere folgende Rechte nach Maßgabe der DSGVO:</p>
                        <ul class="mt-3 list-disc space-y-1 pl-5">
                            <li>Auskunft über gespeicherte personenbezogene Daten</li>
                            <li>Berichtigung unrichtiger Daten</li>
                            <li>Löschung oder Einschränkung der Verarbeitung</li>
                            <li>Datenübertragbarkeit</li>
                            <li>Widerspruch gegen Verarbeitungen auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO</li>
                            <li>Beschwerde bei einer Datenschutzaufsichtsbehörde</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">11. Keine automatisierte Entscheidungsfindung</h2>
                        <p>
                            Eine automatisierte Entscheidungsfindung einschließlich Profiling im Sinne von Art. 22 DSGVO findet nicht statt.
                        </p>
                    </section>

                    <section>
                        <h2 class="text-lg font-semibold text-stone-950">12. Hinweis zum Charakter des Projekts</h2>
                        <p>
                            Wollradar ist ein privates Hobbyprojekt ohne kommerzielle Zielsetzung. Trotz sorgfältiger Entwicklung wird die
                            Anwendung, soweit gesetzlich zulässig, ohne Gewähr, Garantie oder Zusicherung einer bestimmten Verfügbarkeit,
                            Fehlerfreiheit oder Eignung für einen bestimmten Zweck bereitgestellt.
                        </p>
                    </section>
                </div>
            </div>
        </main>
    </body>
</html>
