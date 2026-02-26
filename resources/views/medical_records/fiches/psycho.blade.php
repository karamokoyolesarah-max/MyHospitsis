<div class="space-y-4 bg-purple-50 p-4 rounded-xl border border-purple-100">
    <h4 class="font-bold text-purple-900 border-b border-purple-200 pb-2">FICHE DE SANTÉ MENTALE</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-purple-700 uppercase">État d'Humeur</label>
            <select name="meta[humeur]" class="w-full p-2 border border-purple-200 rounded-lg">
                <option value="stable" {{ ($meta['humeur'] ?? '') == 'stable' ? 'selected' : '' }}>Stable</option>
                <option value="anxieux" {{ ($meta['humeur'] ?? '') == 'anxieux' ? 'selected' : '' }}>Anxieux</option>
                <option value="dépressif" {{ ($meta['humeur'] ?? '') == 'dépressif' ? 'selected' : '' }}>Dépressif</option>
                <option value="irritable" {{ ($meta['humeur'] ?? '') == 'irritable' ? 'selected' : '' }}>Irritable</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-purple-700 uppercase">Qualité du Sommeil</label>
            <select name="meta[sommeil]" class="w-full p-2 border border-purple-200 rounded-lg">
                <option value="bon" {{ ($meta['sommeil'] ?? '') == 'bon' ? 'selected' : '' }}>Bon</option>
                <option value="moyen" {{ ($meta['sommeil'] ?? '') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                <option value="mauvais" {{ ($meta['sommeil'] ?? '') == 'mauvais' ? 'selected' : '' }}>Mauvais (Insomnie)</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-purple-700 uppercase">Évaluation Psychologique</label>
        <textarea name="meta[evaluation]" rows="3" class="w-full p-2 border border-purple-200 rounded-lg" placeholder="Notes sur l'entretien...">{{ $meta['evaluation'] ?? '' }}</textarea>
    </div>
</div>
