@extends('layouts.app')
@section('title', 'Mes favoris — DAR-RENT')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-dark">Mes favoris</h1>
        <p class="text-muted text-sm mt-1">Les biens que vous avez sauvegardés</p>
    </div>

    <div id="fav-skeleton" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @for ($i = 0; $i < 4; $i++)
        <div class="animate-pulse">
            <div class="bg-gray-200 rounded-2xl aspect-square mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
        </div>
        @endfor
    </div>

    <div id="fav-grid"  class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>

    <div id="fav-empty" class="hidden text-center py-24">
        <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </div>
        <p class="text-xl font-bold text-dark">Aucun favori</p>
        <p class="text-muted text-sm mt-1">Explorez les biens et cliquez sur le cœur pour sauvegarder</p>
        <a href="/" class="btn-primary inline-block mt-6 px-6 py-3">Explorer les biens</a>
    </div>
</div>
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const data = await Favorites.getAll();
            document.getElementById('fav-skeleton').classList.add('hidden');
            if (!data.favorites?.length) { document.getElementById('fav-empty').classList.remove('hidden'); return; }
            const grid = document.getElementById('fav-grid');
            grid.classList.remove('hidden');
            data.favorites.forEach(fav => {
                const p   = fav.property;
                const img = p?.media?.[0] ? Helpers.imageUrl(p.media[0].url_fichier) : '/images/default-property.svg';
                const card = document.createElement('div');
                card.className = 'property-card';
                card.id = `fav-card-${p.id}`;
                card.innerHTML = `
                    <div class="relative aspect-square overflow-hidden bg-gray-100">
                        <img src="${img}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        <button onclick="removeFav(event,${p.id})"
                            class="absolute top-3 right-3 p-2 rounded-full bg-white/90 hover:bg-white transition shadow">
                            <svg class="w-4 h-4 text-primary-500" viewBox="0 0 24 24" fill="#C0704A" stroke="#C0704A" stroke-width="1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                        <span class="absolute bottom-3 left-3 bg-primary-500 text-white text-xs font-bold px-3 py-1 rounded-full capitalize">${p?.type||''}</span>
                    </div>
                    <div class="p-4 cursor-pointer" onclick="window.location='/properties/${p.id}'">
                        <p class="font-semibold text-dark text-sm truncate">${p?.titre}</p>
                        <p class="text-muted text-xs mt-1">${p?.city?.nom_ville||''}</p>
                        <p class="font-bold text-primary-600 text-sm mt-2">${new Intl.NumberFormat('fr-MA').format(p?.prix_mensuel)} <span class="font-normal text-muted text-xs">MAD/mois</span></p>
                        <p class="text-xs text-muted mt-1">Ajouté le ${Helpers.formatDate(fav.date_ajout)}</p>
                    </div>`;
                grid.appendChild(card);
            });
        } catch (e) { Toast.error('Erreur lors du chargement.'); }
    });

    async function removeFav(e, id) {
        e.stopPropagation();
        try {
            await Favorites.remove(id);
            document.getElementById(`fav-card-${id}`)?.remove();
            Toast.success('Retiré des favoris');
            if (!document.querySelector('#fav-grid .property-card')) {
                document.getElementById('fav-grid').classList.add('hidden');
                document.getElementById('fav-empty').classList.remove('hidden');
            }
        } catch (e) { Toast.error(e.message); }
    }
</script>
@endpush