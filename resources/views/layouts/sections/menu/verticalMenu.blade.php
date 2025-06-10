<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo">
    <a href="{{url('/')}}" class="app-brand-link">
      {{-- <span class="app-brand-logo demo me-1">
 @include('_partials.macros',["height"=>20])
 </span> --}}
      <img src="{{asset('assets/img/new-logo.png')}}" alt="" style="width: 80px; height: auto;">
      <span class="app-brand-text demo menu-text fw-semibold ms-2">HPMI</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="menu-toggle-icon d-xl-block align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1 ps">

    <li class="menu-item {{ (request()->is('admin/dashboard*')) ? 'open' : '' }}">
      <a href="{{route('admin.dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons ri-home-smile-line"></i>
        <div>Dashboards</div>
      </a>
    </li>
    
    {{-- @auth('admin')
      @if(auth('admin')->user()->user_type == 'Admin') --}}
        <li class="menu-item {{ (request()->is('admin/employees*')) ? 'open' : '' }}" style="">
          <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
            <i class="menu-icon tf-icons ri-store-line"></i>
            <div>Employee Management</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ (request()->is('admin/employees')) ? 'open' : '' }}">
              <a href="{{route('admin.employee.index')}}" class="menu-link">
                <div>Employee List</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="menu-item {{ (request()->is('admin/bom-parts*')) ? 'open' : '' }}" style="">
          <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
            <i class="menu-icon tf-icons ri-store-line"></i>
            <div>Teacher Management</div>
          </a>
          <ul class="menu-sub">
            <li class="menu-item {{ (request()->is('admin/teacher-management/teacher-list*')) ? 'open' : '' }}">
              <a href="{{route('admin.teacherlist')}}" class="menu-link">
                <div>Teacher List</div>
              </a>
            </li>
          </ul>
        </li>
      {{-- @endif
    @endauth --}}
    <li class="menu-item">
      <a class="btn btn-danger d-flex" href="{{ route('admin.logout') }}"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <small class="align-middle">Logout</small>
            <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
        </a>
    </li>
     <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
          @csrf
      </form>
    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
      <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; right: 4px;">
      <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
    </div>
  </ul>
</aside>