<x-guest-layout>
    <!-- Auth Header -->
    <div class="auth-header">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"></path>
                </svg>
            </div>
            <div class="logo-rings">
                <div class="auth-ring auth-ring-1"></div>
                <div class="auth-ring auth-ring-2"></div>
                <div class="auth-ring auth-ring-3"></div>
            </div>
        </div>
        <h1>Reset Kata Sandi</h1>
        <p>Buat kata sandi baru untuk akun Anda</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div class="smart-field {{ $errors->has('email') ? 'has-error' : '' }}">
            <div class="field-background"></div>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $request->email) }}" 
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
                   autocomplete="new-password">
            <label for="password">Kata Sandi Baru</label>
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
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Reset Kata Sandi
            </span>
            <div class="button-glow"></div>
        </button>
    </form>
</x-guest-layout>
