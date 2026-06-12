/**
 * ICCBI Alumni — Web Push client
 * Registers the service worker, subscribes with VAPID,
 * and POSTs the subscription to the Laravel backend.
 *
 * Requires VAPID_PUBLIC_KEY to be set in the inline script before this file loads.
 */

// Convert base64 VAPID public key to Uint8Array (required by PushManager)
function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw = atob(base64);
    return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
}

async function registerPush() {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;
    if (typeof VAPID_PUBLIC_KEY === 'undefined' || !VAPID_PUBLIC_KEY) return;

    try {
        const reg = await navigator.serviceWorker.register('/service-worker.js');
        await navigator.serviceWorker.ready;

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;

        const existing = await reg.pushManager.getSubscription();
        if (existing) return; // already subscribed

        const subscription = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY),
        });

        // Send subscription to Laravel backend
        await fetch('/push/subscribe', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(subscription.toJSON()),
        });

    } catch (err) {
        console.warn('Push registration failed:', err);
    }
}

// Register after DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', registerPush);
} else {
    registerPush();
}
