@extends('layouts.app')
@section('title', 'Connexion — DAR-RENT')
@section('content')

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

    {{-- LEFT: Form --}}
    <div class="flex items-center justify-center px-6 py-16 bg-white">
        <div class="w-full max-w-md">

            <a href="/" class="flex items-center gap-3 mb-10">
                <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 32 32">
                        <path d="M16 1C10.477 1 6 5.477 6 11c0 7.5 10 20 10 20s10-12.5 10-20c0-5.523-4.477-10-10-10zm0 13.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-dark">DAR-RENT</span>
            </a>

            <h1 class="text-3xl font-bold text-dark mb-2">Bon retour !</h1>
            <p class="text-muted mb-8">Connectez-vous pour accéder à votre compte.</p>

            <div id="login-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span id="login-error-msg"></span>
            </div>

            <form onsubmit="handleLogin(event)" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Adresse email</label>
                    <input type="email" id="login-email" required placeholder="vous@example.com" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Mot de passe</label>
                    <div class="relative">
                        <input type="password" id="login-password" required placeholder="••••••••" class="input-field pr-12">
                        <button type="button" onclick="togglePwd('login-password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted hover:text-dark transition">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" id="login-btn" class="btn-primary w-full py-3.5 text-base">Se connecter</button>
            </form>

            <p class="text-center text-sm text-muted mt-6">
                Pas encore de compte ?
                <a href="/register" class="text-primary-600 font-semibold hover:text-primary-700 transition">Créer un compte</a>
            </p>
        </div>
    </div>

    {{-- RIGHT: Branded panel --}}
    <div class="hidden lg:flex items-center justify-center bg-navy p-12">
        <div class="text-center">
            <div class="w-24 h-24 bg-primary-500/20 rounded-full flex items-center justify-center mx-auto mb-6 border border-primary-500/30">
                <svg class="w-12 h-12 text-primary-400" fill="currentColor" viewBox="0 0 32 32">
                    <path d="M16 1C10.477 1 6 5.477 6 11c0 7.5 10 20 10 20s10-12.5 10-20c0-5.523-4.477-10-10-10zm0 13.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white mb-4">Bienvenue sur DAR-RENT</h2>
            <p class="text-gray-400 text-lg leading-relaxed max-w-sm mx-auto">
                La plateforme de référence pour trouver votre logement idéal au Maroc.
            </p>
            <div class="mt-10 grid grid-cols-2 gap-4 max-w-xs mx-auto">
                <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                    <p class="text-2xl font-bold text-white">2K+</p>
                    <p class="text-gray-400 text-sm">Biens</p>
                </div>
                <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                    <p class="text-2xl font-bold text-white">500+</p>
                    <p class="text-gray-400 text-sm">Propriétaires</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    AuthManager.requireGuest();
    async function handleLogin(e) {
        e.preventDefault();
        const btn = document.getElementById('login-btn');
        const errBox = document.getElementById('login-error');
        const errMsg = document.getElementById('login-error-msg');
        btn.disabled = true; btn.textContent = 'Connexion...';
        errBox.classList.add('hidden');
        try {
            const data = await Auth.login({
                email:    document.getElementById('login-email').value,
                password: document.getElementById('login-password').value,
            });
            AuthManager.save(data.token, data.user);
            Toast.success('Bienvenue ' + data.user.nom + ' !');
            setTimeout(() => { window.location.href = data.user.role === 'owner' ? '/my-properties' : '/'; }, 800);
        } catch (err) {
            errMsg.textContent = err.message; errBox.classList.remove('hidden');
            btn.disabled = false; btn.textContent = 'Se connecter';
        }
    }
    function togglePwd(id) {
        const input = document.getElementById(id);
        input.type  = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endpush