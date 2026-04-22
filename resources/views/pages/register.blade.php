@extends('layouts.app')
@section('title', 'Inscription — DAR-RENT')
@section('content')

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-16">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 32 32">
                        <path d="M16 1C10.477 1 6 5.477 6 11c0 7.5 10 20 10 20s10-12.5 10-20c0-5.523-4.477-10-10-10zm0 13.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-dark">DAR-RENT</span>
            </a>
            <h1 class="text-3xl font-bold text-dark mt-6 mb-1">Créer un compte</h1>
            <p class="text-muted text-sm">Rejoignez des milliers d'utilisateurs au Maroc</p>
        </div>

        <div class="bg-white rounded-2xl shadow-card p-8">

            <div id="reg-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-5"></div>

            <p class="text-sm font-semibold text-dark mb-3">Je veux...</p>
            <div class="grid grid-cols-2 gap-3 mb-6">
                <button type="button" onclick="selectRole('tenant')" id="role-tenant"
                    class="border-2 border-primary-500 bg-primary-50 rounded-xl p-4 text-center transition-all">
                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm text-dark">Trouver un logement</p>
                    <p class="text-xs text-muted mt-0.5">Je suis locataire</p>
                </button>
                <button type="button" onclick="selectRole('owner')" id="role-owner"
                    class="border-2 border-gray-200 rounded-xl p-4 text-center transition-all hover:border-primary-300">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mx-auto mb-2">
                        <svg class="w-5 h-5 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-sm text-dark">Louer mon bien</p>
                    <p class="text-xs text-muted mt-0.5">Je suis propriétaire</p>
                </button>
            </div>
            <input type="hidden" id="reg-role" value="tenant">

            <form onsubmit="handleRegister(event)" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Nom complet *</label>
                        <input type="text" id="reg-nom" required placeholder="Ahmed Benali" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Téléphone</label>
                        <input type="tel" id="reg-tel" placeholder="0612345678" class="input-field">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Adresse email *</label>
                    <input type="email" id="reg-email" required placeholder="vous@example.com" class="input-field">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Mot de passe *</label>
                        <input type="password" id="reg-password" required placeholder="min. 8 caractères" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Confirmer *</label>
                        <input type="password" id="reg-confirm" required placeholder="••••••••" class="input-field">
                    </div>
                </div>
                <button type="submit" id="reg-btn" class="btn-primary w-full py-3.5 text-base mt-2">Créer mon compte</button>
            </form>

            <p class="text-center text-sm text-muted mt-6">
                Déjà un compte ?
                <a href="/login" class="text-primary-600 font-semibold hover:text-primary-700 transition">Se connecter</a>
            </p>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    AuthManager.requireGuest();
    function selectRole(role) {
        document.getElementById('reg-role').value = role;
        document.getElementById('role-tenant').className =
            `border-2 ${role==='tenant'?'border-primary-500 bg-primary-50':'border-gray-200'} rounded-xl p-4 text-center transition-all hover:border-primary-300`;
        document.getElementById('role-owner').className =
            `border-2 ${role==='owner'?'border-primary-500 bg-primary-50':'border-gray-200'} rounded-xl p-4 text-center transition-all hover:border-primary-300`;
    }
    async function handleRegister(e) {
        e.preventDefault();
        const btn = document.getElementById('reg-btn');
        const err = document.getElementById('reg-error');
        if (document.getElementById('reg-password').value !== document.getElementById('reg-confirm').value) {
            err.textContent = 'Les mots de passe ne correspondent pas.'; err.classList.remove('hidden'); return;
        }
        btn.disabled = true; btn.textContent = 'Création du compte...'; err.classList.add('hidden');
        try {
            const data = await Auth.register({
                nom: document.getElementById('reg-nom').value,
                email: document.getElementById('reg-email').value,
                telephone: document.getElementById('reg-tel').value,
                password: document.getElementById('reg-password').value,
                password_confirmation: document.getElementById('reg-confirm').value,
                role: document.getElementById('reg-role').value,
            });
            AuthManager.save(data.token, data.user);
            Toast.success('Compte créé avec succès !');
            setTimeout(() => { window.location.href = '/'; }, 800);
        } catch (e) {
            err.textContent = e.message; err.classList.remove('hidden');
            btn.disabled = false; btn.textContent = 'Créer mon compte';
        }
    }
</script>
@endpush