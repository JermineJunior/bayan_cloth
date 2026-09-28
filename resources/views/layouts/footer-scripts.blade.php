<!-- Back-to-top -->
<a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>
<!-- JQuery min js -->
<script src="{{ URL::asset('assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap Bundle js -->
<script src="{{ URL::asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- Ionicons js -->
<script src="{{ URL::asset('assets/plugins/ionicons/ionicons.js') }}"></script>
<!-- Moment js -->
<script src="{{ URL::asset('assets/plugins/moment/moment.js') }}"></script>

<!-- Rating js-->
<script src="{{ URL::asset('assets/plugins/rating/jquery.rating-stars.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/rating/jquery.barrating.js') }}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{ URL::asset('assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>
<!--Internal Sparkline js -->
<script src="{{ URL::asset('assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
<!-- Custom Scroll bar Js-->
<script src="{{ URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.concat.min.js') }}"></script>
<!-- right-sidebar js -->
<script src="{{ URL::asset('assets/plugins/sidebar/sidebar-rtl.js') }}"></script>
<script src="{{ URL::asset('assets/plugins/sidebar/sidebar-custom.js') }}"></script>
<!-- Eva-icons js -->
<script src="{{ URL::asset('assets/js/eva-icons.min.js') }}"></script>

<!-- Sticky js -->
<script src="{{ URL::asset('assets/js/sticky.js') }}"></script>
<!-- custom js -->
<script src="{{ URL::asset('assets/js/custom.js') }}"></script><!-- Left-menu js-->
<script src="{{ URL::asset('assets/plugins/side-menu/sidemenu.js') }}"></script>

{{-- شريط تمرير الصفحة على يسار الصفحة، بنفس مظهر شريط الشريط الجانبي.
     يستخدم CSS/JS مستقل لأن Chromium على النظام يضع شريط الصفحة الأصلي
     على اليمين بغض النظر عن خاصية direction. --}}
<style>
    #pageScrollbar {
        position: fixed;
        top: 0;
        left: 0;
        width: 5px;
        height: 100vh;
        z-index: 1040;
        display: none;
        opacity: 0;
        transition: opacity 0.25s ease;
    }

    #pageScrollbar.is-visible {
        display: block;
    }

    #pageScrollbar.is-hover {
        opacity: 1;
    }

    #pageScrollbar .ps-thumb {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        background: #0162e8;
        border-radius: 3px;
        transition: background 0.2s;
        cursor: pointer;
    }

    #pageScrollbar.is-hover .ps-thumb {
        background: #2b7fff;
    }
</style>
<div id="pageScrollbar" aria-hidden="true"><div class="ps-thumb"></div></div>
<script>
    (function () {
        var bar = document.getElementById('pageScrollbar');
        var thumb = bar && bar.querySelector('.ps-thumb');
        if (!bar || !thumb) {
            return;
        }

        var dragging = false;
        var startY = 0;
        var startScroll = 0;

        function maxScroll() {
            return Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
        }

        function update() {
            var max = maxScroll();

            if (max <= 0) {
                bar.classList.remove('is-visible');
                return;
            }

            bar.classList.add('is-visible');

            var track = bar.clientHeight;
            var ratio = window.innerHeight / document.documentElement.scrollHeight;
            var height = Math.max(40, Math.round(track * ratio));
            var top = height >= track ? 0 : Math.round((track - height) * (window.scrollY / max));

            thumb.style.height = height + 'px';
            thumb.style.transform = 'translateY(' + top + 'px)';
        }

        thumb.addEventListener('mousedown', function (event) {
            dragging = true;
            startY = event.clientY;
            startScroll = window.scrollY;
            document.body.style.userSelect = 'none';
            event.preventDefault();
        });

        window.addEventListener('mousemove', function (event) {
            if (!dragging) {
                return;
            }

            var track = bar.clientHeight;
            var height = thumb.offsetHeight;
            var max = maxScroll();

            if (track <= height || max <= 0) {
                return;
            }

            window.scrollTo(0, startScroll + (event.clientY - startY) * (max / (track - height)));
        });

        window.addEventListener('mouseup', function () {
            dragging = false;
            document.body.style.userSelect = '';
        });

        bar.addEventListener('click', function (event) {
            if (event.target === thumb) {
                return;
            }

            var rect = bar.getBoundingClientRect();
            window.scrollTo(0, ((event.clientY - rect.top) / rect.height) * maxScroll());
        });

        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);

        // الشريط يظهر عند مرور المؤشر على الصفحة فقط، ويختفي عند خروجه.
        // mousemove أيضاً (إضافة إلى mouseenter) لأن mouseenter لا يُطلق إذا
        // كان المؤشر داخل الصفحة لحظة تحميلها.
        document.addEventListener('mouseenter', function () {
            bar.classList.add('is-hover');
        });

        document.addEventListener('mousemove', function () {
            bar.classList.add('is-hover');
        });

        document.addEventListener('mouseleave', function () {
            bar.classList.remove('is-hover');
        });

        if (window.ResizeObserver) {
            new ResizeObserver(update).observe(document.body);
        }

        update();
    })();
</script>

@yield('js')

<script>
    // العثور على جميع الحقول النصية التي تستخدم CKEditor وتطبيق التعطيل عليها
    // document.querySelectorAll('textarea').forEach(function(textarea) {
    //     CKEDITOR.replace(textarea.id);
    // });
</script>
