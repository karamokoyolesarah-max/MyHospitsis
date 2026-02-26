<div class="space-y-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
    <h4 class="font-bold text-blue-900 border-b border-blue-200 pb-2">FICHE DE SANTÉ DE L'ENFANT (PÉDIATRIE)</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Poids (kg)</label>
            <input type="text" name="meta[poids]" value="{{ $meta['poids'] ?? '' }}" class="w-full p-2 border border-blue-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Taille (cm)</label>
            <input type="text" name="meta[taille]" value="{{ $meta['taille'] ?? '' }}" class="w-full p-2 border border-blue-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Périmètre Crânien (cm)</label>
            <input type="text" name="meta[pc]" value="{{ $meta['pc'] ?? '' }}" class="w-full p-2 border border-blue-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Statut Vaccinal</label>
            <select name="meta[vaccination]" class="w-full p-2 border border-blue-200 rounded-lg">
                <option value="à jour" {{ ($meta['vaccination'] ?? '') == 'à jour' ? 'selected' : '' }}>À jour</option>
                <option value="retard" {{ ($meta['vaccination'] ?? '') == 'retard' ? 'selected' : '' }}>En retard</option>
                <option value="inconnu" {{ ($meta['vaccination'] ?? '') == 'inconnu' ? 'selected' : '' }}>Inconnu</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-blue-700 uppercase">Développement Psychomoteur</label>
        <textarea name="meta[developpement]" rows="2" class="w-full p-2 border border-blue-200 rounded-lg" placeholder="Marche, langage, etc.">{{ $meta['developpement'] ?? '' }}</textarea>
    </div>
</div>
