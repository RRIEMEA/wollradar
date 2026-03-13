import './bootstrap';

import Alpine from 'alpinejs';

const DRAFT_STORAGE_PREFIX = 'wollradar:draft:';
const SCROLL_RESTORE_STORAGE_KEY = 'wollradar:scroll-restore';
const IMAGE_MAX_EDGE = 1600;
const IMAGE_QUALITY = 0.82;
const IMAGE_OPTIMIZE_MIN_BYTES = 350 * 1024;

document.addEventListener('alpine:init', () => {
    const isStandalone = () =>
        window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;

    const installDismissedAt = Number.parseInt(window.localStorage.getItem('wollradar-pwa-dismissed-at') ?? '0', 10);
    const installDismissedRecently = Number.isFinite(installDismissedAt) && Date.now() - installDismissedAt < 1000 * 60 * 60 * 24 * 7;
    const ua = window.navigator.userAgent.toLowerCase();
    const isIpadOsDesktopMode = window.navigator.platform === 'MacIntel' && window.navigator.maxTouchPoints > 1;
    const isIos = /iphone|ipad|ipod/.test(ua) || isIpadOsDesktopMode;
    const isSafari = /safari/.test(ua) && !/crios|fxios|edgios|opr\//.test(ua);
    let refreshing = false;

    Alpine.store('pwa', {
        registration: null,
        deferredPrompt: null,
        canInstall: false,
        standalone: isStandalone(),
        iosDevice: isIos && !isStandalone(),
        ios: isIos && isSafari && !isStandalone(),
        online: window.navigator.onLine,
        installDismissed: installDismissedRecently,
        installHelpVisible: false,
        updateDismissed: false,
        updateAvailable: false,
        get installVisible() {
            return !this.standalone && !this.installDismissed && (this.canInstall || this.iosDevice);
        },
        get installActionVisible() {
            return !this.standalone;
        },
        get statusLabel() {
            return this.online ? 'Online' : 'Offline';
        },
        get visible() {
            return (!this.updateDismissed && this.updateAvailable) || this.installVisible || this.installHelpVisible;
        },
        async promptInstall() {
            if (!this.deferredPrompt) {
                return;
            }

            this.deferredPrompt.prompt();
            await this.deferredPrompt.userChoice;
            this.deferredPrompt = null;
            this.canInstall = false;
        },
        dismissInstall() {
            this.installDismissed = true;
            this.installHelpVisible = false;
            window.localStorage.setItem('wollradar-pwa-dismissed-at', String(Date.now()));
        },
        async openInstallOptions() {
            this.installDismissed = false;
            this.installHelpVisible = true;
            window.localStorage.removeItem('wollradar-pwa-dismissed-at');

            if (this.canInstall && this.deferredPrompt) {
                await this.promptInstall();
            }
        },
        dismissUpdate() {
            this.updateDismissed = true;
        },
        showUpdatePrompt(registration) {
            this.registration = registration;
            this.updateDismissed = false;
            this.updateAvailable = true;
        },
        async refreshApp() {
            const waitingWorker = this.registration?.waiting;

            if (!waitingWorker) {
                return;
            }

            waitingWorker.postMessage({ type: 'SKIP_WAITING' });
        },
    });

    const pwaStore = Alpine.store('pwa');

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        pwaStore.deferredPrompt = event;
        pwaStore.canInstall = true;
    });

    window.addEventListener('appinstalled', () => {
        pwaStore.deferredPrompt = null;
        pwaStore.canInstall = false;
        pwaStore.standalone = true;
        window.localStorage.removeItem('wollradar-pwa-dismissed-at');
    });

    window.addEventListener('online', () => {
        pwaStore.online = true;
    });

    window.addEventListener('offline', () => {
        pwaStore.online = false;
    });

    window.navigator.serviceWorker?.addEventListener('controllerchange', () => {
        if (refreshing) {
            return;
        }

        refreshing = true;
        window.location.reload();
    });
});

window.Alpine = Alpine;

Alpine.start();

initializeScrollRestoration();
initializeDraftPersistence();
initializeImageUploads();

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').then((registration) => {
            const pwaStore = Alpine.store('pwa');
            pwaStore.registration = registration;

            if (registration.waiting) {
                pwaStore.showUpdatePrompt(registration);
            }

            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;

                if (!newWorker) {
                    return;
                }

                newWorker.addEventListener('statechange', () => {
                    if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                        pwaStore.showUpdatePrompt(registration);
                    }
                });
            });
        }).catch((error) => {
            console.error('Service worker registration failed:', error);
        });
    });
}

