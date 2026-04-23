@extends('layouts.app')
@section('title', 'Mon profil — DAR-RENT')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <h1 class="text-2xl font-bold text-dark mb-8">Mon profil</h1>

    {{-- Avatar card --}}
    <div class="flex items-center gap-6 mb-8 p-6 bg-white rounded-2xl shadow-card">
        <div class="relative">
                 class="w-20 h-20 rounded-full object-cover border-4 border-primary-100">
            <label for="avatar-input"
                class="absolute bottom-0 right-0 bg-primary-500 text-white rounded-full w-7 h-7
                       flex items-center justify-center cursor-pointer hover:bg-primary-600 transition shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
            </label>
            <input type="file" id="avatar-input" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
        </div>
        <div>
            <p id="profile-name"  class="text-xl font-bold text-dark"></p>
            <span id="profile-role-badge" class="inline-block mt-1 bg-primary-100 text-primary-700 text-xs font-semibold px-3 py-1 rounded-full"></span>
            <p id="profile-since" class="text-xs text-muted mt-2"></p>
        </div>
    </div>

    <div id="profile-success" class="hidden bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3 mb-4 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span id="success-msg"></span>
    </div>
    <div id="profile-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4"></div>

    {{-- Info form --}}
    <div class="bg-white rounded-2xl shadow-card p-6 mb-6">
        <h2 class="text-base font-bold text-dark mb-5 pb-3 border-b border-gray-100">Informations personnelles</h2>
        <form onsubmit="saveProfile(event)" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Nom complet</label>
                    <input type="text" id="p-nom" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Téléphone</label>
                    <input type="tel" id="p-tel" class="input-field">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-dark mb-2">Email</label>
                <input type="email" id="p-email" class="input-field">
            </div>
            <div class="flex justify-end">
                <button type="submit" id="save-btn" class="btn-primary px-6 py-2.5 text-sm">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

    {{-- Password form --}}
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h2 class="text-base font-bold text-dark mb-5 pb-3 border-b border-gray-100">Changer le mot de passe</h2>
        <form onsubmit="changePassword(event)" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Nouveau mot de passe</label>
                    <input type="password" id="p-password" class="input-field" placeholder="min. 8 caractères">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Confirmer</label>
                    <input type="password" id="p-confirm" class="input-field" placeholder="••••••••">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" id="pwd-btn" class="btn-primary px-6 py-2.5 text-sm">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    document.addEventListener('DOMContentLoaded', async () => {
        const data = await Auth.me(); const u = data.user;
        AuthManager.save(AuthManager.getToken(), u);
        document.getElementById('profile-name').textContent = u.nom;
        document.getElementById('profile-role-badge').textContent = u.role==='owner'?'Propriétaire':'Locataire';
        document.getElementById('profile-since').textContent = 'Membre depuis ' + Helpers.formatDate(u.created_at);
        document.getElementById('p-nom').value   = u.nom   || '';
        document.getElementById('p-email').value = u.email || '';
        document.getElementById('p-tel').value   = u.telephone || '';
    });
    async function saveProfile(e) {
        e.preventDefault();
        const btn = document.getElementById('save-btn');
        btn.disabled = true; btn.textContent = 'Enregistrement...'; hideAlerts();
        try {
            const data = await Auth.update({ nom: document.getElementById('p-nom').value, email: document.getElementById('p-email').value, telephone: document.getElementById('p-tel').value });
            AuthManager.save(AuthManager.getToken(), data.user);
            showSuccess('Profil mis à jour !');
            document.getElementById('profile-name').textContent = data.user.nom;
        } catch (e) { showError(e.message); }
        finally { btn.disabled = false; btn.textContent = 'Enregistrer les modifications'; }
    }
    async function changePassword(e) {
        e.preventDefault();
        const pwd = document.getElementById('p-password').value;
        const cfm = document.getElementById('p-confirm').value;
        if (pwd !== cfm) { showError('Les mots de passe ne correspondent pas.'); return; }
        const btn = document.getElementById('pwd-btn');
        btn.disabled = true; btn.textContent = 'Mise à jour...'; hideAlerts();
        try {
            await Auth.update({ password: pwd, password_confirmation: cfm });
            showSuccess('Mot de passe mis à jour !');
            document.getElementById('p-password').value = '';
            document.getElementById('p-confirm').value  = '';
        } catch (e) { showError(e.message); }
        finally { btn.disabled = false; btn.textContent = 'Mettre à jour'; }
    }
    async function uploadAvatar(input) {
        const fd = new FormData(); fd.append('avatar', input.files[0]);
        try {
            const data = await Auth.update(fd);
            AuthManager.save(AuthManager.getToken(), data.user);
            Toast.success('Photo mise à jour !');
        } catch (e) { Toast.error(e.message); }
    }
    function showSuccess(msg) { const el = document.getElementById('profile-success'); document.getElementById('success-msg').textContent = msg; el.classList.remove('hidden'); setTimeout(() => el.classList.add('hidden'), 4000); }
    function showError(msg) { const el = document.getElementById('profile-error'); el.textContent = msg; el.classList.remove('hidden'); }
    function hideAlerts() { document.getElementById('profile-success').classList.add('hidden'); document.getElementById('profile-error').classList.add('hidden'); }
</script>
@endpush