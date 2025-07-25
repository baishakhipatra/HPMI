

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

      <button type="submit" class="btn btn-primary mt-3">Add Progress</button>

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



</script>

@endsection