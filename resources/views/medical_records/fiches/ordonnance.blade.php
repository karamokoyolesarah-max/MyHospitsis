<div class="space-y-4 bg-green-50 p-4 rounded-xl border border-green-100">
    <h4 class="font-bold text-green-900 border-b border-green-200 pb-2 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        ORDONNANCE NUMÉRIQUE
    </h4>
    
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-bold text-green-700 uppercase mb-2">Prescription Médicamenteuse</label>
            <textarea name="meta[prescriptions_list]" rows="8" class="w-full p-4 border border-green-200 rounded-lg text-sm font-mono" placeholder="Ex: 
- Paracétamol 500mg : 1 tab x 3/jour pendant 5 jours
- Amoxicilline 1g : 1 tab matin et soir pendant 7 jours...">{{ $meta['prescriptions_list'] ?? '' }}</textarea>
            <p class="text-[10px] text-green-600 mt-1 italic">Indiquez le nom, le dosage, la posologie et la durée.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase">Examens Complémentaires</label>
                <textarea name="meta[exams_requested]" rows="3" class="w-full p-2 border border-green-200 rounded-lg text-sm">{{ $meta['exams_requested'] ?? '' }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-green-700 uppercase">Conseils Hygiéno-Diététiques</label>
                <textarea name="meta[lifestyle_advice]" rows="3" class="w-full p-2 border border-green-200 rounded-lg text-sm">{{ $meta['lifestyle_advice'] ?? '' }}</textarea>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <input type="checkbox" name="meta[urgent_ordonnance]" id="urgent_ord" value="1" {{ ($meta['urgent_ordonnance'] ?? '') ? 'checked' : '' }} class="rounded text-green-600 focus:ring-green-500">
            <label for="urgent_ord" class="text-sm font-semibold text-green-800">Prescription Urgente</label>
        </div>
    </div>
</div>
