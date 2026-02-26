<div class="space-y-4 bg-orange-50 p-4 rounded-xl border border-orange-100">
    <h4 class="font-bold text-orange-900 border-b border-orange-200 pb-2">FICHE DE SANTÉ CARDIOVASCULAIRE</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-orange-700 uppercase">Fréquence Cardiaque (BPM)</label>
            <input type="text" name="meta[frequence_cardiaque]" value="{{ $meta['frequence_cardiaque'] ?? '' }}" class="w-full p-2 border border-orange-200 rounded-lg">
        </div>
        <div>
            <label class="block text-xs font-bold text-orange-700 uppercase">Rythme Cardiaque</label>
            <select name="meta[rythme]" class="w-full p-2 border border-orange-200 rounded-lg">
                <option value="régulier" {{ ($meta['rythme'] ?? '') == 'régulier' ? 'selected' : '' }}>Régulier</option>
                <option value="irrégulier" {{ ($meta['rythme'] ?? '') == 'irrégulier' ? 'selected' : '' }}>Irrégulier</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-orange-700 uppercase">Œdèmes</label>
            <select name="meta[oedemes]" class="w-full p-2 border border-orange-200 rounded-lg">
                <option value="non" {{ ($meta['oedemes'] ?? '') == 'non' ? 'selected' : '' }}>Non</option>
                <option value="membres inférieurs" {{ ($meta['oedemes'] ?? '') == 'membres inférieurs' ? 'selected' : '' }}>Membres inférieurs</option>
                <option value="généralisés" {{ ($meta['oedemes'] ?? '') == 'généralisés' ? 'selected' : '' }}>Généralisés</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-orange-700 uppercase">Dyspnée</label>
            <select name="meta[dyspnee]" class="w-full p-2 border border-orange-200 rounded-lg">
                <option value="non" {{ ($meta['dyspnee'] ?? '') == 'non' ? 'selected' : '' }}>Non</option>
                <option value="effort" {{ ($meta['dyspnee'] ?? '') == 'effort' ? 'selected' : '' }}>À l'effort</option>
                <option value="repos" {{ ($meta['dyspnee'] ?? '') == 'repos' ? 'selected' : '' }}>Au repos</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-orange-700 uppercase">Commentaires Cardiologiques</label>
        <textarea name="meta[commentaires_cardio]" rows="2" class="w-full p-2 border border-orange-200 rounded-lg">{{ $meta['commentaires_cardio'] ?? '' }}</textarea>
    </div>
</div>
