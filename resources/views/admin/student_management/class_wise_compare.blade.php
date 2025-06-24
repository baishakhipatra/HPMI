<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Student - List')

@section('content')

@if(session('success'))
    <div class="alert alert-success" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="container">
    <h3>Class Wise Comparison: {{ $student->student_name }}</h3>
    <div class="text-end">
            <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-danger">
                <i class="menu-icon tf-icons ri-arrow-left-line"></i> Back
            </a>
    </div>

    <div class="row"> 
        <div class="col-md-5">
            <div class="form-floating form-floating-outline mb-3">
                <select id="comparison1" class="form-control">
                    <option value="">Select Session</option>
                    @foreach ($admissions as $adm)
                        <option value="{{ $adm->session->id }}">{{ $adm->session->session_name }}</option>
                    @endforeach
                </select>
                <label>Comparison 1</label>
            </div>
          
    
            <div class="form-floating form-floating-outline mb-3">
                <div class="mt-2">  
                    <label>Class</label>
                    <select id="class1" class="form-control" disabled></select>
                </div>
            </div>
    
            <div class="form-floating form-floating-outline mb-3">
                <div class="mt-2">
                    <label>Subject</label>
                    <select id="subject1" class="form-control" disabled></select>
                </div>
            </div>
        </div>
    
        
        <div class="col-md-5 offset-md-1">
            <div class="form-floating form-floating-outline mb-3">
                <select id="comparison2" class="form-control">
                    <option value="">Select Session</option>
                    @foreach ($admissions as $adm)
                        <option value="{{ $adm->session->id }}">{{ $adm->session->session_name }}</option>
                    @endforeach
                </select>
                <label>Comparison 2 </label>
            </div>
    
            <div class="form-floating form-floating-outline mb-3">
                <div class="mt-2">  
                    <label>Class</label>
                    <select id="class2" class="form-control" disabled></select>                    
                </div>
            </div>
    
            <div class="form-floating form-floating-outline mb-3">
                <div class="mt-2">
                    <label>Subject</label>
                    <select id="subject2" class="form-control" disabled></select>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection


<script>
    $(document).ready(function () {
        let studentId = {{ $student->id }};
    
        function loadClassAndSubjects(sessionSelectId, classDropdownId, subjectDropdownId) {
            let sessionId = $('#' + sessionSelectId).val();
            if (!sessionId) return;

            function ucwords(str) {
                return str.replace(/\b\w/g, function (char) {
                    return char.toUpperCase();
                });
            }

            $.ajax({
                url: "{{ route('admin.student.getClass') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    student_id: studentId,
                    session_id: sessionId
                },
                success: function (response) {
                    if (response.success) {
                        let classSelect = $('#' + classDropdownId);
                        classSelect.empty().append(`<option value="${response.class.id}">${response.class.name}</option>`);
                        classSelect.prop('disabled', false);
    
                        // Load subjects for that class
                        $.ajax({
                            url: "{{ route('admin.student.getSubjects') }}",
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                class_id: response.class.id,
                                student_id: studentId,
                                session_id: sessionId
                            },
                            success: function (subjectRes) {
                                let subjectSelect = $('#' + subjectDropdownId);
                                subjectSelect.empty();
    
                                if (subjectRes.success && subjectRes.subjects.length > 0) {
                                    subjectSelect.append('<option value="">Select Subject</option>');
                                    $.each(subjectRes.subjects, function (index, subject) {
                                        subjectSelect.append(`<option value="${subject.id}">${ucwords(subject.sub_name)}</option>`);
                                    });
                                    subjectSelect.prop('disabled', false);
                                } else {
                                    subjectSelect.append('<option value="">No subjects found</option>');
                                    subjectSelect.prop('disabled', true);
                                }
                            }
                        });
                    } else {
                        $('#' + classDropdownId).empty().append('<option value="">Class not found</option>').prop('disabled', true);
                        $('#' + subjectDropdownId).empty().prop('disabled', true);
                    }
                }
            });
        }
    
        
        $('#comparison1, #comparison2').change(function () {
            let val1 = $('#comparison1').val();
            let val2 = $('#comparison2').val();
    
            if (val1 && val2 && val1 === val2) {
                toastFire('error', 'Both comparisons cannot be the same session.');
                $('#comparison2').val('');
                $('#class2').empty().prop('disabled', true);
                $('#subject2').empty().prop('disabled', true);
                return;
            }
    
            if (this.id === 'comparison1') {
                loadClassAndSubjects('comparison1', 'class1', 'subject1');
            }
    
            if (this.id === 'comparison2') {
                loadClassAndSubjects('comparison2', 'class2', 'subject2');
            }
        });
    });
</script>
