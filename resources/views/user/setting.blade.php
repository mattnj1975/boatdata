@extends('layouts.admin')
@section('title', 'Settings')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Settings</h4>
            {{-- {{ $errors }} --}}
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                    {{-- <li class="breadcrumb-item active">Orders</li> --}}
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="w-100">

    <div class="card p-4 rounded cShadow">

        <form action="{{ route('user.settings.edit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <div class="row">
                <div class="form-group col-sm-6 mb-2">
                    <label for=""> Name:</label>
                    <input required type="text" name="name" value="{{ !is_null($user->name) ? $user->name : '' }}" class="form-control">
                    @if ($errors->has('name'))
                    <span class="text-danger ml-2">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="col-sm-6 mb-2">

                </div>
                <div class="form-group col-sm-6 mb-2 col-md-3">
                    <label for="">New Password:</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="current-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group col-sm-6 mb-2 col-md-3">
                    <label for="">Confirm Password:</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password_confirmation" autocomplete="current-password">
                    @if ($errors->has('password'))
                    <span class="text-danger ml-2">{{ $errors->first('confirm_password') }}</span>
                    @endif
                </div>
                <div class="col-sm-6 mb-2">

                </div>
                <div class="form-group col-sm-6 mb-2">
                    <label for=""> Profile Image:</label>
                    <input type="file" name="image" value="{{ !is_null($user->image) ? $user->image : '' }}" class="form-control">
                    @if ($errors->has('image'))
                    <span class="text-danger ml-2">{{ $errors->first('image') }}</span>
                    @endif
                </div>
                <div class="col-sm-6 mb-2">

                </div>
                <div class="form-group col-sm-6">
                    <img class="rounded-circle header-profile-user" src="{{ isset(Auth::user()->image) ? asset('profile/'.Auth::user()->image) : asset('/assets/images/users/avatar-9.png') }}" style="width:100px;height:100px;" alt="Header Avatar">
                </div>

            </div>
            <div class="form-group col-sm-12 mb-2">
                <input type="submit" value="Save Settings" class="btn btn-primary btn-sm">
            </div>
        </form>
    </div>
</div>
</div>
@endsection