<div class="space-y-4 bg-red-50 p-4 rounded-xl border border-red-100">
    <h4 class="font-bold text-red-900 border-b border-red-200 pb-2">FICHE D'URGENCE 24/7</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-red-700 uppercase">Niveau de Tri (Triage)</label>
            <select name="meta[triage]" class="w-full p-2 border border-red-200 rounded-lg">
                <option value="code_rouge" {{ ($meta['triage'] ?? '') == 'code_rouge' ? 'selected' : '' }}>Code Rouge (Vital)</option>
                <option value="code_orange" {{ ($meta['triage'] ?? '') == 'code_orange' ? 'selected' : '' }}>Code Orange (Urgent)</option>
                <option value="code_jaune" {{ ($meta['triage'] ?? '') == 'code_jaune' ? 'selected' : '' }}>Code Jaune (Moins urgent)</option>
                <option value="code_vert" {{ ($meta['triage'] ?? '') == 'code_vert' ? 'selected' : '' }}>Code Vert (Standard)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-red-700 uppercase">Mode d'Arrivée</label>
            <select name="meta[arrivee]" class="w-full p-2 border border-red-200 rounded-lg">
                <option value="ambulance" {{ ($meta['arrivee'] ?? '') == 'ambulance' ? 'selected' : '' }}>Ambulance</option>
                <option value="pompier" {{ ($meta['arrivee'] ?? '') == 'pompier' ? 'selected' : '' }}>Pompier</option>
                <option value="moyens personnels" {{ ($meta['arrivee'] ?? '') == 'moyens personnels' ? 'selected' : '' }}>Moyens personnels</option>
            </select>
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-red-700 uppercase">Examen Initial & Premiers Soins</label>
        <textarea name="meta[examen_initial]" rows="3" class="w-full p-2 border border-red-200 rounded-lg" placeholder="Constatations immédiates et gestes effectués...">{{ $meta['examen_initial'] ?? '' }}</textarea>
    </div>
</div>
