<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — TCG Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-900 flex items-center justify-center p-4">

<div class="w-full max-w-sm">

    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold text-white">
            TCG
        </div>
        <h1 class="text-2xl font-bold text-white">TCG Manager</h1>
        <p class="text-slate-400 text-sm mt-1">Inventory & Tournament System</p>
    </div>

    @if($errors->any())
    <div class="mb-4 bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-sm">
        {{ $errors->first() }}
    </div>
    @endif

    @if(session('status'))
    <div class="mb-4 bg-emerald-900/50 border border-emerald-700 text-emerald-300 px-4 py-3 rounded-lg text-sm">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs text-slate-400 mb-1.5">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="w-full bg-slate-800 border border-slate-600 text-white placeholder-slate-500 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-xs text-slate-400 mb-1.5">Password</label>
            <input type="password" name="password" required
                   class="w-full bg-slate-800 border border-slate-600 text-white placeholder-slate-500 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-slate-400">
                <input type="checkbox" name="remember" class="rounded bg-slate-700 border-slate-600">
                Remember me
            </label>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300">Forgot password?</a>
            @endif
        </div>

        <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-3 rounded-lg text-sm transition-colors">
            Sign In
        </button>
    </form>

    <p class="text-center text-xs text-slate-500 mt-6">
        TCG Inventory & Tournament Manager v1.0
    </p>
</div>

</body>
</html>
