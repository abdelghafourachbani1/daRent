const API_BASE = '/api';

async function request(method, endpoint, data = null, isFormData = false) {

    const token = localStorage.getItem('darent_token');
    const headers = {};
    if (token) headers['Authorization'] = `Bearer ${token}`;
    if (window.echoInstance?.socketId?.()) {
        headers['X-Socket-ID'] = window.echoInstance.socketId();
    }
    if (!isFormData) {
        headers['Content-Type'] = 'application/json';
        headers['Accept'] = 'application/json';
    }
    const config = { method, headers };
    if (data) config.body = isFormData ? data : JSON.stringify(data);

    const response = await fetch(`${API_BASE}${endpoint}`, config);
    const json = await response.json();

    if (!response.ok) {
        const error   = new Error(json.message || 'Something went wrong');
        error.status  = response.status;
        error.errors  = json.errors || null;
        throw error;
    }
    return json;
}

const Auth = {
    register: (data) => request('POST','/auth/register', data),
    login: (data) => request('POST', '/auth/login', data),
    me: () => request('GET','/auth/me'),
    update:(data, isFormData = false) => request('PUT','/auth/me', data, isFormData),
    logout:()=> request('POST','/auth/logout'),
};

const Properties = {
    getAll: (params = {}) => {
        const query = new URLSearchParams(params).toString();
        return request('GET', `/properties${query ? '?' + query : ''}`);
    },
    getOne:(id) => request('GET',`/properties/${id}`),
    create:(data)=> request('POST','/properties', data),
    update: (id, data) => request('PUT',`/properties/${id}`, data),
    delete:(id) => request('DELETE', `/properties/${id}`),
    myList:()=> request('GET','/my-properties'),
    markRented:(id)=> request('PATCH',  `/properties/${id}/rent`),
    stats:(id)=> request('GET',`/properties/${id}/stats`),
    uploadImages: (id, fd)=> request('POST',`/properties/${id}/images`, fd, true),
};

const Favorites = {
    getAll:()=> request('GET','/favorites'),
    add:(id) => request('POST',`/favorites/${id}`),
    remove: (id) => request('DELETE', `/favorites/${id}`),
};

const Reviews = {
    getAll: (pid)=> request('GET',`/properties/${pid}/reviews`),
    create: (pid, data) => request('POST', `/properties/${pid}/reviews`, data),
};

const Messaging = {
    getConversations:()=> request('GET','/conversations'),
    getConversation:(id)=> request('GET',`/conversations/${id}`),
    createConversation: (data)=> request('POST','/conversations', data),
    sendMessage:(data)=> request('POST','/messages', data),
    getMessages:(convId) => request('GET',`/messages/${convId}`),
    deleteMessage:(id)=> request('DELETE', `/messages/${id}`),
};

const Reservations = {
    getAll:()=> request('GET','/requests'),
    getOne:(id)=> request('GET',`/requests/${id}`),
    create:(data)=> request('POST','/requests', data),
    accept:(id)=> request('PUT',`/requests/${id}/accept`),
    reject:(id)=> request('PUT',`/requests/${id}/reject`),
    cancel:(id)=> request('PUT',`/requests/${id}/cancel`),
};
