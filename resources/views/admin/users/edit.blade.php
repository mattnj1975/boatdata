@extends('layouts.admin')

@isset($user)
    @section('title', 'Edit User')
@else
    @section('title', 'Add User')
@endisset

@section('content')
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