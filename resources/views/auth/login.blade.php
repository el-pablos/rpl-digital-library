<x-guest-layout>
    <!-- Auth Header -->
    <div class="auth-header">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    <path d="M8 7h8M8 11h8M8 15h5"></path>
                </svg>
            </div>
            <div class="logo-rings">
                <div class="auth-ring auth-ring-1"></div>
                <div class="auth-ring auth-ring-2"></div>
                <div class="auth-ring auth-ring-3"></div>
            </div>
        </div>
        <h1>Selamat Datang</h1>
        <p>Masuk ke Perpustakaan Digital</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="session-status">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
                   autofocus 
                   autocomplete="username">
            <label for="email">Alamat Email</label>
            <div class="field-indicator">
                <div class="indicator-pulse"></div>
            </div>
            <div class="field-completion"></div>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Password -->
        <div class="smart-field {{ $errors->has('password') ? 'has-error' : '' }}">
            <div class="field-background"></div>
            <input type="password" 
                   id="password" 
                   name="password" 
                   placeholder="password"
                   required 
                   autocomplete="current-password">
            <label for="password">Kata Sandi</label>
            <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                <svg class="toggle-show" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
                <svg class="toggle-hide" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            </button>
            <div class="field-indicator">
                <div class="indicator-pulse"></div>
            </div>
            <div class="field-completion"></div>
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="form-options">
            <label class="smart-checkbox">
                <input type="checkbox" id="remember" name="remember">
                <span class="checkbox-visual">
                    <div class="checkbox-box"></div>
                    <svg width="12" height="10" viewBox="0 0 12 10" fill="none">
                        <path d="M1 5l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Lupa kata sandi?</a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button">
            <div class="button-bg"></div>
            <span class="button-text">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Masuk
            </span>
            <div class="button-glow"></div>
        </button>
    </form>

    <!-- Register Link -->
    <div class="auth-separator">
        <div class="separator-line"></div>
        <span class="separator-text">belum punya akun?</span>
        <div class="separator-line"></div>
    </div>

    <div class="auth-switch">
        <a href="{{ route('register') }}">Daftar sekarang</a>
    </div>
</x-guest-layout>
