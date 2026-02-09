@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Liste de Travail Imagerie</h1>
                <p class="text-gray-500 mt-2 font-medium">Gérez le flux des examens d'imagerie (Radio, Echo, Scanner...)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('lab.radio_technician.worklist', ['filter' => 'urgent']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ request('filter') === 'urgent' ? 'bg-rose-100 text-rose-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    🔥 Urgences
                </a>
                <a href="{{ route('lab.radio_technician.worklist', ['filter' => 'all']) }}" class="px-4 py-2 rounded-xl text-sm font-bold transition-all {{ request('filter') === 'all' ? 'bg-purple-100 text-purple-700' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    📂 Tout voir
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6">
            @forelse($pendingRequests as $request)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row gap-6 relative overflow-hidden group hover:border-purple-200 transition-all">
                    @if($request->clinical_info && str_contains(strtolower($request->clinical_info), 'urgent'))
                        <div class="absolute top-0 right-0 bg-rose-500 text-white text-[10px] uppercase font-bold px-3 py-1 rounded-bl-xl">Urgent</div>
                    @endif

                    <div class="flex-shrink-0 flex flex-col items-center justify-center w-full md:w-24 bg-gray-50 rounded-xl p-2">
                        <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-black text-lg mb-2">
                            {{ substr($request->patient_name, 0, 1) }}
                        </div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest text-center">{{ $request->patient_ipu }}</span>
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $request->patient_name }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-purple-600 font-bold bg-purple-50 px-2 py-0.5 rounded-lg text-xs border border-purple-100">{{ $request->test_name }}</span>
                                    <span class="text-gray-400 text-xs">•</span>
                                    <span class="text-gray-500 text-xs font-medium">Prescrit par {{ $request->doctor->name ?? 'N/A' }}</span>
                                </div>
                                @if($request->clinical_info)
                                    <div class="mt-3 text-sm text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Renseignements Cliniques</span>
                                        {{ $request->clinical_info }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col items-end gap-3 min-w-[200px]">
                                {{-- Status Actions --}}
                                @if($request->status === 'pending')
                                    <form action="{{ route('lab.radio_technician.status', $request->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="sample_received">
                                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Prendre en charge
                                        </button>
                                    </form>
                                @elseif($request->status === 'sample_received')
                                    <form action="{{ route('lab.radio_technician.status', $request->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-purple-500/30 transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Démarrer l'examen
                                        </button>
                                    </form>
                                @elseif($request->status === 'in_progress')
                                    <div x-data="{ open: false }" class="w-full">
                                        <button @click="open = !open" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/30 transition-all flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Saisir le Compte-Rendu
                                        </button>

                                        <div x-show="open" class="mt-4 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                            <form action="{{ route('lab.radio_technician.result', $request->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-3">
                                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Compte-rendu / Résultat</label>
                                                    <textarea name="result" rows="4" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Saisir les observations ou le résultat..."></textarea>
                                                </div>
                                                <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold transition-colors">
                                                    Envoyer pour validation
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="text-xs text-gray-400 font-medium mt-1">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border border-gray-100 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Tout est calme</h3>
                    <p class="text-gray-500 mt-2">Aucun examen en attente pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
