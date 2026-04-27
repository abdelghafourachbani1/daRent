@extends('layouts.app')
@section('title', 'Statistiques — DAR-RENT')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-3 mb-8">
        <a href="/my-properties" class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:border-primary-500 transition shadow-sm">
            <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div id="stats-skeleton" class="animate-pulse">
            <div class="h-6 bg-gray-200 rounded w-48 mb-1"></div>
            <div class="h-3 bg-gray-200 rounded w-32"></div>
        </div>
        <div id="stats-header" class="hidden">
            <h1 id="stats-title"   class="text-2xl font-bold text-dark"></h1>
            <p  id="stats-address" class="text-muted text-sm mt-0.5"></p>
        </div>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-white border-l-4 border-navy rounded-2xl p-5 shadow-card">
            <p id="stat-views"  class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Vues</p>
        </div>
        <div class="bg-white border-l-4 border-primary-500 rounded-2xl p-5 shadow-card">
            <p id="stat-favs"   class="text-3xl font-bold text-primary-600">—</p>
            <p class="text-muted text-sm mt-1">Favoris</p>
        </div>
        <div class="bg-white border-l-4 border-blue-400 rounded-2xl p-5 shadow-card">
            <p id="stat-msgs"   class="text-3xl font-bold text-blue-600">—</p>
            <p class="text-muted text-sm mt-1">Messages</p>
        </div>
        <div class="bg-white border-l-4 border-yellow-400 rounded-2xl p-5 shadow-card">
            <p id="stat-rating" class="text-3xl font-bold text-yellow-600">—</p>
            <p class="text-muted text-sm mt-1">Note moyenne</p>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-2xl shadow-card p-6">
        <h2 class="text-base font-bold text-dark mb-4">Actions rapides</h2>
        <div class="flex flex-wrap gap-3">
            <a id="edit-btn"    href="#"          class="btn-outline text-sm px-5 py-2.5">Modifier le bien</a>
            <a href="/requests"                   class="btn-outline text-sm px-5 py-2.5">Voir les demandes</a>
            <a href="/messages"                   class="btn-outline text-sm px-5 py-2.5">Voir les messages</a>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    const PROPERTY_ID = window.location.pathname.split('/')[2];
    document.addEventListener('DOMContentLoaded', async () => {
        if(!AuthManager.isOwner()){window.location.href='/';return;}
        try {
            const data=await Properties.stats(PROPERTY_ID);
            document.getElementById('stats-skeleton').classList.add('hidden');
            document.getElementById('stats-header').classList.remove('hidden');
            document.getElementById('stats-title').textContent   = data.titre;
            document.getElementById('stats-address').textContent = `Bien #${data.property_id}`;
            document.getElementById('stat-views').textContent    = data.nombre_vues;
            document.getElementById('stat-favs').textContent     = data.nombre_favoris;
            document.getElementById('stat-msgs').textContent     = data.nombre_messages;
            document.getElementById('stat-rating').textContent   = data.note_moyenne>0?`★ ${data.note_moyenne}`:'—';
            document.getElementById('edit-btn').href = `/properties/${PROPERTY_ID}/edit`;
            document.title = `${data.titre} — Statistiques`;
        } catch(e) { Toast.error('Impossible de charger les statistiques.'); }
    });
</script>
@endpush