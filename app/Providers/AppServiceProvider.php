<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\{Gate, Blade, Auth, URL, Schema}; // Fusion Railway + Schema
use App\Auth\PatientUserProvider;
use App\Models\Patient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force URL scheme and root URL for subdirectory hosting
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
        Schema::defaultStringLength(191);

        // Enregistrer le provider personnalisé pour patients
        Auth::provider('patient', function ($app, array $config) {
            return new PatientUserProvider($app['hash'], Patient::class);
        });

        // Directives Blade personnalisées
        Blade::directive('role', function ($role) {
            return "<?php if(auth()->check() && auth()->user()->role === {$role}): ?>";
        });

        Blade::directive('endrole', function () {
            return '<?php endif; ?>';
        });

        // Gates pour les permissions
        Gate::define('view-patient', function ($user, $patientId) {
            if ($user->isAdmin()) {
                return true;
            }

            if ($user->isDoctor() || $user->isNurse()) {
                $patient = Patient::find($patientId);
                return $patient && $patient->admissions()
                    ->where('status', 'active')
                    ->whereHas('room', function($q) use ($user) {
                        $q->where('service_id', $user->service_id);
                    })
                    ->exists();
            }

            return false;
        });

        Gate::define('prescribe', function ($user) {
            return $user->isDoctor();
        });

        Gate::define('validate-document', function ($user) {
            return $user->isDoctor();
        });
    }
}