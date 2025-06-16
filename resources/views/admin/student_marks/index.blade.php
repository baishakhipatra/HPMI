<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Marks - List')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Marks Management</h3>
            <small class="text-muted">Manage student marks and assessments</small>
        </div>
        <div>
            <a href="" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Export Data">
                Export
            </a>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMarksModal">
                Add Marks
            </button>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Records</h5>
                    <h2></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Average Percentage</h5>
                    <h2></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <input type="text" name="search" class="form-control" placeholder="Search students...">
        </div>
        <div class="form-floating form-floating-outline col-md-4">
            <select name="classes" class="form-select">
                <option value="">All Classes</option>
                @foreach($classOptions as $class)
                    <option value="{{ $class['id'] }}">{{ $class['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-floating form-floating-outline col-md-4 mb-2">
            <select name="subjects" class="form-select">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Term 1</th>
                        <th>Midterm</th>
                        <th>Term 2</th>
                        <th>Final</th>
                        <th>Total</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @foreach($marks as $mark)
                        <tr>
                            <td>{{ $mark->student->name }}</td>
                            <td>{{ $mark->class->name }}</td>
                            <td>{{ $mark->subject->name }}</td>
                            <td>{{ $mark->term_one_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->mid_term_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->term_two_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->final_exam_stu_marks ?? '-' }}</td>
                            <td><strong>{{ $mark->total_marks }}</strong></td>
                            <td>
                                <span class="badge {{ $mark->grade == 'F' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $mark->grade }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('marks.edit', $mark->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('marks.destroy', $mark->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach --}}
                    {{-- @if($marks->isEmpty())
                    <tr>
                        <td colspan="10" class="text-center text-muted">No records found</td>
                    </tr>
                    @endif --}}
                </tbody>

                <!-- Add Marks Modal -->
                <div class="modal fade" id="addMarksModal" tabindex="-1" aria-labelledby="addMarksModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addMarksModalLabel">Add New Marks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="" method="POST">
                        @csrf
                        <div class="modal-body">
                        <div class="row g-3">

                        <div class="form-floating form-floating-outline col-md-4">
                            <select name="session_id" id="session_id" class="form-select" required>
                                <option value="">Select Session</option>
                                @foreach($sessions as $item)
                                    <option value="{{ $item->session_id }}">{{ $item->session->session_name }}</option>
                                @endforeach
                            </select>
                            <label for="session_id" class="form-label">Session</label>
                        </div>


                    
                        <div class="form-floating form-floating-outline col-md-4">
                            <select name="student_id" id="student_id" class="form-select" required>
                                <option value="">Select student</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->student_name }}</option>
                                @endforeach
                            </select>
                            <label for="student_id" class="form-label">Student</label>
                        </div>

                    
                        <div class="form-floating form-floating-outline col-md-4">
                            <select name="class_id" id="class_id" class="form-select" required>
                                <option value="">Select class</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class }} - {{ $class->sections->pluck('section')->implode(', ') }}</option>
                                @endforeach
                            </select>
                              <label for="class_id" class="form-label">Class</label>
                        </div>

                         
                        <div class="form-floating form-floating-outline col-md-4">
                            <select name="subject_id" id="subject_id" class="form-select" required>
                                <option value="">Select subject</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                                @endforeach
                            </select>
                            <label for="subject_id" class="form-label">Subject</label>
                        </div>

                         
                        {{-- <div class="form-floating form-floating-outline col-md-3">
                        <label for="term_one_stu_marks" class="form-label">Term 1 (out of 100)</label>
                        <input type="number" name="term_one_stu_marks" class="form-control" placeholder="Enter marks">
                        </div>

                    
                        <div class="form-floating form-floating-outline col-md-3">
                        <label for="mid_term_stu_marks" class="form-label">Midterm (out of 100)</label>
                        <input type="number" name="mid_term_stu_marks" class="form-control" placeholder="Enter marks">
                        </div>

                        
                        <div class="form-floating form-floating-outline col-md-3">
                        <label for="term_two_stu_marks" class="form-label">Term 2 (out of 100)</label>
                        <input type="number" name="term_two_stu_marks" class="form-control" placeholder="Enter marks">
                        </div>

                        <div class="form-floating form-floating-outline col-md-3">
                        <label for="final_exam_stu_marks" class="form-label">Final Exam (out of 100)</label>
                        <input type="number" name="final_exam_stu_marks" class="form-control" placeholder="Enter marks">
                        </div> --}}

                        </div>
                        </div>

                        <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Marks</button>
                        </div>

                    </form>
                    </div>
                </div>
                </div>

            </table>
        </div>
    </div>

</div>

@endsection