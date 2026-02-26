<div class="space-y-4 bg-purple-50 p-4 rounded-xl border border-purple-100">
    <h4 class="font-bold text-purple-900 border-b border-purple-200 pb-2">CERTIFICAT MÉDICAL</h4>
    
    <div class="grid grid-cols-1 gap-4">
        <div>
            <label class="block text-xs font-bold text-purple-700 uppercase">Type de Certificat</label>
            <select name="meta[type_certificat]" class="w-full p-2 border border-purple-200 rounded-lg text-sm">
                <option value="aptitude" {{ ($meta['type_certificat'] ?? '') == 'aptitude' ? 'selected' : '' }}>Certificat d'Aptitude</option>
                <option value="repos" {{ ($meta['type_certificat'] ?? '') == 'repos' ? 'selected' : '' }}>Certificat de Repos Médical (Arrêt)</option>
                <option value="constat" {{ ($meta['type_certificat'] ?? '') == 'constat' ? 'selected' : '' }}>Certificat de Constat</option>
                <option value="grossesse" {{ ($meta['type_certificat'] ?? '') == 'grossesse' ? 'selected' : '' }}>Certificat de Grossesse</option>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-purple-700 uppercase">Durée du repos (si applicable)</label>
                <input type="text" name="meta[duree_repos]" value="{{ $meta['duree_repos'] ?? '' }}" class="w-full p-2 border border-purple-200 rounded-lg text-sm" placeholder="Ex: 3 jours">
            </div>
            <div>
                <label class="block text-xs font-bold text-purple-700 uppercase">À compter du</label>
                <input type="date" name="meta[date_debut_repos]" value="{{ $meta['date_debut_repos'] ?? '' }}" class="w-full p-2 border border-purple-200 rounded-lg text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-purple-700 uppercase">Observations / Constatations</label>
            <textarea name="meta[observations_certificat]" rows="4" class="w-full p-2 border border-purple-200 rounded-lg text-sm" placeholder="Je soussigné Dr..., certifie avoir examiné M/Mme...">{{ $meta['observations_certificat'] ?? '' }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-purple-700 uppercase">Destination (À quoi il sert)</label>
            <input type="text" name="meta[destination_certificat]" value="{{ $meta['destination_certificat'] ?? '' }}" class="w-full p-2 border border-purple-200 rounded-lg text-sm" placeholder="Ex: Pour servir et valoir ce que de droit">
        </div>
    </div>
</div>
