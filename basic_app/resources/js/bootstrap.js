// resources/js/shared-broadcast.js

/**
 * دالة مشتركة للاشتراك في كل قنوات الداشبورد
 * تُستدعى مرة واحدة من الـ Blade (dashboard-colored.blade.php)
 */
window.subscribeDashboardBroadcasts = function () {
    if (!window.Echo) {
        console.warn('[broadcast] Echo is not available');
        return;
    }

    console.info('[broadcast] Subscribing to shared dashboard channels');

    // مثال: قناة عامة لتحديث معلومات الشركة
    window.Echo.channel('company_info')
        .listen('.CompanyInfoUpdated', (e) => {
            console.log('[company_info] updated event:', e);

            // مثال: عرض Alert بسيط – تقدري تغيريه لـ toastr / SweetAlert
            alert(e.message ?? 'Company info has been updated');
        });

    // مثال: قناة للفروع
    window.Echo.channel('company_branch')
        .listen('.CompanyBranchUpdated', (e) => {
            console.log('[company_branch] updated:', e);
            // ممكن تعملي reload خفيف أو تحديث عبر Livewire
            // location.reload();
        });

    // مثال: قناة للـ categories
    window.Echo.channel('categories')
        .listen('.CategoryUpdated', (e) => {
            console.log('[categories] updated:', e);
        });

    // 🔁 ضيفي هنا كل القنوات/الأحداث اللي تريديها مشتركة لكل الصفحات
};
