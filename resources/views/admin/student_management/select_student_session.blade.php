<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@extends('layouts/contentNavbarLayout')

@section('title', 'Student - Progress Marking')

@section('content')

<div class="card">
  <div class="card-header">
    <h5>Select Student for Progress Marking</h5>
  </div>
  <div class="card-body">
    <form id="progressForm" action="{{ route('admin.student.progressmarkinglist.redirect') }}" method="GET">
      <div class="row mb-3">
        <div class="col-md-4">
          <label for="session_id">Session</label>
          <select id="session_id" name="session_id" class="form-select" required>
            <option value="">-- Select Session --</option>
            @foreach($sessions as $s)
              <option value="{{ $s->id }}">{{ $s->session_name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label for="class_id">Class</label>
          <select id="class_id" name="class_id" class="form-select" required>
            <option value="">-- Select Class --</option>
          </select>
        </div>
        <div class="col-md=4">
          <label for="student_id">Student</label>
          <select id="student_id" name="student_id" class="form-select" required>
            <option value="">-- Select Student --</option>
          </select>
        </div>
      </div>

      <button type="submit" class="btn btn-primary">Add Progress</button>
    </form>
  </div>
</div>

@endsection
@section('scripts')
<script>
    $(document).ready(function () {
        $('#session_id').on('change', function(){
            let sid = $(this).val();
            $('#class_id').html('<option>Loading…</option>');
            $('#student_id').html('<option>-- Select Student --</option>');
            if (!sid) return $('#class_id').html('<option>-- Select Class --</option>');

            $.get('{{ route("admin.getClassesBySession") }}', { session_id: sid }, function(res){
            let opts = '<option value="">-- Select Class --</option>';
            if (res.success) {
                res.classes.forEach(c => {
                opts += `<option value="${c.id}">${c.class}</option>`;
                });
            }
            $('#class_id').html(opts);
            });
        });

        $('#class_id').on('change', function(){
            let cid = $(this).val(),
                sid = $('#session_id').val();

            $('#student_id').html('<option>Loading…</option>');
            if (!cid || !sid) return $('#student_id').html('<option>-- Select Student --</option>');

            $.get('{{ route("admin.getStudentsByClass") }}', { class_id: cid, session_id: sid }, function(res){
            let opts = '<option value="">-- Select Student --</option>';
            if (res.success) {
                res.students.forEach(s => {
                opts += `<option value="${s.id}">${s.student_name} (${s.student_id})</option>`;
                });
            }
            $('#student_id').html(opts);
            });
        });

    });

</script>
@endsection