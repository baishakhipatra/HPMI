
@extends('layouts/contentNavbarLayout')

@section('title', 'Employee - List')

@section('content')

   @if(session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
<!-- Basic Bootstrap Table -->
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="fw-bold mb-0">Employee List</h4>
    @if (hasPermissionByChild('create_employee'))
      <a href="{{ route('admin.employee.create') }}" class="btn btn-primary btn-sm">+ Add Employee</a>
    @endif
  </div>

  <div class="card-header pt-0 pb-0">
    <form action="" method="get">
      <div class="row">
        <div class="col-md-6"></div>
          <div class="col-md-6">  
            <div class="d-flex justify-content-end align-items-center">
              <div class="form-group me-2 mb-0">
                <input type="search" class="form-control form-control-sm" name="keyword" id="keyword" value="{{ request()->input('keyword') }}" placeholder="Search something...">
              </div>
              <div class="form-group mb-0">
                <div class="btn-group">
                  <button type="submit" class="btn btn-sm btn-primary">
                    <i class=" tf-icons ri-filter-3-line"></i>
                  </button>
                  <a href="{{ url()->current() }}" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Clear filter">
                    <i class=" tf-icons ri-close-line"></i>
                  </a>
                  @if (hasPermissionByChild('export_employee_list'))
                    
                      <a href="{{ route('admin.employee.export', ['keyword' => request()->input('keyword')]) }}" 
                        class="btn btn-sm btn-success" 
                        data-toggle="tooltip" title="Export Data">
                        <i class="tf-icons ri-download-line"></i>
                      </a>
                    
                  @endif
                </div>
              </div>
            </div>
          </div>
      </div>
    </form>
  </div>

  <div class="card-body">
    <div class="table-responsive text-nowrap">
      <table class="table">
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Date of Joining</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @foreach($admins as $item)
            <tr>
              <td>{{ $item->user_id }}</td>
              <td>{{ ucfirst($item->name) }}</td>
              <td>{{ $item->email }}</td>
              <td>{{ $item->mobile }}</td>
              <td>{{ $item->date_of_joining }}</td>
              <td>
                 <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                    <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$item->id}}"
                      {{ $item->status ? 'checked' : ''}} onclick="statusToggle('{{route('admin.employee.status', $item->id)}}', this)">
                    <label class="form-check-label" for="customSwitch{{$item->id}}"></label>
                  </div>
              </td>
              {{-- Edit and delete --}}
              <td>
                <div class="btn-group" role="group" aria-label="Action Buttons">
                  @if (hasPermissionByChild('employee_details'))
                    
                      <a href="{{ route('admin.employee.show', $item->id) }}"  class="btn btn-sm btn-icon btn-success"         
                        data-bs-toggle="tooltip" title="View">                  
                        <i class="ri-eye-line"></i>
                      </a>
                    
                  @endif
                  
                  @if (hasPermissionByChild('edit_employee'))
                    
                      <a href="{{ route('admin.employee.edit', $item->id) }}" class="btn btn-sm btn-icon btn-dark"                     
                        data-bs-toggle="tooltip"  title="Edit">
                        <i class="ri-pencil-line"></i>
                      </a>
                    
                  @endif
                  
                  @if (hasPermissionByChild('delete_employee'))
                    
                      <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-danger" onclick="deleteUser({{ $item->id }})"     
                        data-bs-toggle="tooltip" title="Delete">
                        <i class="ri-delete-bin-6-line"></i>
                      </a>
                       
                  @endif           
                </div>
              </td>
 
            </tr>
          @endforeach         
        </tbody>
      </table>
      {{-- Pagination Links --}}
      <div class="pagination-container">
          {{$admins->links()}}
      </div>
    </div>
  </div>
  
</div>
@endsection
@section('scripts')
<script>
  function deleteUser(userId) {
    Swal.fire({
        icon: 'warning',
        title: "Are you sure you want to delete this?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Delete",
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.employee.delete')}}",
                type: 'POST',
                data: {
                    "id": userId,
                    "_token": '{{ csrf_token() }}',
                },
                success: function (data){
                    if (data.status != 200) {
                        toastFire('error', data.message);
                    } else {
                        toastFire('success', data.message);
                        location.reload();
                    }
                }
            });
        }
    });
  }
</script>
@endsection