function initializeImageUploads() {
    const boot = () => {
        document.querySelectorAll('[data-image-upload]').forEach(setupImageUpload);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
        return;
    }

    boot();
}

function initializeScrollRestoration() {
    const boot = () => {
        restorePendingScrollPosition();

        document.querySelectorAll('form[data-preserve-scroll="true"]').forEach((form) => {
            if (form.dataset.scrollRestoreInitialized === 'true') {
                return;
            }

            form.dataset.scrollRestoreInitialized = 'true';
            form.addEventListener('submit', () => {
                window.sessionStorage.setItem(
                    SCROLL_RESTORE_STORAGE_KEY,
                    JSON.stringify({
                        path: currentScrollPath(),
                        y: window.scrollY,
                    })
                );
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
        return;
    }

    boot();
}

function initializeDraftPersistence() {
    const boot = () => {
        clearDraftMarkers();
        document.querySelectorAll('form[data-draft-key]').forEach(setupDraftForm);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
        return;
    }

    boot();
}

function restorePendingScrollPosition() {
    const raw = window.sessionStorage.getItem(SCROLL_RESTORE_STORAGE_KEY);

    if (!raw) {
        return;
    }

    try {
        const state = JSON.parse(raw);

        if (!state || state.path !== currentScrollPath() || typeof state.y !== 'number') {
            window.sessionStorage.removeItem(SCROLL_RESTORE_STORAGE_KEY);
            return;
        }

        window.sessionStorage.removeItem(SCROLL_RESTORE_STORAGE_KEY);
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                window.scrollTo({ top: state.y, left: 0, behavior: 'auto' });
            });
        });
    } catch {
        window.sessionStorage.removeItem(SCROLL_RESTORE_STORAGE_KEY);
    }
}

function currentScrollPath() {
    return `${window.location.pathname}${window.location.search}`;
}

function setupImageUpload(container) {
    if (container.dataset.imageInitialized === 'true') {
        return;
    }

    container.dataset.imageInitialized = 'true';

    const currentUrl = container.dataset.imageCurrentUrl || '';
    const preview = container.querySelector('[data-image-preview]');
    const emptyState = container.querySelector('[data-image-empty]');
    const feedback = container.querySelector('[data-image-feedback]');
    const clearButton = container.querySelector('[data-image-clear]');
    const inputs = {
        camera: container.querySelector('[data-image-input="camera"]'),
        gallery: container.querySelector('[data-image-input="gallery"]'),
    };
    let objectUrl = null;

    Object.entries(inputs).forEach(([mode, input]) => {
        if (!input) {
            return;
        }

        input.addEventListener('change', () => {
            const file = input.files?.[0];

            if (!file) {
                return;
            }

            updateDraftFeedback(
                feedback,
                `${mode === 'camera' ? 'Kamerafoto' : 'Galeriebild'} wird optimiert...`
            );

            Object.entries(inputs).forEach(([otherMode, otherInput]) => {
                if (otherMode !== mode && otherInput) {
                    otherInput.value = '';
                }
            });

            optimizeUploadImage(file).then((result) => {
                const fileForUpload = result.file;

                assignFileToInput(input, fileForUpload);

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                }

                objectUrl = URL.createObjectURL(fileForUpload);
                showImagePreview(preview, emptyState, objectUrl);

                const sizeInfo = `${formatFileSize(fileForUpload.size)}${result.optimized ? ` statt ${formatFileSize(file.size)}` : ''}`;
                updateDraftFeedback(
                    feedback,
                    `${mode === 'camera' ? 'Kamerafoto' : 'Galeriebild'} bereit: ${fileForUpload.name} · ${sizeInfo}`
                );
            }).catch((error) => {
                console.error('Image optimization failed:', error);

                if (objectUrl) {
                    URL.revokeObjectURL(objectUrl);
                }

                objectUrl = URL.createObjectURL(file);
                showImagePreview(preview, emptyState, objectUrl);
                updateDraftFeedback(feedback, `Bildauswahl aktiv: ${file.name}`);
            });
        });
    });

    container.querySelectorAll('[data-image-trigger]').forEach((button) => {
        button.addEventListener('click', () => {
            const mode = button.dataset.imageTrigger;
            inputs[mode]?.click();
        });
    });

    clearButton?.addEventListener('click', () => {
        Object.values(inputs).forEach((input) => {
            if (input) {
                input.value = '';
            }
        });

        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }

        if (currentUrl) {
            showImagePreview(preview, emptyState, currentUrl);
            updateDraftFeedback(feedback, 'Neue Auswahl entfernt. Das aktuell gespeicherte Foto bleibt aktiv.');
            return;
        }

        hideImagePreview(preview, emptyState);
        updateDraftFeedback(feedback, 'Keine Bildauswahl aktiv.');
    });

    if (currentUrl) {
        showImagePreview(preview, emptyState, currentUrl);
        return;
    }

    hideImagePreview(preview, emptyState);
}

