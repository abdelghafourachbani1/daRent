@extends('layouts.app')
@section('title', 'Conversation — DAR-RENT')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    <a href="/messages" class="inline-flex items-center gap-2 text-sm text-muted hover:text-dark mb-6 transition">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Toutes les conversations
    </a>

    <div class="bg-white rounded-2xl shadow-card overflow-hidden border border-gray-100 flex flex-col" style="height:75vh;">

        <div id="chat-header" class="px-5 py-4 border-b border-gray-100 bg-white flex items-center gap-3">
            <div class="animate-pulse flex gap-3 items-center w-full">
                <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                <div class="h-3 bg-gray-200 rounded w-32"></div>
            </div>
        </div>

        <div id="messages-area" class="flex-1 overflow-y-auto px-5 py-4 space-y-3 bg-gray-50"></div>

        <div class="px-5 py-4 border-t border-gray-100 bg-white flex gap-3">
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
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    const CONV_ID     = {{ $convId }};
    let currentUser = AuthManager.getUser();
    const reverbConfig = {
        key:    @json(env('REVERB_APP_KEY', 'darrent-key')),
        host:   @json(env('VITE_REVERB_HOST', env('REVERB_HOST', 'localhost'))),
        port:   {{ (int) env('VITE_REVERB_PORT', env('REVERB_PORT', 8080)) }},
        scheme: @json(env('VITE_REVERB_SCHEME', env('REVERB_SCHEME', 'http'))),
    };

    document.addEventListener('DOMContentLoaded', async () => {
        await hydrateCurrentUser();
        await loadConversation();
        setupEcho();
        subscribeToConversation();
    });

    async function hydrateCurrentUser() {
        try {
            const data = await Auth.me();
            currentUser = data.user || data;
            if (currentUser) {
                AuthManager.save(AuthManager.getToken(), currentUser);
            }
        } catch (e) {
            AuthManager.clear();
            window.location.href = '/login';
        }
    }

    function resolveWsHost(configuredHost) {
        if (!configuredHost || ['0.0.0.0', '::'].includes(configuredHost)) {
            return window.location.hostname;
        }

        return configuredHost;
    }

    async function loadConversation() {
        const data  = await Messaging.getConversation(CONV_ID);
        const conv  = data.conversation;
        const other = currentUser.role==='owner' ? conv.tenant : conv.owner;
        document.getElementById('chat-header').innerHTML=`
            <img src="${Helpers.avatarUrl(other?.avatar,other?.nom)}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 border-2 border-primary-100">
            <div>
                <p class="font-bold text-sm text-dark">${other?.nom||''}</p>
                <p class="text-xs text-muted"><a href="/properties/${conv.property?.id}" class="hover:text-primary-600 transition">${conv.property?.titre||''}</a></p>
            </div>`;
        document.title=`${other?.nom||'Conversation'} — DAR-RENT`;
        const area=document.getElementById('messages-area');area.innerHTML='';
        data.messages.forEach(msg=>appendMessage(msg));area.scrollTop=area.scrollHeight;
    }

    function appendMessage(msg) {
        const area=document.getElementById('messages-area');
        if (msg.id && area.querySelector(`[data-message-id="${msg.id}"]`)) return;

        const isMe=Number(msg.sender_id)===Number(currentUser.id)||Number(msg.sender?.id)===Number(currentUser.id);
        const div=document.createElement('div');
        div.className=`flex ${isMe?'justify-end':'justify-start'} items-end gap-2`;
        if (msg.id) {
            div.dataset.messageId = msg.id;
        }
        div.innerHTML=`
            ${!isMe?`<img src="${Helpers.avatarUrl(msg.sender?.avatar,msg.sender?.nom)}" class="w-7 h-7 rounded-full object-cover flex-shrink-0 border border-gray-100">`:'' }
            <div class="max-w-sm">
                <div class="px-4 py-2.5 rounded-2xl text-sm ${isMe?'bg-primary-500 text-white rounded-br-sm':'bg-white border border-gray-100 text-dark rounded-bl-sm shadow-sm'}">
                    ${msg.contenu}
                </div>
                <p class="text-xs text-muted mt-1 ${isMe?'text-right':'text-left'}">${Helpers.timeAgo(msg.date_envoie||msg.created_at)}</p>
            </div>`;
        area.appendChild(div);area.scrollTop=area.scrollHeight;
    }

    async function sendMsg() {
        const input=document.getElementById('msg-input');const content=input.value.trim();
        if(!content)return;input.value='';
        try{const data=await Messaging.sendMessage({conversation_id:CONV_ID,contenu:content});appendMessage(data.data);}catch(e){Toast.error(e.message);input.value=content;}
    }

function setupEcho() {
    if (!window.Echo || !window.Pusher) {
        console.warn('Echo/Pusher unavailable; conversation will still load without realtime.');
        return;
    }

    // destroy previous instance if exists
    if (window.echoInstance) {
        window.echoInstance.disconnect();
    }

    const wsHost = resolveWsHost(reverbConfig.host);
    const isSecure = reverbConfig.scheme === 'https';

    window.echoInstance = new Echo({
        broadcaster:       'reverb',
        key:               reverbConfig.key,
        wsHost:            wsHost,
        wsPort:            reverbConfig.port,
        wssPort:           reverbConfig.port,
        forceTLS:          isSecure,
        enabledTransports: isSecure ? ['wss', 'ws'] : ['ws'],
        authEndpoint:      '/api/broadcasting/auth',  // ← important: /api prefix
        auth: {
            headers: {
                'Authorization': 'Bearer ' + AuthManager.getToken(),
                'Accept':        'application/json',
            }
        },
    });
}

function subscribeToConversation() {
    if (!window.echoInstance) return;

    window.echoInstance.leave(`conversation.${CONV_ID}`);

    window.echoInstance
        .private(`conversation.${CONV_ID}`)
        .listen('.MessageSent', (event) => {
            appendMessage(event.message);
        });

    if (currentUser?.id) {
        window.echoInstance
            .private(`App.Models.User.${currentUser.id}`)
            .listen('.MessageSent', (event) => {
                if (Number(event.message?.conversation_id) === Number(CONV_ID)) {
                    appendMessage(event.message);
                }
            });
    }
}
</script>
@endpush
