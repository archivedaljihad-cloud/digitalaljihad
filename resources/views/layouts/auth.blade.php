<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $setting['nama_aplikasi'] ?? config('app.name', 'Sistem Informasi Masjid Digital') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        :root {
            --nu-green-darkest: #02170d;
            --nu-green-dark: #042918;
            --nu-green-base: #064426;
            --nu-green-light: #0d6338;
            --nu-green-bright: #128049;
            --gold-primary: #ffd700;
            --gold-dark: #c59b27;
            --gold-light: #ffea79;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            background: radial-gradient(circle at 50% 30%, #084c2b 0%, #042918 55%, #02170d 100%) !important;
            color: #ffffff;
            position: relative;
            overflow-x: hidden;
        }

        .bg-gradient-primary {
            background: radial-gradient(circle at 50% 30%, #084c2b 0%, #042918 55%, #02170d 100%) !important;
            min-height: 100vh;
            position: relative;
        }

        /* Islamic Arabesque Geometric Pattern Background */
        .bg-gradient-primary::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: 
                radial-gradient(rgba(255, 215, 0, 0.08) 1.5px, transparent 1.5px),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Cg fill='none' stroke='%23ffd700' stroke-width='0.75' stroke-opacity='0.08'%3E%3Cpath d='M40,0 L80,40 L40,80 L0,40 Z'/%3E%3Cpath d='M40,10 L70,40 L40,70 L10,40 Z'/%3E%3Ccircle cx='40' cy='40' r='18'/%3E%3Cpath d='M0,0 L80,80 M80,0 L0,80'/%3E%3Cpolygon points='40,18 46,34 62,40 46,46 40,62 34,46 18,40 34,34'/%3E%3C/g%3E%3C/svg%3E");
            background-size: 80px 80px, 80px 80px;
            pointer-events: none;
            z-index: 1;
        }

        .card {
            border-radius: 20px !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        }

        .form-control {
            border-radius: 30px !important;
            padding: 12px 20px;
            font-size: 14px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(45, 90, 59, 0.25);
            border-color: var(--secondary-green);
        }

        .btn {
            border-radius: 30px !important;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border: none;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(45, 90, 59, 0.4);
            background: linear-gradient(135deg, var(--secondary-green), var(--primary-green));
        }

        .btn-outline-success {
            border-radius: 30px !important;
            border: 2px solid var(--secondary-green);
            color: var(--secondary-green);
        }

        .btn-outline-success:hover {
            background: linear-gradient(135deg, var(--primary-green), var(--secondary-green));
            border-color: transparent;
        }

        .input-group-text {
            border-radius: 30px 0 0 30px !important;
            background-color: #f8f9fc;
            border: none;
        }

        .input-group .form-control {
            border-radius: 0 30px 30px 0 !important;
        }

        /* Custom Checkbox */
        .custom-control-input:checked~.custom-control-label::before {
            background-color: var(--secondary-green);
            border-color: var(--secondary-green);
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Islamic Corner Ornament */
        .islamic-corner {
            position: fixed;
            width: 90px;
            height: 90px;
            pointer-events: none;
            z-index: 2;
            opacity: 0.6;
        }

        .corner-tl {
            top: 15px;
            left: 15px;
            border-top: 3px solid var(--gold-primary);
            border-left: 3px solid var(--gold-primary);
            border-top-left-radius: 16px;
        }

        .corner-tr {
            top: 15px;
            right: 15px;
            border-top: 3px solid var(--gold-primary);
            border-right: 3px solid var(--gold-primary);
            border-top-right-radius: 16px;
        }

        .corner-bl {
            bottom: 15px;
            left: 15px;
            border-bottom: 3px solid var(--gold-primary);
            border-left: 3px solid var(--gold-primary);
            border-bottom-left-radius: 16px;
        }

        .corner-br {
            bottom: 15px;
            right: 15px;
            border-bottom: 3px solid var(--gold-primary);
            border-right: 3px solid var(--gold-primary);
            border-bottom-right-radius: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .islamic-corner {
                display: none;
            }
        }

        /* Loading Spinner */
        .spinner-custom {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.6s linear infinite;
            display: inline-block;
            margin-right: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            transition: all 0.3s ease;
        }

        .strength-weak {
            width: 33%;
            background: #dc3545;
        }

        .strength-medium {
            width: 66%;
            background: #ffc107;
        }

        .strength-strong {
            width: 100%;
            background: #28a745;
        }

        /* Floating Labels */
        .floating-label-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #999;
            pointer-events: none;
            transition: 0.2s ease all;
            font-size: 14px;
        }

        .floating-input:focus~.floating-label,
        .floating-input:not(:placeholder-shown)~.floating-label {
            top: -10px;
            left: 10px;
            font-size: 11px;
            background: white;
            padding: 0 5px;
            color: var(--secondary-green);
        }
    </style>

    <!-- Favicon -->
    <link href="{{ isset($setting['favicon']) ? asset('storage/' . $setting['favicon']) : asset('img/favicon.png') }}" rel="icon" type="image/png">
</head>

<body class="bg-gradient-primary min-vh-100 d-flex justify-content-center align-items-center py-4">

    <!-- Islamic Corner Ornaments -->
    <div class="islamic-corner corner-tl"></div>
    <div class="islamic-corner corner-tr"></div>
    <div class="islamic-corner corner-bl"></div>
    <div class="islamic-corner corner-br"></div>

    @yield('main-content')

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>

    <!-- Custom Scripts -->
    <script>
        // Auto-hide alerts after 5 seconds
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Add loading effect on form submit
            $('form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                if (submitBtn.length) {
                    const originalText = submitBtn.html();
                    submitBtn.html('<span class="spinner-custom"></span> Memproses...');
                    submitBtn.prop('disabled', true);

                    // Reset button after 3 seconds if no response (fallback)
                    setTimeout(function() {
                        submitBtn.html(originalText);
                        submitBtn.prop('disabled', false);
                    }, 3000);
                }
            });
        });

        // Password strength checker
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[$@#&!]+/)) strength++;
            return strength;
        }

        // Toggle password visibility
        function togglePasswordVisibility(button) {
            const input = button.previousElementSibling;
            const icon = button.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = $(`
                <div class="toast-custom alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(toast);
            setTimeout(() => toast.fadeOut('slow', function() {
                $(this).remove();
            }), 5000);
        }
    </script>
</body>

</html>