@extends('layouts.app')
@section('title', 'DAR-RENT — Trouvez votre logement idéal au Maroc')
@section('content')

{{-- HERO --}}
<section class="relative min-h-[600px] flex items-center overflow-hidden"
         style="background: linear-gradient(135deg, #1a2332 0%, #243044 50%, #1a2332 100%);">
    <div class="absolute inset-0 opacity-10"
         style="background-image: radial-gradient(circle at 25% 50%, #C0704A 0%, transparent 50%), radial-gradient(circle at 75% 50%, #C0704A 0%, transparent 50%);"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-20">
        <div class="max-w-3xl">
            <span class="inline-block bg-[#C0704A]/20 text-primary-300 text-sm font-semibold px-4 py-1.5 rounded-full mb-6 border border-[#C0704A]/30">
                🏡 Plateforme N°1 au Maroc
            </span>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                Trouvez votre<br>
                <span class="text-primary-400">logement idéal</span><br>
                au Maroc
            </h1>
            <p class="text-gray-400 text-xl mb-10 leading-relaxed">
                Des milliers de biens à louer à Marrakech, Casablanca, Rabat et partout au Maroc.
            </p>
            <div class="flex gap-8 mb-12">
                <div><p class="text-3xl font-bold text-white">2K+</p><p class="text-gray-400 text-sm">Biens disponibles</p></div>
                <div class="w-px bg-gray-700"></div>
                <div><p class="text-3xl font-bold text-white">500+</p><p class="text-gray-400 text-sm">Propriétaires</p></div>
                <div class="w-px bg-gray-700"></div>
                <div><p class="text-3xl font-bold text-white">10+</p><p class="text-gray-400 text-sm">Villes</p></div>
            </div>
        </div>

        {{-- SEARCH BAR --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.10)]-hover p-2 max-w-4xl">
            <div class="flex flex-col md:flex-row gap-2">
                <div class="flex-1 px-4 py-3 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="block text-xs font-bold text-[#6b7280] uppercase tracking-wider mb-1">Recherche</label>
                    <input id="search-keyword" type="text" placeholder="Quartier, titre..."
                        class="w-full text-sm text-[#1a1a1a] outline-none placeholder-gray-400 font-medium">
                </div>
                <div class="flex-1 px-4 py-3 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="block text-xs font-bold text-[#6b7280] uppercase tracking-wider mb-1">Ville</label>
                    <select id="search-city" class="w-full text-sm text-[#1a1a1a] outline-none bg-transparent font-medium">
                        <option value="">Toutes les villes</option>
                        <option>Marrakech</option><option>Casablanca</option>
                        <option>Rabat</option><option>Agadir</option><option>Fès</option>
                    </select>
                </div>
                <div class="flex-1 px-4 py-3 border-b md:border-b-0 md:border-r border-gray-100">
                    <label class="block text-xs font-bold text-[#6b7280] uppercase tracking-wider mb-1">Type</label>
                    <select id="search-type" class="w-full text-sm text-[#1a1a1a] outline-none bg-transparent font-medium">
                        <option value="">Tous les types</option>
                        <option value="apartment">Appartement</option>
                        <option value="villa">Villa</option>
                        <option value="studio">Studio</option>
                        <option value="riad">Riad</option>
                    </select>
                </div>
                <button onclick="doSearch()" class="btn-primary flex items-center justify-center gap-2 px-6 py-3 rounded-xl whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    Rechercher
                </button>
            </div>
        </div>
    </div>
</section>

{{-- CATEGORY PILLS --}}
<section class="bg-white border-b border-gray-100 sticky top-16 z-30 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 py-3 overflow-x-auto scrollbar-hide">
            <button onclick="filterByType('')"          class="cat-pill active" data-type="">Tout</button>
            <button onclick="filterByType('apartment')" class="cat-pill" data-type="apartment">Appartements</button>
            <button onclick="filterByType('villa')"     class="cat-pill" data-type="villa">Villas</button>
            <button onclick="filterByType('studio')"    class="cat-pill" data-type="studio">Studios</button>
            <button onclick="filterByType('riad')"      class="cat-pill" data-type="riad">Riads</button>
            <div class="ml-auto flex-shrink-0">
                <button onclick="toggleFilters()"
                    class="flex items-center gap-2 border border-gray-200 rounded-xl px-4 py-2 text-sm font-medium text-[#1a1a1a] hover:border-[#C0704A] hover:text-[#C0704A] transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filtres
                </button>
            </div>
        </div>

        <div id="advanced-filters" class="hidden pb-4 flex flex-wrap gap-4 items-end pt-2 border-t border-gray-100">
            <div>
                <label class="block text-xs font-semibold text-[#6b7280] mb-1">Prix min (MAD)</label>
                <input id="filter-min" type="number" placeholder="0" class="input-field w-36 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6b7280] mb-1">Prix max (MAD)</label>
                <input id="filter-max" type="number" placeholder="50 000" class="input-field w-36 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-[#6b7280] mb-1">Chambres min</label>
                <input id="filter-beds" type="number" placeholder="0" min="0" class="input-field w-28 py-2 text-sm">
            </div>
            <button onclick="applyFilters()" class="btn-primary py-2 px-5 text-sm">Appliquer</button>
            <button onclick="resetFilters()" class="btn-outline py-2 px-5 text-sm">Réinitialiser</button>
        </div>
    </div>
</section>

{{-- PROPERTIES GRID --}}
<section id="properties" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex justify-between items-center mb-6">
        <p id="results-count" class="text-sm text-[#6b7280]">Chargement...</p>
        <select id="sort-select" onchange="applyFilters()"
            class="text-sm border border-gray-200 rounded-xl px-3 py-2 outline-none text-[#1a1a1a] focus:border-[#C0704A]">
            <option value="newest">Plus récents</option>
            <option value="price_asc">Prix croissant</option>
            <option value="price_desc">Prix décroissant</option>
        </select>
    </div>

    <div id="properties-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @for ($i = 0; $i < 8; $i++)
        <div class="animate-pulse">
            <div class="bg-gray-200 rounded-2xl aspect-square mb-3"></div>
            <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2 mb-2"></div>
            <div class="h-4 bg-gray-200 rounded w-1/3"></div>
        </div>
        @endfor
    </div>

    <div id="load-more-wrap" class="hidden text-center mt-10">
        <button onclick="loadMore()" class="btn-outline px-10 py-3">Voir plus de biens</button>
    </div>

    <div id="empty-state" class="hidden text-center py-24">
        <div class="w-20 h-20 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <p class="text-xl font-bold text-[#1a1a1a]">Aucun bien trouvé</p>
        <p class="text-[#6b7280] mt-2 text-sm">Essayez d'autres critères de recherche</p>
        <button onclick="resetFilters()" class="btn-primary mt-6 px-8 py-3">Voir tous les biens</button>
    </div>
</section>

{{-- CTA --}}
<section class="bg-[#1a2332] py-16 px-4">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Vous avez un bien à louer ?</h2>
        <p class="text-gray-400 mb-8">Publiez votre annonce et trouvez des locataires de confiance.</p>
        <a href="/register" class="btn-primary px-8 py-4 text-base">Publier mon bien</a>
    </div>
</section>

@endsection
@push('head')
<style>
    .cat-pill { flex-shrink:0; padding:.5rem 1rem; border-radius:9999px; font-size:.875rem; font-weight:500; color:#6b7280; border:2px solid transparent; transition:all .15s; white-space:nowrap; }
    .cat-pill:hover { background:#fdf3ee; color:#C0704A; }
    .cat-pill.active { border-color:#C0704A; color:#C0704A; background:#fdf3ee; }
</style>
@endpush
@push('scripts')
<script>
    let currentPage=1, currentFilters={}, currentType='';

    document.addEventListener('DOMContentLoaded', () => loadProperties());

    async function loadProperties(page=1, append=false) {
        const params={...currentFilters,page,per_page:12};
        Object.keys(params).forEach(k=>!params[k]&&delete params[k]);
        try {
            const data=await Properties.getAll(params);
            if(!append) document.getElementById('properties-grid').innerHTML='';
            if(data.total===0&&!append){
                document.getElementById('empty-state').classList.remove('hidden');
                document.getElementById('load-more-wrap').classList.add('hidden');
            } else {
                document.getElementById('empty-state').classList.add('hidden');
                data.data.forEach(p=>renderCard(p));
                if(data.current_page<data.last_page){currentPage=data.current_page;document.getElementById('load-more-wrap').classList.remove('hidden');}
                else document.getElementById('load-more-wrap').classList.add('hidden');
            }
            document.getElementById('results-count').textContent=`${data.total} bien${data.total>1?'s':''} trouvé${data.total>1?'s':''}`;
        } catch(e){Toast.error('Erreur lors du chargement.');}
    }

    function renderCard(p) {
        const grid=document.getElementById('properties-grid');
        const card=document.createElement('div');
        card.className='property-card';
        card.innerHTML=`
            <div class="relative aspect-square overflow-hidden bg-gray-100">
                <button onclick="toggleFav(event,${p.id},this)"
                    class="absolute top-3 right-3 p-2 rounded-full bg-white/90 hover:bg-white transition shadow" data-favorited="false">
                    <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </button>
                <span class="absolute bottom-3 left-3 bg-[#C0704A] text-white text-xs font-bold px-3 py-1 rounded-full capitalize">${p.type}</span>
            </div>
            <div class="p-4 cursor-pointer" onclick="window.location='/properties/${p.id}'">
                <p class="font-semibold text-[#1a1a1a] truncate text-sm">${p.titre}</p>
                <p class="text-[#6b7280] text-xs mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    ${p.city?.nom_ville||''} · ${Helpers.bedsLabel(p.bedrooms)}
                </p>
                <p class="font-bold text-primary-600 text-sm mt-2">${new Intl.NumberFormat('fr-MA').format(p.prix_mensuel)} <span class="font-normal text-[#6b7280] text-xs">MAD/mois</span></p>
            </div>`;
        grid.appendChild(card);
    }

    function doSearch() {
        currentFilters={search:document.getElementById('search-keyword').value,city:document.getElementById('search-city').value,type:document.getElementById('search-type').value,sort:document.getElementById('sort-select').value};
        loadProperties(1);
        document.getElementById('properties').scrollIntoView({behavior:'smooth'});
    }
    function filterByType(type) {
        currentType=type;currentFilters.type=type;
        document.querySelectorAll('.cat-pill').forEach(b=>b.classList.toggle('active',b.dataset.type===type));
        loadProperties(1);
    }
    function applyFilters() {
        currentFilters={...currentFilters,type:currentType,min_price:document.getElementById('filter-min')?.value,max_price:document.getElementById('filter-max')?.value,bedrooms:document.getElementById('filter-beds')?.value,sort:document.getElementById('sort-select').value};
        loadProperties(1);
    }
    function resetFilters() {
        currentFilters={};currentType='';
        ['search-keyword','search-city','search-type','filter-min','filter-max','filter-beds'].forEach(id=>{const el=document.getElementById(id);if(el)el.value='';});
        document.querySelectorAll('.cat-pill').forEach(b=>b.classList.remove('active'));
        document.querySelector('[data-type=""]')?.classList.add('active');
        loadProperties(1);
    }
    function loadMore(){loadProperties(currentPage+1,true);}
    function toggleFilters(){document.getElementById('advanced-filters').classList.toggle('hidden');}
    async function toggleFav(event,id,btn) {
        event.stopPropagation();
        if(!AuthManager.isLoggedIn()){window.location.href='/login';return;}
        const isFav=btn.dataset.favorited==='true';const svg=btn.querySelector('svg');
        try {
            if(isFav){await Favorites.remove(id);svg.setAttribute('fill','none');svg.classList.remove('text-[#C0704A]');svg.classList.add('text-gray-500');btn.dataset.favorited='false';Toast.success('Retiré des favoris');}
            else{await Favorites.add(id);svg.setAttribute('fill','#C0704A');svg.classList.add('text-[#C0704A]');svg.classList.remove('text-gray-500');btn.dataset.favorited='true';Toast.success('Ajouté aux favoris');}
        } catch(e){Toast.error(e.message);}
    }
</script>
@endpush