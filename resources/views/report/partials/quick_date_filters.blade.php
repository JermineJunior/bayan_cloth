<div class="mb-2 d-flex flex-wrap gap-1 justify-content-center">
    <button type="button" class="btn btn-outline-primary btn-sm" data-quick-date="yesterday">أمس</button>
    <button type="button" class="btn btn-outline-primary btn-sm" data-quick-date="last30">آخر 30 يوم</button>
    <button type="button" class="btn btn-outline-primary btn-sm" data-quick-date="month">هذا الشهر</button>
    <button type="button" class="btn btn-outline-primary btn-sm" data-quick-date="year">هذا السنة</button>
</div>
<script>
    document.querySelectorAll('[data-quick-date]').forEach(function (btn) {
        if (btn.dataset.quickDateBound) return;
        btn.dataset.quickDateBound = '1';
        btn.addEventListener('click', function () {
            var form = btn.closest('form');
            if (!form) return;
            var start = form.querySelector('input[name="start"]');
            var end = form.querySelector('input[name="end"]');
            if (!start || !end) return;
            var now = new Date();
            var y = now.getFullYear(), m = now.getMonth(), d = now.getDate();
            var pad = function (n) { return String(n).padStart(2, '0'); };
            var fmt = function (dt) { return dt.getFullYear() + '-' + pad(dt.getMonth() + 1) + '-' + pad(dt.getDate()); };
            var range = btn.getAttribute('data-quick-date');
            if (range === 'yesterday') {
                var yest = fmt(new Date(y, m, d - 1));
                start.value = yest;
                end.value = yest;
            } else if (range === 'last30') {
                start.value = fmt(new Date(y, m, d - 29));
                end.value = fmt(now);
            } else if (range === 'month') {
                start.value = fmt(new Date(y, m, 1));
                end.value = fmt(now);
            } else if (range === 'year') {
                start.value = fmt(new Date(y, 0, 1));
                end.value = fmt(now);
            }
        });
    });
</script>
