<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DAR-RENT — Location immobilière au Maroc')</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('head')
</head>
<body class="bg-gray-50 text-dark font-sans">
     {{-- NAVBAR --}}
<nav class="bg-navy sticky top-0 z-50 shadow-nav">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 flex-shrink-0">
                <div class="w-9 h-9 bg-primary-500 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 32 32">
                        <path d="M16 1C10.477 1 6 5.477 6 11c0 7.5 10 20 10 20s10-12.5 10-20c0-5.523-4.477-10-10-10zm0 13.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-white tracking-wide">DAR-RENT</span>
            </a>
            {{-- Center links (desktop) --}}
            <div class="hidden md:flex items-center gap-6">
                <a href="/"class="text-gray-300 hover:text-white text-sm font-medium transition">Accueil</a>
                <a href="/#properties" class="text-gray-300 hover:text-white text-sm font-medium transition">Biens</a>
                <a href="/about"class="text-gray-300 hover:text-white text-sm font-medium transition">À propos</a>
                <a href="/contact"class="text-gray-300 hover:text-white text-sm font-medium transition">Contact</a>
            </div>
            {{-- Right side --}}
            <div class="flex items-center gap-3">
                {{-- Guest --}}
                <div id="nav-guest" class="flex items-center gap-2">
                    <a href="/login"class="text-sm font-medium text-gray-300 hover:text-white px-4 py-2 transition">Connexion</a>
                    <a href="/register" class="btn-primary text-sm px-5 py-2 rounded-lg">S'inscrire</a>
                </div>
                {{-- Owner: add property --}}
                <div id="nav-owner-btn" class="hidden">
                    <a href="/properties/create"
                        class="text-sm font-medium text-gray-300 hover:text-white border border-gray-600 px-4 py-2 rounded-lg hover:border-gray-400 transition">
                        + Publier un bien
                    </a>
                </div>
                {{-- User dropdown --}}
                <div id="nav-user" class="hidden relative">
                    <button onclick="toggleUserMenu()"
                        class="flex items-center gap-2.5 bg-navy-light border border-gray-600 rounded-xl px-3 py-2 hover:border-primary-500 transition">
                        <img id="nav-avatar" src="/images/placeholder.jpg"
                             class="w-7 h-7 rounded-full object-cover" alt="avatar">
                        <div class="hidden sm:block text-left">
                            <p id="nav-username" class="text-white text-xs font-semibold leading-none"></p>
                            <p id="nav-role"     class="text-gray-400 text-xs mt-0.5 leading-none"></p>
                        </div>
                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="user-menu"
                         class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-card-hover border border-gray-100 overflow-hidden z-50">
                        <div class="px-4 py-3 bg-primary-50 border-b border-gray-100">
                            <p id="menu-name" class="font-semibold text-sm text-dark"></p>
                            <p id="menu-role" class="text-xs text-muted mt-0.5"></p>
                        </div>
                        <div class="py-1">
                            <a href="/profile"class="flex items-center gap-3 px-4 py-2.5 text-sm text-dark hover:bg-gray-50">
                                <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Mon profil
                            </a>
                            <a href="/messages"class="flex items-center gap-3 px-4 py-2.5 text-sm text-dark hover:bg-gray-50">
                                <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Messages
                            </a>
                            <a href="/favorites"class="tenant-only hidden flex items-center gap-3 px-4 py-2.5 text-sm text-dark hover:bg-gray-50">
                                <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                Mes favoris
                            </a>
                            <a href="/reservations"class="tenant-only hidden flex items-center gap-3 px-4 py-2.5 text-sm text-dark hover:bg-gray-50">
                                <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Mes réservations
                            </a>
                            <a href="/my-properties"class="owner-only hidden flex items-center gap-3 px-4 py-2.5 text-sm text-dark hover:bg-gray-50">
                                <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Mes biens
                            </a>
                            <a href="/requests"class="owner-only hidden flex items-center gap-3 px-4 py-2.5 text-sm text-dark hover:bg-gray-50">
                                <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Demandes
                            </a>
                        </div>
                        <div class="border-t border-gray-100 py-1">
                            <button onclick="handleLogout()"
                                class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Déconnexion
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<main>@yield('content')</main>

{{-- FOOTER --}}
<footer class="bg-navy text-gray-300 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 bg-primary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 32 32">
                            <path d="M16 1C10.477 1 6 5.477 6 11c0 7.5 10 20 10 20s10-12.5 10-20c0-5.523-4.477-10-10-10zm0 13.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">DAR-RENT</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">La plateforme de référence pour la location immobilière au Maroc.</p>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Navigation</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/"         class="hover:text-primary-400 transition">Accueil</a></li>
                    <li><a href="/register" class="hover:text-primary-400 transition">S'inscrire</a></li>
                    <li><a href="/login"    class="hover:text-primary-400 transition">Connexion</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Villes</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/?city=Marrakech"  class="hover:text-primary-400 transition">Marrakech</a></li>
                    <li><a href="/?city=Casablanca" class="hover:text-primary-400 transition">Casablanca</a></li>
                    <li><a href="/?city=Rabat"      class="hover:text-primary-400 transition">Rabat</a></li>
                    <li><a href="/?city=Agadir"     class="hover:text-primary-400 transition">Agadir</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold text-sm mb-3">Support</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-primary-400 transition">Aide</a></li>
                    <li><a href="#" class="hover:text-primary-400 transition">Confidentialité</a></li>
                    <li><a href="#" class="hover:text-primary-400 transition">Conditions</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-gray-700 pt-6 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p class="text-xs text-gray-500">© 2024 DAR-RENT. Tous droits réservés.</p>
            <p class="text-xs text-gray-500">Fait avec  au Maroc</p>
        </div>
    </div>
</footer>

<script src="/js/api.js"></script>
<script src="/js/auth.js"></script>
<script src="/js/helpers.js"></script>
<script src="/js/toast.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const user  = AuthManager.getUser();
        const token = AuthManager.getToken();
        if (token && user) {
            document.getElementById('nav-guest').classList.add('hidden');
            document.getElementById('nav-user').classList.remove('hidden');
            document.getElementById('nav-username').textContent = user.nom;
            document.getElementById('nav-role').textContent = user.role === 'owner' ? 'Propriétaire' : 'Locataire';
            document.getElementById('menu-name').textContent = user.nom;
            document.getElementById('menu-role').textContent = user.role === 'owner' ? 'Propriétaire' : 'Locataire';
            document.getElementById('nav-avatar').src = Helpers.avatarUrl(user.avatar, user.nom);
            if (user.role === 'owner') {
                document.getElementById('nav-owner-btn').classList.remove('hidden');
                document.querySelectorAll('.owner-only').forEach(el => el.classList.remove('hidden'));
            } else {
                document.querySelectorAll('.tenant-only').forEach(el => el.classList.remove('hidden'));
            }
        }
    });

    function toggleUserMenu() { document.getElementById('user-menu').classList.toggle('hidden'); }
    document.addEventListener('click', (e) => {
        const menu = document.getElementById('user-menu');
        const btn  = document.querySelector('#nav-user button');
        if (menu && !menu.contains(e.target) && !btn?.contains(e.target)) menu.classList.add('hidden');
    });
    async function handleLogout() {
        try { await Auth.logout(); } catch (e) {}
        AuthManager.clear();
        window.location.href = '/';
    }
</script>

@stack('scripts')
</body>
</html>