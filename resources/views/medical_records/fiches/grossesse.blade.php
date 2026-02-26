<div class="space-y-4 bg-pink-50 p-4 rounded-xl border border-pink-100">
    <h4 class="font-bold text-pink-900 border-b border-pink-200 pb-2">FICHE DE SUIVI DE GROSSESSE</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-pink-700 uppercase">Âge Gestationnel (SA)</label>
            <input type="text" name="meta[age_gestationnel]" value="{{ $meta['age_gestationnel'] ?? '' }}" class="w-full p-2 border border-pink-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-pink-700 uppercase">Poids (kg)</label>
            <input type="text" name="meta[poids]" value="{{ $meta['poids'] ?? '' }}" class="w-full p-2 border border-pink-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-pink-700 uppercase">Tension Artérielle</label>
            <input type="text" name="meta[tension]" value="{{ $meta['tension'] ?? '' }}" class="w-full p-2 border border-pink-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-pink-700 uppercase">Hauteur Utérine (cm)</label>
            <input type="text" name="meta[hauteur_uterine]" value="{{ $meta['hauteur_uterine'] ?? '' }}" class="w-full p-2 border border-pink-200 rounded-lg">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-pink-700 uppercase">Bruits du Cœur Fœtal (BCF)</label>
            <select name="meta[bcf]" class="w-full p-2 border border-pink-200 rounded-lg">
                <option value="présents" {{ ($meta['bcf'] ?? '') == 'présents' ? 'selected' : '' }}>Présents</option>
                <option value="absents" {{ ($meta['bcf'] ?? '') == 'absents' ? 'selected' : '' }}>Absents</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-pink-700 uppercase">Mouvements Fœtaux</label>
            <select name="meta[mouvements]" class="w-full p-2 border border-pink-200 rounded-lg">
                <option value="perçus" {{ ($meta['mouvements'] ?? '') == 'perçus' ? 'selected' : '' }}>Perçus</option>
                <option value="non perçus" {{ ($meta['mouvements'] ?? '') == 'non perçus' ? 'selected' : '' }}>Non perçus</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-pink-700 uppercase">Observations Obstétricales</label>
        <textarea name="meta[observations_obstetricales]" rows="2" class="w-full p-2 border border-pink-200 rounded-lg">{{ $meta['observations_obstetricales'] ?? '' }}</textarea>
    </div>
</div>
