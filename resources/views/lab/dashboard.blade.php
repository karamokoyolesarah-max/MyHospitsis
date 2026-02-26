@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-teal-50 to-cyan-50">
    <div class="max-w-7xl mx-auto px-6 py-8">
        {{-- Header --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6 border-t-4 border-teal-500">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900">🧪 Laboratoire</h1>
                        <p class="text-sm text-gray-500 font-medium">{{ auth()->user()->name }} • {{ auth()->user()->hospital->name ?? 'HospitSIS' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold">Analyses terminées aujourd'hui</p>
                        <p class="text-3xl font-black text-teal-600">{{ $completedToday }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            @php
                $stats = [
                    ['label' => 'En attente', 'count' => $pendingRequests->where('status', 'pending')->count(), 'color' => 'gray', 'icon' => '⏳'],
                    ['label' => 'Reçu', 'count' => $pendingRequests->where('status', 'sample_received')->count(), 'color' => 'blue', 'icon' => '📦'],
                    ['label' => 'En cours', 'count' => $pendingRequests->where('status', 'in_progress')->count(), 'color' => 'orange', 'icon' => '🔬'],
                    ['label' => 'À valider', 'count' => $pendingRequests->where('status', 'to_be_validated')->count(), 'color' => 'purple', 'icon' => '🖋️'],
                    ['label' => 'Total', 'count' => $pendingRequests->count(), 'color' => 'teal', 'icon' => '📊'],
                ];
            @endphp

            @foreach($stats as $stat)
                <div class="bg-white rounded-xl shadow-md p-4 border-l-4 border-{{ $stat['color'] }}-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-bold">{{ $stat['label'] }}</p>
                            <p class="text-2xl font-black text-{{ $stat['color'] }}-600">{{ $stat['count'] }}</p>
                        </div>
                        <span class="text-3xl">{{ $stat['icon'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Demandes d'analyses --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-teal-600 to-cyan-500 p-6">
                <h2 class="text-2xl font-black text-white flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Demandes d'Analyses
                </h2>
            </div>

            @if($pendingRequests->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($pendingRequests as $request)
                        <div class="p-6 hover:bg-gray-50 transition-all">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ $request->test_category === 'laboratoire' ? 'bg-teal-100 text-teal-700' : 'bg-purple-100 text-purple-700' }}">
                                            {{ $request->test_category === 'laboratoire' ? '🧪 Biologie' : '📸 Imagerie' }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold
                                            {{ $request->status === 'pending' ? 'bg-gray-100 text-gray-700' : '' }}
                                            {{ $request->status === 'sample_received' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $request->status === 'in_progress' ? 'bg-orange-100 text-orange-700' : '' }}">
                                            {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                                        </span>
                                    </div>

                                    <h3 class="text-xl font-black text-gray-900 mb-1">{{ $request->test_name }}</h3>
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm mb-3">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-bold">Patient</p>
                                            <p class="font-bold text-gray-900">{{ $request->patient_name }}</p>
                                            <p class="text-xs text-gray-600">IPU: {{ $request->patient_ipu }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-bold">Prescripteur</p>
                                            <p class="font-medium text-gray-900">Dr. {{ $request->doctor->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $request->service->name }}</p>
                                        </div>
                                    </div>

                                    @if($request->clinical_info)
                                        <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg mb-3">
                                            <p class="text-xs font-bold text-blue-800 uppercase mb-1">📝 Informations cliniques</p>
                                            <p class="text-sm text-gray-700">{{ $request->clinical_info }}</p>
                                        </div>
                                    @endif

                                    <p class="text-xs text-gray-500">
                                        Demandé le {{ $request->requested_at->format('d/m/Y à H:i') }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-2">
                                    @if($request->status === 'pending')
                                        <form action="{{ route('lab.requests.status', $request) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="sample_received">
                                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg shadow transition-all">
                                                📦 Échantillon reçu
                                            </button>
                                        </form>
                                    @endif

                                    @if($request->status === 'sample_received')
                                        <form action="{{ route('lab.requests.status', $request) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="in_progress">
                                            <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-bold rounded-lg shadow transition-all">
                                                🔬 Démarrer l'analyse
                                            </button>
                                        </form>
                                    @endif

                                    @if($request->status === 'in_progress')
                                        <button onclick="openResultModal({{ $request->id }}, '{{ $request->test_name }}', '{{ $request->patient_name }}')" 
                                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg shadow transition-all">
                                            ✅ Saisir le résultat
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">🎉</div>
                    <p class="text-xl font-bold text-gray-600">Aucune analyse en attente</p>
                    <p class="text-sm text-gray-500 mt-2">Toutes les demandes ont été traitées !</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODAL SAISIE RÉSULTAT --}}
<div id="resultModal" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full">
        <div class="bg-gradient-to-r from-green-600 to-emerald-500 p-6 rounded-t-2xl">
            <h2 class="text-2xl font-black text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Saisir le Résultat
            </h2>
            <p class="text-white/80 text-sm mt-1" id="modalTestInfo"></p>
        </div>

        <form id="resultForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">📊 Résultats de l'analyse</label>
                <div class="mb-3 p-3 bg-amber-50 border border-amber-100 rounded-lg">
                    <p class="text-[10px] font-bold text-amber-800 uppercase mb-1">💡 Astuce Formatage</p>
                    <p class="text-[11px] text-amber-900 line-height-1">
                        Pour un affichage optimal sur le rapport (style BioGroupe), saisissez vos résultats ligne par ligne.<br>
                        Exemple : <code class="bg-amber-100 px-1">Leucocytes : 8500 /mm3 [4000 - 10000]</code>
                    </p>
                </div>
                <textarea name="result" rows="8" required
                          placeholder="Saisissez les résultats ici...&#10;Ex: Hémoglobine : 13.5 g/dl [14.0 - 17.0]"
                          class="w-full p-4 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-4 focus:ring-green-500/10 outline-none transition-all font-mono text-sm"></textarea>
            </div>

            <div class="flex gap-3 pt-4 border-t">
                <button type="button" onclick="closeResultModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-xl transition-all">
                    Annuler
                </button>
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold py-3 rounded-xl shadow-lg transform hover:-translate-y-0.5 transition-all">
                    🚀 Valider et Envoyer au Médecin
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResultModal(requestId, testName, patientName) {
    document.getElementById('resultModal').classList.remove('hidden');
    document.getElementById('modalTestInfo').textContent = `${testName} - Patient: ${patientName}`;
    document.getElementById('resultForm').action = `/lab/requests/${requestId}/result`;
}

function closeResultModal() {
    document.getElementById('resultModal').classList.add('hidden');
    document.getElementById('resultForm').reset();
}
</script>
@endsection
