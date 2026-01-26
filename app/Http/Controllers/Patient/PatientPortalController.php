<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, MedicalRecord, Prescription, Invoice};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PatientPortalController extends Controller 
{
    public function __construct()
    {
        $this->middleware('auth:patients'); 
    }

    public function dashboard()
    {
        \Log::info('=== ACCÈS AU DASHBOARD PATIENT ===');
        \Log::info('Guard patients authentifié ?', [
            'authentifié' => Auth::guard('patients')->check() ? 'OUI' : 'NON',
            'patient_id' => Auth::guard('patients')->id() ?? 'N/A',
        ]);

        $patient = Auth::guard('patients')->user();

        if (!$patient) {
            \Log::error('AUCUN PATIENT CONNECTÉ - Redirection vers login');
            return redirect()->route('patient.login');
        }

        \Log::info('Patient trouvé', [
            'id' => $patient->id,
            'nom' => $patient->full_name,
            'email' => $patient->email,
        ]);

        $patient->load([
            'referringDoctor',
            'prescriptions' => fn($query) => $query->latest()->take(3),
            'medicalRecords' => fn($query) => $query->latest()->take(3),
            'appointments' => fn($query) => $query->latest()->take(5)
        ]);

        $totalAppointments = $patient->appointments()->count();
        $totalPrescriptions = $patient->prescriptions()->count();

        $upcomingAppointments = $patient->appointments()
            ->where('appointment_datetime', '>', now())
            ->where('status', 'confirmed')
            ->orderBy('appointment_datetime')
            ->with(['doctor', 'service'])
            ->take(3)
            ->get();

        $recentRecords = $patient->medicalRecords()
            ->with('doctor')
            ->latest()
            ->take(5)
            ->get();

        \Log::info('Chargement de la vue dashboard');

        return view('patients.auth.dashboard', compact(
            'patient', 
            'upcomingAppointments', 
            'recentRecords', 
            'totalAppointments', 
            'totalPrescriptions'
        ));
    }

    public function appointments()
    {
        $patient = Auth::guard('patients')->user();

        $appointments = $patient->appointments()
            ->with(['doctor', 'service'])
            ->latest()
            ->paginate(10);

        return view('portal.appointments', compact('appointments'));
    }
    public function showBookAppointmentForm()
{
    $patient = Auth::guard('patients')->user();
    
    // 1. Récupérer tous les hôpitaux
    $hospitals = \App\Models\Hospital::all();

    // 2. Préparer les services groupés par hôpital pour le JavaScript
    // On récupère les services (ou prestations) liés aux hôpitaux
    $allServices = \App\Models\Service::all(); 

    $servicesAndPrestations = [];
    foreach ($allServices as $service) {
        // On crée un tableau où la clé est l'ID de l'hôpital
        $servicesAndPrestations[$service->hospital_id][] = [
            'id' => $service->id,
            'name' => $service->name,
            'price' => $service->price
        ];
    }

    return view('patients.auth.book-appointment', compact('patient', 'hospitals', 'servicesAndPrestations'));
}
    public function bookAppointment()
    {
        $patient = Auth::guard('patients')->user();
        $hospitals = \App\Models\Hospital::where('is_active', true)->get();

        $hospitalsData = [];

        foreach ($hospitals as $hospital) {
            $services = \App\Models\Service::where('hospital_id', $hospital->id)
                ->where('is_active', true)
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'price' => $s->consultation_price ?? 0
                ]);

            $prestations = \App\Models\Prestation::where('hospital_id', $hospital->id)
                ->where('is_active', true)
                ->get()
                ->map(fn($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $p->price ?? 0,
                    'service_id' => $p->service_id
                ]);

            $hospitalsData[$hospital->id] = [
                'services' => $services,
                'prestations' => $prestations,
                'address' => $hospital->address
            ];
        }

        return view('portal.book-appointment', compact('patient', 'hospitals', 'hospitalsData'));
    }


    /**
     * Récupérer les prestations de consultation d'un hôpital via AJAX
     */
    public function getHospitalServices($hospitalId)
    {
        $prestations = \App\Models\Prestation::where('hospital_id', $hospitalId)
            ->where('category', 'consultation')
            ->where('is_active', true)
            ->get()
            ->map(function($prestation) {
                return [
                    'id' => $prestation->id,
                    'name' => $prestation->name,
                    'price' => $prestation->price,
                ];
            });

        return response()->json($prestations);
    }

    public function profile()
    {
        $patient = Auth::guard('patients')->user();
        $hospitals = \App\Models\Hospital::where('is_active', true)->get();
        return view('portal.profile', compact('patient', 'hospitals'));
    }

    public function updateProfile(Request $request)
    {
        $patient = Auth::guard('patients')->user();

        $validated = $request->validate([
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:patients,email,' . $patient->id, 
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $patient->update($validated);

        return back()->with('success', 'Vos informations ont été mises à jour avec succès.');
    }

    public function medicalHistory()
    {
        $patient = Auth::guard('patients')->user();
        
        $records = $patient->medicalRecords()
            ->with(['doctor', 'service', 'hospital'])
            ->latest()
            ->paginate(10);

        return view('portal.medical-history', compact('records'));
    }

    public function prescriptions()
    {
        $patient = Auth::guard('patients')->user();
        $prescriptions = $patient->prescriptions()->with('doctor')->latest()->paginate(10);
        return view('portal.prescriptions', compact('prescriptions'));
    }

    public function invoices()
    {
        $patient = Auth::guard('patients')->user();
        $invoices = Invoice::where('patient_id', $patient->id)->latest()->paginate(10);
        return view('portal.invoices', compact('invoices'));
    }

    public function messaging()
    {
        $patient = Auth::guard('patients')->user();
        $conversations = []; 
        return view('portal.messaging', compact('conversations'));
    }

    public function documents()
    {
        $patient = Auth::guard('patients')->user();
        $documents = $patient->documents()->latest()->paginate(10);
        return view('portal.documents', compact('documents'));
    }

    public function storeAppointment(Request $request)
    {
        $validated = $request->validate([
            'consultation_type' => 'required|in:hospital,home',
            'hospital_id' => 'required|exists:hospitals,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'service_id' => 'required|exists:services,id',
            'prestation_id' => 'nullable|exists:prestations,id',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'home_address' => 'required_if:consultation_type,home|nullable|string',
        ]);

        $patient = Auth::guard('patients')->user();

        // Combiner date et heure
        $appointmentDateTime = $validated['appointment_date'] . ' ' . $validated['appointment_time'];

        $serviceId = $validated['service_id'];
        $prestationId = $validated['prestation_id'];

        // --- PLUS D'ATTRIBUTION AUTOMATIQUE ---
        // Le rendez-vous reste à NULL pour être visible par tous les médecins du service
        // Ils pourront l'approuver et l'assigner eux-mêmes depuis leur dashboard.
        $assignedDoctorId = null;

        // Créer le rendez-vous
        $appointment = Appointment::create([
            'patient_id' => Auth::guard('patients')->id(),
            'service_id' => $serviceId,
            'doctor_id' => $assignedDoctorId,
            'appointment_datetime' => $appointmentDateTime,
            'status' => 'pending',
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'consultation_type' => $validated['consultation_type'],
            'home_address' => $validated['home_address'] ?? null,
            'hospital_id' => $validated['hospital_id'],
        ]);

        // Attach the prestation if selected
        if ($prestationId) {
            $prestation = \App\Models\Prestation::find($prestationId);
            if ($prestation) {
                $appointment->prestations()->attach($prestationId, [
                    'quantity' => 1,
                    'unit_price' => $prestation->price,
                    'total' => $prestation->price,
                ]);
            }
        }

        return redirect()->route('patient.appointments')
            ->with('success', 'Votre demande de rendez-vous a été enregistrée. Vous serez contacté pour confirmation.');
    }


    public function cancelAppointment(Appointment $appointment)
    {
        $patient = Auth::guard('patients')->user();

        // Vérifier que le rendez-vous appartient au patient
        if ($appointment->patient_id !== $patient->id) {
            abort(403, 'Vous n\'êtes pas autorisé à annuler ce rendez-vous.');
        }

        // Vérifier que le rendez-vous peut être annulé (pas déjà passé et pas déjà annulé)
        if ($appointment->appointment_datetime <= now() || $appointment->status === 'cancelled') {
            return back()->with('error', 'Ce rendez-vous ne peut pas être annulé.');
        }

        // Annuler le rendez-vous
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Votre rendez-vous a été annulé avec succès.');
    }
}