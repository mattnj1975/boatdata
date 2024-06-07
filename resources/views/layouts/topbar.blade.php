
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="@if(Auth::user()->role_as == 1){{ route('dashboard') }}@elseif (Auth::user()->role_as == 2){{ route('master.dashboard') }}@else{{ route('user.dashboard') }}@endif" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{ URL('assets/images/logo-light.svg') }}" alt="" height="22">
                    </span>
                    <span style="color:white;font-size: 17px;font-weight:bold;" class="logo-lg">
                        Boat Data Portal
                        <!-- <img src="{{ URL('assets/images/logo-light.png') }}" alt="" height="19"> -->
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>  
        </div>
        
        <div class="d-flex">
            
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="{{ isset(Auth::user()->image) ? asset('profile/'.Auth::user()->image) : asset('/assets/images/users/avatar-9.png') }}" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">{{ ucfirst(Auth::user()->name) }}</span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="@if(Auth::user()->role_as == 1){{ route('setting') }}@elseif (Auth::user()->role_as == 2){{ route('master.setting') }}@else{{ route('user.setting') }}@endif"><i  class="bx bx-cog font-size-16 align-middle me-1"></i> <span key="t-my-wallet">Settings</span></a>
                    <a class="dropdown-item text-danger" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i  class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>

        </div>
    </div>
</header>