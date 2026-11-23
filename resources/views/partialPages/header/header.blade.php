<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-black" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">


        <li class="nav-item dropdown mt-4">
            <a class="nav-link" href="#" id="notificationDropdown" data-toggle="dropdown">
                <i class="fa fa-bell"></i>
                <span class="badge badge-danger" id="unreadCount" style="display: none;">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" id="notificationDropdownMenu">
                <div class="notification-header">
                    <span class="dropdown-item dropdown-header">Unread Messages</span>
                    <button id="markAllAsRead" class="btn btn-link">Mark All as Read</button>
                </div>
                <div id="notificationList">
                    <div class="text-center p-2" id="noUnreadMessages" style="display: none;">No unread messages</div>
                </div>
            </div>
        </li>


        <!-- User Profile Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-toggle="dropdown">
                <img src="{{ asset('/img/default-avatar.svg') }}" alt="Avatar" class="rounded-circle"
                     style="width: 50px; height: 50px;" onerror="this.onerror=null;this.src='{{ asset('/img/default-avatar.svg') }}';">
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <span class="dropdown-item">{{ auth()->user()->email }}</span>
                <div class="dropdown-divider"></div>
               @if(auth()->id()=='1')
                <a class="dropdown-item" href="{{ url('/reset-pwd') }}">Password Reset</a>
                @endif
                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="dropdown-item text-danger">Logout</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
