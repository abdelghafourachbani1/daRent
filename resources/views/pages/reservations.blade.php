@extends('layouts.app')
@section('title', 'Mes réservations — DAR-RENT')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-dark">Mes réservations</h1>
        <p class="text-muted text-sm mt-1">Suivez l'état de vos demandes de location</p>
    </div>

    <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
        <button onclick="filterRes('')"         class="tab-pill active" data-s="">Toutes</button>
        <button onclick="filterRes('pending')"  class="tab-pill" data-s="pending">En attente</button>
        <button onclick="filterRes('accepted')" class="tab-pill" data-s="accepted">Acceptées</button>
        <button onclick="filterRes('rejected')" class="tab-pill" data-s="rejected">Refusées</button>
    </div>

    <div id="res-skeleton" class="space-y-4">
        @for ($i = 0; $i < 3; $i++)<div class="animate-pulse bg-gray-200 rounded-2xl h-28"></div>@endfor
    </div>
    <div id="res-list"  class="hidden space-y-4"></div>
    <div id="res-empty" class="hidden text-center py-24">
        <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-xl font-bold text-dark">Aucune réservation</p>
        <a href="/" class="btn-primary inline-block mt-6 px-6 py-3">Chercher un bien</a>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.js"></script>
<script>
    AuthManager.requireAuth();
    let allRes=[];
    document.addEventListener('DOMContentLoaded', async () => {
        setupWS(AuthManager.getUser().id);
        const data=await Reservations.getAll();
        allRes=data.data||[];
        document.getElementById('res-skeleton').classList.add('hidden');
        renderRes(allRes);
    });
    function filterRes(status){
        document.querySelectorAll('.tab-pill').forEach(b=>b.classList.toggle('active',b.dataset.s===status));
        renderRes(status?allRes.filter(r=>r.status===status):allRes);
    }
    function renderRes(list){
        const c=document.getElementById('res-list');const e=document.getElementById('res-empty');c.innerHTML='';
        if(!list.length){c.classList.add('hidden');e.classList.remove('hidden');return;}
        e.classList.add('hidden');c.classList.remove('hidden');
        const borderColor={pending:'border-yellow-400',accepted:'border-green-400',rejected:'border-red-400'};
        list.forEach(r=>{
            c.innerHTML+=`
                <div class="bg-white border-l-4 ${borderColor[r.status]||'border-gray-200'} rounded-2xl p-5 shadow-card">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <div class="flex gap-4">
                            <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="${r.property?.media?.[0]?Helpers.imageUrl(r.property.media[0].url_fichier):'/images/default-property.svg'}" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-bold text-dark"><a href="/properties/${r.property_id}" class="hover:text-primary-600 transition">${r.property?.titre||'Bien supprimé'}</a></p>
                                <p class="text-sm text-muted">Propriétaire: <span class="font-medium text-dark">${r.owner?.nom||''}</span></p>
                                <p class="text-sm text-muted">${Helpers.formatDate(r.date_debut)} → ${Helpers.formatDate(r.date_fin)}</p>
                                <p class="text-sm font-bold text-primary-600 mt-1">${new Intl.NumberFormat('fr-MA').format(r.prix_total)} MAD</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-3">
                            ${Helpers.statusBadge(r.status)}
                            ${r.status==='pending'?`<button onclick="cancelRes(${r.id},this)" class="text-xs border-2 border-red-200 text-red-500 rounded-xl px-3 py-1.5 hover:bg-red-50 transition font-medium">Annuler</button>`:''}
                        </div>
                    </div>
                </div>`;
        });
    }
    async function cancelRes(id,btn){
        if(!confirm('Annuler cette demande ?'))return;btn.disabled=true;
        try{await Reservations.cancel(id);Toast.success('Demande annulée');const data=await Reservations.getAll();allRes=data.data||[];renderRes(allRes);}catch(e){Toast.error(e.message);btn.disabled=false;}
    }
    function setupWS(userId){
        try{
            const echo=new Echo({broadcaster:'reverb',key:'my-app-key',wsHost:'localhost',wsPort:8080,forceTLS:false,enabledTransports:['ws'],authEndpoint:'/broadcasting/auth',auth:{headers:{Authorization:'Bearer '+AuthManager.getToken()}}});
            echo.private(`reservations.${userId}`).listen('ReservationStatusChanged',async(event)=>{
                const r=event.reservation;
                Toast.show(r.status==='accepted'?`Demande pour "${r.property?.titre}" acceptée !`:`Demande pour "${r.property?.titre}" refusée.`,r.status==='accepted'?'success':'error',5000);
                const data=await Reservations.getAll();allRes=data.data||[];renderRes(allRes);
            });
        }catch(e){}
    }
</script>
@endpush