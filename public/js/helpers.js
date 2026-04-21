const Helpers = {
    formatPrice(price) {
        return new Intl.NumberFormat('fr-MA').format(price) + ' MAD / mois';
    },
    formatDate(dateStr) {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleDateString('fr-MA', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    },
    timeAgo(dateStr) {
        const diff = Math.floor((new Date() - new Date(dateStr)) / 1000);
        if (diff < 60) return 'il y a quelques secondes';
        if (diff < 3600) return `il y a ${Math.floor(diff / 60)} min`;
        if (diff < 86400) return `il y a ${Math.floor(diff / 3600)} h`;
        return this.formatDate(dateStr);
    },
    imageUrl(path) {
        if (!path) return '/images/placeholder.jpg';
        if (path.startsWith('http')) return path;
        return `/storage/${path}`;
    },
    avatarUrl(path, name) {
        if (path) return this.imageUrl(path);
        const initials = name?.split(' ').map(w => w[0]).join('').toUpperCase() || '?';
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&background=ff385c&color=fff&size=80`;
    },
    bedsLabel(n) { return n === 1 ? '1 chambre' : `${n} chambres`; },
    truncate(str, max = 80) { return str?.length > max ? str.slice(0, max) + '...' : str; },
    statusBadge(status) {
        const map = {
            available: ['badge-available','Disponible'],
            rented:['badge-rented','Loué'],
            archived:['badge-rented','Archivé'],
            pending:['badge-pending','En attente'],
            accepted:['badge-accepted','Accepté'],
            rejected:['badge-rented','Refusé'],
        };
        const [cls, label] = map[status] || ['badge', status];
        return `<span class="badge ${cls}">${label}</span>`;
    },
};

const Toast = {
    container: null,
    init() {
        if (this.container) return;
        this.container = document.createElement('div');
        this.container.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 items-center';
        document.body.appendChild(this.container);
    },
    show(message, type = 'success', duration = 3500) {
        this.init();
        const colors = {
            success: 'bg-dark text-white',
            error:'bg-red-600 text-white',
            info:'bg-blue-600 text-white',
        };
        const toast = document.createElement('div');
        toast.className = `px-5 py-3 rounded-xl text-sm font-medium shadow-lg transition-all duration-300 opacity-0 translate-y-2 ${colors[type] || colors.success}`;
        toast.textContent = message;
        this.container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('opacity-0', 'translate-y-2'));
        setTimeout(() => {
            toast.classList.add('opacity-0', 'translate-y-2');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    success:(msg) => Toast.show(msg, 'success'),
    error:(msg) => Toast.show(msg, 'error'),
    info:(msg) => Toast.show(msg, 'info'),
};