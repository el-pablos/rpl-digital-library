<x-guest-layout>
    <!-- Auth Header -->
    <div class="auth-header">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    <circle cx="12" cy="16" r="1"></circle>
                </svg>
            </div>
            <div class="logo-rings">
                <div class="auth-ring auth-ring-1"></div>
                <div class="auth-ring auth-ring-2"></div>
                <div class="auth-ring auth-ring-3"></div>
            </div>
        </div>
        <h1>Lupa Kata Sandi?</h1>
        <p>Kami akan mengirimkan link reset ke email Anda</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="session-status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="smart-field {{ $errors->has('email') ? 'has-error' : '' }}">
            <div class="field-background"></div>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   placeholder="email@example.com"
                   required 
                   autofocus>
            <label for="email">Alamat Email</label>
            <div class="field-indicator">
                <div class="indicator-pulse"></div>
            </div>
            <div class="field-completion"></div>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button">
            <div class="button-bg"></div>
            <span class="button-text">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                Kirim Link Reset
            </span>
            <div class="button-glow"></div>
        </button>
    </form>

    <!-- Back to Login Link -->
    <div class="auth-separator">
        <div class="separator-line"></div>
        <span class="separator-text">atau</span>
        <div class="separator-line"></div>
    </div>

    <div class="auth-switch">
        <a href="{{ route('login') }}">Kembali ke halaman login</a>
    </div>
</x-guest-layout>
