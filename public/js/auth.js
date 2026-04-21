const AuthManager = {
    save(token, user) {
        localStorage.setItem('darent_token', token);
        localStorage.setItem('darent_user',  JSON.stringify(user));
    },
    getToken(){ 
        return localStorage.getItem('darent_token'); 
    },
    getUser(){
        const u = localStorage.getItem('darent_user');
        return u ? JSON.parse(u) : null;
    },
    isLoggedIn(){
         return !!this.getToken(); 
    },
    isOwner(){
         return this.getUser()?.role === 'owner'; 
    },
    isTenant(){
         return this.getUser()?.role === 'tenant'; 
    },
    clear() {
        localStorage.removeItem('darent_token');
        localStorage.removeItem('darent_user');
    },
    requireAuth() {
        if (!this.isLoggedIn()) window.location.href = '/login';
    },
    requireGuest() {
        if (this.isLoggedIn()) window.location.href = '/';
    },
};