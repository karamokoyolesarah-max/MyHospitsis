<div class="space-y-4 bg-green-50 p-4 rounded-xl border border-green-100">
    <h4 class="font-bold text-green-900 border-b border-green-200 pb-2">FICHE NUTRITION & BIEN-ÊTRE</h4>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-bold text-green-700 uppercase">IMC (Calculé)</label>
            <input type="text" name="meta[imc]" value="{{ $meta['imc'] ?? '' }}" class="w-full p-2 border border-green-200 rounded-lg" readonly>
        </div>
        <div>
            <label class="block text-xs font-bold text-green-700 uppercase">Tour de Taille (cm)</label>
            <input type="text" name="meta[tour_taille]" value="{{ $meta['tour_taille'] ?? '' }}" class="w-full p-2 border border-green-200 rounded-lg">
        </div>
    </div>
    <div>
        <label class="block text-xs font-bold text-green-700 uppercase">Habitudes Alimentaires</label>
        <textarea name="meta[habitudes]" rows="2" class="w-full p-2 border border-green-200 rounded-lg" placeholder="Fréquence repas, types d'aliments...">{{ $meta['habitudes'] ?? '' }}</textarea>
    </div>
    <div>
        <label class="block text-xs font-bold text-green-700 uppercase">Objectifs Nutritionnels</label>
        <textarea name="meta[objectifs]" rows="2" class="w-full p-2 border border-green-200 rounded-lg" placeholder="Perte de poids, rééquilibrage...">{{ $meta['objectifs'] ?? '' }}</textarea>
    </div>
</div>
