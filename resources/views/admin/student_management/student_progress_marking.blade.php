@extends('layouts/contentNavbarLayout')

@section('title', 'Student - List')

@section('content')

<div class="card">
  <div class="card-body">
    <div class="col-12">
      <h6 class="text-body-secondary">Learning Perspective Of Conginitive Domain</h6>
        <div class="text-end">
            <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-danger">
                <i class="menu-icon tf-icons ri-arrow-left-line"></i> Back
            </a>
        </div>
      <div class="nav-align-left nav-tabs-shadow">
        <ul class="nav nav-tabs" role="tablist">
            @foreach ($sessionMap as $index=>$session_item)
                <li class="nav-item" role="presentation">
                    <a href="{{ route('admin.student.progressmarkinglist', [$student->id, $index]) }}" class="nav-link waves-effect {{$current_session==$index?"active":""}}" role="tab" aria-controls="navs-left-{{$session_item}}" aria-selected="false"
                    tabindex="-1">{{$index}}</a>
                </li>
            @endforeach
          <span class="tab-slider" style="height: 38px; top: 38px; inset-inline-end: 0px;"></span>
        </ul>
        <div class="tab-content">
            @foreach ($sessionMap as $index=>$session_item)
                @if($current_session == $index)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Student Progress Marking</h5>
                            <button type="button" class="btn btn-xs btn-outline-primary waves-effect" onclick="AddMoreItem()">
                            Add New Item
                            </button>
                        </div>
                        <div class="card-body">

                            {{-- Table header --}}
                            <div class="row fw-bold text-center mb-2">
                            <div class="col">Progress Category</div>
                            <div class="col">First Phase</div>
                            <div class="col">Second Phase</div>
                            <div class="col">Third Phase</div>
                            <div class="col-auto">Action</div>
                            </div>

                            {{-- Dynamic rows will be appended here --}}
                            <div id="AppendData"></div>

                        </div>
                    </div>

                @endif
            @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('page-script')
<script>
    let studentProgressCategory = @json($student_progress_category);
  function AddMoreItem() {

    let fieldOptions = `<option value="">Select Field</option>`;
    Object.keys(studentProgressCategory).forEach(field => {
        fieldOptions += `<option value="${field}">${field}</option>`;
    });

    let html = `
      <div class="row g-2 align-items-center mb-2 dynamic-item">
        <div class="col"> 
          <select name="progress_category[]" class="form-select progress-field" required>
            ${fieldOptions}
          </select>
        </div>
        <div class="col">
          <select name="formative_first_phase[]" class="form-select progress-value" required>
            <option value="">Select Value</option>
          </select>
        </div>
        <div class="col">
          <select name="formative_second_phase[]" class="form-select progress-value" required>
            <option value="">Select Value</option>
          </select>
        </div>
        <div class="col">
          <select name="formative_third_phase[]" class="form-select progress-value" required>
            <option value="">Select Value</option>
          </select>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-sm btn-outline-danger remove-item">
            <i class="ri-close-line"></i>
          </button>
        </div>
      </div>
    `;

    $('#AppendData').append(html);
}
// Handle remove
$(document).on('click', '.remove-item', function () {
  $(this).closest('.dynamic-item').remove();
});

// Populate value dropdowns based on field select
$(document).on('change', '.progress-field', function () {
    let selectedField = $(this).val();
    let valueSelects = $(this).closest('.dynamic-item').find('.progress-value');
    
    let values = studentProgressCategory[selectedField] || [];
    console.log(values);
    let options = `<option value="">Select Value</option>`;
    values.forEach(val => {
        options += `<option value="${val}">${val}</option>`;
    });

    valueSelects.html(options);
});

</script>
@endsection
