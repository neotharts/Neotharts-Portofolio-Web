@extends('admin.layout')

@section('pageTitle', 'Profil Admin')

@section('content')
    <div class="form-card glass-card">
        <div class="form-header">
            <div>
                <p class="eyebrow">Pengaturan Akun</p>
                <h2>Profil Admin</h2>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <span class="material-icons-outlined">check_circle</span>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Informasi Akun</h3>

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">Username Login</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $user->username ?? '') }}" placeholder="Masukkan username untuk login" required>
                    <small class="muted-text">Username ini digunakan untuk login ke dashboard admin.</small>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="{{ $user->email }}" disabled class="disabled-input">
                    <small class="muted-text">Email tidak dapat diubah.</small>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">
                    <span class="material-icons-outlined">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <hr class="form-divider">

        <form action="{{ route('admin.profile.password') }}" method="POST" class="password-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Ubah Password</h3>

                <div class="form-group">
                    <label for="current_password">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" placeholder="Masukkan password saat ini" required>
                    @error('current_password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input type="password" name="password" id="password" placeholder="Minimal 6 karakter" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ulangi password baru" required>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">
                    <span class="material-icons-outlined">lock</span>
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .form-divider {
        border: none;
        border-top: 1px solid var(--border-color, #e0e0e0);
        margin: 32px 0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    @media (max-width: 640px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .disabled-input {
        background: var(--bg-tertiary, #f0f0f0);
        color: var(--text-muted, #666);
        cursor: not-allowed;
    }

    .profile-form,
    .password-form {
        margin-bottom: 0;
    }

    .form-actions {
        display: flex;
        justify-content: flex-start;
        margin-top: 24px;
    }

    .button .material-icons-outlined {
        font-size: 18px;
        vertical-align: middle;
        margin-right: 4px;
    }

    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .alert.alert-success {
        background: rgba(40, 167, 69, 0.1);
        color: #28a745;
        border: 1px solid rgba(40, 167, 69, 0.2);
    }

    .alert .material-icons-outlined {
        font-size: 20px;
    }
</style>
@endpush