<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Water Technology BD Ltd</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: url('{{ asset('backend/logo/loginpage_bg.png') }}') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        .wrapper {
            background: rgb(255 255 255 / 18%);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 340px;
            padding: 40px 35px;
            transform: translateX(-17vw);
            transition: transform .3s;
            position: relative;
            z-index: 2;
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo img {
            max-width: 150px;
        }

        /* ===== floating input with fill color + hover/focus effect ===== */

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 12px;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            font-size: 14px;
            background: #eef3f23d;
            outline: none;
            color: #cfd8dc;
            transition: background .25s ease, border-color .25s ease, box-shadow .25s ease, color .25s ease;
        }

        .form-group input:hover {
            background: #e3ede9;
            border-color: #7fb69d;
            color: #7fb69d;
        }

        .form-group input:focus,
        .form-group input:valid {
            background: #ffffff73;
            border-color: #05693a;
            box-shadow: 0 0 0 3px rgba(5, 105, 58, 0.15);
            color: #05693a;
            font-weight: 500;
        }

        .form-group label {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            padding: 0 5px;
            color: #555;
            font-size: 14px;
            transition: .3s;
            pointer-events: none;
        }

        /* ===== button ===== */

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #0077b6, #00b4d8, #90e0ef);
            color: white;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            transition: background .3s, transform .15s, box-shadow .3s;
        }

        button:hover {
            background: linear-gradient(135deg, #005f8f, #0096c7, #48cae4);
            box-shadow: 0 4px 15px rgba(0, 180, 216, 0.4);
        }

        button:active {
            transform: scale(0.98);
        }

        /* ===== password toggle ===== */

        .form-group.password-group input {
            padding-right: 42px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            color: #667;
            transition: color .2s;
        }

        .toggle-password:hover {
            color: #05693a;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        /*
         * ===== water wave animation (rebuilt) =====
         * Each layer is its own <svg>, width 200% of the viewport.
         * The path is a mathematically exact sine-wave (Q + T commands)
         * drawn for exactly TWO periods, matching the svg's viewBox width.
         * Animating translateX from 0 -> -50% therefore shifts the artwork
         * by EXACTLY one period, so the loop point is perfectly seamless
         * (no jump, no stutter, no visible seam).
         */

        .wave-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
            will-change: transform;
        }

        .wave-layer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 200%;
            height: 100%;
            display: block;
        }

        /* farthest layer: slow, faint, softly blurred (atmospheric depth) */
        .wave-layer.wave3 {
            animation: waveScrollBack 26s linear infinite;
            opacity: 0.12;
            filter: blur(1.5px);
        }

        /* mid layer: medium speed/size, moves opposite direction for parallax */
        .wave-layer.wave2 {
            animation: waveScrollFront 20s linear infinite reverse;
            opacity: 0.30;
        }

        /* nearest layer: fastest, brightest, sharpest — feels "closest" to viewer */
        .wave-layer.wave1 {
            animation: waveScrollFront 10s linear infinite;
            opacity: 0.50;
            filter: drop-shadow(0 -2px 3px rgba(0, 60, 90, 0.15));
        }

        /* Each layer's path is drawn twice (2 full periods) inside the viewBox,
           so translateX(-50%) shifts the artwork by EXACTLY one period —
           a perfectly seamless, glitch-free loop. */
        @keyframes waveScrollFront {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        @keyframes waveScrollBack {
            from {
                transform: translateX(-50%);
            }

            to {
                transform: translateX(0);
            }
        }

        /* ===== Responsive ===== */

        @media (max-width: 1024px) {
            .wrapper {
                transform: translateX(-10vw);
                max-width: 320px;
            }
        }

        @media (max-width: 768px) {
            .wrapper {
                transform: none;
                padding: 30px 25px;
                max-width: 360px;
                background: rgba(255, 255, 255, 0.75);
            }

            .logo img {
                max-width: 120px;
            }

            .wave-wrapper {
                height: 90px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }

            .wrapper {
                max-width: 100%;
                padding: 25px 20px;
            }
        }
    </style>

</head>

<body>

    <div class="wrapper">

        <div class="logo">

            @php
                $company = \App\Models\Company::where('status', 'Active')->first();
            @endphp

            @if (isset($company['logo']))
                <img src="{{ asset('/backend/logo/' . $company['logo']) }}">
            @else
                <h4 style="color:red">Logo Missing</h4>
            @endif

        </div>

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <div class="form-group">
                <input type="text" name="email" id="email" placeholder="Enter Your Email" required>
            </div>

            <div class="form-group password-group">
                <input type="password" name="password" id="password" placeholder="Enter Your Password" required>

                <span class="toggle-password" id="togglePassword">
                    <!-- eye (visible) icon -->
                    <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <!-- eye-off (hidden) icon -->
                    <svg id="eyeOffIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                        <path
                            d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24">
                        </path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>
                </span>
            </div>

            <button type="submit">Sign In</button>

        </form>

        @if (env('APP_ENV') == 'local')
            <table class="credentials-table">

                <tr>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Role</th>
                </tr>

                <tr data-email="info@xtreem.com" data-password="12345678">
                    <td>info@xtreem.com</td>
                    <td>12345678</td>
                    <td>Admin</td>
                </tr>

            </table>
        @endif

    </div>

    <!--
      ===== Wave layers (natural, irregular, seamless) =====
      Each svg viewBox is "0 0 3200 140": exactly TWO periods of an
      irregular 1600-unit wave, built from 8 hand-placed height points
      per period and connected with a Catmull-Rom -> cubic-Bezier spline
      (the long C... chains below). Unlike a plain sine wave, each crest
      and trough has a different height, so the water looks organic
      instead of "printed". Because period 2 is an exact copy of period 1
      (same relative heights), translateX(-50%) still shifts the artwork
      by exactly one period -> perfectly seamless, glitch-free looping.
    -->
    <div class="wave-wrapper" id="waveWrapper">

        <!-- back layer: subtle, small, far away -->
        <svg class="wave-layer wave3" viewBox="0 0 3200 140" preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="waveGrad3" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#8fd6e8" />
                    <stop offset="100%" stop-color="#0077b6" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGrad3)" d="M0,96.2
                   C66.667,96.4 133.333,90.6 200,91.4
                   C266.667,92.2 333.333,101.6 400,101
                   C466.667,100.4 533.333,88 600,87.8
                   C666.667,87.6 733.333,99 800,99.8
                   C866.667,100.6 933.333,92.8 1000,92.6
                   C1066.667,92.4 1133.333,99 1200,98.6
                   C1266.667,98.2 1333.333,90.6 1400,90.2
                   C1466.667,89.8 1533.333,96 1600,96.2
                   C1666.667,96.4 1733.333,90.6 1800,91.4
                   C1866.667,92.2 1933.333,101.6 2000,101
                   C2066.667,100.4 2133.333,88 2200,87.8
                   C2266.667,87.6 2333.333,99 2400,99.8
                   C2466.667,100.6 2533.333,92.8 2600,92.6
                   C2666.667,92.4 2733.333,99 2800,98.6
                   C2866.667,98.2 2933.333,90.6 3000,90.2
                   C3066.667,89.8 3133.333,96 3200,96.2
                   L3200,140 L0,140 Z">
            </path>
        </svg>

        <!-- mid layer: medium size, opposite drift direction -->
        <svg class="wave-layer wave2" viewBox="0 0 3200 140" preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="waveGrad2" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#5fd4ea" />
                    <stop offset="100%" stop-color="#00b4d8" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGrad2)" d="M0,80.4
                   C66.667,79.8 133.333,64.2 200,66
                   C266.667,67.8 333.333,90.3 400,91.2
                   C466.667,92.1 533.333,72.3 600,71.4
                   C666.667,70.5 733.333,87.9 800,85.8
                   C866.667,83.7 933.333,59.4 1000,58.8
                   C1066.667,58.2 1133.333,80.4 1200,82.2
                   C1266.667,84 1333.333,69.9 1400,69.6
                   C1466.667,69.3 1533.333,81 1600,80.4
                   C1666.667,79.8 1733.333,64.2 1800,66
                   C1866.667,67.8 1933.333,90.3 2000,91.2
                   C2066.667,92.1 2133.333,72.3 2200,71.4
                   C2266.667,70.5 2333.333,87.9 2400,85.8
                   C2466.667,83.7 2533.333,59.4 2600,58.8
                   C2666.667,58.2 2733.333,80.4 2800,82.2
                   C2866.667,84 2933.333,69.9 3000,69.6
                   C3066.667,69.3 3133.333,81 3200,80.4
                   L3200,140 L0,140 Z">
            </path>
        </svg>

        <!-- front layer: biggest, brightest, closest — has a foam-line highlight -->
        <svg class="wave-layer wave1" viewBox="0 0 3200 140" preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="waveGrad1" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#bdeeff" />
                    <stop offset="100%" stop-color="#48cae4" />
                </linearGradient>
            </defs>
            <path fill="url(#waveGrad1)" stroke="rgba(255,255,255,0.45)" stroke-width="2.5" stroke-linejoin="round" d="M0,55
                   C66.667,54.375 133.333,32.5 200,35
                   C266.667,37.5 333.333,69.167 400,70
                   C466.667,70.833 533.333,38.333 600,40
                   C666.667,41.667 733.333,79.583 800,80
                   C866.667,80.417 933.333,43.75 1000,42.5
                   C1066.667,41.25 1133.333,73.125 1200,72.5
                   C1266.667,71.875 1333.333,41.667 1400,38.75
                   C1466.667,35.833 1533.333,55.625 1600,55
                   C1666.667,54.375 1733.333,32.5 1800,35
                   C1866.667,37.5 1933.333,69.167 2000,70
                   C2066.667,70.833 2133.333,38.333 2200,40
                   C2266.667,41.667 2333.333,79.583 2400,80
                   C2466.667,80.417 2533.333,43.75 2600,42.5
                   C2666.667,41.25 2733.333,73.125 2800,72.5
                   C2866.667,71.875 2933.333,41.667 3000,38.75
                   C3066.667,35.833 3133.333,55.625 3200,55
                   L3200,140 L0,140 Z">
            </path>
        </svg>

    </div>

    <script>
        // ===== password toggle =====
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        const eyeOffIcon = document.getElementById('eyeOffIcon');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            eyeIcon.style.display = isPassword ? 'none' : 'block';
            eyeOffIcon.style.display = isPassword ? 'block' : 'none';
        });

        // ===== login credentials autofill (local env only) =====
        document.querySelectorAll('.credentials-table tr').forEach(row => {
            row.addEventListener('click', function() {
                const email = this.getAttribute('data-email');
                const password = this.getAttribute('data-password');
                if (email) {
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                }
            });
        });

        // ===== water wave mouse interaction (smooth, lerp-based parallax tilt) =====
        // Instead of snapping the wave straight to the cursor position (which felt
        // jerky), we lerp (linearly interpolate) towards a target value every
        // animation frame. This makes the tilt feel like it is "catching up" to
        // the mouse smoothly and naturally, and it never fights with the
        // continuous CSS translateX wave-scroll animation running on each layer.
        const waveWrapper = document.getElementById('waveWrapper');

        let targetX = 0;
        let targetY = 0;
        let targetRotate = 0;

        let currentX = 0;
        let currentY = 0;
        let currentRotate = 0;

        const EASE = 0.00; // lower = smoother/slower catch-up, higher = snappier

        document.addEventListener('mousemove', function(e) {
            const x = e.clientX / window.innerWidth - 0.5;
            const y = e.clientY / window.innerHeight - 0.5;

            targetX = x * 20;
            targetY = y * 15;
            targetRotate = x * -2;
        });

        document.addEventListener('mouseleave', function() {
            targetX = 0;
            targetY = 0;
            targetRotate = 0;
        });

        function animateTilt() {
            currentX += (targetX - currentX) * EASE;
            currentY += (targetY - currentY) * EASE;
            currentRotate += (targetRotate - currentRotate) * EASE;

            waveWrapper.style.transform =
                `translateX(${currentX.toFixed(2)}px) translateY(${currentY.toFixed(2)}px) rotate(${currentRotate.toFixed(2)}deg)`;

            requestAnimationFrame(animateTilt);
        }

        requestAnimationFrame(animateTilt);
    </script>

</body>

</html>
