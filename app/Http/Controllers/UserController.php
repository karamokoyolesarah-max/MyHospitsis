<?php

namespace App\Http\Controllers;

use App\Models\{User, Service, AuditLog, Patient, Appointment, Admission, Invoice, Hospital};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, DB, Auth};
use Carbon\Carbon;

// ============ USER CONTROLLER ============
class UserController extends Controller
{
    public function __construct()
    {
        // On n'applique le middleware de restriction qu'aux méthodes de gestion administrative
        // Sauf pour l'affichage du formulaire et le traitement de l'inscription
        $this->middleware('role:administrative,admin')->except(['showRegistrationForm', 'register']);
    }

    /**
     * Affiche le formulaire d'auto-inscription pour les médecins/staff
     */
    public function showRegistrationForm($hospital_slug)
    {
        // Récupérer l'hôpital par son slug
        $hospital = Hospital::where('slug', $hospital_slug)->where('is_active', true)->firstOrFail();

        // On récupère les services de cet hôpital pour les afficher dans le menu déroulant
        $services = Service::where('hospital_id', $hospital->id)->where('is_active', true)->get();

        return view('auth.register', compact('services', 'hospital'));
    }

    /**
     * Gère la soumission du formulaire d'auto-inscription (Action du Bouton)
     */
    public function register(Request $request, $hospital_slug)
    {
        // 1. Récupérer l'hôpital
        $hospital = Hospital::where('slug', $hospital_slug)->where('is_active', true)->firstOrFail();

        // 2. Validation stricte des données venant du formulaire
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:doctor,nurse,internal_doctor,administrative,cashier', // Ajout de cashier ici
            'service_id' => 'required|exists:services,id',
            'phone' => 'nullable|string|max:20',
            'registration_number' => 'required|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Hachage du mot de passe
            $validated['password'] = Hash::make($validated['password']);

            // Sécurité : Compte inactif par défaut et liaison à l'hôpital
            $validated['is_active'] = false;
            $validated['hospital_id'] = $hospital->id;

            // Création de l'utilisateur
            $user = User::create($validated);

            // Journalisation de l'action
            AuditLog::log('register', 'User', $user->id, [
                'description' => 'Auto-inscription d\'un nouveau praticien (en attente d\'activation)',
                'hospital_id' => $hospital->id,
            ]);

            DB::commit();

            // Redirection avec message de succès vers la page de login de l'hôpital
            return redirect()->route('hospital.login', $hospital->slug)
                             ->with('success', 'Votre demande d\'inscription a été transmise. Un administrateur activera votre compte après vérification.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]);
        }
    }

    // --- MÉTHODES DE GESTION ADMINISTRATIVE (CONSERVÉES) ---

    public function index(Request $request)
    {
        $query = User::with('service')->where('hospital_id', auth()->user()->hospital_id);

        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'doctor') {
                $query->whereIn('role', ['doctor', 'medecin', 'internal_doctor', 'doctor_lab', 'doctor_radio']);
            } elseif ($role === 'nurse') {
                $query->whereIn('role', ['nurse']); // Add sub-roles here if they exist
            } elseif ($role === 'lab_technician') {
                $query->whereIn('role', ['lab_technician', 'radio_technician']);
            } else {
                $query->where('role', $role);
            }
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('pole')) {
            $pole = $request->pole;
            if ($pole === 'medical') {
                $query->medical();
            } elseif ($pole === 'technical') {
                $query->technical();
            } elseif ($pole === 'support') {
                $query->support();
            }
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('service', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->latest()->paginate(20);
        $services = Service::where('hospital_id', auth()->user()->hospital_id)->where('is_active', true)->get();

        return view('users.index', compact('users', 'services'));
    }

    public function create()
    {
        $services = Service::where('hospital_id', auth()->user()->hospital_id)
            ->where('is_active', true)
            ->with('parent')
            ->get();
        return view('users.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,nurse,administrative,cashier,lab_technician,internal_doctor,doctor_lab,pharmacist,secretary',
            'service_id' => 'nullable|exists:services,id',
            'phone' => 'nullable|string|max:20',
            'registration_number' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Restriction métier : Pas de médecin/infirmier dans les pôles Support (Caisse)
            if ($request->filled('service_id')) {
                $service = Service::find($request->service_id);
                if ($service && $service->type === 'support') {
                    if (in_array($validated['role'], ['doctor', 'nurse', 'internal_doctor', 'doctor_lab'])) {
                        return back()->withInput()->withErrors(['service_id' => 'Les rôles médicaux ne peuvent pas être affectés à un Pôle de Caisse (Support).']);
                    }
                }
            }

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = true;
            $validated['hospital_id'] = auth()->user()->hospital_id;

            $user = User::create($validated);

            AuditLog::log('create', 'User', $user->id, [
                'description' => 'Création d\'un compte utilisateur par un admin',
            ]);

            DB::commit();

            return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la création : ' . $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        $user->load('service');
        $stats = [];
        
        if ($user->role === 'doctor' || $user->role === 'internal_doctor') {
            $stats['appointments'] = Appointment::where('doctor_id', $user->id)->count();
            $stats['patients'] = Admission::where('doctor_id', $user->id)
                ->distinct('patient_id')
                ->count('patient_id');
        }

        return view('users.show', compact('user', 'stats'));
    }

    public function edit(User $user)
    {
        $services = Service::where('hospital_id', auth()->user()->hospital_id)
            ->where('is_active', true)
            ->with('parent')
            ->get();
        return view('users.edit', compact('user', 'services'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,doctor,nurse,administrative,cashier,lab_technician,internal_doctor,doctor_lab,pharmacist,secretary',
            'service_id' => 'nullable|exists:services,id',
            'phone' => 'nullable|string|max:20',
            'registration_number' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Restriction métier : Pas de médecin/infirmier dans les pôles Support (Caisse)
            if ($request->filled('service_id')) {
                $service = Service::find($request->service_id);
                if ($service && $service->type === 'support') {
                    if (in_array($validated['role'], ['doctor', 'nurse', 'internal_doctor', 'doctor_lab'])) {
                        return back()->withInput()->withErrors(['service_id' => 'Les rôles médicaux ne peuvent pas être affectés à un Pôle de Caisse (Support).']);
                    }
                }
            }

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['is_active'] = $request->has('is_active');

            $oldData = $user->toArray();
            $user->update($validated);

            AuditLog::log('update', 'User', $user->id, [
                'description' => 'Modification d\'un compte utilisateur',
                'old' => $oldData,
                'new' => $user->toArray()
            ]);

            DB::commit();

            return redirect()->route('users.show', $user)->with('success', 'Utilisateur mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Erreur lors de la mise à jour : ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(User $user)
    {
        DB::beginTransaction();
        try {
            $user->update(['is_active' => !$user->is_active]);

            AuditLog::log('update', 'User', $user->id, [
                'description' => $user->is_active ? 'Activation du compte' : 'Désactivation du compte',
            ]);

            DB::commit();

            return back()->with('success', $user->is_active ? 'Utilisateur activé.' : 'Utilisateur désactivé.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors du changement de statut.']);
        }
    }

    public function enableMfa(Request $request)
    {
        auth()->user()->update(['mfa_enabled' => true]);
        return back()->with('success', 'Authentification à deux facteurs activée.');
    }

    public function disableMfa(Request $request)
    {
        auth()->user()->update(['mfa_enabled' => false]);
        return back()->with('success', 'Authentification à deux facteurs désactivée.');
    }
}