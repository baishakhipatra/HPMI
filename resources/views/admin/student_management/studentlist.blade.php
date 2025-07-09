<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Student - List')

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
    <h3 class="mb-0 text-primary">Student List</h3>
    <a href="{{ route('admin.studentcreate') }}" class="btn btn-primary btn-sm">+ Add Student</a>
  </div>

  {{-- <div class="px-3 py-2">
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
                    <a href="{{ route('admin.student.export', ['keyword' => request()->input('keyword')]) }}" 
                      class="btn buttons-collection btn-outline-secondary dropdown-toggle waves-effect" 
                      data-toggle="tooltip" title="Export Data">
                      Export
                    </a>
                  </div>
                </div>
              </div>
              
              <hr>

              <!-- CSV Upload Form (Placed outside of the filter/export button group) -->
              <form method="POST" action="{{ route('admin.student.import') }}" enctype="multipart/form-data" class="mt-3">
                  @csrf
                  <div class="row align-items-end">
                      <div class="col-md-6">
                          <label for="csv_file" class="form-label">Upload CSV File</label>
                          <input type="file" name="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv">
                          @error('csv_file')
                              <div class="text-danger small">{{ $message }}</div>
                          @enderror
                      </div>

                      <div class="col-md-4">
                          <button type="submit" class="btn btn-success mt-2">
                              <i class="fas fa-upload"></i> Import Students
                          </button>
                      </div>
                  </div>
              </form>
            </div>
          </div>
      </div>
    </form>
  </div> --}}
  <div class="px-3 py-2">
    <form action="" method="get">
        <div class="row">
            <div class="col-md-6"></div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center"> {{-- Added align-items-center for vertical alignment --}}
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
                            {{-- Export Button (already present) --}}
                            <div class="d-md-flex justify-content-between align-items-center dt-layout-start">
                                <a href="{{ route('admin.student.export', ['keyword' => request()->input('keyword')]) }}"
                                    class="btn buttons-collection btn-outline-secondary waves-effect"
                                    data-toggle="tooltip" title="Export Data">
                                    Export Student <i class="tf-icons ri-download-line"></i>
                                </a>
                            </div>

                            {{-- Removed the extra <hr> and CSV form placement here --}}

                        </div>
                    </div>

                    {{-- NEW: CSV Import Button and Hidden Form --}}
                    {{-- This section replaces the original separate CSV form block --}}
                  <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#importStudentModal">
                      <i class="tf-icons ri-upload-line"></i> Import Student
                  </button>
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
            <th>Student Name</th>
            <th>Student ID</th>
            <th>Date Of Birth</th>
            <th>Gender</th>
            <th>Session</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          @foreach($students as $item)
            @php
              $current_session = $item->admission?->academicsession?->session_name ?? 'Null';
            @endphp

            <tr> 
              <td>{{ ucwords($item->student_name) }}</td>
              <td>{{ ($item->student_id) }}</td>
              <td>{{ ($item->date_of_birth) }}</td>
              <td>{{ $item->gender}}</td>
              <td>{{ $item->admission?->session?->session_name ?? 'N/A' }}</td>
              <td>
                 <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                    <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$item->id}}"
                      {{ $item->status ? 'checked' : ''}} onclick="statusToggle('{{route('admin.studentstatus', $item->id)}}', this)">
                    <label class="form-check-label" for="customSwitch{{$item->id}}"></label>
                  </div>
              </td>
              <td>
                {{-- <div class="dropdown">
                  <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="ri-more-2-line"></i>
                  </button >
                  <div class="dropdown-menu">
                      <a class="dropdown-item" href="{{ route('admin.studentedit',  $item->id) }}" title="Edit" data-bs-toggle="tooltip">
                          <i class="ri-pencil-line me-1"></i> Edit
                      </a>
                      <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="tooltip" title="Delete" onclick="deleteStudent({{$item->id}})">
                          <i class="ri-delete-bin-6-line me-1"></i> Delete
                      </a>

                      <a class="dropdown-item" href="{{ route('admin.student.admissionhistory',  $item->id) }}" title="Edit" data-bs-toggle="tooltip">
                          <i class="ri-graduation-cap-line me-1"></i> Admission History
                      </a>
                      <a class="dropdown-item" href="{{ route('admin.student.progressmarkinglist', [$item->id, $current_session]) }}" title="Student Progress Marking" data-bs-toggle="tooltip">
                          <i class="ri-file-list-3-line"></i> Student Progress Marking
                      </a>
                      <a class="dropdown-item" href="{{ route('admin.student.classcompare', [$item->id]) }}" title="Class Wise Comparison" data-bs-toggle="tooltip">
                          <i class="ri-bar-chart-grouped-line"></i> Class Wise Comparison
                      </a>
                  </div>
                </div> --}}
                <div class="btn-group" role="group" aria-label="Action Buttons">
                    {{-- Edit --}}
                    <a href="{{ route('admin.student.show', $item->id) }}"  class="btn btn-sm btn-icon btn-outline-success"         
                      data-bs-toggle="tooltip" title="View">                  
                      <i class="ri-eye-line"></i>
                    </a>

                    <a href="{{ route('admin.studentedit', $item->id) }}"
                      class="btn btn-sm btn-icon btn-outline-dark"
                      data-bs-toggle="tooltip"
                      title="Edit">
                        <i class="ri-pencil-line"></i>
                    </a>

                    {{-- Delete --}}
                    <a href="javascript:void(0);"
                      class="btn btn-sm btn-icon btn-outline-danger"
                      onclick="deleteStudent({{ $item->id }})"
                      data-bs-toggle="tooltip"
                      title="Delete">
                        <i class="ri-delete-bin-6-line"></i>
                    </a>

                    {{-- Admission History --}}
                    {{-- <a href="{{ route('admin.student.admissionhistory', $item->id) }}"
                      class="btn btn-sm btn-icon btn-outline-info"
                      data-bs-toggle="tooltip"
                      title="Admission History">
                        <i class="ri-graduation-cap-line"></i>
                    </a> --}}

                    {{-- Progress Marking --}}
                    {{-- <a href="{{ route('admin.student.progressmarkinglist', [$item->id, $current_session]) }}"
                      class="btn btn-sm btn-icon btn-outline-warning"
                      data-bs-toggle="tooltip"
                      title="Student Progress Marking">
                        <i class="ri-file-list-3-line"></i>
                    </a> --}}

                    {{-- Classwise Comparison --}}
                    <a href="{{ route('admin.student.classcompare', $item->id) }}"
                      class="btn btn-sm btn-icon btn-outline-primary"
                      data-bs-toggle="tooltip"
                      title="Class Wise Comparison">
                        <i class="ri-bar-chart-grouped-line"></i>
                    </a>
                </div>
              </td>
            </tr>
          @endforeach         
        </tbody>
      </table>
      {{ $students->links() }}
    </div>
  </div>
  
  <!-- Import Student Modal -->
