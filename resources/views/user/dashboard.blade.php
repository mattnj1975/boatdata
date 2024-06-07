@extends('layouts.admin')
@section('title')
{{ 'Dashboard' }}
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Dashboard</h4>



        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-lg-12">

    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-4">
        <div class="card bg-primary bg-soft">
            <div>
                <div class="row">
                    <div class="col-7">
                        <div class="text-primary p-3">
                            <h5 class="text-primary">Welcome Back
                                <strong>
                                    {{Auth::user()->name}}
                                </strong>

                            </h5>
                            <p>User Dashboard</p>


                        </div>
                    </div>
                    <div class="col-5 align-self-end">
                        <img src="assets/images/profile-img.png" alt="" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</div>
</div>

@endsection