{{-- resources/views/pages/ — DAY 5 --}}
@extends('layouts.app')
@section('title', 'Messages — DAR-RENT')
@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-dark">Messages</h1>
        <p class="text-muted text-sm mt-1">Vos conversations avec les propriétaires et locataires</p>
    </div>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden border border-gray-100 flex" style="height:72vh;">

        {{-- Sidebar --}}
        <div class="w-80 flex-shrink-0 border-r border-gray-100 flex flex-col">
            <div class="px-4 py-4 border-b border-gray-100 bg-gray-50">
                <p class="text-sm font-bold text-dark">Conversations</p>
            </div>
            <div id="conv-list" class="flex-1 overflow-y-auto">
                @for ($i = 0; $i < 4; $i++)
                <div class="animate-pulse flex gap-3 px-4 py-3 border-b border-gray-50">
                    <div class="w-10 h-10 bg-gray-200 rounded-full flex-shrink-0"></div>
                    <div class="flex-1">
                        <div class="h-3 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-2 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        {{-- Chat --}}
        <div class="flex-1 flex flex-col">
            <div id="chat-header" class="px-5 py-4 border-b border-gray-100 bg-white flex items-center gap-3">
                <p class="text-muted text-sm">Sélectionnez une conversation</p>
            </div>
            <div id="messages-area" class="flex-1 overflow-y-auto px-5 py-4 space-y-3 bg-gray-50"></div>
            <div id="chat-input" class="hidden px-5 py-4 border-t border-gray-100 bg-white flex gap-3">
                <input type="text" id="msg-input" placeholder="Écrire un message..."
                    class="input-field flex-1 py-3 text-sm"
                    onkeypress="if(event.key==='Enter') sendMsg()">
                <button onclick="sendMsg()" class="btn-primary px-5 py-3 flex-shrink-0 rounded-xl">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.js"></script>
<script>
    AuthManager.requireAuth();
    let activeConvId=null, echoInstance=null;
    const currentUser=AuthManager.getUser();

    document.addEventListener('DOMContentLoaded', async () => { setupEcho(); await loadConversations(); });

    function setupEcho() {
        echoInstance=new Echo({broadcaster:'reverb',key:'my-app-key',wsHost:'localhost',wsPort:8080,forceTLS:false,enabledTransports:['ws'],authEndpoint:'/broadcasting/auth',auth:{headers:{Authorization:'Bearer '+AuthManager.getToken()}}});
    }

    async function loadConversations() {
        const data=await Messaging.getConversations();
        const list=document.getElementById('conv-list');list.innerHTML='';
        if(!data.conversations.length){list.innerHTML='<p class="text-sm text-muted text-center py-8">Aucune conversation</p>';return;}
        data.conversations.forEach(conv=>{
            const other=currentUser.role==='owner'?conv.tenant:conv.owner;
            const div=document.createElement('div');
            div.className='flex gap-3 px-4 py-3 border-b border-gray-50 cursor-pointer hover:bg-primary-50 transition';
            div.dataset.convId=conv.id;div.onclick=()=>openConversation(conv.id);
            div.innerHTML=`
                <img src="${Helpers.avatarUrl(other?.avatar,other?.nom)}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border-2 border-gray-100">
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-center">
                        <p class="text-sm font-semibold text-dark truncate">${other?.nom||'Inconnu'}</p>
                        ${conv.unread_count>0?`<span class="bg-primary-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0 font-bold">${conv.unread_count}</span>`:''}
                    </div>
                    <p class="text-xs text-muted truncate">${conv.property?.titre||''}</p>
                    <p class="text-xs text-muted truncate mt-0.5">${conv.latest_message?.contenu||''}</p>
                </div>`;
            list.appendChild(div);
        });
    }

    async function openConversation(convId) {
        activeConvId=convId;
        document.querySelectorAll('[data-conv-id]').forEach(el=>el.classList.toggle('bg-primary-50',el.dataset.convId==convId));
        const data=await Messaging.getConversation(convId);
        const conv=data.conversation;const other=currentUser.role==='owner'?conv.tenant:conv.owner;
        document.getElementById('chat-header').innerHTML=`
            <img src="${Helpers.avatarUrl(other?.avatar,other?.nom)}" class="w-9 h-9 rounded-full object-cover border-2 border-primary-100">
            <div>
                <p class="font-bold text-sm text-dark">${other?.nom||''}</p>
                <p class="text-xs text-muted">${conv.property?.titre||''}</p>
            </div>`;
        const area=document.getElementById('messages-area');area.innerHTML='';
        data.messages.forEach(msg=>appendMessage(msg));area.scrollTop=area.scrollHeight;
        document.getElementById('chat-input').classList.remove('hidden');
        if(echoInstance){
            echoInstance.leave(`conversation.${activeConvId}`);
            echoInstance.private(`conversation.${convId}`).listen('MessageSent',(event)=>{if(activeConvId===convId)appendMessage(event.message);loadConversations();});
        }
    }

    function appendMessage(msg) {
        const area=document.getElementById('messages-area');
        const isMe=msg.sender_id===currentUser.id||msg.sender?.id===currentUser.id;
        const div=document.createElement('div');
        div.className=`flex ${isMe?'justify-end':'justify-start'} items-end gap-2`;
        div.innerHTML=`
            ${!isMe?`<img src="${Helpers.avatarUrl(msg.sender?.avatar,msg.sender?.nom)}" class="w-7 h-7 rounded-full object-cover flex-shrink-0 border border-gray-100">`:'' }
            <div class="max-w-xs lg:max-w-md">
                <div class="px-4 py-2.5 rounded-2xl text-sm ${isMe?'bg-primary-500 text-white rounded-br-sm':'bg-white border border-gray-100 text-dark rounded-bl-sm shadow-sm'}">
                    ${msg.contenu}
                </div>
                <p class="text-xs text-muted mt-1 ${isMe?'text-right':'text-left'}">${Helpers.timeAgo(msg.date_envoie||msg.created_at)}</p>
            </div>`;
        area.appendChild(div);area.scrollTop=area.scrollHeight;
    }

    async function sendMsg() {
        const input=document.getElementById('msg-input');const content=input.value.trim();
        if(!content||!activeConvId)return;input.value='';
        try{const data=await Messaging.sendMessage({conversation_id:activeConvId,contenu:content});appendMessage(data.data);}catch(e){Toast.error(e.message);input.value=content;}
    }
</script>
@endpush