async function optimizeUploadImage(file) {
    if (!file.type.startsWith('image/') || file.size < IMAGE_OPTIMIZE_MIN_BYTES) {
        return { file, optimized: false };
    }

    const imageSource = await readImageSource(file);
    const sourceWidth = imageSource.naturalWidth ?? imageSource.width;
    const sourceHeight = imageSource.naturalHeight ?? imageSource.height;
    const { width, height } = scaleImageDimensions(sourceWidth, sourceHeight, IMAGE_MAX_EDGE);
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;

    const context = canvas.getContext('2d', { alpha: false });

    if (!context) {
        return { file, optimized: false };
    }

    context.drawImage(imageSource, 0, 0, width, height);
    closeImageSource(imageSource);

    const preferredType = file.type === 'image/png' ? 'image/png' : 'image/jpeg';
    const optimizedBlob = await canvasToBlob(canvas, preferredType, IMAGE_QUALITY);

    if (!optimizedBlob || optimizedBlob.size >= file.size) {
        return { file, optimized: false };
    }

    const optimizedFile = new File(
        [optimizedBlob],
        renameFileExtension(file.name, preferredType === 'image/png' ? 'png' : 'jpg'),
        {
            type: preferredType,
            lastModified: Date.now(),
        }
    );

    return { file: optimizedFile, optimized: true };
}

async function readImageSource(file) {
    if ('createImageBitmap' in window) {
        return window.createImageBitmap(file);
    }

    const dataUrl = await readFileAsDataUrl(file);
    return loadImageElement(dataUrl);
}

function closeImageSource(imageSource) {
    if (typeof imageSource.close === 'function') {
        imageSource.close();
    }
}

function scaleImageDimensions(width, height, maxEdge) {
    const longestEdge = Math.max(width, height);

    if (longestEdge <= maxEdge) {
        return { width, height };
    }

    const scale = maxEdge / longestEdge;

    return {
        width: Math.round(width * scale),
        height: Math.round(height * scale),
    };
}

function canvasToBlob(canvas, type, quality) {
    return new Promise((resolve) => {
        canvas.toBlob(resolve, type, quality);
    });
}

function readFileAsDataUrl(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = () => reject(reader.error);
        reader.readAsDataURL(file);
    });
}

function loadImageElement(src) {
    return new Promise((resolve, reject) => {
        const image = new Image();
        image.onload = () => resolve(image);
        image.onerror = reject;
        image.src = src;
    });
}

function assignFileToInput(input, file) {
    try {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
    } catch (error) {
        console.error('Assigning optimized image failed:', error);
    }
}

function renameFileExtension(name, extension) {
    const base = name.replace(/\.[^.]+$/, '') || 'photo';
    return `${base}.${extension}`;
}

