@extends('layouts.admin')

@section('title', 'Settings')

@section('content')

<style>
    .bd-page-header {
        margin-bottom: 20px;
    }

    .bd-title {
        color: #f8fafc;
        font-size: 24px;
        font-weight: 800;
        margin: 0;
    }

    .bd-subtitle {
        color: #94a3b8;
        font-size: 13px;
        margin-top: 4px;
    }

    .bd-card {
        background: #111827;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 18px;
        box-shadow: 0 16px 40px rgba(0,0,0,.22);
        overflow: hidden;
    }

    .bd-card-body {
        padding: 24px;
    }

    .bd-label {
        color: #cbd5e1;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 7px;
    }

    .bd-input {
        background: #0f172a !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        color: #f8fafc !important;
        border-radius: 12px;
        padding: 11px 14px;
        min-height: 46px;
    }

    .bd-input:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.18rem rgba(59,130,246,.18) !important;
    }

    .bd-avatar-wrap {
        display: flex;
        align-items: center;
        gap: 18px;
        margin-top: 10px;
        flex-wrap: wrap;
    }

    .bd-avatar {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid rgba(59,130,246,.28);
        box-shadow: 0 10px 25px rgba(0,0,0,.3);
    }

    .bd-avatar-info {
        color: #94a3b8;
        font-size: 13px;
    }

    .bd-save-btn {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        border: none;
        border-radius: 12px;
        padding: 12px 22px;
        font-weight: 700;
        color: #fff;
        transition: .15s ease;
    }

    .bd-save-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(37,99,235,.25);
    }

    .text-danger,
    .invalid-feedback {
        font-size: 12px;
    }
</style>

<div class="bd-page-header">
    <h1 class="bd-title">Account Settings</h1>
    <div class="bd-subtitle">
        Manage your profile details, password and avatar image.
    </div>
</div>

<div class="bd-card">
    <div class="bd-card-body">

        <form action="{{ route('user.settings.edit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="row g-3">

                <div class="col-lg-6">
                    <label class="bd-label">Name</label>

                    <input
                        required
                        type="text"
                        name="name"
                        value="{{ !is_null($user->name) ? $user->name : '' }}"
                        class="form-control bd-input"
                    >

                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>

                <div class="col-lg-6"></div>

                <div class="col-lg-3">
                    <label class="bd-label">New Password</label>

                    <input
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        class="form-control bd-input @error('password') is-invalid @enderror"
                    >

                    @error('password')
                        <span class="invalid-feedback d-block">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="col-lg-3">
                    <label class="bd-label">Confirm Password</label>

                    <input
                        type="password"
                        name="password_confirmation"
                        autocomplete="current-password"
                        class="form-control bd-input @error('password') is-invalid @enderror"
                    >

                    @if ($errors->has('password'))
                        <span class="text-danger">
                            {{ $errors->first('confirm_password') }}
                        </span>
                    @endif
                </div>

                <div class="col-lg-6"></div>

                <div class="col-lg-6">
                    <label class="bd-label">Profile Image</label>

                    <input
                        type="file"
                        name="image"
                        class="form-control bd-input"
                    >

                    @if ($errors->has('image'))
                        <span class="text-danger">
                            {{ $errors->first('image') }}
                        </span>
                    @endif
                </div>

                <div class="col-12">
                    <div class="bd-avatar-wrap">

                        <img
                            class="bd-avatar"
                            src="{{ isset(Auth::user()->image) ? asset('profile/'.Auth::user()->image) : asset('/assets/images/users/avatar-9.png') }}"
                            alt="Profile Image"
                        >

                        <div class="bd-avatar-info">
                            Current profile image<br>
                            Recommended square image for best appearance.
                        </div>

                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn bd-save-btn">
                        <i class="fas fa-save me-2"></i>
                        Save Settings
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

@endsection