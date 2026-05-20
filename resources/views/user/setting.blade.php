@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
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