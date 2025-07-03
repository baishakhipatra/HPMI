<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Permission - List')

@section('content')

   @if(session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Manage Permissions for: {{ $designation->name }}</h4>
            <a href="{{ route('admin.designation.list') }}" class="btn btn-sm btn-danger">
                <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back</a>
        </div>
        <form method="POST" action="{{ route('admin.designation.permissions.update') }}">
            @csrf
            <input type="hidden" name="designation_id" value="{{ $designation->id }}">
    
            @foreach($permissions->groupBy('parent_name') as $group => $groupPermissions)
                <div class="card-header" style="background: #f4f0ff;">
                    <h5 class="text-primary mb-0">{{ ucwords(str_replace('_', ' ', $group)) }}</h5>
                </div>

                <div class="card-body mt-2">
                    <div class="row">
                        @php $chunked = $groupPermissions->chunk(8); @endphp

                        @foreach($chunked as $chunk)
                            <div class="col-md-6">
                                @foreach($chunk as $permission)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                            {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}
                                            onchange="updatePermissionAjax(this, {{ $designation->id }})">

                                        <label class="form-check-label" for="perm_{{ $permission->id }}">
                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <div class="card-footer text-end">
                
            </div>
        </form>
    </div>
@endsection

<script>
    function updatePermissionAjax(checkbox, designationId) {
        const permissionId = checkbox.value;
        const isChecked = checkbox.checked;

        fetch("{{ route('admin.designation.permissions.ajax')}}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                designation_id: designationId,
                permission_id: permissionId,
                checked: isChecked
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastFire('success','Permission updated successfully');
            } else {
                toastFire(data.message || 'error','Failed to update permission');
                checkbox.checked = !isChecked; // revert checkbox
            }
        })
        .catch(() => {
            toastFire('error','Error occurred while updating permission');
            checkbox.checked = !isChecked; // revert checkbox
        });
    }

</script>