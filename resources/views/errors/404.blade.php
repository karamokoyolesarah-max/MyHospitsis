<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page non trouvée | HospitSIS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0a0e27;
            overflow: hidden;
            position: relative;
        }

        /* Animated gradient background */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(99, 102, 241, 0.12) 0%, transparent 50%),
                        radial-gradient(ellipse at 60% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%);
            animation: bgShift 15s ease-in-out infinite alternate;
        }

        @keyframes bgShift {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-5%, 3%) rotate(3deg); }
        }

        /* Floating particles */
        .particles {
            position: absolute;
            inset: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(99, 102, 241, 0.4);
            border-radius: 50%;
            animation: float linear infinite;
        }

        .particle:nth-child(1) { left: 10%; animation-duration: 12s; animation-delay: 0s; width: 3px; height: 3px; }
        .particle:nth-child(2) { left: 25%; animation-duration: 15s; animation-delay: 2s; width: 5px; height: 5px; background: rgba(59, 130, 246, 0.3); }
        .particle:nth-child(3) { left: 40%; animation-duration: 10s; animation-delay: 4s; }
        .particle:nth-child(4) { left: 55%; animation-duration: 18s; animation-delay: 1s; width: 6px; height: 6px; background: rgba(16, 185, 129, 0.3); }
        .particle:nth-child(5) { left: 70%; animation-duration: 13s; animation-delay: 3s; width: 3px; height: 3px; }
        .particle:nth-child(6) { left: 85%; animation-duration: 16s; animation-delay: 5s; width: 5px; height: 5px; background: rgba(59, 130, 246, 0.25); }
        .particle:nth-child(7) { left: 15%; animation-duration: 20s; animation-delay: 7s; width: 4px; height: 4px; }
        .particle:nth-child(8) { left: 60%; animation-duration: 11s; animation-delay: 6s; width: 3px; height: 3px; background: rgba(16, 185, 129, 0.35); }

        @keyframes float {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100vh) scale(1); opacity: 0; }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }

        /* Heartbeat / pulse icon */
        .pulse-icon {
            margin: 0 auto 2rem;
            width: 100px;
            height: 100px;
            position: relative;
        }

        .pulse-ring {
            position: absolute;
            inset: 0;
            border: 2px solid rgba(99, 102, 241, 0.3);
            border-radius: 50%;
            animation: pulseRing 2s ease-out infinite;
        }

        .pulse-ring:nth-child(2) { animation-delay: 0.6s; }
        .pulse-ring:nth-child(3) { animation-delay: 1.2s; }

        @keyframes pulseRing {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.8); opacity: 0; }
        }

        .pulse-core {
            position: absolute;
            inset: 15px;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 40px rgba(99, 102, 241, 0.4);
        }

        .pulse-core svg {
            width: 36px;
            height: 36px;
            color: #fff;
        }

        /* ECG Line animation */
        .ecg-line {
            margin: 0 auto 2.5rem;
            width: 280px;
            height: 50px;
            overflow: hidden;
        }

        .ecg-line svg {
            width: 100%;
            height: 100%;
        }

        .ecg-path {
            stroke: #6366f1;
            stroke-width: 2;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 600;
            stroke-dashoffset: 600;
            animation: drawEcg 3s ease-in-out infinite;
        }

        @keyframes drawEcg {
            0% { stroke-dashoffset: 600; opacity: 0.3; }
            50% { stroke-dashoffset: 0; opacity: 1; }
            100% { stroke-dashoffset: -600; opacity: 0.3; }
        }

        /* Error code */
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #6366f1 0%, #3b82f6 40%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -4px;
            margin-bottom: 0.5rem;
            animation: glowText 3s ease-in-out infinite alternate;
        }

        @keyframes glowText {
            0% { filter: drop-shadow(0 0 10px rgba(99, 102, 241, 0.3)); }
            100% { filter: drop-shadow(0 0 25px rgba(99, 102, 241, 0.6)); }
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #e2e8f0;
            margin-bottom: 0.75rem;
        }

        .error-desc {
            font-size: 1rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 2.5rem;
            max-width: 440px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            background: linear-gradient(135deg, #6366f1, #3b82f6);
            color: #fff;
            font-size: 0.95rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.5);
        }

        .btn-primary:hover::before { opacity: 1; }

        .btn-primary svg { width: 18px; height: 18px; }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            transition: color 0.3s;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .btn-back:hover {
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.05);
        }

        /* Footer branding */
        .branding {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .branding-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #6366f1;
            animation: blink 2s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .error-code { font-size: 5rem; letter-spacing: -2px; }
            .error-title { font-size: 1.2rem; }
            .error-desc { font-size: 0.9rem; }
            .ecg-line { width: 200px; }
        }
    </style>
</head>
<body>

    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="container">
        <!-- Pulse Icon -->
        <div class="pulse-icon">
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <div class="pulse-ring"></div>
            <div class="pulse-core">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                </svg>
            </div>
        </div>

        <!-- ECG Line -->
        <div class="ecg-line">
            <svg viewBox="0 0 280 50" preserveAspectRatio="none">
                <path class="ecg-path" d="M0,25 L40,25 L50,25 L60,10 L70,40 L80,5 L90,45 L100,25 L110,25 L140,25 L150,25 L160,10 L170,40 L180,5 L190,45 L200,25 L210,25 L280,25"/>
            </svg>
        </div>

        <!-- Error Content -->
        <div class="error-code">404</div>
        <h1 class="error-title">Page non trouvée</h1>
        <p class="error-desc">
            La page que vous recherchez semble introuvable. Elle a peut-être été déplacée ou n'existe plus.
        </p>

        <!-- Actions -->
        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Retour à l'accueil
            </a>
            <button onclick="history.back()" class="btn-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6"/>
                </svg>
                Page précédente
            </button>
        </div>
    </div>

    <div class="branding">
        <span class="branding-dot"></span>
        HospitSIS
    </div>

</body>
</html>
