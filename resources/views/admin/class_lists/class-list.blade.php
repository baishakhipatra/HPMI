<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Class - List')

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
  <ul class="mb-0">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif


<div class="container-fluid">
  <div class="row">
    <div class="col-8">
      <div class="card">
        <div class="card-body">
          <table class="table table-sm table-hover">
            <thead>
              <tr class="text-center">
                <th>#</th>
                <th>Class</th>
                <th>Sections</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($classlist as $index => $class)
              <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">
                  <p class="text-muted mb-0">{{ ucwords($class->class) }}</p>
                </td>
                <td class="text-center">
                  <div class="d-flex flex-column align-items-center">
                    @foreach($class->sections as $section)
                    <span class="badge bg-light-primary text-primary mb-1">
                      {{ ucwords($section->section) }}
                    </span>
                    @endforeach
                  </div>
                </td>
                <td class="text-center">
                  <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                    <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$class->id}}"
                      {{ $class->status ? 'checked' : ''}}
                      onclick="statusToggle('{{route('admin.classstatus', $class->id)}}', this)">
                    <label class="form-check-label" for="customSwitch{{$class->id}}"></label>
                  </div>
                </td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="ri-more-2-line"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('admin.class.subjects', ['id'=> $class->id]) }}" title="Edit">
                          <i class="ri-book-2-line"></i> Subject
                      </a>
                      <a class="dropdown-item" href="{{ route('admin.classlist',['update_id'=> $class->id]) }}"
                        title="Edit">
                        <i class="ri-pencil-line me-1"></i> Edit
                      </a>
                      <a class="dropdown-item" href="javascript:void(0);" onclick="deleteClass({{$class->id}})">
                        <i class="ri-delete-bin-6-line me-1"></i> Delete
                      </a>
                    </div>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="100%" class="text-center">No records found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="col-4">
        @if(isset($update_data))
        <div class="card">
            <div class="card-header">
            <h4>Edit Class & Sections</h4>
            </div>
            <div class="card-body">
            <form action="{{ route('admin.classupdate',['id' => $update_data->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-floating form-floating-outline mb-3">
                <input type="text" name="class" class="form-control" placeholder="Enter Class Name" value="{{$update_data->class}}">
                <label>Class Name</label>
                @error('class') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>
                {{-- <label>Section</label> --}}

                <input type="hidden" name="deleted_section_ids" id="deleted_section_ids" value="">

                {{-- @foreach($update_data->sections as $key=>$section_item)
                    <div>
                        <div class="input-group mb-2">
                            <div class="form-floating form-floating-outline flex-grow-1">
                            <input type="text" name="section[]" class="form-control" placeholder="Enter Section" value="{{$section_item->section}}">
                            <label>Section</label>
                            </div>
                            <button type="button" class="btn btn-outline-danger remove-section ms-2" id="{{$section_item->id}}">
                            <i class="ri-close-line"></i>
                            </button>
                        </div>
                    </div>
                @endforeach --}}

                @foreach($update_data->sections as $key=>$section_item)
                    <div class="input-group mb-2 existing-section">
                        <div class="form-floating form-floating-outline flex-grow-1">
                            <input type="text" name="existing_section[{{ $section_item->id }}]" class="form-control" placeholder="Enter Section" value="{{ $section_item->section }}">
                            <label>Section</label>
                        </div>
                        <button type="button" class="btn btn-outline-danger remove-existing-section ms-2" data-id="{{ $section_item->id }}">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                @endforeach

                <div id="section-container">
                    <div class="input-group mb-2">
                        <div class="form-floating form-floating-outline flex-grow-1">
                        <input type="text" name="section[]" class="form-control" placeholder="Enter Section">
                        <label>Section</label>
                        </div>
                        <button type="button" class="btn btn-outline-secondary add-section ms-2">
                        <i class="menu-icon tf-icons ri-add-line"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <div>
                        <button type="submit" class="btn btn-danger">Back</button>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
            <h4>Add Class & Sections</h4>
            </div>
            <div class="card-body">
            <form action="{{ route('admin.classstore') }}" method="post">
                @csrf

                <div class="form-floating form-floating-outline mb-3">
                <input type="text" name="class" class="form-control" placeholder="Enter Class Name">
                <label>Class Name</label>
                @error('class') <p class="text-danger small">{{ $message }}</p> @enderror
                </div>

                {{-- <label>Section</label> --}}

                <div id="section-container">
                <div class="input-group mb-2">
                    <div class="form-floating form-floating-outline flex-grow-1">
                    <input type="text" name="section[]" class="form-control" placeholder="Enter Section">
                    <label>Section</label>
                    </div>
                    <button type="button" class="btn btn-outline-secondary add-section ms-2">
                    <i class="menu-icon tf-icons ri-add-line"></i>
                    </button>
                </div>
                </div>

                <div class="d-flex justify-content-end">
                    <div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
        @endif
    </div>
    <template id="section-template">
      <div class="input-group mb-2">
        <div class="form-floating form-floating-outline flex-grow-1">
          <input type="text" name="section[]" class="form-control" placeholder="Enter Section">
          <label>Section</label>
        </div>
        <button type="button" class="btn btn-outline-danger remove-section ms-2">
          <i class="ri-close-line"></i>
        </button>
      </div>
    </template>
  </div>
</div>

@endsection

<script>
  $(document).ready(function () {
    let deletedSectionIds = [];

    $(document).on('click', '.add-section', function() {
        let template = $('#section-template').html();
        $('#section-container').append(template);
    });

    $(document).on('click', '.remove-section', function() {
        $(this).closest('.input-group').remove();
    });

    $(document).on('click', '.remove-existing-section', function () {
        let sectionId = $(this).data('id');
        deletedSectionIds.push(sectionId);
        $('#deleted_section_ids').val(deletedSectionIds.join(','));
        $(this).closest('.input-group').remove();
    });
  });


  function deleteClass(classId) {
    Swal.fire({
      icon: 'warning',
      title: "Are you sure you want to delete this?",
      text: "You won't be able to revert this!",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Delete",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{ route('admin.classdelete') }}",
          type: 'POST',
          data: {
            "id": classId,
            "_token": '{{ csrf_token() }}',
          },
          success: function (data) {
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