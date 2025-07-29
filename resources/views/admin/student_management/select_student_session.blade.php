

@extends('layouts/contentNavbarLayout')



@section('title', 'Student - Progress Marking')



@section('content')



<div class="card">

  <div class="card-header mb-4">
    <h4 class="fw-bold mb-0">Select Student for Progress Marking</h4>
  </div>

  <div class="card-body">

    <form id="progressForm" action="{{ route('admin.student.progressmarkinglist.redirect') }}" method="GET">

      <div class="row mb-4">

        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <select id="session_id" name="session_id" class="form-select" required>

              <option value="">Select Session</option>

              @foreach($sessions as $s)

                <option value="{{ $s->id }}">{{ $s->session_name }}</option>

              @endforeach

            </select>
            <label>Select Session</label>
          </div>

        </div>

        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            
            <select id="class_id" name="class_id" class="form-select" required>

              <option value="">Select Class</option>

            </select>
            <label>Class</label>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-floating form-floating-outline">
            <select id="student_id" name="student_id" class="form-select" required>
              <option value="">Select Student</option>
            </select>
            <label for="student_id">Student</label>
          </div>
        </div>

      </div>

      <button type="submit" class="btn btn-primary mt-3">Student Progress Marking</button>

      <button type="button" id="exportPDFBtn" class="btn btn-danger mt-3">Export to PDF</button>

    </form>

  </div>

</div>



@endsection

@section('scripts')

<script>

    function toUcwords(str) {
        return (str || '').toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
    }
    $(document).ready(function () {

        $('#session_id').on('change', function(){

            let sid = $(this).val();

            $('#class_id').html('<option>Loading…</option>');

            $('#student_id').html('<option>Select Student</option>');

            if (!sid) return $('#class_id').html('<option>-- Select Class --</option>');



            $.get('{{ route("admin.getClassesBySession") }}', { session_id: sid }, function(res){

            let opts = '<option value="">Select Class</option>';

            if (res.success) {

                res.classes.forEach(c => {

                opts += `<option value="${c.id}">${c.class.toUpperCase()}</option>`;

                });

            }

            $('#class_id').html(opts);

            });

        });



        $('#class_id').on('change', function(){

            let cid = $(this).val(),

                sid = $('#session_id').val();



            $('#student_id').html('<option>Loading…</option>');

            if (!cid || !sid) return $('#student_id').html('<option>Select Student</option>');



            $.get('{{ route("admin.getStudentsByClass") }}', { class_id: cid, session_id: sid }, function(res){

            let opts = '<option value="">Select Student</option>';

            if (res.success) {

                res.students.forEach(s => {
                  let studentNameUc = toUcwords(s.student_name);
                  opts += `<option value="${s.id}">${studentNameUc} (${s.student_id})</option>`;

                });

            }

            $('#student_id').html(opts);

            });

        });



    });


$(document).ready(function() {
    $('#exportPDFBtn').on('click', function(e) {
        e.preventDefault();

        let sessionId  = $('#session_id').val(); // This gets the ID (e.g., 11)
        let classId = $('#class_id').val();
        let studentId = $('#student_id').val();

        if (!sessionId ) {
          toastFire('error', 'Please select session');
          return;
        }

        if (!classId) {
          toastFire('error', 'Please select a class');
          return;
        }

        if (!studentId) {
          toastFire('error', 'Please select student');
          return;
        }

        // Get the text content (session_name) of the selected option
        let sessionName = $('#session_id option:selected').text();

        if (!sessionName) {
            toastFire('error', 'Session name not found. Please select a valid session.');
            return;
        }

        // The Laravel route expects the session name, so pass sessionName
        let url = "{{ route('admin.student.progress.export.pdf', ['student_id' => '__STUDENT__', 'session' => '__SESSION_NAME__']) }}";
        url = url.replace('__STUDENT__', studentId).replace('__SESSION_NAME__', sessionName);

        window.open(url, '_blank');
        
    });
});


</script>

@endsection