<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'User - List')

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
            <h3 class="mb-0">Teacher List</h3>
            <a href="{{ route('admin.teacherlist.create') }}" class="btn btn-primary btn-sm">+ Create</a>
        </div>

        <div class="px-3 py-2">
            <form action="" method="get">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">  
                    <div class="d-flex justify-content-end">
                    <div class="form-group me-2 mb-0">
                        <input type="search" class="form-control form-control-sm" name="keyword" id="keyword" value="{{ request()->input('keyword') }}" placeholder="Search something...">
                    </div>
                    <div class="form-group mb-0">
                        <div class="btn-group">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="menu-icon tf-icons ri-filter-3-line"></i>
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-toggle="tooltip" title="Clear filter">
                            <i class="menu-icon tf-icons ri-close-line"></i>
                        </a>
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
                    <th>Teacher ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date of Birth</th>
                    <th>Date of Joining</th>
                    <th>Qualifications</th>
                    <th>Subjects Taught</th>
                    <th>Classes Assigned</th>
                    <th>Role Assignment</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($teachers as $item)
                        <tr>
                            <td>{{ $item->teacher_id }}</td>
                            <td>{{ ucfirst($item->name) }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ $item->phone }}</td>
                            <td>{{ $item->date_of_birth }}</td>
                            <td>{{ $item->date_of_joining }}</td>
                            <td>{{ $item->qualifications }}</td>
                            <td>{{ $item->subjects_taught }}</td>
                            <td>{{ $item->classes_assigned }}</td>
                            <td>{{ $item->role }}</td>
                            {{-- Edit and delete --}}
                            <td>
                                <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="ri-more-2-line"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.teacherlist.edit', $item->id) }}" title="Edit" data-bs-toggle="tooltip">
                                        <i class="ri-pencil-line me-1"></i> Edit
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="tooltip" title="Delete" onclick="deleteTeacher({{$item->id}})">
                                        <i class="ri-delete-bin-6-line me-1"></i> Delete
                                    </a>
                                </div>
                                </div>
                            </td>
            
                        </tr>
                    @endforeach         
                </tbody>
            </table>
            {{ $teachers->links() }}
            </div>
        </div>
    
    </div>
    <script>
    function deleteTeacher(teacherId) {
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
                    url: "{{ route('admin.teacherlist.delete')}}",
                    type: 'POST',
                    data: {
                        "id": teacherId,
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
