@extends('layouts.app')

@section('title', 'Gestion Globale des Assurances')

@section('content')
<div class="p-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                <i class="fas fa-shield-alt text-blue-600"></i>
                GESTION DES ASSURANCES
            </h1>
            <p class="text-sm text-gray-500 font-medium whitespace-nowrap overflow-hidden text-ellipsis">Supervision, Configuration et Recouvrement des créances</p>
        </div>
        
        <!-- Alerts Counter -->
        <div class="flex items-center gap-4">
            <div class="px-4 py-2 bg-rose-50 border border-rose-100 rounded-xl flex items-center gap-3 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-rose-500 flex items-center justify-center text-white relative">
                    <i class="fas fa-exclamation-triangle text-xs"></i>
                    @if($fraudAlertsCount > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-700 border-2 border-white rounded-full flex items-center justify-center text-[8px] font-black">{{ $fraudAlertsCount }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-[10px] font-black text-rose-600 uppercase tracking-widest">Alertes Fraude</p>
                    <p class="text-xs font-bold text-gray-900">{{ $fraudAlertsCount }} tentatives invalides</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation & Stats Widgets -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-2xl w-fit shadow-inner">
            @php
                $tabs = [
                    'simulator' => ['icon' => 'fas fa-vial', 'label' => 'Simulateur'],
                    'connectors' => ['icon' => 'fas fa-plug', 'label' => 'Connecteurs API'],
                    'recovery' => ['icon' => 'fas fa-hand-holding-usd', 'label' => 'Recouvrement'],
                    'history' => ['icon' => 'fas fa-history', 'label' => 'Historique Reçus'],
                    'audit' => ['icon' => 'fas fa-chart-line', 'label' => 'Audit & Stats'],
                ];
            @endphp
            @foreach($tabs as $id => $tab)
            <a href="{{ route('admin.insurance.index', ['tab' => $id]) }}"
               class="px-6 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest transition-all
               {{ $activeTab === $id ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                <i class="{{ $tab['icon'] }} mr-2"></i> {{ $tab['label'] }}
            </a>
            @endforeach
        </div>

        <div class="flex items-center gap-4">
            <!-- Paid this Month -->
            <div class="px-5 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm group">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-blue-600 transition-colors">Encaissé (Ce Mois)</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-black text-gray-900">{{ number_format($totalPaidMonth, 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">FCFA</span>
                </div>
            </div>
            <!-- Pending -->
            <div class="px-5 py-3 bg-white border border-gray-100 rounded-2xl shadow-sm group">
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-amber-600 transition-colors">Reste à Percevoir</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-black text-amber-600">{{ number_format($totalPending, 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">FCFA</span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center gap-3 animate-fade-in text-emerald-700 font-bold text-sm">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 gap-8">
        @if($activeTab === 'simulator')
            @include('admin.insurance.tabs.simulator')
        @elseif($activeTab === 'connectors')
            @include('admin.insurance.tabs.connectors')
        @elseif($activeTab === 'recovery')
            @include('admin.insurance.tabs.recovery')
        @elseif($activeTab === 'history')
            @include('admin.insurance.tabs.history')
        @elseif($activeTab === 'audit')
            @include('admin.insurance.tabs.audit')
        @endif
    </div>
</div>

<style>
@keyframes fade-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
.animate-fade-in { animation: fade-in 0.4s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
</style>
@endsection