function formatFileSize(bytes) {
    if (bytes < 1024 * 1024) {
        return `${Math.round(bytes / 1024)} KB`;
    }

    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function clearDraftMarkers() {
    document.querySelectorAll('[data-clear-draft-key]').forEach((node) => {
        try {
            window.localStorage.removeItem(storageKey(node.dataset.clearDraftKey));
        } catch (error) {
            console.error('Draft cleanup failed:', error);
        }
    });
}

function showImagePreview(preview, emptyState, src) {
    if (preview) {
        preview.src = src;
        preview.classList.remove('hidden');
    }

    if (emptyState) {
        emptyState.classList.add('hidden');
    }
}

function hideImagePreview(preview, emptyState) {
    if (preview) {
        preview.src = '';
        preview.classList.add('hidden');
    }

    if (emptyState) {
        emptyState.classList.remove('hidden');
    }
}

function setupDraftForm(form) {
    if (form.dataset.draftInitialized === 'true') {
        return;
    }

    form.dataset.draftInitialized = 'true';

    const key = form.dataset.draftKey;
    const feedback = form.querySelector('[data-draft-feedback]');
    const clearButton = form.querySelector('[data-draft-clear]');
    const persist = debounce(() => saveDraft(form, key, feedback), 250);
    const draft = loadDraft(key);

    if (draft) {
        restoreDraft(form, draft);
        updateDraftFeedback(feedback, `Entwurf wiederhergestellt · ${formatDraftDate(draft.updatedAt)}`);
    }

    form.addEventListener('input', persist);
    form.addEventListener('change', persist);

    clearButton?.addEventListener('click', () => {
        removeDraft(key);
        updateDraftFeedback(feedback, 'Gespeicherter Entwurf geloscht. Aktuelle Eingaben bleiben im Formular.');
    });
}

function saveDraft(form, key, feedback) {
    const payload = serializeDraft(form);

    if (Object.keys(payload.data).length === 0) {
        removeDraft(key);
        updateDraftFeedback(feedback, 'Eingaben werden lokal im Browser zwischengespeichert.');
        return;
    }

    try {
        window.localStorage.setItem(storageKey(key), JSON.stringify(payload));
        updateDraftFeedback(feedback, `Entwurf lokal gespeichert · ${formatDraftDate(payload.updatedAt)}`);
    } catch (error) {
        console.error('Draft save failed:', error);
        updateDraftFeedback(feedback, 'Lokales Speichern war nicht moglich.');
    }
}

function loadDraft(key) {
    try {
        const raw = window.localStorage.getItem(storageKey(key));

        if (!raw) {
            return null;
        }

        const parsed = JSON.parse(raw);
        return parsed && typeof parsed === 'object' && parsed.data ? parsed : null;
    } catch (error) {
        console.error('Draft load failed:', error);
        return null;
    }
}

function removeDraft(key) {
    try {
        window.localStorage.removeItem(storageKey(key));
    } catch (error) {
        console.error('Draft removal failed:', error);
    }
}

function serializeDraft(form) {
    const data = {};

    Array.from(form.elements).forEach((element) => {
        if (!shouldPersistElement(element)) {
            return;
        }

        if (element.type === 'radio') {
            if (element.checked) {
                data[element.name] = element.value;
            }

            return;
        }

        if (element.type === 'checkbox') {
            if (element.checked) {
                data[element.name] = true;
            }

            return;
        }

        if (element instanceof HTMLSelectElement && element.multiple) {
            const values = Array.from(element.selectedOptions).map((option) => option.value).filter(Boolean);

            if (values.length > 0) {
                data[element.name] = values;
            }

            return;
        }

        const value = element.value?.trim?.() ?? element.value;

        if (value !== '') {
            data[element.name] = value;
        }
    });

    return {
        updatedAt: Date.now(),
        data,
    };
}

function restoreDraft(form, draft) {
    Object.entries(draft.data).forEach(([name, storedValue]) => {
        const elements = form.elements.namedItem(name);

        if (!elements) {
            return;
        }

        const fieldList = elements instanceof RadioNodeList ? Array.from(elements) : [elements];

        fieldList.forEach((element) => {
            if (!shouldPersistElement(element)) {
                return;
            }

            if (element.type === 'radio') {
                if (!fieldList.some((radio) => radio.checked) && element.value === storedValue) {
                    element.checked = true;
                }

                return;
            }

            if (element.type === 'checkbox') {
                if (!element.checked && storedValue === true) {
                    element.checked = true;
                }

                return;
            }

            if (element instanceof HTMLSelectElement && element.multiple && Array.isArray(storedValue)) {
                if (Array.from(element.selectedOptions).length === 0) {
                    Array.from(element.options).forEach((option) => {
                        option.selected = storedValue.includes(option.value);
                    });
                }

                return;
            }

            if (fieldHasValue(element)) {
                return;
            }

            element.value = String(storedValue);
        });
    });
}

function shouldPersistElement(element) {
    if (!(element instanceof HTMLElement) || !('name' in element)) {
        return false;
    }

    const ignoredNames = new Set(['_token', '_method']);
    const ignoredTypes = new Set(['hidden', 'password', 'file', 'submit', 'button', 'reset']);

    return Boolean(
        element.name &&
        !element.disabled &&
        !ignoredNames.has(element.name) &&
        !ignoredTypes.has(element.type)
    );
}

function fieldHasValue(element) {
    if (element.type === 'checkbox' || element.type === 'radio') {
        return element.checked;
    }

    if (element instanceof HTMLSelectElement && element.multiple) {
        return Array.from(element.selectedOptions).length > 0;
    }

    return element.value !== '';
}

function updateDraftFeedback(element, message) {
    if (element) {
        element.textContent = message;
    }
}

function formatDraftDate(timestamp) {
    return new Intl.DateTimeFormat('de-DE', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    }).format(new Date(timestamp));
}

function storageKey(key) {
    return `${DRAFT_STORAGE_PREFIX}${key}`;
}

function debounce(fn, delay) {
    let timeoutId;

    return (...args) => {
        window.clearTimeout(timeoutId);
        timeoutId = window.setTimeout(() => fn(...args), delay);
    };
}
