@extends('layouts.app')
@section('title', 'Demandes — DAR-RENT')
@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-dark">Demandes de location</h1>
        <p class="text-muted text-sm mt-1">Acceptez ou refusez les demandes de vos locataires</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
        <div class="bg-white border-l-4 border-yellow-400 rounded-2xl p-5 shadow-card">
            <p id="stat-pending"  class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">En attente</p>
        </div>
        <div class="bg-white border-l-4 border-green-400 rounded-2xl p-5 shadow-card">
            <p id="stat-accepted" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Acceptées</p>
        </div>
        <div class="bg-white border-l-4 border-red-400 rounded-2xl p-5 shadow-card">
            <p id="stat-rejected" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Refusées</p>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="stat-studio" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Demandes pour Studios</p>
        </div>
        <div class="bg-white rounded-2xl p-5 shadow-card">
            <p id="stat-riad" class="text-3xl font-bold text-dark">—</p>
            <p class="text-muted text-sm mt-1">Demandes pour Riads</p>
        </div>
    </div>

    <div class="flex gap-1 mb-6 bg-gray-100 rounded-xl p-1 w-fit">
        <button onclick="filterReq('')"         class="tab-pill active" data-s="">Toutes</button>
        <button onclick="filterReq('pending')"  class="tab-pill" data-s="pending">En attente</button>
        <button onclick="filterReq('accepted')" class="tab-pill" data-s="accepted">Acceptées</button>
        <button onclick="filterReq('rejected')" class="tab-pill" data-s="rejected">Refusées</button>
    </div>

    <div id="req-skeleton" class="space-y-4">
        @for ($i = 0; $i < 3; $i++)<div class="animate-pulse bg-gray-200 rounded-2xl h-32"></div>@endfor
    </div>
    <div id="req-list"  class="hidden space-y-4"></div>
    <div id="req-empty" class="hidden text-center py-20">
        <div class="w-16 h-16 bg-primary-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <p class="text-xl font-bold text-dark">Aucune demande reçue</p>
        <p class="text-muted text-sm mt-1">Les demandes de vos locataires apparaîtront ici</p>
    </div>
</div>
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    let allReqs=[];
    document.addEventListener('DOMContentLoaded', async () => {
        if(!AuthManager.isOwner()){window.location.href='/';return;}
        const data=await Reservations.getAll();allReqs=data.data||[];
        document.getElementById('req-skeleton').classList.add('hidden');
        updateStats(allReqs);renderReqs(allReqs);
    });
    function updateStats(list){
        document.getElementById('stat-pending').textContent  = list.filter(r=>r.status==='pending').length;
        document.getElementById('stat-accepted').textContent = list.filter(r=>r.status==='accepted').length;
        document.getElementById('stat-rejected').textContent = list.filter(r=>r.status==='rejected').length;
        document.getElementById('stat-studio').textContent  = list.filter(r=>r.property?.type==='studio').length;
        document.getElementById('stat-riad').textContent    = list.filter(r=>r.property?.type==='riad').length;
    }
    function filterReq(status){
        document.querySelectorAll('.tab-pill').forEach(b=>b.classList.toggle('active',b.dataset.s===status));
        renderReqs(status?allReqs.filter(r=>r.status===status):allReqs);
    }
    function renderReqs(list){
        const c=document.getElementById('req-list');const e=document.getElementById('req-empty');c.innerHTML='';
        if(!list.length){c.classList.add('hidden');e.classList.remove('hidden');return;}
        e.classList.add('hidden');c.classList.remove('hidden');
        const borderColor={pending:'border-yellow-400',accepted:'border-green-400',rejected:'border-red-400'};
        list.forEach(r=>{
            c.innerHTML+=`
                <div class="bg-white border-l-4 ${borderColor[r.status]||'border-gray-200'} rounded-2xl p-5 shadow-card">
                    <div class="flex flex-col md:flex-row justify-between gap-4">
                        <div class="flex gap-4">
                            <img src="${Helpers.avatarUrl(r.tenant?.avatar,r.tenant?.nom)}" class="w-12 h-12 rounded-full object-cover flex-shrink-0 border-2 border-gray-100">
                            <div>
                                <p class="font-bold text-dark">${r.tenant?.nom||'Locataire'}</p>
                                <p class="text-sm text-muted mt-0.5"><a href="/properties/${r.property_id}" class="hover:text-primary-600 font-medium transition">${r.property?.titre||''}</a></p>
                                <p class="text-sm text-muted">${Helpers.formatDate(r.date_debut)} → ${Helpers.formatDate(r.date_fin)}</p>
                                <p class="text-sm font-bold text-primary-600 mt-1">${new Intl.NumberFormat('fr-MA').format(r.prix_total)} MAD</p>
                                ${r.message?`<p class="text-xs text-muted mt-1 italic bg-gray-50 rounded-lg px-3 py-1.5">"${r.message}"</p>`:''}
                            </div>
                        </div>
                        <div class="flex flex-col items-end justify-between gap-3">
                            ${Helpers.statusBadge(r.status)}
                            ${r.status==='pending'?`
                            <div class="flex gap-2">
                                <button onclick="handleReq(${r.id},'accept')" id="accept-${r.id}"
                                    class="text-sm bg-green-500 hover:bg-green-600 text-white rounded-xl px-4 py-2 font-semibold transition">Accepter</button>
                                <button onclick="handleReq(${r.id},'reject')" id="reject-${r.id}"
                                    class="text-sm border-2 border-red-200 text-red-500 hover:bg-red-50 rounded-xl px-4 py-2 font-semibold transition">Refuser</button>
                            </div>`:''}
                        </div>
                    </div>
                </div>`;
        });
    }
    async function handleReq(id,action){
        const ab=document.getElementById(`accept-${id}`);const rb=document.getElementById(`reject-${id}`);
        if(ab)ab.disabled=true;if(rb)rb.disabled=true;
        try{
            if(action==='accept')await Reservations.accept(id);else await Reservations.reject(id);
            Toast.success(action==='accept'?'Demande acceptée !':'Demande refusée.');
            const data=await Reservations.getAll();allReqs=data.data||[];updateStats(allReqs);renderReqs(allReqs);
        }catch(e){Toast.error(e.message);if(ab)ab.disabled=false;if(rb)rb.disabled=false;}
    }
</script>
@endpush