{{-- resources/views/pages/property-detail.blade.php --}}
@extends('layouts.app')
@section('title', 'Détail du bien — DAR-RENT')
@section('content')

{{-- Gallery — renders property media dynamically --}}
<div id="property-gallery" class="grid grid-cols-4 grid-rows-2 gap-2 max-h-[480px] overflow-hidden px-0 md:px-8 mt-4 rounded-2xl">
    <div class="col-span-2 row-span-2 bg-gray-200 overflow-hidden rounded-l-2xl flex items-center justify-center">
        <img id="gallery-main-image" src="/images/default-property.svg" class="w-full h-full object-cover" alt="Property image">
    </div>
    <div id="gallery-thumb-1" class="bg-gray-200 overflow-hidden flex items-center justify-center">
        <img src="/images/default-property.svg" class="w-full h-full object-cover opacity-50" alt="Property image">
    </div>
    <div id="gallery-thumb-2" class="bg-gray-200 overflow-hidden rounded-tr-2xl flex items-center justify-center">
        <img src="/images/default-property.svg" class="w-full h-full object-cover opacity-50" alt="Property image">
    </div>
    <div id="gallery-thumb-3" class="bg-gray-200 overflow-hidden flex items-center justify-center">
        <img src="/images/default-property.svg" class="w-full h-full object-cover opacity-50" alt="Property image">
    </div>
    <div id="gallery-thumb-4" class="bg-gray-200 overflow-hidden rounded-br-2xl flex items-center justify-center">
        <img src="/images/default-property.svg" class="w-full h-full object-cover opacity-50" alt="Property image">
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

        {{-- LEFT --}}
        <div class="lg:col-span-2">
            <div id="detail-skeleton" class="animate-pulse space-y-4">
                <div class="h-8 bg-gray-200 rounded w-3/4"></div>
                <div class="h-4 bg-gray-200 rounded w-1/2"></div>
            </div>

            <div id="detail-content" class="hidden">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 id="prop-title" class="text-2xl font-bold text-[#1a1a1a]"></h1>
                        <p id="prop-location" class="text-[#6b7280] mt-1 flex items-center gap-1 text-sm">
                            <svg class="w-4 h-4 text-[#C0704A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span id="prop-location-text"></span>
                        </p>
                        <div class="flex items-center gap-3 mt-3 text-sm text-[#6b7280]">
                            <span id="prop-beds"></span>
                            <span>·</span>
                            <span id="prop-baths"></span>
                            <span>·</span>
                            <span id="prop-type" class="capitalize bg-primary-100 text-primary-700 px-2 py-0.5 rounded-full text-xs font-semibold"></span>
                        </div>
                    </div>
                    <div id="prop-status-badge"></div>
                </div>

                <hr class="my-6 border-gray-100">

                {{-- Owner info --}}
                <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-2xl">
                    <div class="w-12 h-12 rounded-full bg-gray-200 border-2 border-primary-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm text-[#1a1a1a]">Proposé par <span id="owner-name"></span></p>
                        <p class="text-xs text-[#6b7280]">Propriétaire vérifié</p>
                    </div>
                </div>

                <hr class="my-6 border-gray-100">

                <h2 class="text-lg font-bold text-[#1a1a1a] mb-3">À propos de ce bien</h2>
                <p id="prop-desc" class="text-[#6b7280] leading-relaxed text-sm"></p>

                <hr class="my-6 border-gray-100">

                <h2 class="text-lg font-bold text-[#1a1a1a] mb-3">Équipements</h2>
                <div id="prop-equipements" class="grid grid-cols-2 gap-2"></div>

                <hr class="my-6 border-gray-100">

                <div class="flex items-center gap-2 mb-4">
                    <h2 class="text-lg font-bold text-[#1a1a1a]">Avis</h2>
                    <span id="rating-badge" class="text-sm text-[#6b7280]"></span>
                </div>
                <div id="reviews-list" class="space-y-4"></div>

                {{-- Add review (tenant only) --}}
                <div id="add-review" class="hidden mt-6 bg-gray-50 border border-gray-100 rounded-2xl p-5">
                    <h3 class="font-bold text-[#1a1a1a] mb-3 text-sm">Laisser un avis</h3>
                    <div class="flex gap-1 mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                        <button onclick="setRating({{ $i }})" data-star="{{ $i }}"
                            class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition">★</button>
                        @endfor
                    </div>
                    <textarea id="review-comment" rows="3" placeholder="Partagez votre expérience..."
                        class="input-field text-sm resize-none"></textarea>
                    <button onclick="submitReview()" class="btn-primary mt-3 py-2 px-5 text-sm">Publier</button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Booking card --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 bg-white border border-gray-100 rounded-2xl shadow-[0_2px_16px_rgba(0,0,0,0.10)] p-6">

                <p class="text-2xl font-bold text-[#1a1a1a] mb-1">
                    <span id="card-price" class="text-[#C0704A]"></span>
                </p>
                <p class="text-xs text-[#6b7280] mb-4">par mois</p>

                <div class="border border-gray-200 rounded-xl overflow-hidden mb-4">
                    <div class="grid grid-cols-2">
                        <div class="p-3 border-r border-gray-200">
                            <label class="block text-xs font-bold text-[#1a1a1a] uppercase tracking-wide">Arrivée</label>
                            <input type="date" id="book-start" class="w-full text-sm outline-none mt-1 text-[#1a1a1a]">
                        </div>
                        <div class="p-3">
                            <label class="block text-xs font-bold text-[#1a1a1a] uppercase tracking-wide">Départ</label>
                            <input type="date" id="book-end" class="w-full text-sm outline-none mt-1 text-[#1a1a1a]">
                        </div>
                    </div>
                </div>

                <textarea id="book-message" rows="3" placeholder="Présentez-vous au propriétaire..."
                    class="input-field text-sm resize-none mb-4"></textarea>

                <button id="book-btn"    onclick="submitReservation()" class="btn-primary w-full mb-3 hidden">Demander à louer</button>
                <button id="contact-btn" onclick="openConversation()"  class="btn-outline w-full hidden">Contacter le propriétaire</button>

                <div id="login-prompt" class="hidden text-center">
                    <p class="text-sm text-[#6b7280] mb-3">Connectez-vous pour réserver</p>
                    <a href="/login" class="btn-primary w-full block text-center">Se connecter</a>
                </div>

                <div id="price-breakdown" class="hidden mt-4 pt-4 border-t border-gray-100 space-y-2 text-sm">
                    <div class="flex justify-between text-[#6b7280]">
                        <span id="breakdown-nights"></span>
                        <span id="breakdown-total"></span>
                    </div>
                    <div class="flex justify-between font-bold text-[#1a1a1a] pt-2 border-t border-gray-100">
                        <span>Total</span>
                        <span id="breakdown-grand"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    const PROPERTY_ID = {{ $propertyId }};
    let selectedRating = 0;
    let propertyData   = null;

    document.addEventListener('DOMContentLoaded', async () => {
        await loadProperty();
        setupDates();

        const user = AuthManager.getUser();
        if (!user) {
            document.getElementById('login-prompt').classList.remove('hidden');
        } else if (user.role === 'tenant') {
            document.getElementById('book-btn').classList.remove('hidden');
            document.getElementById('contact-btn').classList.remove('hidden');
            document.getElementById('add-review').classList.remove('hidden');
        }
    });

    async function loadProperty() {
        try {
            const data   = await Properties.getOne(PROPERTY_ID);
            propertyData = data.property;
            const p      = data.property;

            // Show content, hide skeleton
            document.getElementById('detail-skeleton').classList.add('hidden');
            document.getElementById('detail-content').classList.remove('hidden');

            // Fill all fields
            document.getElementById('prop-title').textContent        = p.titre;
            document.getElementById('prop-location-text').textContent = `${p.adress}${p.city ? ' · ' + p.city.nom_ville : ''}`;
            document.getElementById('prop-beds').textContent          = Helpers.bedsLabel(p.bedrooms);
            document.getElementById('prop-baths').textContent         = `${p.bathrooms} sdb`;
            document.getElementById('prop-type').textContent          = p.type;
            document.getElementById('prop-desc').textContent          = p.description;
            document.getElementById('prop-status-badge').innerHTML    = Helpers.statusBadge(p.status);
            document.getElementById('owner-name').textContent         = p.owner?.nom || '';
            document.getElementById('card-price').textContent         = new Intl.NumberFormat('fr-MA').format(p.prix_mensuel) + ' MAD';
            document.getElementById('rating-badge').textContent       = data.average_rating > 0 ? `★ ${data.average_rating}` : '';
            document.title = `${p.titre} — DAR-RENT`;

            // Gallery images
            const galleryMedia = p.media || [];
            const mainImg = document.getElementById('gallery-main-image');
            const thumbs = [
                document.getElementById('gallery-thumb-1'),
                document.getElementById('gallery-thumb-2'),
                document.getElementById('gallery-thumb-3'),
                document.getElementById('gallery-thumb-4'),
            ];

            if (galleryMedia.length) {
                mainImg.src = Helpers.imageUrl(galleryMedia[0].url_fichier);
                mainImg.classList.remove('opacity-50');
                thumbs.forEach((thumb, index) => {
                    const img = thumb.querySelector('img');
                    if (galleryMedia[index + 1]) {
                        img.src = Helpers.imageUrl(galleryMedia[index + 1].url_fichier);
                        img.classList.remove('opacity-50');
                    } else {
                        img.src = '/images/default-property.svg';
                        img.classList.add('opacity-50');
                    }
                });
            } else {
                mainImg.src = '/images/default-property.svg';
                thumbs.forEach((thumb) => {
                    const img = thumb.querySelector('img');
                    img.src = '/images/default-property.svg';
                    img.classList.add('opacity-50');
                });
            }

            // Equipements
            const eq = document.getElementById('prop-equipements');
            eq.innerHTML = '';
            (p.equipements || []).forEach(e => {
                eq.innerHTML += `
                    <div class="flex items-center gap-2 text-sm text-[#6b7280] py-2 bg-gray-50 rounded-lg px-3">
                        <span class="text-[#C0704A] font-bold">✓</span>
                        <span>${e.nom_equipement}</span>
                    </div>`;
            });

            await loadReviews();

        } catch (e) {
            Toast.error('Impossible de charger ce bien.');
        }
    }

    async function loadReviews() {
        const data = await Reviews.getAll(PROPERTY_ID);
        const list = document.getElementById('reviews-list');
        list.innerHTML = '';

        if (!data.reviews?.length) {
            list.innerHTML = '<p class="text-sm text-[#6b7280] bg-gray-50 rounded-xl p-4">Aucun avis pour ce bien.</p>';
            return;
        }

        data.reviews.forEach(r => {
            list.innerHTML += `
                <div class="flex gap-4 py-4 border-b border-gray-50">
                    <div class="w-10 h-10 rounded-full bg-gray-200 border-2 border-primary-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-sm text-[#1a1a1a]">${r.user?.nom || ''}</p>
                        <p class="text-yellow-400 text-sm">${'★'.repeat(r.note)}${'☆'.repeat(5 - r.note)}</p>
                        <p class="text-sm text-[#6b7280] mt-1">${r.commentaire}</p>
                        <p class="text-xs text-[#6b7280] mt-1">${Helpers.formatDate(r.date_publication)}</p>
                    </div>
                </div>`;
        });
    }

    function setRating(n) {
        selectedRating = n;
        document.querySelectorAll('.star-btn').forEach(b => {
            b.classList.toggle('text-yellow-400', parseInt(b.dataset.star) <= n);
            b.classList.toggle('text-gray-300',   parseInt(b.dataset.star) > n);
        });
    }

    async function submitReview() {
        if (!selectedRating) { Toast.error('Choisissez une note'); return; }
        try {
            await Reviews.create(PROPERTY_ID, {
                note:        selectedRating,
                commentaire: document.getElementById('review-comment').value,
            });
            Toast.success('Avis publié !');
            await loadReviews();
            document.getElementById('review-comment').value = '';
            setRating(0);
        } catch (e) { Toast.error(e.message); }
    }

    function setupDates() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('book-start').min = today;
        document.getElementById('book-start').addEventListener('change', () => {
            document.getElementById('book-end').min = document.getElementById('book-start').value;
            updateBreakdown();
        });
        document.getElementById('book-end').addEventListener('change', updateBreakdown);
    }

    function updateBreakdown() {
        const s = document.getElementById('book-start').value;
        const e = document.getElementById('book-end').value;
        if (!s || !e || !propertyData) return;
        const days  = Math.ceil((new Date(e) - new Date(s)) / 86400000);
        const total = Math.round((propertyData.prix_mensuel / 30) * days);
        document.getElementById('breakdown-nights').textContent = `${days} nuit${days > 1 ? 's' : ''}`;
        document.getElementById('breakdown-grand').textContent  = new Intl.NumberFormat('fr-MA').format(total) + ' MAD';
        document.getElementById('price-breakdown').classList.remove('hidden');
    }

    async function submitReservation() {
        const s = document.getElementById('book-start').value;
        const e = document.getElementById('book-end').value;
        if (!s || !e) { Toast.error('Choisissez les dates'); return; }
        try {
            await Reservations.create({
                property_id: PROPERTY_ID,
                date_debut:  s,
                date_fin:    e,
                message:     document.getElementById('book-message').value,
            });
            Toast.success('Demande envoyée !');
        } catch (e) { Toast.error(e.message); }
    }

    async function openConversation() {
        try {
            const data = await Messaging.createConversation({
                property_id: PROPERTY_ID,
                message:     document.getElementById('book-message').value || 'Bonjour, je suis intéressé par ce bien.',
            });
            window.location.href = `/messages/${data.conversation.id}`;
        } catch (e) {
            if (e.status === 409) {
                const convs = await Messaging.getConversations();
                const ex    = convs.conversations.find(c => c.property_id == PROPERTY_ID);
                if (ex) window.location.href = `/messages/${ex.id}`;
            } else {
                Toast.error(e.message);
            }
        }
    }
</script>
@endpush