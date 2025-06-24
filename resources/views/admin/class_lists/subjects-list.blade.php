<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


@extends('layouts/contentNavbarLayout')

@section('title', 'Teacher - List')

@section('content')

   @if(session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- Subjects List --}}
        <div class="col-md-8">
            {{-- <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Class: {{$classData->class}}</h5>
                    <a href="{{ route('admin.classlist') }}" class="btn btn-sm btn-danger">
                        <i class="ri-arrow-left-line"></i> Back
                    </a>
                </div>
                <div class="px-3 py-2">
                <form method="GET" action="">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
                                <div class="form-group me-2 mb-0">
                                    <input type="search" class="form-control form-control-sm" name="keyword" id="keyword" 
                                        value="{{ request()->input('keyword') }}" placeholder="Search something...">
                                </div>
                                <div class="form-group mb-0">
                                    <div class="btn-group">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="ri-filter-3-line"></i>
                                        </button>
                                        <a href="{{ url()->current() }}" class="btn btn-sm btn-light" title="Clear filter">
                                            <i class="ri-close-line"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> --}}
            <div class="card">
                <div class="card-header">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">Class: {{ $classData->class }}</h5>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="">
                                <div class="d-flex justify-content-end">
                                    <div class="form-group me-2 mb-0">
                                        <input type="search" class="form-control form-control-sm" name="keyword" id="keyword"
                                            value="{{ request()->input('keyword') }}" placeholder="Search something...">
                                    </div>
                                    <div class="form-group me-2 mb-0">
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="ri-filter-3-line"></i>
                                        </button>
                                    </div>
                                    <div class="form-group mb-0">
                                        <a href="{{ route('admin.classlist') }}" class="btn btn-sm btn-danger">
                                            <i class="ri-arrow-left-line"></i> Back
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Subject Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($classSubjects as $subject)
                                    <tr class="text-center">
                                        <td>{{ $subject->subject ? ucwords($subject->subject->sub_name) : 'N/A' }}</td>
                                        <td>
                                            <div class="dropdown">
                                            <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="ri-more-2-line"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="tooltip" title="Delete" onclick="deleteClassToSub({{ $subject->id }})">
                                                    <i class="ri-delete-bin-6-line me-1"></i> Delete
                                                </a>
                                            </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5">No subjects found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>

                    {{ $classSubjects->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Subject Create Form --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Add Subject</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.class.subjects.assign') }}" method="POST">
                        @csrf
                        <input type="hidden" name="classId" value="{{$classData->id}}" />
                        <div class="mb-3">
                            <div class="form-floating form-floating-outline">
                                <select class="form-control" name="subjectId" required>
                                    <option>Select subject</option>
                                    @foreach($allSubjects as $subject)
                                    <option value="{{$subject->id}}">{{$subject->sub_name}}</option>
                                    @endforeach
                                </select>
                            
                            @error('subject_name') <p class="text-danger small">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary d-block" onClick="addSubject({{$classData->id}})">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

<script>
  function addSubject(classId) {
        $.ajax({
        url: "{{ route('admin.subjectlist.delete')}}",
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

    function deleteClassToSub(userId) {
        //toastFire('success', 'test message');
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
                    url: "{{ route('admin.class.subjects.delete')}}",
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
