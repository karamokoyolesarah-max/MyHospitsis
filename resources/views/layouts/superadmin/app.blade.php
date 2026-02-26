<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HospitSIS | SaaS Engine</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        /* Sidebar Moderne */
        .sidebar { 
            min-height: 100vh; 
            background: #0f172a; /* Slate 900 */
            color: #94a3b8; 
            transition: all 0.3s;
        }
        .sidebar-brand {
            padding: 2rem 1.5rem;
            color: white;
            font-weight: 800;
            letter-spacing: -1px;
        }
        .sidebar a { 
            color: #94a3b8; 
            text-decoration: none; 
            padding: 12px 20px; 
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 12px;
            margin: 4px 15px;
            transition: 0.2s;
        }
        .sidebar a:hover, .sidebar a.active { 
            background: rgba(59, 130, 246, 0.1); 
            color: #3b82f6; 
        }
        .sidebar a i { font-size: 1.2rem; }
        
        /* Navbar & Cards */
        .main-content { padding-top: 20px; }
        .top-navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        .btn-primary {
            background: #3b82f6;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
        }
        .text-blue-system { color: #3b82f6; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 d-none d-md-block sidebar shadow-lg px-0">
            <div class="sidebar-brand text-center">
                <h3 class="m-0">HospitSIS <span class="text-primary" style="font-size: 0.8rem;">SaaS</span></h3>
            </div>
            
            <div class="nav flex-column mt-2">
                <a href="{{ route('superadmin.dashboard') }}" class="{{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a href="#">
                    <i class="bi bi-building-gear"></i> Gestion Hôpitaux
                </a>
                <a href="#">
                    <i class="bi bi-shield-check"></i> Spécialistes
                </a>
                <a href="{{ route('superadmin.dashboard', ['tab' => 'wave-validation']) }}" class="{{ request()->routeIs('superadmin.wave.index') ? 'active' : '' }}">
                    <i class="bi bi-coin"></i> Validation Wave @if(App\Models\ExternalDoctorRecharge::where('requires_manual_validation', true)->where('status', 'pending')->count() > 0) <span class="badge bg-danger ms-auto rounded-pill">{{ App\Models\ExternalDoctorRecharge::where('requires_manual_validation', true)->where('status', 'pending')->count() }}</span> @endif
                </a>
                <a href="{{ route('superadmin.financial-monitoring') }}" class="{{ request()->routeIs('superadmin.financial-monitoring') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front"></i> Finances
                </a>
                
                <div class="mt-auto p-4" style="position: absolute; bottom: 0; width: 100%;">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 rounded-pill border-0">
                            <i class="bi bi-power"></i> Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </nav>

        <main class="col-md-10 ms-sm-auto px-md-4 main-content">
            <header class="top-navbar d-flex justify-content-between align-items-center p-3 mb-4 rounded-4 shadow-sm">
                <h5 class="m-0 fw-bold text-secondary">Panneau de Contrôle Système</h5>
                <div class="user-profile d-flex align-items-center gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block">Connecté en tant que</small>
                        <span class="fw-bold text-blue-system">Super Admin</span>
                    </div>
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: 800;">
                        SA
                    </div>
                </div>
            </header>

            @include('components.notification-sound')

            <!-- Premium Floating Flash Messages -->
            <div class="fixed-top px-3 py-3 pointer-events-none" style="z-index: 9999; left: auto; right: 0; max-width: 400px;">
                @if(session('success'))
                    <div class="alert alert-success border-0 shadow-lg rounded-4 animate__animated animate__fadeInRight pointer-events-auto d-flex align-items-center gap-3">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                        <div>
                            <strong class="d-block">Succès</strong>
                            <small>{{ session('success') }}</small>
                        </div>
                        <script>window.onload = () => window.playNotificationSound();</script>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger border-0 shadow-lg rounded-4 animate__animated animate__fadeInRight pointer-events-auto d-flex align-items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div>
                            <strong class="d-block">Erreur</strong>
                            <small>{{ session('error') }}</small>
                        </div>
                        <script>window.onload = () => window.playNotificationSound();</script>
                    </div>
                @endif
            </div>

            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>