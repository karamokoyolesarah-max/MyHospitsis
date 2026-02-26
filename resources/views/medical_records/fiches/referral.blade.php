<div class="space-y-4 bg-rose-50 p-4 rounded-xl border border-rose-100">
    <h4 class="font-bold text-rose-900 border-b border-rose-200 pb-2 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
        LETTRE DE LIAISON / RÉFÉRENCE
    </h4>
    
    <div class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-rose-700 uppercase">Médecin / Service Destinataire</label>
                <input type="text" name="meta[destinataire]" value="{{ $meta['destinataire'] ?? '' }}" class="w-full p-2 border border-rose-200 rounded-lg text-sm" placeholder="Ex: Chef de service Gastro-entérologie">
            </div>
            <div>
                <label class="block text-xs font-bold text-rose-700 uppercase">Hôpital / Établissement</label>
                <input type="text" name="meta[etablissement_dest]" value="{{ $meta['etablissement_dest'] ?? '' }}" class="w-full p-2 border border-rose-200 rounded-lg text-sm" placeholder="Ex: CHU de Treichville">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-rose-700 uppercase">Motif de la Référence</label>
            <textarea name="meta[motif_reference]" rows="3" class="w-full p-2 border border-rose-200 rounded-lg text-sm" placeholder="Ex: Suspicion de pathologie chirurgicale, Nécessité d'endoscopie...">{{ $meta['motif_reference'] ?? '' }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-rose-700 uppercase">Résumé de l'Observation</label>
            <textarea name="meta[resume_clinique]" rows="5" class="w-full p-2 border border-rose-200 rounded-lg text-sm" placeholder="Signes cliniques majeurs, traitements déjà instaurés...">{{ $meta['resume_clinique'] ?? '' }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-rose-700 uppercase">Questions posées au confrère</label>
            <textarea name="meta[questions_confrere]" rows="2" class="w-full p-2 border border-rose-200 rounded-lg text-sm" placeholder="Avis chirurgical ? Adaptabilité du traitement ?">{{ $meta['questions_confrere'] ?? '' }}</textarea>
        </div>
    </div>
</div>
