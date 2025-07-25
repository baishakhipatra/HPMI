@extends('layouts/contentNavbarLayout')

@section('title', 'Student - Re-admission')

@section('content')

<div class="card">

    <div class="card-header">
        <h4 class="fw-bold mb-0">Search Student for Re-admission</h4>
    </div>

    <div class="card-body">
        <form method="GET" action="{{ route('admin.student.readmission.index') }}">
            <div class="row">
                <div class="col-md-6">
                    <select name="class_id" class="form-control select2" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                {{ strtoupper($cls->class) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('admin.student.readmission.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>

</div>

{{-- @if(request('class_id'))
    @if($students->count())
        <form method="POST" action="{{ route('admin.student.readmission.bulkStore') }}">
            @csrf
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Previous session</th>
                        <th>Name</th>
                        <th>From Class</th>
                        <th>Section</th>
                        <th>Old Roll</th>
                        <th>New session</th>
                        <th>To Class</th>
                        <th>New Section</th>
                        <th>New Roll No.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $stu)
                    @php
                        $fromClass = $class;
                        $toClass = \App\Models\ClassList::find(optional($fromClass)->id + 1);
                        $toSections = $toClass ? $toClass->sections : collect();
                        
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ ucwords($stu->student_name) }}</td>
                        <td>{{ strtoupper($fromClass->class ?? '-') }}</td>
                        <td>{{ strtoupper($stu->latestAdmission->section ?? '-') }}</td>
                        <td>{{ $stu->latestAdmission->roll_number ?? '-' }}</td>

                        <td>
                            {{ strtoupper($toClass->class ?? 'N/A') }}
                            <input type="hidden" name="students[{{ $stu->id }}][student_id]" value="{{ $stu->id }}">
                            <input type="hidden" name="students[{{ $stu->id }}][to_class_id]" value="{{ $toClass->id ?? '' }}">
                        </td>

                        <td>
                            <select name="students[{{ $stu->id }}][section]" class="form-control">
                                <option value="">Select</option>
                                @foreach($toSections as $sec)
                                    <option value="{{ $sec->section }}">{{ $sec->section }}</option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="number" name="students[{{ $stu->id }}][roll_number]" class="form-control">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="text-end">
                <button type="submit" class="btn btn-success">Promote Selected Students</button>
            </div>
        </form>
    @else
        <div class="alert alert-warning mt-4">No records found for selected class.</div>
    @endif
@endif --}}

{{-- @if(request('class_id'))
    @if($students->count())
        <form method="POST" action="{{ route('admin.student.readmission.bulkStore') }}">
            @csrf
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Previous Session</th>
                        <th>From Class</th>
                        <th>Section</th>
                        <th>Old Roll No.</th>
                        <th>New Session</th>
                        <th>To Class</th>
                        <th>New Section</th>
                        <th>New Roll No.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $stu)
                        @php
                            $fromClass = $class;
                            $toClass = \App\Models\ClassList::find(optional($fromClass)->id + 1);
                            $toSections = $toClass ? $toClass->sections : collect();
                            $allSessions = \App\Models\AcademicSession::orderBy('id', 'desc')->get();

                            $previousSession = optional($stu->latestAdmission->session)->session_name;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ ucwords($stu->student_name) }}</td>
                            <td>{{ $previousSession ?? '-' }}</td>
                            <td>{{ strtoupper($fromClass->class ?? '-') }}</td>
                            <td>{{ strtoupper($stu->latestAdmission->section ?? '-') }}</td>
                            <td>{{ $stu->latestAdmission->roll_number ?? '-' }}</td>

                            <td>
                                <select name="students[{{ $stu->id }}][to_session]" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($allSessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                {{ strtoupper($toClass->class ?? 'N/A') }}
                                <input type="hidden" name="students[{{ $stu->id }}][student_id]" value="{{ $stu->id }}">
                                <input type="hidden" name="students[{{ $stu->id }}][from_class_id]" value="{{ $fromClass->id ?? '' }}">
                                <input type="hidden" name="students[{{ $stu->id }}][to_class_id]" value="{{ $toClass->id ?? '' }}">
                            </td>

                            <td>
                                <select name="students[{{ $stu->id }}][section]" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($toSections as $sec)
                                        <option value="{{ $sec->section }}">{{ strtoupper($sec->section) }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" name="students[{{ $stu->id }}][roll_number]" class="form-control">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-end">
                <button type="submit" class="btn btn-success">Promote Selected Students</button>
            </div>
        </form>
    @else
        <div class="alert alert-warning mt-4">No records found for selected class.</div>
    @endif
@endif --}}
@if(request('class_id'))
    @if($students->count())
        <form method="POST" action="{{ route('admin.student.readmission.bulkStore') }}">
            @csrf
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student Name</th>
                        <th>Previous Session</th>
                        <th>From Class</th>
                        <th>Previous Section</th>
                        <th>Old Roll No.</th>
                        <th>New Session</th>
                        <th>To Class</th>
                        <th>New Section</th>
                        <th>New Roll No.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $stu)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ ucwords($stu->student_name) }}</td>
                            <td>{{ $stu->previous_session_name ?? '-' }}</td>
                            <td>{{ strtoupper($stu->to_class->class ?? '-') }}</td>
                            <td>{{ strtoupper($stu->latestAdmission->section ?? '-') }}</td>
                            <td>{{ $stu->latestAdmission->roll_number ?? '-' }}</td>

                            <td>
                                {{ $stu->to_session->session_name ?? 'N/A' }}
                                <input type="hidden" name="students[{{ $stu->id }}][to_session]" value="{{ $stu->to_session->id ?? '' }}">
                            </td>

                            <td>
                                <select name="students[{{ $stu->id }}][to_class_id]" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($stu->available_classes as $cls)
                                        <option value="{{ $cls->id }}">{{ strtoupper($cls->class) }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="students[{{ $stu->id }}][student_id]" value="{{ $stu->id }}">
                            </td>

                            <td>
                                <select name="students[{{ $stu->id }}][section]" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($stu->to_sections as $sec)
                                        <option value="{{ $sec->section }}">
                                             {{ strtoupper(optional($sec->class)->class . '-' . $sec->section) }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            <td>
                                <input type="number" name="students[{{ $stu->id }}][roll_number]" class="form-control">
                            </td>
                        </tr>
                        @if($errors->has($stu->id))
                            <tr>
                                <td colspan="10">
                                    <div class="alert alert-danger">{{ $errors->first($stu->id) }}</div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-success">Promote Selected Students</button>
            </div>
        </form>
    @else
        <div class="alert alert-warning mt-4">No records found for selected class.</div>
    @endif
@endif



@endsection

@section('scripts')
{{-- <script>
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
</script> --}}

@endsection