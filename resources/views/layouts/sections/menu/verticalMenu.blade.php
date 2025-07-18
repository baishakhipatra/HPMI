<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <!-- ! Hide app brand if navbar-full -->
  <div class="app-brand demo">
    {{-- <a href="{{url('/')}}" class="app-brand-link"> --}}
      {{--<span class="app-brand-logo demo me-1">
            @include('_partials.macros',["height"=>20])
            </span> --}}
      <img src="{{asset('assets/img/logo-color.png')}}" alt="" style="width: 15px; height: auto;">
      <span class="app-brand-text demo menu-text fw-semibold ms-2">Ayachak Ashram</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="menu-toggle-icon d-xl-block align-middle"></i>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

  <div class="menu-part">
    <ul class="menu-inner py-1 ps">

      <li class="menu-item {{ (request()->is('admin/dashboard*')) ? 'open' : '' }}">
        <a href="{{route('admin.dashboard')}}" class="menu-link">
          <i class="menu-icon fa-solid fa-house"></i>
          <div>Dashboards</div>
        </a>
      </li>
      
      {{-- Employee Management --}}

      <li class="menu-item {{ (request()->is('admin/employees*')) ? 'open' : '' }}" style="">
        <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
          <i class="menu-icon tf-icons ri-briefcase-line"></i>
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


      {{-- Teacher Management --}}
     
      <li class="menu-item {{ (request()->is('admin/teachers*')) ? 'open' : '' }}" style="">
        <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
          <i class="menu-icon fa-solid fa-person-chalkboard"></i>
          <div>Teacher Management</div>
        </a>
        <ul class="menu-sub">
          
          <li class="menu-item {{ (request()->is('admin/teachers*')) ? 'open' : '' }}">
            <a href="{{route('admin.teacher.index')}}" class="menu-link">
              <div>Teacher List</div>
            </a>
          </li>
          
        </ul>
      </li>
      


      {{-- Student Management --}}
      
        <li class="menu-item {{ (request()->is('admin/student-management*')) ? 'open' : '' }}" style="">
          <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
            <i class="menu-icon fa-solid fa-graduation-cap"></i>
            <div>Student Management</div>
          </a>
          <ul class="menu-sub">
              <li class="menu-item {{ (request()->is('admin/student-management/student-list*')) ? 'open' : '' }}">
                <a href="{{route('admin.studentlist')}}" class="menu-link">
                  <div>Student List</div>
                </a>
              </li>

              <li class="menu-item {{ (request()->is('admin/student-management/student-readmission*')) ? 'open' : '' }}">
                <a href="{{route('admin.student.readmission.index')}}" class="menu-link">
                  <div>Re-admission</div>
                </a>
              </li>

              @if($exist_student)
                <li class="menu-item {{ (request()->is('admin/student-management/studentmark-list*')) ? 'open' : '' }}">
                  <a href="{{route('admin.studentmarklist')}}" class="menu-link">
                    <div>Student Marks</div>
                  </a>
                </li>
              @endif

            {{-- @if (hasPermissionByChild('student_progress_marking_list')) --}}
            {{-- <li class="menu-item {{ request()->is('admin/student-progress-marking*') ? 'active' : '' }}">
                <a href="{{ route('admin.student.progressmarkinglist', [$defaultStudentId, $defaultSession]) }}" class="menu-link">
                    Progress Marking
                </a>
            </li> --}}
            <li class="menu-item {{ request()->is('admin/student-progress-marking') ? 'active' : '' }}">
              <a href="{{ route('admin.student.progressmarking.select') }}" class="menu-link">
                  <div>Progress Marking</div>
              </a>
            </li>
            {{-- @endif --}}
          </ul>
        </li>
     

      {{-- Master Management --}}
        <li class="menu-item {{ (request()->is('admin/master-module*')) ? 'open' : '' }}" style="">
          <a href="#" class="menu-link menu-toggle waves-effect" target="_blank">
            <i class="menu-icon fa-solid fa-address-book"></i>
            <div>Master Module</div>
          </a>
          <ul class="menu-sub">
           
            <li class="menu-item {{ (request()->is('admin/master-module/subject-list')) ? 'open' : '' }}">
              <a href="{{route('admin.subjectlist.index')}}" class="menu-link">
                <div>Subject List</div>
              </a>
            </li>
           

            <li class="menu-item {{ (request()->is('admin/master-module/class-list*')) ? 'open' : '' }}">
              <a href="{{route('admin.classlist')}}" class="menu-link">
                <div>Class List</div>
              </a>
            </li>

            <li class="menu-item {{ (request()->is('admin/master-module/student-progress-marking*')) ? 'open' : '' }}">
              <a href="{{route('admin.student.progresslist')}}" class="menu-link">
                <div>Progress Marking Category</div>
              </a>
            </li>

            <li class="menu-item {{ (request()->is('admin/master-module/progress-chart*')) ? 'open' : '' }}">
              <a href="{{route('admin.progresschart')}}" class="menu-link">
                <div>Progress Chart</div>
              </a>
            </li>

            {{-- Designation --}}
            {{-- <li class="menu-item {{ (request()->is('admin/master-module/designations*')) ? 'open' : '' }}">
              <a href="{{route('admin.designation.list')}}" class="menu-link">
                <div>Designations</div>
              </a>
            </li> --}}
          </ul>
        </li>

      {{-- Report Management --}}
        <li class="menu-item {{ (request()->is('admin/report*')) ? 'open' : '' }}">
          <a href="{{route('admin.report.index')}}" class="menu-link">
            <i class="menu-icon fa-solid fa-chart-pie"></i>
            <div>Report</div>
          </a>
        </li>
      
      <li class="menu-item">
        <a class="btn btn-danger d-flex rounded-0" href="{{ route('admin.logout') }}"
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
  </div>

</aside>