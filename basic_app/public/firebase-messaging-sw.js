/* global importScripts, firebase */
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js');

// IMPORTANT: these values come from your .env (they’re duplicated here because SW can’t read Blade)
firebase.initializeApp({
  apiKey: "YOUR_API_KEY",
  authDomain: "YOUR_PROJECT.firebaseapp.com",
  projectId: "YOUR_PROJECT_ID",
  storageBucket: "YOUR_PROJECT.appspot.com",
  messagingSenderId: "YOUR_SENDER_ID",
  appId: "YOUR_APP_ID",
});

const messaging = firebase.messaging();

// Background handler (when tab is hidden or closed)
messaging.onBackgroundMessage((payload) => {
  const n = payload.notification || {};
  const data = payload.data || {};
  self.registration.showNotification(n.title || 'Update', {
    body: n.body || '',
    icon: n.icon || '/favicon.ico',
    data: { click_action: data.click_action || '/' },
  });
});

self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const url = (event.notification.data && event.notification.data.click_action) || '/';
  event.waitUntil(clients.matchAll({ type: 'window', includeUncontrolled: true }).then(clientsArr => {
    for (const c of clientsArr) {
      if (c.url.includes(new URL(url, self.location.origin).pathname) && 'focus' in c) return c.focus();
    }
    if (clients.openWindow) return clients.openWindow(url);
  }));
});
