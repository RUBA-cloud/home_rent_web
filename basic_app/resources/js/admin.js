// resources/js/admin.js

// 1) jQuery
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// 2) Bootstrap bundle

// 3) AdminLTE

// 4) bs-custom-file-input
import bsCustomFileInput from 'bs-custom-file-input';
window.bsCustomFileInput = bsCustomFileInput;

// 5) Echo (ينشئ window.Echo)
import './echo';

// 6) Shared subscriptions (ينشئ window.subscribeDashboardBroadcasts)

// DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.bsCustomFileInput?.init) {
        window.bsCustomFileInput.init();
    }

    // نعمل الاشتراك المشترك مرة واحدة
    if (typeof window.subscribeDashboardBroadcasts === 'function') {
        window.subscribeDashboardBroadcasts();
    }
});
