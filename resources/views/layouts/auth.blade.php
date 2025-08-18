<!doctype html>
<html lang="es">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Autenticación') - {{ config('app.name', 'Laravel') }}</title>

        {{-- Vite: CSS/JS locales (sin CDNs) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root { --auth-overlay: rgba(0,0,0,.45); }

            html, body { height: 100%; margin: 0; }
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; overflow-y: auto; }

            /* Fondo fijo y contenido desplazable (mejor mobile) */
            #background-layer { position: fixed; inset: 0; background-size: cover; background-position: center; z-index: 0; opacity: 1; transition: opacity .9s ease-in-out; will-change: opacity, background-image; }
            .overlay { position: fixed; inset: 0; background: var(--auth-overlay); z-index: 1; backdrop-filter: blur(1.5px); }

            .auth-wrapper { position: relative; z-index: 2; min-height: 100dvh; display: flex; align-items: center; justify-content: center; padding: 20px 14px; }

            .auth-card { background: rgba(255,255,255,.96); border-radius: 16px; box-shadow: 0 12px 40px rgba(0,0,0,.25); width: 100%; max-width: 560px; padding: 24px; animation: slideUp .35s ease-out both; }
            @keyframes slideUp { from{ transform: translateY(10px); opacity: 0; } to{ transform: translateY(0); opacity: 1; } }

            .brand-img { display:block; margin:0 auto 12px; max-height: 84px; }
            .auth-title { text-align:center; font-weight: 700; color:#0d6efd; margin-bottom: 6px; }
            .auth-subtitle { text-align:center; color:#6c757d; margin-bottom: 16px; font-size: .95rem; }

            .form-control, .form-select { border-radius: 10px; }
            .input-group-text { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
            .btn-primary { border-radius: 10px; font-weight: 600; }

            .caps-hint { font-size: .85rem; color:#d63384; display:none; margin-top: .25rem; }
            .footer-note { text-align:center; font-size: .8rem; color:#6c757d; margin-top: 10px; }

            /* Selector de tipo de usuario responsive */
            .user-type .btn { width: 100%; padding: 10px 12px; }

            @media (max-width: 575.98px) {
            .auth-card { padding: 18px; border-radius: 14px; max-width: 94vw; }
            .brand-img { max-height: 64px; }
            .auth-title { font-size: 1.25rem; }
            .auth-subtitle { font-size: .9rem; }
            }

            @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
            }
        </style>

        
        @stack('scripts')
    </head>

    <body>

        <div id="background-layer" role="img" aria-label="Imagen de fondo"></div>
        <div class="overlay"></div>

        <div class="auth-wrapper">
            <div class="auth-card">
            @yield('card')
            <div class="footer-note">Servicio Nacional de Migración © <span id="year"></span> — {{ config('app.name') }}</div>
            </div>
        </div>

        {{-- Bootstrap/CoreUI/Icons y jQuery se cargan desde resources/js/app.js, no uses CDNs aquí --}}
        <script>
            // Fondo rotativo
            (function(){
            const images = [
                "{{ asset('images/background_1.jpg') }}",
                "{{ asset('images/background_2.jpg') }}",
                "{{ asset('images/background_3.jpg') }}",
                "{{ asset('images/background_4.jpg') }}"
            ].filter(Boolean);
            const bg = document.getElementById('background-layer');
            let i = 0; function set(idx){ bg.style.backgroundImage = `url('${images[idx]}')`; }
            if (images.length) set(0);
            setInterval(()=>{ if(!images.length) return; i = (i+1)%images.length; bg.style.opacity=0; setTimeout(()=>{ set(i); bg.style.opacity=1; }, 900); }, 6000);
            })();
            document.getElementById('year').textContent = new Date().getFullYear();
        </script>

        @yield('modals')
        
        @stack('scripts')

    </body>
</html>
