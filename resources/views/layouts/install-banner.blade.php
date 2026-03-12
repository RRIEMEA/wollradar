<div
    x-data
    x-cloak
    x-show="$store.pwa && $store.pwa.visible"
    x-transition.origin.bottom.duration.300ms
    class="pointer-events-none fixed inset-x-0 bottom-0 z-50 px-4 pb-4 sm:px-6"
>
    <div class="mx-auto w-full max-w-lg pointer-events-auto rounded-[28px] border border-amber-200/80 bg-white/95 p-4 shadow-[0_24px_60px_-30px_rgba(28,25,23,0.45)] backdrop-blur">
        <div class="flex items-start gap-3">
            <div class="mt-1 flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-amber-100 text-amber-700">
                <svg x-show="$store.pwa.updateAvailable" viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 12a8 8 0 0 1 13.66-5.66" />
                    <path d="M20 4v6h-6" />
                    <path d="M20 12a8 8 0 0 1-13.66 5.66" />
                    <path d="M4 20v-6h6" />
                </svg>
                <svg x-show="!$store.pwa.updateAvailable" viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 16V4" />
                    <path d="M8 8l4-4 4 4" />
                    <path d="M4 14v3a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-3" />
                </svg>
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-stone-950" x-show="$store.pwa.updateAvailable">Update verfügbar</p>
                        <p class="text-sm font-semibold text-stone-950" x-show="!$store.pwa.updateAvailable">Wollradar als App installieren</p>
                        <p class="mt-1 text-sm leading-6 text-stone-600" x-show="$store.pwa.updateAvailable">
                            Eine neue Version ist geladen. Mit einem kurzen Reload aktivierst du die aktuelle App-Version.
                        </p>
                        <p class="mt-1 text-sm leading-6 text-stone-600" x-show="!$store.pwa.updateAvailable && $store.pwa.canInstall">
                            Starte Wollradar direkt vom Homescreen und nutze die App wie eine native WebApp.
                        </p>
                        <p class="mt-1 text-sm leading-6 text-stone-600" x-show="!$store.pwa.updateAvailable && $store.pwa.ios">
                            In Safari auf <span class="font-medium text-stone-900">Teilen</span> tippen und dann <span class="font-medium text-stone-900">Zum Home-Bildschirm</span> wählen.
                        </p>
                        <p class="mt-1 text-sm leading-6 text-stone-600" x-show="!$store.pwa.updateAvailable && $store.pwa.iosDevice && !$store.pwa.ios">
                            Auf iPhone oder iPad bitte in <span class="font-medium text-stone-900">Safari</span> öffnen. Dort findest du über <span class="font-medium text-stone-900">Teilen</span> die Option <span class="font-medium text-stone-900">Zum Home-Bildschirm</span>.
                        </p>
                        <p class="mt-1 text-sm leading-6 text-stone-600" x-show="!$store.pwa.updateAvailable && !$store.pwa.canInstall && !$store.pwa.iosDevice">
                            In diesem Browser ist keine direkte Installation verfügbar. Nutze dafür Safari auf iPhone/iPad oder einen unterstützten Browser mit Install-Funktion.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="$store.pwa.updateAvailable ? $store.pwa.dismissUpdate() : $store.pwa.dismissInstall()"
                        class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl text-stone-400 transition hover:bg-stone-100 hover:text-stone-700"
                        aria-label="PWA banner schließen"
                    >
                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current" stroke-width="2" stroke-linecap="round">
                            <path d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>

                <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                    <button
                        type="button"
                        @click="$store.pwa.refreshApp()"
                        x-show="$store.pwa.updateAvailable"
                        class="app-button w-full sm:w-auto"
                    >
                        Jetzt aktualisieren
                    </button>

                    <button
                        type="button"
                        @click="$store.pwa.promptInstall()"
                        x-show="!$store.pwa.updateAvailable && $store.pwa.canInstall"
                        class="app-button w-full sm:w-auto"
                    >
                        Jetzt installieren
                    </button>

                    <button
                        type="button"
                        @click="$store.pwa.updateAvailable ? $store.pwa.dismissUpdate() : $store.pwa.dismissInstall()"
                        class="app-button-secondary w-full sm:w-auto"
                    >
                        Später
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
