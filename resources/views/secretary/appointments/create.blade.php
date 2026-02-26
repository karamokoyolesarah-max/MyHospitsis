@extends('layouts.app')

@section('title', 'Planifier un Rendez-vous - Secrétariat')

@section('content')
<div class="p-8 bg-gray-50 min-h-screen">
    <!-- Header Section -->
    <div class="mb-12 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-1.5 h-8 bg-emerald-600 rounded-full"></div>
                <h1 class="text-4xl font-black text-slate-900 uppercase tracking-tighter italic">Planification RDV</h1>
            </div>
            <p class="text-slate-500 font-bold uppercase tracking-widest text-xs opacity-70">Enregistrement d'une nouvelle visite dans le calendrier.</p>
        </div>
        <a href="{{ route('secretary.appointments.index') }}" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Retour au registre
        </a>
    </div>

    <div class="max-w-4xl">
        <form action="{{ route('secretary.appointments.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Sélection du Patient -->
            <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl p-10">
                <div class="flex items-center justify-between gap-4 mb-10">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase italic tracking-tighter">Sélection du Patient</h3>
                    </div>
                    <a href="{{ route('secretary.patients.create') }}" class="text-[9px] font-black text-blue-600 bg-blue-50 px-4 py-2 rounded-full uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                        <i class="fas fa-user-plus text-xs"></i>
                        Nouveau Patient
                    </a>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Choisir un patient</label>
                    <select name="patient_id" required class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 appearance-none">
                        <option value="">-- Sélectionnez un patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (isset($patient) && $patient->id == $p->id) || old('patient_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->full_name }} ({{ $p->ipu }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Détails du Rendez-vous -->
            <div class="bg-white rounded-[3.5rem] border border-slate-100 shadow-2xl p-10">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase italic tracking-tighter">Détails de la Visite</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Service de destination</label>
                        <select name="service_id" required class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 appearance-none">
                            <option value="">-- Choisir un service --</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}" {{ old('service_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Type de rendez-vous</label>
                        <select name="type" required class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 appearance-none">
                            <option value="consultation">Première Consultation</option>
                            <option value="follow_up">Suivi / Contrôle</option>
                            <option value="emergency">Urgence</option>
                            <option value="routine_checkup">Examen de routine</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Date du RDV</label>
                        <input type="date" name="appointment_date" required min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', date('Y-m-d')) }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Heure du RDV</label>
                        <input type="time" name="appointment_time" required value="{{ old('appointment_time', date('H:i')) }}"
                               class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Motif de la visite (Optionnel)</label>
                        <textarea name="reason" rows="3" placeholder="Description courte du symptôme ou de la demande..."
                                  class="w-full px-8 py-4 bg-slate-50 border-none rounded-3xl text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500/20 placeholder-slate-300">{{ old('reason') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-4 pb-12">
                <button type="submit" class="px-12 py-4 bg-emerald-600 text-white rounded-3xl font-black uppercase tracking-widest text-[10px] hover:bg-emerald-700 transition shadow-2xl shadow-emerald-200 italic">
                    Confirmer le Rendez-vous <i class="fas fa-check-circle ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
