<x-guest-layout>
    <!-- Auth Header -->
    <div class="auth-header">
        <div class="auth-logo">
            <div class="logo-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
            </div>
            <div class="logo-rings">
                <div class="auth-ring auth-ring-1"></div>
                <div class="auth-ring auth-ring-2"></div>
                <div class="auth-ring auth-ring-3"></div>
            </div>
        </div>
        <h1>Verifikasi Email</h1>
        <p>Periksa email Anda untuk link verifikasi</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="session-status">
            Link verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div style="display: flex; flex-direction: column; gap: 16px;">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-button">
                <div class="button-bg"></div>
                <span class="button-text">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                    Kirim Ulang Email Verifikasi
                </span>
                <div class="button-glow"></div>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="auth-switch">
                <button type="submit" style="background: none; border: none; color: #10b981; cursor: pointer; font-size: 15px; font-weight: 600;">
                    Keluar
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
