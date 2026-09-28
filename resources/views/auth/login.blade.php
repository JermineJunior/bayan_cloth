@extends('layouts.login')

@section('title', 'تسجيل الدخول')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* هذا القسم موضوع داخل المحتوى حتى يأتي بعد أنماط القالب
           المحمّلة في الترويسة، فيتغلّب عليها دون الحاجة إلى !important */
        .login-page {
            --lp-primary: #1d4ed8;
            --lp-primary-strong: #1e3a8a;
            --lp-accent: #06b6d4;
            --lp-surface: #ffffff;
            --lp-bg: #eef2f9;
            --lp-border: #dbe3ef;
            --lp-text: #0f172a;
            --lp-muted: #64748b;
            --lp-heading: #0b2545;
            --lp-danger: #dc2626;
        }

        /* إخفاء علامة "main" الكبيرة التي يرسمها القالب في الخلفية */
        .main-body::before {
            content: none !important;
        }

        /* القالب يضع background-color !important على body عبر bg-primary-transparent */
        .main-body {
            background: var(--lp-bg, #eef2f9) !important;
        }

        .lp-split {
            display: flex;
            min-height: 100vh;
            font-family: 'Cairo', 'Noto Kufi Arabic', sans-serif;
        }

        /* ---------- جانب النموذج ---------- */
        .lp-form-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            background: var(--lp-bg);
        }

        .lp-card {
            width: 100%;
            max-width: 420px;
            background: var(--lp-surface);
            border: 1px solid var(--lp-border);
            border-radius: 22px;
            padding: 2.5rem 2rem;
            box-shadow: 0 22px 55px rgba(11, 37, 69, 0.10);
            text-align: center;
            animation: lpFade .6s ease;
        }

        @keyframes lpFade {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .lp-logo img {
            width: 118px;
            max-height: 78px;
            object-fit: contain;
            margin-bottom: 14px;
        }

        .lp-card h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--lp-heading);
            margin: 8px 0 6px;
        }

        .lp-sub {
            color: var(--lp-muted);
            font-size: 14px;
            margin-bottom: 26px;
        }

        .lp-field {
            margin-bottom: 18px;
            text-align: right;
        }

        .lp-field label {
            display: block;
            text-align: right;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 7px;
            color: var(--lp-text);
        }

        .lp-input {
            position: relative;
        }

        .lp-input input {
            width: 100%;
            padding: 12px 44px;
            border: 1px solid var(--lp-border);
            border-radius: 10px;
            background: var(--lp-surface);
            color: var(--lp-text);
            font-family: inherit;
            font-size: 15px;
            outline: none;
            transition: border-color .25s ease, box-shadow .25s ease;
        }

        .lp-input input::placeholder {
            color: var(--lp-muted);
            opacity: .75;
        }

        .lp-input input:focus {
            border-color: var(--lp-primary);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, .16);
        }

        .lp-input input.is-invalid {
            border-color: var(--lp-danger);
        }

        .lp-icon {
            position: absolute;
            top: 50%;
            inset-inline-start: 14px;
            transform: translateY(-50%);
            color: var(--lp-muted);
            display: flex;
            align-items: center;
            pointer-events: none;
        }

        .lp-toggle-pass {
            position: absolute;
            top: 50%;
            inset-inline-end: 10px;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--lp-muted);
            display: flex;
            align-items: center;
            padding: 4px;
            border-radius: 6px;
            transition: color .2s ease;
        }

        .lp-toggle-pass:hover {
            color: var(--lp-primary);
        }

        .lp-error {
            display: block;
            margin-top: 7px;
            font-size: 13px;
            color: var(--lp-danger);
            text-align: right;
            animation: lpShake .3s;
        }

        @keyframes lpShake {
            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-3px);
            }

            75% {
                transform: translateX(3px);
            }
        }

        .lp-options {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            font-size: 14px;
            margin-bottom: 22px;
        }

        .lp-options label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            font-weight: 500;
            cursor: pointer;
            color: var(--lp-text);
        }

        .lp-options input[type="checkbox"] {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: var(--lp-primary);
        }

        .lp-btn {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, var(--lp-primary-strong), var(--lp-primary));
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
        }

        .lp-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(29, 78, 216, .32);
            filter: brightness(1.06);
        }

        .lp-btn:active {
            transform: translateY(0);
        }

        /* ---------- الجانب الزخرفي ---------- */
        .lp-aside {
            flex: 1.05;
            position: relative;
            overflow: hidden;
            color: #fff;
            /* تدرّج مختلف عن التصميم المرجعي (أخضر/كهرماني) */
            background: linear-gradient(155deg, #0b2545 0%, #1d4ed8 52%, #06b6d4 100%);
            display: flex;
            flex-direction: column;
            padding: 3rem 2.75rem;
        }

        .lp-pattern {
            position: absolute;
            inset: 0;
            background-image:
                repeating-linear-gradient(45deg, rgba(255, 255, 255, .07) 0 1px, transparent 1px 26px),
                repeating-linear-gradient(-45deg, rgba(255, 255, 255, .07) 0 1px, transparent 1px 26px);
            mask-image: linear-gradient(180deg, rgba(0, 0, 0, .9), rgba(0, 0, 0, .35));
            -webkit-mask-image: linear-gradient(180deg, rgba(0, 0, 0, .9), rgba(0, 0, 0, .35));
        }

        .lp-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(95px);
            opacity: .55;
            pointer-events: none;
        }

        .lp-blob-1 {
            width: 430px;
            height: 430px;
            background: rgba(56, 189, 248, .45);
            top: -140px;
            inset-inline-start: -140px;
        }

        .lp-blob-2 {
            width: 390px;
            height: 390px;
            background: rgba(99, 102, 241, .45);
            bottom: -130px;
            inset-inline-end: -130px;
        }

        .lp-ring {
            position: absolute;
            border: 1.5px solid rgba(255, 255, 255, .18);
            border-radius: 50%;
            pointer-events: none;
        }

        .lp-ring-1 {
            width: 330px;
            height: 330px;
            inset-inline-end: -95px;
            top: 15%;
        }

        .lp-ring-2 {
            width: 205px;
            height: 205px;
            inset-inline-end: -26px;
            top: 30%;
        }

        .lp-aside-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .lp-aside-top {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .lp-aside-logo {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, .95);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 7px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, .18);
        }

        .lp-aside-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .lp-aside-brand h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .lp-aside-brand p {
            font-size: 13px;
            margin: 2px 0 0;
            opacity: .85;
        }

        .lp-aside-hero h3 {
            font-size: 30px;
            font-weight: 700;
            line-height: 1.5;
            margin: 0;
        }

        .lp-aside-hero>p {
            margin-top: 12px;
            opacity: .9;
            line-height: 1.9;
            max-width: 430px;
            font-size: 15px;
        }

        .lp-feats {
            list-style: none;
            margin: 28px 0 0;
            padding: 0;
            display: grid;
            gap: 14px;
            max-width: 410px;
        }

        .lp-feats li {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 12px;
            padding: 10px 14px;
            backdrop-filter: blur(4px);
        }

        .lp-tick {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lp-foot {
            font-size: 13px;
            opacity: .75;
            margin: 0;
        }

        @media (max-width: 991.98px) {
            .lp-split {
                flex-direction: column;
            }

            .lp-aside {
                display: none;
            }

            .lp-form-side {
                padding: 1.5rem;
            }
        }
    </style>

    <div class="lp-split login-page">

        {{-- جانب النموذج --}}
        <div class="lp-form-side">
            <div class="lp-card">
                <div class="lp-logo">
                    <img
                        src="{{ GeneralSiteSettings('logo') ? asset('image/logo/' . GeneralSiteSettings('logo')) : asset('image/logo/bayan.jpg') }}"
                        alt="شعار {{ GeneralSiteSettings('name') }}">
                </div>

                <h1>تسجيل الدخول</h1>
                <p class="lp-sub">مرحباً بك في {{ GeneralSiteSettings('name') }}</p>

                <form id="loginForm" action="{{ route('login') }}" method="POST" novalidate>
                    @csrf

                    <div class="lp-field">
                        <label for="username">البريد الإلكتروني أو رقم التلفون</label>
                        <div class="lp-input">
                            <input type="text" id="username" name="username" value="{{ old('username') }}"
                                class="@error('username') is-invalid @enderror" placeholder="أدخل البريد الإلكتروني أو رقم التلفون"
                                required autocomplete="username" autofocus>
                            <span class="lp-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </span>
                        </div>
                        @error('username')
                            <span class="lp-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="lp-field">
                        <label for="password">كلمة المرور</label>
                        <div class="lp-input">
                            <input type="password" id="password" name="password"
                                class="@error('password') is-invalid @enderror" placeholder="أدخل كلمة المرور" required
                                autocomplete="current-password">
                            <button type="button" class="lp-toggle-pass" aria-label="إظهار كلمة المرور"
                                title="إظهار كلمة المرور">
                                <svg id="lpEyeOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg id="lpEyeClosed" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" style="display:none">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94" />
                                    <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19" />
                                    <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                            <span class="lp-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" />
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                                </svg>
                            </span>
                        </div>
                        @error('password')
                            <span class="lp-error" role="alert">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="lp-options">
                        <label>
                            <input type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            تذكرني
                        </label>
                    </div>

                    <button type="submit" class="lp-btn">تسجيل الدخول</button>
                </form>
            </div>
        </div>

        {{-- الجانب الزخرفي --}}
        <div class="lp-aside">
            <div class="lp-pattern"></div>
            <div class="lp-blob lp-blob-1"></div>
            <div class="lp-blob lp-blob-2"></div>
            <div class="lp-ring lp-ring-1"></div>
            <div class="lp-ring lp-ring-2"></div>

            <div class="lp-aside-inner">
                <div class="lp-aside-top">
                    <span class="lp-aside-logo">
                        <img
                            src="{{ GeneralSiteSettings('logo') ? asset('image/logo/' . GeneralSiteSettings('logo')) : asset('image/logo/bayan.jpg') }}"
                            alt="شعار">
                    </span>
                    <div class="lp-aside-brand">
                        <h2>{{ GeneralSiteSettings('name') }}</h2>
                        <p>منظومة إدارة المبيعات والمخازن</p>
                    </div>
                </div>

                <div class="lp-aside-hero">
                    <h3>نظام متكامل لإدارة المبيعات والمخازن</h3>
                    <p>منصة واحدة لإدارة الفواتير والمخازن والعملاء والموردين، مع تقارير مالية ومحاسبية دقيقة وإدارة كاملة للصلاحيات.</p>

                    <ul class="lp-feats">
                        <li>
                            <span class="lp-tick">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            إدارة الفواتير والمخازن والمخزون بدقة
                        </li>
                        <li>
                            <span class="lp-tick">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            تقارير مالية ومحاسبية لحظية
                        </li>
                        <li>
                            <span class="lp-tick">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            </span>
                            صلاحيات مستخدمين دقيقة وآمنة
                        </li>
                    </ul>
                </div>

                <p class="lp-foot">&copy; جميع الحقوق محفوظة {{ date('Y') }} {{ GeneralSiteSettings('name') }}</p>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        (function() {
            var toggle = document.querySelector('.lp-toggle-pass');
            var password = document.getElementById('password');
            var eyeOpen = document.getElementById('lpEyeOpen');
            var eyeClosed = document.getElementById('lpEyeClosed');

            if (!toggle || !password) {
                return;
            }

            toggle.addEventListener('click', function() {
                var show = password.type === 'password';
                password.type = show ? 'text' : 'password';
                eyeOpen.style.display = show ? 'none' : 'block';
                eyeClosed.style.display = show ? 'block' : 'none';
                toggle.setAttribute('aria-label', show ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
                toggle.setAttribute('title', show ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور');
            });
        })();
    </script>
@endsection
