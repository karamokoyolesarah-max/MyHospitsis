<?php

namespace App\Http\Controllers;

use App\Models\{Appointment, User, Service, AuditLog};
use Illuminate\Http\Request;
use Carbon\Carbon;

class SecretaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:secretary,admin');
    }

    public function index()
    {
        $hospital_id = auth()->user()->hospital_id;

        // Statistiques globales pour la secrétaire
        $stats = [
            'total_patients' => \App\Models\Patient::where('hospital_id', $hospital_id)->count(),
            'new_patients_today' => \App\Models\Patient::where('hospital_id', $hospital_id)->whereDate('created_at', Carbon::today())->count(),
            'today_appointments' => Appointment::where('hospital_id', $hospital_id)->whereDate('appointment_datetime', Carbon::today())->count(),
            'pending_assignments' => Appointment::where('hospital_id', $hospital_id)->whereNull('doctor_id')->whereNull('secretary_archived_at')->where('status', '!=', 'cancelled')->count(),
            'active_doctors' => User::where('hospital_id', $hospital_id)->whereIn('role', ['doctor', 'internal_doctor', 'medecin'])->where('is_active', true)->count(),
        ];

        // Dernières activités d'assignation
        $recentAssignments = Appointment::where('hospital_id', $hospital_id)
            ->whereNotNull('doctor_id')
            ->with(['patient', 'doctor', 'service'])
            ->latest('updated_at')
            ->limit(5)
            ->get();

        return view('secretary.home', compact('stats', 'recentAssignments'));
    }

    public function dashboard()
    {
        $hospital_id = auth()->user()->hospital_id;

        // Appointments waiting for doctor assignment (Orientation Table)
        $pendingAppointments = Appointment::where('hospital_id', $hospital_id)
            ->whereNull('doctor_id')
            ->whereNull('secretary_archived_at')
            ->where('status', '!=', 'cancelled')
            ->with(['patient', 'service'])
            ->latest()
            ->get();

        // Doctors available for assignment
        $doctors = User::where('hospital_id', $hospital_id)
            ->whereIn('role', ['doctor', 'internal_doctor', 'medecin'])
            ->where('is_active', true)
            ->with(['service', 'availabilities'])
            ->get();

        // Recently Assigned Appointments (Today, and NOT archived)
        $assignedToday = Appointment::where('hospital_id', $hospital_id)
            ->whereNotNull('doctor_id')
            ->whereNull('secretary_archived_at')
            ->whereDate('updated_at', Carbon::today())
            ->with(['patient', 'service', 'doctor'])
            ->latest()
            ->get();

        // Recent Archives (Preview for the dashboard tab)
        $recentArchives = Appointment::where('hospital_id', $hospital_id)
            ->whereNotNull('secretary_archived_at')
            ->with(['patient', 'service', 'doctor'])
            ->latest('secretary_archived_at')
            ->limit(10)
            ->get();

        $stats = $this->getStats($hospital_id);

        return view('secretary.dashboard', compact('pendingAppointments', 'doctors', 'assignedToday', 'recentArchives', 'stats'));
    }

    private function getStats($hospital_id)
    {
        return [
            'pending_count' => Appointment::where('hospital_id', $hospital_id)
                ->whereNull('doctor_id')
                ->whereNull('secretary_archived_at')
                ->where('status', '!=', 'cancelled')
                ->count(),
            'today_assigned_count' => Appointment::where('hospital_id', $hospital_id)
                ->whereNotNull('doctor_id')
                ->whereNull('secretary_archived_at')
                ->whereDate('updated_at', Carbon::today())
                ->count(),
            'total_today' => Appointment::where('hospital_id', $hospital_id)
                ->whereDate('appointment_datetime', Carbon::today())
                ->count(),
        ];
    }

    public function archiveAssignment(Appointment $appointment)
    {
        if ($appointment->hospital_id !== auth()->user()->hospital_id) {
            abort(403);
        }

        $appointment->update([
            'secretary_archived_at' => now()
        ]);

        AuditLog::log('update', 'Appointment', $appointment->id, [
            'description' => "Assignation archivée par la secrétaire " . auth()->user()->name,
        ]);

        return back()->with('success', "Assignation archivée avec succès.");
    }

    public function destroyAssignment(Appointment $appointment)
    {
        if ($appointment->hospital_id !== auth()->user()->hospital_id) {
            abort(403);
        }

        $patientName = $appointment->patient->name;
        $appointment->delete();

        AuditLog::log('delete', 'Appointment', $appointment->id, [
            'description' => "Assignation de {$patientName} supprimée définitivement par la secrétaire " . auth()->user()->name,
        ]);

        return back()->with('success', "Assignation supprimée définitivement.");
    }

    public function assignmentHistory(Request $request)
    {
        $hospital_id = auth()->user()->hospital_id;

        $query = Appointment::where('hospital_id', $hospital_id)
            ->whereNotNull('secretary_archived_at')
            ->with(['patient', 'service', 'doctor']);

        // Filtrage par recherche (Nom, IPU)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('ipu', 'like', "%{$search}%");
            });
        }

        // Filtrage par Date
        if ($request->filled('date_filter')) {
            $filter = $request->date_filter;
            $date = match($filter) {
                'today' => Carbon::today(),
                'yesterday' => Carbon::yesterday(),
                '2_days' => Carbon::today()->subDays(2),
                'week' => Carbon::today()->subWeek(),
                'month' => Carbon::today()->subMonth(),
                default => null
            };

            if ($date) {
                if ($filter === 'yesterday' || $filter === '2_days') {
                    $query->whereDate('secretary_archived_at', $date);
                } else {
                    $query->where('secretary_archived_at', '>=', $date);
                }
            }
        }

        // Filtrage personnalisé (Range)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('secretary_archived_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }

        // Filtrage par Docteur
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        $archives = $query->latest('secretary_archived_at')->paginate(20);
        $stats = $this->getStats($hospital_id);
        
        // Liste des médecins pour le filtre
        $doctors = User::where('hospital_id', $hospital_id)
            ->whereIn('role', ['doctor', 'internal_doctor', 'medecin'])
            ->get();

        return view('secretary.history', compact('archives', 'stats', 'doctors'));
    }

    public function assignDoctor(Request $request, Appointment $appointment)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
        ]);

        $doctor = User::findOrFail($request->doctor_id);

        if ($appointment->hospital_id !== auth()->user()->hospital_id) {
            abort(403);
        }

        $appointment->update([
            'doctor_id' => $doctor->id,
            'status' => 'confirmed' // Auto-confirm when assigned by secretary
        ]);

        AuditLog::log('update', 'Appointment', $appointment->id, [
            'description' => "Rendez-vous assigné au Dr. {$doctor->name} par la secrétaire " . auth()->user()->name,
        ]);

        return back()->with('success', "Rendez-vous assigné au Dr. {$doctor->name} avec succès.");
    }

    public function doctorAgendas()
    {
        $hospital_id = auth()->user()->hospital_id;
        $doctors = User::where('hospital_id', $hospital_id)
            ->whereIn('role', ['doctor', 'internal_doctor', 'medecin'])
            ->with(['service', 'availabilities'])
            ->get();

        return view('secretary.agendas', compact('doctors'));
    }

    // --- GESTION DES PATIENTS (VUES DÉDIÉES) ---

    public function patientsIndex(Request $request)
    {
        $hospital_id = auth()->user()->hospital_id;
        $query = \App\Models\Patient::where('hospital_id', $hospital_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('ipu', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate(15);
        return view('secretary.patients.index', compact('patients'));
    }

    public function patientsCreate()
    {
        return view('secretary.patients.create');
    }

    public function patientsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:Homme,Femme,Other',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['hospital_id'] = auth()->user()->hospital_id;
        $validated['ipu'] = \App\Models\Patient::generateIpu();

        $patient = \App\Models\Patient::create($validated);

        AuditLog::log('create', 'Patient', $patient->id, [
            'description' => "Patient créé par la secrétaire " . auth()->user()->name,
        ]);

        return redirect()->route('secretary.patients.index')->with('success', "Patient {$patient->full_name} créé avec succès. IPU: {$patient->ipu}");
    }

    // --- GESTION DES RENDEZ-VOUS (VUES DÉDIÉES) ---

    public function appointmentsIndex(Request $request)
    {
        $hospital_id = auth()->user()->hospital_id;
        $query = Appointment::where('hospital_id', $hospital_id)
            ->with(['patient', 'doctor', 'service']);

        if ($request->filled('date')) {
            $query->whereDate('appointment_datetime', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->latest('appointment_datetime')->paginate(20);
        return view('secretary.appointments.index', compact('appointments'));
    }

    public function appointmentsCreate(Request $request)
    {
        $hospital_id = auth()->user()->hospital_id;
        $patientId = $request->input('patient_id');
        $patient = $patientId ? \App\Models\Patient::where('hospital_id', $hospital_id)->findOrFail($patientId) : null;
        
        $patients = \App\Models\Patient::where('hospital_id', $hospital_id)->where('is_active', true)->orderBy('name')->get();
        $services = Service::where('is_active', true)->get();
        
        return view('secretary.appointments.create', compact('patient', 'patients', 'services'));
    }

    public function appointmentsStore(Request $request)
    {
        $request->merge([
            'appointment_datetime' => $request->input('appointment_date') . ' ' . $request->input('appointment_time'),
        ]);

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'appointment_datetime' => 'required|date|after:now',
            'type' => 'required|in:consultation,follow_up,emergency,routine_checkup',
            'reason' => 'nullable|string|max:500',
        ]);

        $validated['hospital_id'] = auth()->user()->hospital_id;
        $validated['status'] = 'scheduled';
        $validated['duration'] = 30; // Default duration

        unset($validated['appointment_date'], $validated['appointment_time']);

        $appointment = Appointment::create($validated);

        AuditLog::log('create', 'Appointment', $appointment->id, [
            'description' => "Rendez-vous créé par la secrétaire " . auth()->user()->name,
        ]);

        return redirect()->route('secretary.appointments.index')->with('success', "Rendez-vous enregistré avec succès pour le " . Carbon::parse($appointment->appointment_datetime)->format('d/m/Y à H:i'));
    }
}
