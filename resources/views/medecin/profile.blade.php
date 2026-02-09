@extends('layouts.app')

@section('title', 'Mon Profil Professionnel - Dr. ' . $user->name)

@section('content')
<div class="p-6 lg:p-10 bg-gray-50 min-h-screen" x-data="{ 
    showEditInfo: false, 
    showEditSlot: false,
    selectedSlot: { id: '', day: '', start: '', end: '', active: true }
}">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header Card -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden relative">
            <div class="h-40 gradient-primary"></div>
            <div class="px-8 pb-10">
                <div class="flex flex-col md:flex-row items-end gap-6 -mt-16">
                    <div class="relative group">
                        <div class="h-40 w-40 rounded-[2rem] bg-white p-2 shadow-2xl ring-8 ring-white overflow-hidden">
                            @if(isset($user->profile_photo_path))
                                <img src="{{ asset('storage/'.$user->profile_photo_path) }}" class="h-full w-full object-cover rounded-[1.5rem]" alt="{{ $user->name }}">
                            @else
                                <div class="h-full w-full bg-blue-50 flex items-center justify-center text-blue-600 font-black text-5xl rounded-[1.5rem] uppercase">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                            @endif
                        </div>
                        <button class="absolute bottom-2 right-2 p-3 bg-white hover:bg-gray-100 rounded-2xl shadow-lg transition-all group-hover:scale-110">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    
                    <div class="flex-1 pb-2">
                        <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase">Dr. {{ $user->name }}</h1>
                        <p class="text-blue-600 font-bold uppercase tracking-widest text-sm mb-4">
                            <span class="inline-flex items-center">
                                <i class="fas fa-stethoscope mr-2"></i> {{ $user->service->name ?? 'Médecin' }} • {{ $user->hospital->name ?? 'Établissement' }}
                            </span>
                        </p>
                    </div>
                    
                    <div class="pb-2">
                        <span class="px-6 py-2 bg-green-50 text-green-700 rounded-full font-black text-[10px] uppercase tracking-tighter ring-1 ring-green-100 italic">Compte Actif</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Info Pane -->
            <div class="space-y-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-6 pb-3 border-b-2 border-gray-50">
                        <h2 class="text-xl font-black text-gray-900 uppercase italic tracking-tighter">Informations</h2>
                        <button @click="showEditInfo = true" class="text-blue-600 hover:text-blue-800 p-2 rounded-xl bg-blue-50 hover:bg-blue-100 transition-all">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                    <ul class="space-y-6">
                        <li>
                            <p class="text-[10px] text-gray-400 font-black uppercase mb-1">Nouveau Matricule</p>
                            <p class="font-bold text-gray-800">{{ $user->registration_number ?? 'NON RENSEIGNÉ' }}</p>
                        </li>
                        <li>
                            <p class="text-[10px] text-gray-400 font-black uppercase mb-1">Email Professionnel</p>
                            <p class="font-bold text-gray-800">{{ $user->email }}</p>
                        </li>
                        <li>
                            <p class="text-[10px] text-gray-400 font-black uppercase mb-1">Téléphone</p>
                            <p class="font-bold text-gray-800">{{ $user->phone ?? 'NON RENSEIGNÉ' }}</p>
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-tr from-gray-900 to-gray-800 p-8 rounded-[2rem] shadow-xl text-white">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-12 w-12 bg-white/10 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h2 class="text-lg font-black uppercase tracking-tight italic">Sécurité</h2>
                    </div>
                    <p class="text-gray-400 text-xs font-bold leading-relaxed mb-6 italic opacity-70">Gérez vos accès et la confidentialité de vos interventions médicales.</p>
                    <a href="{{ route('profile.edit') }}" class="block text-center w-full py-4 bg-white text-gray-900 rounded-2xl font-black hover:bg-blue-50 transition-all text-xs tracking-widest uppercase">Modifier Accès</a>
                </div>
            </div>

            <!-- Main Agenda Pane -->
            <div class="lg:col-span-2">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-10 pb-6 border-b-2 border-gray-50">
                        <h2 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Agenda & Planning de Disponibilité</h2>
                        @if($availability->isNotEmpty())
                        <div class="flex gap-2">
                            <form action="{{ route('profile.availability.initialize') }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-gray-100 text-gray-600 px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-200 transition-all">Réinitialiser</button>
                            </form>
                        </div>
                        @else
                        <button class="bg-blue-50 text-blue-600 px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-blue-100 transition-all">Gérer mon Agenda</button>
                        @endif
                    </div>

                    <div class="space-y-4">
                        @php
                            $daysMapping = [
                                'monday' => 'Lundi',
                                'tuesday' => 'Mardi',
                                'wednesday' => 'Mercredi',
                                'thursday' => 'Jeudi',
                                'friday' => 'Vendredi',
                                'saturday' => 'Samedi',
                                'sunday' => 'Dimanche'
                            ];
                        @endphp

                        @forelse($availability as $availabilitySlot)
                        <div class="flex items-center gap-6 p-6 rounded-[1.5rem] bg-gray-50 border border-transparent hover:border-blue-100 hover:bg-white hover:shadow-lg transition-all group">
                            <div class="h-16 w-16 bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center">
                                <span class="text-xs font-black {{ $availabilitySlot->is_active ? 'text-blue-600' : 'text-gray-400' }} uppercase leading-none">{{ substr($daysMapping[$availabilitySlot->day_of_week] ?? $availabilitySlot->day_of_week, 0, 3) }}.</span>
                            </div>
                            
                            <div class="flex-1">
                                <h3 class="font-black text-gray-900 text-lg uppercase">{{ $daysMapping[$availabilitySlot->day_of_week] ?? $availabilitySlot->day_of_week }}</h3>
                                <p class="text-sm text-gray-500 font-bold uppercase tracking-widest">Consultations régulières</p>
                            </div>

                            <div class="flex items-center gap-6">
                                <div class="text-right">
                                    <div class="flex items-center justify-end gap-2 text-blue-600 font-black text-xl italic tracking-tighter mb-1">
                                        <span>{{ \Carbon\Carbon::parse($availabilitySlot->start_time)->format('H:i') }}</span>
                                        <span class="text-gray-300">/</span>
                                        <span>{{ \Carbon\Carbon::parse($availabilitySlot->end_time)->format('H:i') }}</span>
                                    </div>
                                    <button 
                                        onclick="event.preventDefault(); document.getElementById('toggle-slot-{{ $availabilitySlot->id }}').submit();"
                                        class="px-3 py-1 {{ $availabilitySlot->is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} rounded-lg font-black text-[9px] uppercase tracking-widest hover:scale-105 transition-all">
                                        {{ $availabilitySlot->is_active ? 'En Service' : 'Absent' }}
                                    </button>
                                    <form id="toggle-slot-{{ $availabilitySlot->id }}" action="{{ route('profile.availability.toggle', $availabilitySlot->id) }}" method="POST" class="hidden">@csrf</form>
                                </div>
                                <button 
                                    @click="selectedSlot = { 
                                        id: '{{ $availabilitySlot->id }}', 
                                        day: '{{ $daysMapping[$availabilitySlot->day_of_week] ?? $availabilitySlot->day_of_week }}', 
                                        start: '{{ \Carbon\Carbon::parse($availabilitySlot->start_time)->format('H:i') }}', 
                                        end: '{{ \Carbon\Carbon::parse($availabilitySlot->end_time)->format('H:i') }}',
                                        active: {{ $availabilitySlot->is_active ? 'true' : 'false' }} 
                                    }; showEditSlot = true"
                                    class="flex items-center gap-2 px-4 py-3 bg-white text-gray-500 hover:text-blue-600 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-100 transition-all group/edit">
                                    <i class="fas fa-cog transition-transform group-hover/edit:rotate-90"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Modifier</span>
                                </button>
                            </div>
                        </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-20 bg-gray-50 rounded-[2rem] border-2 border-dashed border-gray-200">
                            <p class="text-gray-400 font-black uppercase tracking-widest italic opacity-50">Aucun créneau configuré pour cette semaine.</p>
                            <form action="{{ route('profile.availability.initialize') }}" method="POST">
                                @csrf
                                <button type="submit" class="mt-6 px-10 py-4 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lg hover:shadow-blue-200 transition-all">Initialiser mon Planning</button>
                            </form>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-10 p-6 bg-blue-50/50 rounded-[1.5rem] border border-blue-100">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-blue-600 text-white rounded-xl shadow-lg">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-blue-900 uppercase text-xs mb-1">Note Importante</h4>
                                <p class="text-blue-700/70 text-xs font-bold italic leading-relaxed">Les rendez-vous patients sont filtrés selon votre présence journalière. Assurez-vous que votre planning est à jour pour recevoir vos dossiers en temps réel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Info Modal -->
    <div x-show="showEditInfo" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200" @click.away="showEditInfo = false">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter">Modifier mes informations</h3>
                <button @click="showEditInfo = false" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white text-gray-400 hover:text-red-500 shadow-sm transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PATCH')
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom Complet</label>
                    <input type="text" name="name" value="{{ $user->name }}" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all" required>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Téléphone</label>
                        <input type="text" name="phone" value="{{ $user->phone }}" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nouveau Matricule</label>
                        <input type="text" name="registration_number" value="{{ $user->registration_number }}" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 hover:-translate-y-1 transition-all">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Slot Modal -->
    <div x-show="showEditSlot" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-200" @click.away="showEditSlot = false">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-2xl font-black text-gray-900 uppercase italic tracking-tighter" x-text="'Modifier - ' + selectedSlot.day"></h3>
                    <p class="text-[10px] text-blue-600 font-bold uppercase tracking-widest">Configuration du créneau</p>
                </div>
                <button @click="showEditSlot = false" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white text-gray-400 hover:text-red-500 shadow-sm transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('profile.availability.update') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <input type="hidden" name="slot_id" :value="selectedSlot.id">
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Heure de début</label>
                        <input type="time" name="start_time" :value="selectedSlot.start" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Heure de fin</label>
                        <input type="time" name="end_time" :value="selectedSlot.end" class="w-full px-6 py-4 bg-gray-50 border-transparent rounded-[1.5rem] font-bold text-gray-800 focus:bg-white focus:ring-4 focus:ring-blue-50 transition-all" required>
                    </div>
                </div>

                <div class="flex items-center gap-4 p-6 bg-gray-50 rounded-[1.5rem]">
                    <div class="flex-1">
                        <h4 class="font-black text-gray-900 uppercase text-xs">Statut du créneau</h4>
                        <p class="text-[10px] text-gray-400 font-bold">Activer ou désactiver ce jour</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" class="sr-only peer" :checked="selectedSlot.active" value="1">
                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black uppercase tracking-widest shadow-xl shadow-blue-100 hover:bg-blue-700 hover:-translate-y-1 transition-all">
                        Enregistrer les changements
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
