<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Designation - List')

@section('content')

   @if(session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h4>Manage Permissions for: {{ $designation->name }}</h4>
        </div>
        <form method="POST" action="{{ route('admin.designation.permissions.update') }}">
            @csrf
            <input type="hidden" name="designation_id" value="{{ $designation->id }}">
            <div class="card-body">
                <div class="row">
                    @foreach($permissions->groupBy('parent_name') as $group => $groupPermissions)
                        <div class="col-md-6">
                            <h5 class="text-primary">{{ ucfirst(str_replace('_', ' ', $group)) }}</h5>
                            @foreach($groupPermissions as $permission)
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="permissions[]"
                                        value="{{ $permission->id }}" id="perm_{{ $permission->id}}"
                                        {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save Permissions</button>
                <a href="{{ route('admin.designation.list') }}" class="btn btn-sm btn-danger">
                    <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back</a>
                </div>
            </div>
        </form>
    </div>
@endsection