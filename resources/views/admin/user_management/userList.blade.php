@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard - Analytics')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection

@section('page-script')
@vite('resources/assets/js/dashboards-analytics.js')
@endsection

@section('content')
<div class="card">
  <div class="card-header">
    <div class="row mb-3">
      <div class="col-md-6 text-left">
        {{-- Optional Back button if needed --}}
      </div>
      <div class="col-md-12 text-left">
        <a href="{{ route('auth-register-basic') }}" class="btn btn-sm btn-primary">
          <i class="fa fa-plus"></i> Add User
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <form action="{{ route('admin.userlist') }}" method="get">
          <div class="d-flex justify-content-end">
            <div class="form-group mr-2">
              <input type="search" class="form-control form-control-sm" name="keyword" id="keyword" value="{{ request()->input('keyword') }}" placeholder="Search by name/email...">
            </div>
            <div class="form-group">
              <div class="btn-group">
                <button type="submit" class="btn btn-sm btn-primary">
                  <i class="fa fa-filter"></i>
                </button>
                <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-toggle="tooltip" title="Clear filter">
                  <i class="fa fa-times"></i>
                </a>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card-body p-0">
    <table class="table table-sm table-hover mb-0">
      <thead class="thead-light">
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($admins as $index => $admin)
          <tr>
            <td>{{ $index + $admins->firstItem() }}</td>
            <td>{{ $admin->name }}</td>
            <td>{{ $admin->email }}</td>
            <td>
              {{-- You can add status display here, e.g., Active/Inactive --}}
            </td>
            <td>
              {{-- Action buttons (Edit/Delete/View) if any --}}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center">No users found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="card-footer d-flex justify-content-end">
    {{ $admins->appends(request()->query())->links() }}
  </div>
</div>

@endsection
