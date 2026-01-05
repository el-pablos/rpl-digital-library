<x-guest-layout>
    <!-- Auth Header -->
    <div class="auth-header">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" y1="8" x2="19" y2="14"></line>
                    <line x1="22" y1="11" x2="16" y2="11"></line>
                </svg>
            </div>
            <div class="logo-rings">
                <div class="auth-ring auth-ring-1"></div>
                <div class="auth-ring auth-ring-2"></div>
                <div class="auth-ring auth-ring-3"></div>
            </div>
        </div>
        <h1>Bergabunglah</h1>
        <p>Daftar di Perpustakaan Digital</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="smart-field {{ $errors->has('name') ? 'has-error' : '' }}">
            <div class="field-background"></div>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}" 
                   placeholder="Nama Lengkap"
                   required 
                   autofocus 
                   autocomplete="name">
            <label for="name">Nama Lengkap</label>
            <div class="field-indicator">
                <div class="indicator-pulse"></div>
            </div>
            <div class="field-completion"></div>
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="smart-field {{ $errors->has('email') ? 'has-error' : '' }}">
            <div class="field-background"></div>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   placeholder="email@example.com"
                   required 
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
                   autocomplete="new-password">
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

        <!-- Confirm Password -->
        <div class="smart-field">
            <div class="field-background"></div>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   placeholder="konfirmasi password"
                   required 
                   autocomplete="new-password">
            <label for="password_confirmation">Konfirmasi Kata Sandi</label>
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
        </div>

        <!-- Submit Button -->
        <button type="submit" class="auth-button">
            <div class="button-bg"></div>
            <span class="button-text">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" y1="8" x2="19" y2="14"></line>
                    <line x1="22" y1="11" x2="16" y2="11"></line>
                </svg>
                Daftar
            </span>
            <div class="button-glow"></div>
        </button>
    </form>

    <!-- Login Link -->
    <div class="auth-separator">
        <div class="separator-line"></div>
        <span class="separator-text">sudah punya akun?</span>
        <div class="separator-line"></div>
    </div>

    <div class="auth-switch">
        <a href="{{ route('login') }}">Masuk sekarang</a>
    </div>
</x-guest-layout>
