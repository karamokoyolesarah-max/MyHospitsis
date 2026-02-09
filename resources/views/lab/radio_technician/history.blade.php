@extends('layouts.app')

@section('title', 'Historique Imagerie')

@section('content')
<div class="px-6 py-8">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Historique Imagerie</h1>
            <p class="text-gray-500 mt-1">Archives des examens d'imagerie validés</p>
        </div>
        
        {{-- Filter Buttons --}}
        <div class="flex gap-2">
            <a href="{{ route('lab.radio_technician.history', ['period' => 'today'] + request()->except('period')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('period') === 'today' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Aujourd'hui
            </a>
            <a href="{{ route('lab.radio_technician.history', ['period' => 'week'] + request()->except('period')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('period') === 'week' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Cette Semaine
            </a>
            <a href="{{ route('lab.radio_technician.history', ['period' => 'month'] + request()->except('period')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('period') === 'month' ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Ce Mois
            </a>
            <a href="{{ route('lab.radio_technician.history', request()->except('period')) }}" 
               class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('period') ? 'bg-purple-600 text-white shadow-lg' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                Tout
            </a>
        </div>
    </div>
    
    <div class="mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="date" name="date" value="{{ request('date') }}" 
                class="rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 text-sm">
                
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher un patient..." 
                    class="pl-10 pr-4 py-2 rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500 w-full sm:w-64 text-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                Filtrer
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-xs uppercase text-gray-500 font-semibold border-b border-gray-100">
                    <th class="px-6 py-4">Patient</th>
                    <th class="px-6 py-4">Examen</th>
                    <th class="px-6 py-4">Résultat / CR</th>
                    <th class="px-6 py-4">Validé par</th>
                    <th class="px-6 py-4">Date Validation</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($completedRequests as $request)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs">
                                {{ substr($request->patient_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $request->patient_name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $request->patient_ipu }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                            {{ $request->test_category }}
                        </span>
                        <div class="mt-1 font-medium text-gray-900">{{ $request->test_name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-xs truncate text-sm text-gray-600" title="{{ $request->result }}">
                            {{ Str::limit($request->result, 50) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        Dr. {{ $request->biologist->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $request->completed_at ? $request->completed_at->format('d/m/Y H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($request->patientVital)
                            <a href="{{ route('medical-records.show', $request->patientVital->id) }}" 
                               class="text-purple-600 hover:text-purple-900 font-medium text-sm hover:underline">
                                Voir Dossier
                            </a>
                        @else
                            <span class="text-gray-400 text-sm">Archivé</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p>Aucun résultat trouvé dans l'historique.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $completedRequests->withQueryString()->links() }}
    </div>
</div>
@endsection
