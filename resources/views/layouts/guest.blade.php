<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Login') — Gorengan Mama Ilham</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6',
                            900: '#4c1d95',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .login-bg {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1744 0%, #252260 30%, #3b1d8e 55%, #4c1d6e 80%, #1a1744 100%);
            background-size: 300% 300%;
            animation: gradientShift 10s ease infinite;
            position: relative;
        }

        .login-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M50 50c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10S50 55.5 50 50zm-20 0c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10S30 55.5 30 50zm-20 0c0-5.5 4.5-10 10-10s10 4.5 10 10-4.5 10-10 10S10 55.5 10 50z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 32px 80px rgba(30, 27, 75, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
        }

        .input-field {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border-radius: 14px;
            outline: none;
            border: 2px solid #ede9fe;
            background: #faf9ff;
            font-size: 14px;
            font-weight: 500;
            color: #1e1b4b;
            transition: all 0.2s;
        }

        .input-field:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.12);
        }

        .input-field::placeholder {
            color: #c4b5fd;
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, #7c3aed, #c026d3);
            color: white;
            font-weight: 800;
            font-size: 15px;
            padding: 15px;
            border-radius: 16px;
            border: none;
            cursor: pointer;
            box-shadow: 0 6px 24px rgba(139, 92, 246, 0.45);
            transition: all 0.2s;
            letter-spacing: 0.02em;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 32px rgba(139, 92, 246, 0.55);
        }

        .btn-login:active {
            transform: scale(0.97);
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased">
    @yield('content')
    @stack('scripts')
</body>

</html>