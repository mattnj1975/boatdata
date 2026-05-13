@extends('layouts.admin')

@isset($user)
    @section('title', 'Edit User')
@else
    @section('title', 'Add User')
@endisset

@section('content')

<style>
    .bd-page-header { margin-bottom:20px; }
    .bd-title { color:#f8fafc; font-size:24px; font-weight:800; margin:0; }
    .bd-subtitle { color:#94a3b8; font-size:13px; margin-top:4px; }

    .bd-card {
        background:#111827;
        border:1px solid rgba(148,163,184,.18);
        border-radius:18px;
        box-shadow:0 16px 40px rgba(0,0,0,.22);
        padding:24px;
        color:#e5e7eb;
    }

    .bd-label {
        color:#cbd5e1;
        font-size:13px;
        font-weight:700;
        margin-bottom:7px;
    }

    .bd-input {
        background:#0f172a !important;
        border:1px solid rgba(148,163,184,.22) !important;
        color:#f8fafc !important;
        border-radius:12px !important;
        min-height:46px;
    }

    .bd-input:focus {
        border-color:#3b82f6 !important;
        box-shadow:0 0 0 .18rem rgba(59,130,246,.18) !important;
    }

    .bd-save-btn {
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        border:none;
        border-radius:12px;
        padding:12px 22px;
        color:#fff;
        font-weight:800;
    }

    .bd-save-btn:hover {
        color:#fff;
        transform:translateY(-1px);
        box-shadow:0 10px 22px rgba(37,99,235,.25);
    }

    .invalid-feedback {
        color:#f87171;
        font-size:12px;
    }

    .form-switch .form-check-input {
        cursor:pointer;
    }

    .bd-status-text {
        color:#94a3b8;
        font-size:13px;
    }
</style>

<div class="bd-page-header">
    @isset($user)
        <h1 class="bd-title">Edit User</h1>
        <div class="bd-subtitle">Update user details and account status.</div>
    @else
        <h1 class="bd-title">Add New User</h1>
        <div class="bd-subtitle">Create a new user account.</div>
    @endisset
</div>

<div class="bd-card">
    @isset($user)
        <form action="{{ route('users.update', $user->id) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
    @else
        <form action="{{ route('users.store') }}" method="post" enctype="multipart/form-data">
    @endisset

        @csrf

        <div class="row g-3">
            <div class="form-group col-sm-6">
                <label class="bd-label">Name <span class="text-danger">*</span></label>
                <input
                    type="text"
                    class="form-control bd-input"
                    name="name"
                    @isset($user)value="{{ $user->name }}"@endisset
                    placeholder="Enter name"
                >

                @error('name')
                    <span class="invalid-feedback d-block mt-1">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group col-sm-6">
                <label class="bd-label">Email <span class="text-danger">*</span></label>
                <input
                    type="email"
                    class="form-control bd-input"
                    name="email"
                    @isset($user)value="{{ $user->email }}"@endisset
                    placeholder="Enter email"
                >

                @error('email')
                    <span class="invalid-feedback d-block mt-1">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group col-sm-6">
                <label class="bd-label">
                    Password
                    @empty($user)
                        <span class="text-danger">*</span>
                    @endempty
                </label>

                <input
                    type="password"
                    class="form-control bd-input"
                    name="password"
                    placeholder="@isset($user)Leave blank to keep current password @else Enter password @endisset"
                >

                @error('password')
                    <span class="invalid-feedback d-block mt-1">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group col-sm-6 d-flex align-items-end">
                <div>
                    <label class="bd-label d-block">Status</label>

                    <div class="form-check form-switch form-switch-lg" dir="ltr">
                        <input
                            class="form-check-input"
                            name="status"
                            type="checkbox"
                            id="SwitchCheckSizelg"
                            @if(isset($user) && $user->active == 1) checked @endif
                        >
                        <label class="form-check-label bd-status-text" for="SwitchCheckSizelg">
                            Active user
                        </label>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn bd-save-btn">
                    <i class="fas fa-save me-2"></i>
                    @isset($user)
                        Save Changes
                    @else
                        Create User
                    @endisset
                </button>
            </div>
        </div>
    </form>
</div>

@endsection