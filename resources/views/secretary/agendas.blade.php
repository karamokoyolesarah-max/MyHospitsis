@extends('layouts.app')

@section('title', 'Agendas des Médecins')

@section('content')
<div class="p-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12 animate-in fade-in slide-in-from-top-4 duration-700">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-slate-900 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic">Agendas Médecins</h1>
            </div>
            <p class="text-slate-400 font-bold text-sm ml-4 tracking-wide uppercase">Planning et disponibilités des équipes médicales</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('secretary.dashboard') }}" 
               class="px-6 py-3 bg-white border border-slate-100 text-slate-600 rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-slate-200/40 hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12 animate-in fade-in slide-in-from-top-4 duration-1000">
        @foreach($doctors as $doctor)
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/40 flex flex-col hover:border-blue-200 hover:shadow-2xl hover:shadow-blue-500/10 transition-all duration-500 group overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/30">
                <div class="flex items-center gap-6">
                    <div class="w-20 h-20 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-[2rem] flex items-center justify-center text-white font-black text-2xl shadow-xl shadow-blue-900/20 uppercase transform group-hover:scale-110 transition-transform duration-500">
                        {{ substr($doctor->name, 0, 1) }}{{ substr($doctor->first_name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-black text-slate-900 uppercase text-lg tracking-tighter">Dr. {{ $doctor->name }}</h3>
                        <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full mt-2 inline-block">
                            {{ $doctor->service->name ?? 'Spécialiste' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-8 flex-1 space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <i class="fas fa-clock text-slate-300"></i>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Disponibilités Hebdomadaires</h4>
                    </div>
                    <div class="space-y-3">
                        @forelse($doctor->availabilities as $avail)
                            <div class="flex justify-between items-center text-xs p-3 bg-slate-50/50 rounded-2xl border border-slate-50 group-hover:border-blue-50 transition-colors">
                                <span class="font-black text-slate-600 capitalize tracking-tight">{{ $avail->day_of_week }}</span>
                                <span class="px-4 py-1.5 bg-white text-blue-700 rounded-xl font-black border border-blue-100 shadow-sm">
                                    {{ substr($avail->start_time, 0, 5) }} — {{ substr($avail->end_time, 0, 5) }}
                                </span>
                            </div>
                        @empty
                            <div class="p-6 border-2 border-dashed border-slate-100 rounded-[2rem] text-center">
                                <p class="text-[10px] text-slate-300 font-black uppercase italic italic">Aucune disponibilité</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-1">Charge Aujourd'hui</p>
                            @php
                                $todayAppointments = $doctor->appointments()->whereDate('appointment_datetime', date('Y-m-d'))->count();
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $todayAppointments }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase">RDV Programmés</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 group-hover:bg-slate-900 group-hover:text-white transition-all duration-500">
                            <i class="fas fa-calendar-check lg:text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    @keyframes fade-in-down {
        0% { opacity: 0; transform: translateY(-10px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-in { animation: fade-in-down 0.7s ease-out; }
</style>
@endsection
