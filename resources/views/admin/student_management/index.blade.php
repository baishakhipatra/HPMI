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

                        {{-- <input type="text" name="keyword" class="form-control" placeholder="Enter student name..." value="{{ request('keyword') }}"> --}}

                        <select id="student_keyword" name="student_id" class="form-control" style="width: 100%;"></select>
                        <div id="student_suggestions" class="list-group position-absolute w-50" style="z-index: 1000;"></div>
                    </div>

                    <div class="col-md-4 d-flex justify-content-start align-items-center gap-2">

                        {{-- <button type="submit" class="btn btn-primary">Search</button> --}}

                        <a href="{{ route('admin.student.readmission.index') }}" class="btn btn-secondary">Reset</a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    @if(request()->filled('keyword'))

        @if($students->count() > 1 && !$selectedStudent)

            <div class="card mt-4">

                <div class="card-header">

                    <h6>Multiple students found. Please select one:</h6>

                </div>

                <div class="card-body">

                    @foreach($students as $stu)

                        <form method="GET" action="{{ route('admin.student.readmission.index') }}" class="mb-2">

                            <input type="hidden" name="keyword" value="{{ request('keyword') }}">

                            <input type="hidden" name="student_id" value="{{ $stu->student_id }}">

                            <button type="submit" class="btn btn-outline-dark btn-sm">

                                {{ ucwords($stu->student_name) }} ({{ $stu->student_id }}) - DOB: {{date('d-m-Y',strtotime($stu->date_of_birth))}}

                            </button>

                        </form>

                    @endforeach

                </div>

            </div>

        @elseif($students->isEmpty())

            <div class="alert alert-warning mt-4">No student found matching this name.</div>

        @endif

    @endif

    @if($selectedStudent)

        <div class="card mt-4">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h4 class="mb-0">Student: {{ ucwords($selectedStudent->student_name) }} ({{ $selectedStudent->student_id }})</h4>

                <a href="{{ route('admin.student.readmission', $selectedStudent->id) }}" class="btn btn-success btn-sm">+ Re-Admission</a>

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

                                    <td>{{ ucwords($history->section) }}</td>

                                    <td>{{ $history->roll_number }}</td>

                                    <td>{{date('d-m-Y',strtotime($history->admission_date))}}</td>



                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                @else

                    <p class="text-danger">No admission history found for this student.</p>

                @endif

            </div>

        </div>

    @endif

@endsection

@section('scripts')
<script>
    function toUcwords(str) {
        return str.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
    }
   
    
    var $jq = jQuery.noConflict();

    $jq(document).ready(function () {
        $jq('#student_keyword').select2({
            placeholder: 'Search student name...',
            minimumInputLength: 1,
            ajax: {
                url: '{{ route("admin.student.readmission.autocomplete") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term
                    };
                },
                processResults: function (data) {
                    
                    return {
                        results: data.map(function (student) {
                            let nameUcwords = toUcwords(student.student_name);
                            return {
                                id: nameUcwords, // Use name as ID too
                                text: `${nameUcwords} (${student.student_id})`
                            };
                        })
                    };
                },
                cache: true
            }
        });

        // Auto-submit with just keyword
        $jq('#student_keyword').on('select2:select', function (e) {
            const selectedName = e.params.data.id;

            // Redirect to readmission?keyword=SelectedName
            const url = new URL("{{ route('admin.student.readmission.index') }}", window.location.origin);
            url.searchParams.set("keyword", selectedName);
            window.location.href = url.toString();
        });
    });
</script>

@endsection