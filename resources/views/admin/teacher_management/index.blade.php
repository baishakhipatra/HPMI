<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Teacher - List')

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
    <h3 class="mb-0 text-primary">Teacher List</h3>
    <a href="{{ route('admin.teacher.create') }}" class="btn btn-primary btn-sm">+ Add Teacher</a>
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
                    <i class="tf-icons ri-filter-3-line"></i>
                  </button>
                  <a href="{{ url()->current() }}" class="btn btn-sm btn-light" data-toggle="tooltip" title="Clear filter">
                    <i class="tf-icons ri-close-line"></i>
                  </a>
                  <div class="d-md-flex justify-content-between align-items-center dt-layout-start">
                    <a href="{{ route('admin.teacher.export', ['keyword' => request()->input('keyword')]) }}" 
                      class="btn buttons-collection btn-outline-secondary dropdown-toggle waves-effect" 
                      data-toggle="tooltip" title="Export Data">
                      Export
                    </a>
                  </div>
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
            <th width="20%">Name</th>
            <th width="10%">Email</th>
            <th width="10%">Mobile</th>
            <th>Date of Joining</th>
            {{-- <th width="15%">Classes</th>
            <th width="40%">Subjects</th> --}}
            <th width="5%">Status</th>
            <th width="10%">Actions</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @foreach($admins as $item)
            @php
              $classes = [];
              $subjects = [];
              foreach ($item->teacherSubjects as $teacherSubject) {
                $clasName = $teacherSubject->classList->class;
                $subjectName = $teacherSubject->subjectList->sub_name;

                if (!in_array($clasName, $classes)) {
                  $classes[] = $clasName;
                }
                $subjects[] = 'Class ' . $clasName . ' - ' . ucfirst($subjectName);
              }
              // dd($subjects);
            @endphp
            <tr>
              <td>{{ $item->user_id}}</td>
              <td>{{ ucfirst($item->name) }}</td>
              <td>{{ $item->email }}</td>
              <td>{{ $item->mobile }}</td>
              <td>{{ $item->date_of_joining}}</td>
              {{-- <td>
                @if(count($classes) > 0)
                  <ul class="mb-0">
                      @foreach($classes as $eachClassItem)
                        <li>{{ $eachClassItem ?? '-' }}</li>
                      @endforeach
                  </ul>
                @else
                    -
                @endif
              </td>
             
              <td>
                @if(count($subjects) > 0)
                    <ul class="mb-0">
                        @foreach($subjects as $eachSubjectItem)
                            <li>
                                {{ $eachSubjectItem }}
                            </li>
                        @endforeach
                    </ul>
                @else
                    -
                @endif
              </td> --}}

              <td>
                 <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                    <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$item->id}}"
                      {{ $item->status ? 'checked' : ''}} onclick="statusToggle('{{route('admin.teacher.status', $item->id)}}', this)">
                    <label class="form-check-label" for="customSwitch{{$item->id}}"></label>
                  </div>
              </td>
              {{-- Edit and delete --}}
              <td>
                <div class="btn-group" role="group" aria-label="Action Buttons">
                  <a href="{{ route('admin.teacher.show', $item->id) }}"  class="btn btn-sm btn-icon btn-outline-success"         
                    data-bs-toggle="tooltip" title="View">                  
                    <i class="ri-eye-line"></i>
                  </a>
                  <a href="{{ route('admin.teacher.edit', $item->id) }}" class="btn btn-sm btn-icon btn-outline-dark"                     
                    data-bs-toggle="tooltip"  title="Edit">
                    <i class="ri-pencil-line"></i>
                  </a>
                  <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-outline-danger" onclick="deleteTeacher({{ $item->id }})"     
                    data-bs-toggle="tooltip" title="Delete">
                    <i class="ri-delete-bin-6-line"></i>
                  </a>
                </div>
              </td> 
            </tr>
          @endforeach         
        </tbody>
      </table>
      {{ $admins->links() }}
    </div>
  </div>
  
</div>
<script>
  function deleteTeacher(userId) {
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
                url: "{{ route('admin.teacher.delete')}}",
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