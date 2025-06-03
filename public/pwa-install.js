let deferredPrompt;

function isInStandaloneMode() {
    return (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true);
}

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;

    const installBtn = document.getElementById('pwa-install-btn');
    const pwaSection = document.getElementById('pwa-section');

    if (installBtn) installBtn.style.display = 'block';
    if (pwaSection) pwaSection.classList.remove('hidden');
});

document.addEventListener('DOMContentLoaded', () => {
    const installBtn = document.getElementById('pwa-install-btn');

    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (!deferredPrompt) return;

            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;

            console.log(`User response to the install prompt: ${outcome}`);

            if (outcome === 'accepted') {
                installBtn.style.display = 'none';
                const pwaSection = document.getElementById('pwa-section');
                if (pwaSection) pwaSection.classList.add('hidden');
            }

            deferredPrompt = null;
        });
    }
});

window.addEventListener('appinstalled', () => {
    console.log('PWA was installed');
    const pwaSection = document.getElementById('pwa-section');
    if (pwaSection) pwaSection.classList.add('hidden');
    // your logic to handle the PWA installation
});
