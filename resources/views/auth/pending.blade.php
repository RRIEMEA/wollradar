<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrierung eingegangen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="w-full max-w-md rounded-lg bg-white p-6 shadow">
        <h1 class="text-2xl font-semibold text-gray-900">
            Registrierung eingegangen
        </h1>

        <p class="mt-2 text-gray-700">
            Du erhältst Zugriff nach Freigabe durch einen Admin.
        </p>

        <a href="{{ route('login') }}"
           class="mt-6 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Zurück zum Login
        </a>
    </div>
</body>
</html>
