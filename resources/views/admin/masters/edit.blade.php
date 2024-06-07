@extends('layouts.admin')
@isset($master)
@section('title', 'Edit Admin')
@else
@section('title', 'Add Admin ')
@endisset
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            @isset($master)
            <h4 class="mb-sm-0 font-size-18">Edit Admin User</h4>
            @else
            <h4 class="mb-sm-0 font-size-18">Add New Admin User</h4>
            @endisset
            {{-- {{ $errors }}--}}
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admin User</li>
                    @isset($master)
                    <li class="breadcrumb-item active">Edit Admin User</li>
                    @else
                    <li class="breadcrumb-item active">Add New Admin User</li>
                    @endisset
                </ol>
            </div>

        </div>
    </div>
</div>

<div class="card p-4 rounded cShadow container-fluid">
    @isset($master)
    <form action="{{ route('masters.update', $master->id) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @else
        <form action="{{ route('masters.store') }}" method="post" enctype="multipart/form-data">
            @endisset
            @csrf
            <div class="row">

                <div class="form-group col-sm-6 mb-2">
                    <label for="">Name<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" @isset($master)value="{{$master->name}}" @endisset placeholder="Enter Name">
                    </div>
                    @error('name')
                    <span class="invalid-feedback mt-0" @error('name')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Email<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="email" class="form-control" name="email" @isset($master)value="{{$master->email}}" @endisset placeholder="Enter Email">
                    </div>
                    @error('email')
                    <span class="invalid-feedback mt-0" @error('email')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2">
                    <label for="">Password<span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password" placeholder="Enter Password">
                    </div>
                    @error('password')
                    <span class="invalid-feedback mt-0" @error('password')style="display: block" @enderror role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group col-sm-6 mb-2 d-flex align-items-end">

                    <label for="switch4" data-on-label="Yes" data-off-label="No">
                        <label for="">Status: </label>
                        <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">

                            <input class="form-check-input" name="status" type="checkbox" id="SwitchCheckSizelg" @if(isset($master) && $master->active == 1) checked="" @endif>
                        </div>
                    </label>
                </div>
                <div class="form-group col-sm-12 mb-2">
                    <input type="submit" value="Save" class="btn btn-outline-info btn-sm ">
                </div>

            </div>
        </form>
</div>
@endsection