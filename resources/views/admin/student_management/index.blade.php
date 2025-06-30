<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Student - List')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5>Search Student for Re-admission</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.student.readmission.index') }}">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="keyword" class="form-control" placeholder="Enter student name..." value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-4 d-flex justify-content-start align-items-center gap-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('admin.student.readmission.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
            {{-- <form method="GET" action="{{ route('admin.student.readmission.index') }}">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="keyword" class="form-control" placeholder="Enter student name..." value="{{ request('keyword') }}">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="student_id" class="form-control" placeholder="Enter student ID..." value="{{ request('student_id') }}">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="{{ route('admin.student.readmission.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form> --}}
        </div>
    </div>

    @if(request()->has('keyword'))
        @if($student)
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Student: {{ ucwords($student->student_name) }} ({{ $student->student_id }})</h4>
                    <a href="{{ route('admin.student.readmission', $student->id) }}" class="btn btn-success btn-sm">+ Re-Admission</a>
                </div>
                <div class="card-body">
                    @if(count($admissionHistories))
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Session</th>
                                    <th>Class</th>
                                    <th>Section</th>
                                    <th>Roll No</th>
                                    <th>Admission Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($admissionHistories as $history)
                                    <tr>
                                        <td>{{ $history->session->session_name ?? '-' }}</td>
                                        <td>{{ $history->class->class ?? '-' }}</td>
                                        <td>{{ $history->section }}</td>
                                        <td>{{ $history->roll_number }}</td>
                                        <td>{{ \Carbon\Carbon::parse($history->admission_date)->format('d-m-Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-danger">No admission history found for this student.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="alert alert-warning mt-4">
                No student found matching for this name.
            </div>
        @endif
    @endif

@endsection