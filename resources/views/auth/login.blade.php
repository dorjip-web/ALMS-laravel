<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Sign In</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="/css/login.css">
        <style>
            /* mobile background for small screens only */
            .mobile-bg { background-size: cover; background-position: center; background-image: url("{{ asset('images/left-panel.jpg') }}"); }
            @media (min-width: 768px) { .mobile-bg { background-image: none !important; background-color: #f3f4f6; } }
        </style>
    </head>
    <body class="bg-gray-100 font-poppins">

        <!-- Mobile-only login (visible on <md) -->
        <div class="md:hidden mobile-bg flex items-center justify-center min-h-screen">
            <div class="bg-white bg-opacity-70 backdrop-blur-sm shadow-xl rounded-xl w-11/12 max-w-xs p-6">
                <div class="text-center mb-6">
                    <img src="{{ asset('images/ntmh-logo.png') }}" alt="NTMH Logo" class="mx-auto w-16 h-16 rounded-full mb-2" />
                    <h1 class="text-xl font-bold text-orange-600">NTMH ALMS</h1>
                    <p class="text-gray-600 text-xs">Welcome back — please enter your username and password</p>
                </div>

                <form action="{{ route('login', [], false) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="text" name="username" placeholder="Enter your username"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" />

                    <input type="password" name="password" placeholder="Enter your password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500" />

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="mr-1" /> Remember me
                        </label>
                        <a href="/forgot-password" class="text-orange-600 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-600 text-white font-semibold py-2 rounded-lg text-sm hover:bg-orange-700 transition">
                        Login
                    </button>
                </form>

                <div class="mt-5 text-center text-xs text-gray-600">
                    <p>Quick Links:</p>
                    <div class="flex justify-center space-x-3 mt-2">
                        <a href="/google-login" class="bg-gray-100 px-3 py-1 rounded-lg hover:bg-gray-200">Sign in with Google</a>
                        <a href="/admin-login" class="bg-gray-100 px-3 py-1 rounded-lg hover:bg-gray-200">Admin Login</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="hidden md:flex min-h-screen items-center justify-center p-6">
            <div class="w-full max-w-6xl rounded-2xl overflow-hidden shadow-2xl grid grid-cols-1 md:grid-cols-2">
                <div class="relative hidden md:block">
                    <img
                        src="{{ asset('images/left-panel.jpg') }}"
                        alt="Left panel artwork"
                        class="absolute inset-0 h-full w-full object-cover"
                        onerror="this.style.display='none'; this.parentElement.querySelector('[data-image-fallback]').style.display='flex';"
                    >
                    <div data-image-fallback class="absolute inset-0 hidden items-center justify-center bg-slate-800 text-slate-200 text-sm">
                        Add image at /public/images/left-panel.jpg
                    </div>
                    <div class="absolute inset-0 left-tint pointer-events-none"></div>
                    <div class="relative z-10 h-full p-8 flex flex-col justify-between text-white">
                        <div class="flex items-start justify-between">
                            <div class="text-2xl font-bold tracking-wide"></div>
                            <a href="#" class="text-sm bg-white/10 px-3 py-1 rounded-full"></a>
                        </div>
                        <div class="mb-6">
                            <h2 class="text-3xl md:text-4xl font-semibold"></h2>
                        </div>
                    </div>
                </div>

                <div class="bg-[#20204D] p-8 md:p-12 flex items-center">
                    <div class="w-full max-w-md mx-auto text-white">
                        <div class="mb-4 flex items-center justify-center gap-4 text-white">
                            <span class="text-2xl font-bold">NTMH</span>
                            <img
                                src="{{ asset('images/ntmh-logo.png') }}"
                                alt="NTMH logo"
                                class="h-20 w-20 rounded-full bg-white p-1 object-contain shadow-lg"
                            >
                            <span class="text-2xl font-bold">ALMS</span>
                        </div>
                        <p class="text-sm text-gray-300 mb-6">Welcome back - please enter your username and password.</p>

                        @if ($errors->any())
                            <div class="mb-4 rounded-md border border-red-300 bg-red-100 px-3 py-2 text-sm text-red-800">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('login', [], false) }}" method="post" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm text-gray-300 mb-2">Username</label>
                                <input name="username" value="{{ old('username') }}" type="text" required class="w-full px-4 py-3 rounded bg-[#213683] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500" placeholder="Enter your username">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-300 mb-2">Password</label>
                                <div class="relative">
                                    <input id="password" name="password" type="password" required class="w-full px-4 py-3 rounded bg-[#213683] placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 pr-10" placeholder="Enter your password">
                                    <button type="button" onclick="togglePassword()" class="absolute right-3 top-3 text-gray-900" aria-label="toggle password visibility">
                                        <!-- Only the original black eye icon should remain, remove SVG here if present -->
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-300">
                                <label class="flex items-center gap-2"><input name="remember" type="checkbox" class="form-checkbox h-4 w-4 text-purple-500"> Remember me</label>
                                <a href="#" class="text-purple-300 hover:underline">Forgot password?</a>
                            </div>

                            <button type="submit" class="w-full py-3 rounded bg-gradient-to-r from-purple-600 to-indigo-600 font-semibold hover:opacity-95">Login</button>
                        </form>

                        <div class="mt-6 text-center text-gray-400">
                            Quick Link
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <button type="button" class="py-2 px-4 rounded border border-gray-600 bg-transparent text-white flex items-center justify-center gap-2">
                                <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" class="w-5 h-5">
                                Google
                            </button>
                            <a href="{{ route('admin.login', [], false) }}" class="py-2 px-4 rounded border border-gray-600 bg-transparent text-white flex items-center justify-center gap-2 no-underline">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10zm0 2c-5.33 0-8 2.67-8 5v1h16v-1c0-2.33-2.67-5-8-5z"/></svg>
                                Admin Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function togglePassword() {
                var passwordField = document.getElementById('password');

                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                } else {
                    passwordField.type = 'password';
                }
            }
        </script>
    </body>
</html>
