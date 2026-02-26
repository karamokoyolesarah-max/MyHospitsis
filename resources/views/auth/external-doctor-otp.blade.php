@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50 p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
                    <i class="bi bi-shield-lock-fill text-4xl"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-800">Vérification de sécurité</h2>
                <p class="text-slate-500 mt-2">
                    Un code de vérification a été envoyé à votre adresse email.
                </p>
                <div class="inline-block mt-3 px-4 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-bold border border-amber-100">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> Vérifiez vos spams
                </div>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 flex items-start gap-3">
                    <i class="bi bi-exclamation-octagon-fill text-xl mt-0.5"></i>
                    <div>
                        <span class="block font-bold">Erreur</span>
                        <span class="text-sm">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-2xl border border-green-100 flex items-start gap-3">
                    <i class="bi bi-check-circle-fill text-xl mt-0.5"></i>
                    <div>
                        <span class="block font-bold">Succès</span>
                        <span class="text-sm">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('external.register.otp.verify') }}" method="POST">
                @csrf
                <div class="mb-8">
                    <label class="block text-sm font-bold text-slate-700 mb-4 text-center">Entrez le code à 6 chiffres</label>
                    <div class="flex justify-between gap-2" id="otp-inputs">
                        @for($i = 1; $i <= 6; $i++)
                            <input type="text" 
                                name="otp_digit_{{ $i }}" 
                                maxlength="1" 
                                pattern="[0-9]" 
                                inputmode="numeric" 
                                required
                                class="w-12 h-14 text-center text-2xl font-bold border-2 border-slate-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 outline-none transition-all"
                                oninput="this.value=this.value.replace(/[^0-9]/g,''); if(this.value.length === 1) { focusNext(this) } else if(this.value.length === 0) { focusPrev(this) }"
                                onkeydown="if(event.key === 'Backspace' && this.value.length === 0) { focusPrev(this) }"
                            >
                        @endfor
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-xl hover:scale-[1.02] transition-all transform flex items-center justify-center gap-3">
                    <i class="bi bi-check-lg text-xl"></i>
                    Vérifier mon code
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <p class="text-slate-500 text-sm mb-4">Vous n'avez pas reçu le code ?</p>
                <form action="{{ route('external.register.otp.resend') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-blue-600 font-bold hover:text-blue-700 hover:underline transition-colors text-sm">
                        Renvoyer un nouveau code
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function focusNext(input) {
        const next = input.nextElementSibling;
        if (next) {
            next.focus();
        }
    }

    function focusPrev(input) {
        const prev = input.previousElementSibling;
        if (prev) {
            prev.focus();
        }
    }

    // Auto-focus first input on load
    document.addEventListener('DOMContentLoaded', () => {
        const firstInput = document.querySelector('input[name="otp_digit_1"]');
        if (firstInput) firstInput.focus();
    });
</script>
@endsection