<div class="modal fade" id="importStudentModal" tabindex="-1" aria-labelledby="importStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="importStudentForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="importStudentModalLabel">Import Student CSV</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div id="importMessage"></div> <!-- Message Area -->
            <div class="mb-3">
              <label for="excel_file" class="form-label">Upload CSV File</label>
              <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".csv,.xls,.xlsx">
            </div>

            <div class="mb-3">
              <a href="{{ asset('assets/csv/student.xlsx') }}" class="btn btn-outline-primary" download>
                <i class="ri-download-line"></i> Download Sample CSV
              </a>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Import</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
    </form>
  </div>
</div>

</div>
@endsection
@section('scripts')
<script>
  function deleteStudent(studentId) {
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
                url: "{{ route('admin.studentdelete')}}",
                type: 'POST',
                data: {
                    "id": studentId,
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

  $(document).ready(function() {
    $('#importStudentForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        $('#importMessage').html('<div class="alert alert-info">Importing...</div>');

        $.ajax({
            url: "{{ route('admin.student.import') }}",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
              console.log(response);
              return false;
                $('#importMessage').html('<div class="alert alert-success">Students imported successfully!</div>');
                $('#excel_file').val('');
                // Optionally close modal after short delay
                setTimeout(function() {
                    $('#importStudentModal').modal('hide');
                    location.reload(); // Reload to reflect changes
                }, 1500);
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors || { error: ['Something went wrong.'] };
                let errorHtml = '<div class="alert alert-danger"><ul>';
                $.each(errors, function(key, messages) {
                    messages.forEach(msg => {
                        errorHtml += '<li>' + msg + '</li>';
                    });
                });
                errorHtml += '</ul></div>';
                $('#importMessage').html(errorHtml);
            }
        });
    });
  });

</script>

@endsection
