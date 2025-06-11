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

    <div class="row">
        {{-- Subjects List --}}
        <div class="col-md-8">
            <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subject List</h5>
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
            </div>

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject Name</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($subjects as $subject)
                                <tr>
                                    <td>{{ $subject->sub_name }}</td>
                                    <td>{{ $subject->sub_code }}</td>
                                    <td>{{ $subject->description }}</td>
                                    <td>
                                        <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                                            <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$subject->id}}"
                                            {{ $subject->status ? 'checked' : ''}} onclick="statusToggle('{{route('admin.subjectlist.status', $subject->id)}}', this)">
                                            <label class="form-check-label" for="customSwitch{{$subject->id}}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                        <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="ri-more-2-line"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" data-bs-toggle="modal" data-target="#editModal" data-id="{{$subject->id}}">
                                                <i class="ri-pencil-line me-1"></i> Edit
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0);" onclick="deleteSubject({{ $subject->id }})">
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

                {{ $subjects->links() }}
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
                <form action="{{ route('admin.subjectlist.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <div class="form-floating form-floating-outline">
                    <input type="text" name="sub_name" class="form-control" placeholder="Subject Name"
                            value="{{ old('sub_name', $subject->subject_name) }}">
                    <label>Subject Name</label>
                    @error('subject_name') <p class="text-danger small">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating form-floating-outline">
                    <input type="text" name="sub_code" class="form-control" placeholder="Subject Code"
                            value="{{ old('sub_code') }}">
                    <label>Subject Code</label>
                    @error('sub_code') <p class="text-danger small">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating form-floating-outline">
                    <textarea name="description" class="form-control" placeholder="Description" style="height: 100px">{{ old('description') }}</textarea>
                    <label>Description</label>
                    @error('description') <p class="text-danger small">{{ $message }}</p> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary d-block ">
                    Create
                </button>
                </form>
            </div>
            </div>
        </div>
    </div>

@endsection

<script>
  function deleteSubject(userId) {
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
    });
  }

</script>