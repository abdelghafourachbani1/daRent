@extends('layouts.app')
@section('title', 'Mes biens — DAR-RENT')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-dark">Mes biens</h1>
            <p class="text-muted text-sm mt-1">Gérez vos annonces de location</p>
        </div>
        <a href="/properties/create" class="btn-primary px-5 py-2.5 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Ajouter un bien
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="prop-stat-total" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Biens</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="prop-stat-available" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Disponibles</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="prop-stat-rented" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Loués</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="prop-stat-studio" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Studios</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="prop-stat-riad" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Riads</p>
        </div>
    </div>
    <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
        <button onclick="filterStatus('')"          class="tab-pill active" data-s="">Tous</button>
        <button onclick="filterStatus('available')" class="tab-pill" data-s="available">Disponibles</button>
        <button onclick="filterStatus('rented')"    class="tab-pill" data-s="rented">Loués</button>
    </div>

    <div id="my-grid"  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @for ($i=0;$i<3;$i++)<div class="animate-pulse bg-gray-200 rounded-2xl h-64"></div>@endfor
    </div>
    <div id="my-empty" class="hidden text-center py-20">
        <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </div>
        <p class="text-xl font-bold text-dark">Aucun bien publié</p>
        <a href="/properties/create" class="btn-primary mt-6 inline-block px-6 py-2">Publier un bien</a>
    </div>
</div>
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    let allProperties=[];
    document.addEventListener('DOMContentLoaded', async () => {
        if(!AuthManager.isOwner()){window.location.href='/';return;}
        const data=await Properties.myList();allProperties=data.data||[];
        renderStats(allProperties);
        renderProperties(allProperties);
    });
    function filterStatus(status){
        document.querySelectorAll('.tab-pill').forEach(b=>b.classList.toggle('active',b.dataset.s===status));
        renderProperties(status?allProperties.filter(p=>p.status===status):allProperties);
    }
    function renderStats(props){
        document.getElementById('prop-stat-total').textContent     = props.length;
        document.getElementById('prop-stat-available').textContent = props.filter(p=>p.status==='available').length;
        document.getElementById('prop-stat-rented').textContent    = props.filter(p=>p.status==='rented').length;
        document.getElementById('prop-stat-studio').textContent    = props.filter(p=>p.type==='studio').length;
        document.getElementById('prop-stat-riad').textContent      = props.filter(p=>p.type==='riad').length;
    }
    function renderProperties(props){
        const grid=document.getElementById('my-grid');const empty=document.getElementById('my-empty');
        grid.innerHTML='';
        if(!props.length){empty.classList.remove('hidden');return;}
        empty.classList.add('hidden');
        props.forEach(p=>{
            const img=p.media?.[0]?Helpers.imageUrl(p.media[0].url_fichier):'/images/default-property.svg';
            grid.innerHTML+=`
                <div class="bg-white rounded-2xl overflow-hidden shadow-card hover:shadow-card-hover transition-all duration-300">
                    <div class="relative aspect-video overflow-hidden bg-gray-100">
                        <img src="${img}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        <div class="absolute top-3 left-3">${Helpers.statusBadge(p.status)}</div>
                        <span class="absolute top-3 right-3 bg-primary-500 text-white text-xs font-bold px-2 py-1 rounded-full capitalize">${p.type}</span>
                    </div>
                    <div class="p-5">
                        <p class="font-bold text-dark truncate">${p.titre}</p>
                        <p class="text-muted text-xs mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            ${p.city?.nom_ville||''}
                        </p>
                        <p class="font-bold text-primary-600 mt-2">${new Intl.NumberFormat('fr-MA').format(p.prix_mensuel)} <span class="font-normal text-muted text-xs">MAD/mois</span></p>
                        <div class="flex gap-2 mt-4 pt-4 border-t border-gray-100">
                            <a href="/properties/${p.id}"       class="flex-1 text-center text-xs btn-outline py-2 px-2 rounded-lg">Voir</a>
                            <a href="/properties/${p.id}/edit"  class="flex-1 text-center text-xs btn-outline py-2 px-2 rounded-lg">Modifier</a>
                            <a href="/properties/${p.id}/stats" class="flex-1 text-center text-xs btn-outline py-2 px-2 rounded-lg">Stats</a>
                            <button onclick="deleteProperty(${p.id},this)" class="text-xs text-red-500 border border-red-200 rounded-lg py-2 px-3 hover:bg-red-50 transition">✕</button>
                        </div>
                    </div>
                </div>`;
        });
    }
    async function deleteProperty(id,btn){
        if(!confirm('Supprimer ce bien ?'))return;btn.disabled=true;
        try{
            await Properties.delete(id);
            Toast.success('Bien supprimé');
            allProperties = allProperties.filter(p=>p.id!==id);
            renderStats(allProperties);
            renderProperties(allProperties);
        }catch(e){Toast.error(e.message);btn.disabled=false;}
    }
</script>
@endpush