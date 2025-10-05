<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    @vite('resources/css/app.css')

</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 via-white to-red-100 p-4">
        <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md border-t-4 border-red-500">
            <h1 class="text-4xl font-bold text-red-600 mb-6 text-center ">Login</h1>

            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="Name"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"
                    required />
                <input type="password" name="password" placeholder="Password"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400"
                    required />
                <button type="submit" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                    Login
                </button>
            </form>

            <p class="text-xs text-gray-500 mt-4 text-center">
                © {{ date('Y') }} TenAspiration. All rights reserved.
            </p>
        </div>
    </div>
</body>

</html>
