
<div id="sidebar-menu">
    <!-- Left Menu Start -->
    <ul class="metismenu list-unstyled" id="side-menu">
        <li class="menu-title" key="t-menu">Menu</li>

        <li>
            <a href="@if(Auth::user()->role_as == 1){{ route('dashboard') }}@elseif (Auth::user()->role_as == 2){{ route('master.dashboard') }}@else{{ route('user.dashboard') }}@endif" class="waves-effect">
                <i class="bx bx-home-circle"></i>
                <span >Dashboard</span>
            </a>
        </li>
        @if(Auth::user()->role_as == 1)
        <li>
            <a href="{{ route('masters.index') }}" class="waves-effect">
                <i class="fas fa-user-friends"></i>
                <span >Admins</span>
            </a>
        </li>
        <li>
            <a href="{{ route('users.index') }}" class="waves-effect">
                <i class="fas fa-users"></i>
                <span >Users</span>
            </a>
        </li>
        <li>
            <a href="{{ route('boats.index') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Boats</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.userboats') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Boat Users</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.myTrips') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Trips</span>
            </a>
        </li>
        @elseif (Auth::user()->role_as == 2)
        <li>
            <a href="{{ route('users.index') }}" class="waves-effect">
                <i class="fas fa-users"></i>
                <span >Users</span>
            </a>
        </li>
        <li>
            <a href="{{ route('master_boats') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Boats</span>
            </a>
        </li>
        <li>
            <a href="{{ route('master.userboats') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Boat Users</span>
            </a>
        </li>
        <li>
            <a href="{{ route('master.myTrips') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Trips</span>
            </a>
        </li>
        @else
        <li>
            <a href="{{ route('user.userboats') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Boats</span>
            </a>
        </li>
        <li>
            <a href="{{ route('user.myTrips') }}" class="waves-effect">
                <i class="fas fa-ship"></i>
                <span >Trips</span>
            </a>
        </li>
        @endif
        
        <li>
            <a href="@if(Auth::user()->role_as == 1){{ route('setting') }}@elseif (Auth::user()->role_as == 2){{ route('master.setting') }}@else{{ route('user.setting') }}@endif" class="waves-effect">
                <i class="bx bx-cog"></i>
                <span >Setting</span>
            </a>
        </li>
    </ul>
</div>