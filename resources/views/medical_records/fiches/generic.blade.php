<div class="space-y-4 bg-blue-50 p-4 rounded-xl border border-blue-100">
    <h4 class="font-bold text-blue-900 border-b border-blue-200 pb-2">RAPPORT DE CONSULTATION GÉNÉRALE</h4>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Antécédents du patient</label>
            <textarea name="meta[antecedents]" rows="2" class="w-full p-2 border border-blue-200 rounded-lg text-sm" placeholder="Médicaux, chirurgicaux, familiaux...">{{ $meta['antecedents'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Histoire de la maladie</label>
            <textarea name="meta[histoire_maladie]" rows="2" class="w-full p-2 border border-blue-200 rounded-lg text-sm" placeholder="Début, évolution, symptômes...">{{ $meta['histoire_maladie'] ?? '' }}</textarea>
        </div>
    </div>

    <div class="space-y-3">
        <label class="block text-xs font-bold text-blue-700 uppercase">Examen Clinique</label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-blue-500 uppercase">État Général</label>
                <select name="meta[etat_general]" class="w-full p-2 border border-blue-200 rounded-lg text-sm">
                    <option value="bon" {{ ($meta['etat_general'] ?? '') == 'bon' ? 'selected' : '' }}>Bon</option>
                    <option value="moyen" {{ ($meta['etat_general'] ?? '') == 'moyen' ? 'selected' : '' }}>Moyen</option>
                    <option value="altere" {{ ($meta['etat_general'] ?? '') == 'altere' ? 'selected' : '' }}>Altéré</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-blue-500 uppercase">Conjonctives</label>
                <select name="meta[conjonctives]" class="w-full p-2 border border-blue-200 rounded-lg text-sm">
                    <option value="normocolorees" {{ ($meta['conjonctives'] ?? '') == 'normocolorees' ? 'selected' : '' }}>Normocolorées</option>
                    <option value="pales" {{ ($meta['conjonctives'] ?? '') == 'pales' ? 'selected' : '' }}>Pâles</option>
                    <option value="icteriques" {{ ($meta['conjonctives'] ?? '') == 'icteriques' ? 'selected' : '' }}>Ictériques</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-blue-500 uppercase">Dhydratation</label>
                <select name="meta[deshydratation]" class="w-full p-2 border border-blue-200 rounded-lg text-sm">
                    <option value="non" {{ ($meta['deshydratation'] ?? '') == 'non' ? 'selected' : '' }}>Non</option>
                    <option value="pli cutané" {{ ($meta['deshydratation'] ?? '') == 'pli cutané' ? 'selected' : '' }}>Pli cutané</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-blue-500 uppercase">Oedèmes</label>
                <select name="meta[oedemes]" class="w-full p-2 border border-blue-200 rounded-lg text-sm">
                    <option value="non" {{ ($meta['oedemes'] ?? '') == 'non' ? 'selected' : '' }}>Non</option>
                    <option value="oui" {{ ($meta['oedemes'] ?? '') == 'oui' ? 'selected' : '' }}>Oui</option>
                </select>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Appareil Respiratoire/CV</label>
            <textarea name="meta[examen_respi_cv]" rows="2" class="w-full p-2 border border-blue-200 rounded-lg text-sm">{{ $meta['examen_respi_cv'] ?? '' }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-bold text-blue-700 uppercase">Abdomen/Autres</label>
            <textarea name="meta[examen_abdominal]" rows="2" class="w-full p-2 border border-blue-200 rounded-lg text-sm">{{ $meta['examen_abdominal'] ?? '' }}</textarea>
        </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-blue-700 uppercase">Hypothèses Diagnostiques</label>
        <input type="text" name="meta[hypotheses]" value="{{ $meta['hypotheses'] ?? '' }}" class="w-full p-2 border border-blue-200 rounded-lg text-sm" placeholder="Diagnostics suspectés...">
    </div>

    <div>
        <label class="block text-xs font-bold text-blue-700 uppercase">Conduite à Tenir / Examens demandés</label>
        <textarea name="meta[conduite_a_tenir]" rows="2" class="w-full p-2 border border-blue-200 rounded-lg text-sm">{{ $meta['conduite_a_tenir'] ?? '' }}</textarea>
    </div>
</div>
