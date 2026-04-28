@extends('layouts.app')
@section('title', isset($propertyId) ? 'Modifier le bien — DAR-RENT' : 'Publier un bien — DAR-RENT')
@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="flex items-center gap-3 mb-8">
        <a href="/my-properties" class="w-9 h-9 bg-white border border-gray-200 rounded-xl flex items-center justify-center hover:border-primary-500 transition shadow-sm">
            <svg class="w-4 h-4 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-dark">{{ isset($propertyId) ? 'Modifier le bien' : 'Publier un bien' }}</h1>
            <p class="text-muted text-sm mt-0.5">{{ isset($propertyId) ? 'Mettez à jour les informations' : 'Renseignez les détails de votre bien' }}</p>
        </div>
    </div>

    <div id="form-error" class="hidden bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-6 flex items-center gap-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span id="form-error-msg"></span>
    </div>

    <form onsubmit="submitForm(event)" class="space-y-6">

        {{-- Step 1: Basic info --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h2 class="text-base font-bold text-dark mb-5 pb-3 border-b border-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                Informations de base
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Titre de l'annonce *</label>
                    <input type="text" id="f-titre" required placeholder="Ex: Bel appartement meublé à Guéliz" class="input-field">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Type de bien *</label>
                        <select id="f-type" required class="input-field">
                            <option value="">Sélectionner...</option>
                            <option value="apartment">Appartement</option>
                            <option value="villa">Villa</option>
                            <option value="studio">Studio</option>
                            <option value="riad">Riad</option>
                            <option value="house">Maison</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-dark mb-2">Ville</label>
                        <select id="f-city" class="input-field">
                            <option value="">Sélectionner...</option>
                            <option value="1">Marrakech</option>
                            <option value="2">Casablanca</option>
                            <option value="3">Rabat</option>
                            <option value="4">Agadir</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Adresse complète *</label>
                    <input type="text" id="f-adress" required placeholder="Ex: 23 Rue Mohammed V, Guéliz, Marrakech" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Description *</label>
                    <textarea id="f-desc" rows="5" required placeholder="Décrivez votre bien: équipements, quartier, transports..." class="input-field resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- Step 2: Price --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h2 class="text-base font-bold text-dark mb-5 pb-3 border-b border-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                Prix et caractéristiques
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Prix mensuel (MAD) *</label>
                    <input type="number" id="f-price" required min="0" placeholder="4 500" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Chambres</label>
                    <input type="number" id="f-beds" min="0" value="1" class="input-field">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-dark mb-2">Salles de bain</label>
                    <input type="number" id="f-baths" min="0" value="1" class="input-field">
                </div>
            </div>
        </div>

        {{-- Step 3: Photos --}}
        <div class="bg-white rounded-2xl shadow-card p-6">
            <h2 class="text-base font-bold text-dark mb-5 pb-3 border-b border-gray-100 flex items-center gap-2">
                <span class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                Photos du bien
            </h2>
            <div>
                <label class="block text-sm font-semibold text-dark mb-3">Ajouter des photos (max 5)</label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary-500 transition cursor-pointer" onclick="document.getElementById('f-images').click()">
                    <input type="file" id="f-images" name="images[]" multiple accept="image/*" onchange="previewImages(this)" hidden>
                    <svg class="w-8 h-8 text-muted mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    <p class="text-muted text-sm">Cliquez ou déposez vos images (JPEG, PNG, GIF)</p>
                    <p class="text-xs text-gray-400 mt-1">Sélectionnez jusqu'à 5 images, 5 MB maximum par image.</p>
                </div>
                <p id="images-count" class="text-xs text-gray-500 mt-2">0 / 5 images sélectionnées</p>
                <div id="image-preview" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4"></div>
                <div id="existing-image-preview" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6"></div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="/my-properties" class="btn-outline px-6 py-3">Annuler</a>
            <button type="submit" id="submit-btn" class="btn-primary px-8 py-3">
                {{ isset($propertyId) ? 'Enregistrer les modifications' : 'Publier le bien' }}
            </button>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
    AuthManager.requireAuth();
    const PROPERTY_ID = @json($propertyId ?? null);
    const IS_EDIT     = !!PROPERTY_ID;

    document.addEventListener('DOMContentLoaded', async () => {
        if(!AuthManager.isOwner()){window.location.href='/';return;}
        if(IS_EDIT){
            const data=await Properties.getOne(PROPERTY_ID);const p=data.property;
            document.getElementById('f-titre').value = p.titre;
            document.getElementById('f-type').value  = p.type;
            document.getElementById('f-city').value  = p.city_id||'';
            document.getElementById('f-adress').value= p.adress;
            document.getElementById('f-desc').value  = p.description;
            document.getElementById('f-price').value = p.prix_mensuel;
            document.getElementById('f-beds').value  = p.bedrooms;
            document.getElementById('f-baths').value = p.bathrooms;

            const existingPreview = document.getElementById('existing-image-preview');
            if(p.media?.length){
                existingPreview.innerHTML = `<p class="text-sm font-semibold text-dark mb-2">Images existantes</p>`;
                p.media.slice(0,5).forEach(media => {
                    existingPreview.innerHTML += `
                        <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 border-2 border-gray-200">
                            <img src="${Helpers.imageUrl(media.url_fichier)}" class="w-full h-full object-cover">
                        </div>`;
                });
            }
        }
    });

    function previewImages(input){
        const preview = document.getElementById('image-preview');
        const counter = document.getElementById('images-count');
        preview.innerHTML = '';
        const selectedFiles = Array.from(input.files).slice(0,5);
        counter.textContent = `${selectedFiles.length} / 5 images sélectionnées`;

        if (input.files.length > 5) {
            Toast.warning('Seulement les 5 premières images seront téléchargées.');
        }

        selectedFiles.forEach(file => {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML += `
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 border-2 border-primary-100">
                        <img src="${e.target.result}" class="w-full h-full object-cover">
                    </div>`;
            };
            reader.readAsDataURL(file);
        });
    }

    async function submitForm(e){
        e.preventDefault();
        const btn    = document.getElementById('submit-btn');
        const errBox = document.getElementById('form-error');
        const errMsg = document.getElementById('form-error-msg');

        btn.disabled = true;
        btn.textContent = IS_EDIT ? 'Enregistrement...' : 'Publication...';
        errBox.classList.add('hidden');

        const payload = {
            titre:        document.getElementById('f-titre').value,
            type:         document.getElementById('f-type').value,
            city_id:      document.getElementById('f-city').value || null,
            adress:       document.getElementById('f-adress').value,
            description:  document.getElementById('f-desc').value,
            prix_mensuel: document.getElementById('f-price').value,
            bedrooms:     document.getElementById('f-beds').value,
            bathrooms:    document.getElementById('f-baths').value,
        };

        try {
            let propertyId;
            if (IS_EDIT) {
                await Properties.update(PROPERTY_ID, payload);
                propertyId = PROPERTY_ID;
                Toast.success('Bien mis à jour !');
            } else {
                const resp = await Properties.create(payload);
                propertyId = resp.property.id;
                Toast.success('Bien publié avec succès !');
            }

            // Upload images if selected
            const imageFiles = Array.from(document.getElementById('f-images').files).slice(0,5);
            if (imageFiles.length > 0) {
                const formData = new FormData();
                imageFiles.forEach(file => formData.append('images[]', file));
                await Properties.uploadImages(propertyId, formData);
            }

            setTimeout(() => { window.location.href = '/my-properties'; }, 1000);

        } catch(err) {
            errMsg.textContent = err.message;
            errBox.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = IS_EDIT ? 'Enregistrer les modifications' : 'Publier le bien';
        }
    }
</script>
@endpush