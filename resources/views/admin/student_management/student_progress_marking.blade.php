@extends('layouts/contentNavbarLayout')

@section('title', 'Student - Progress')

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
                </div>
                <div class="card-body">

                  {{-- Table header --}}
                  {{-- <div class="row fw-bold text-center mb-2">
                  <div class="col">Progress Category</div>
                  <div class="col">Score(1-10)</div> --}}
                  {{-- <div class="col">Second Phase</div>
                  <div class="col">Third Phase</div> --}}
                  {{-- </div> --}}

                  {{-- @foreach ($getDetails as $item_detail)
                    <div class="row g-2 align-items-center mb-2 dynamic-item">
                      <div class="col">
                        <input type="text" class="form-control" disabled value="{{ $item_detail->progress_category }}">
                      </div>

                      <div class="col">
                        <select name="formative_first_phase" class="form-select progress-value"
                            data-phase="formative_first_phase"
                            data-student="{{ $student->id }}"
                            data-session="{{ $academic_session_id }}"
                            data-category="{{ $item_detail->progress_category }}">
                          <option value="">Select Value</option>
                          @foreach ($item_detail->pcategory as $pcategory_item)
                            <option value="{{ ucwords($pcategory_item->value) }}" {{ucwords($item_detail->formative_first_phase)==ucwords($pcategory_item->value)?"selected":""}}>{{ ucwords($pcategory_item->value) }}</option>
                          @endforeach
                        </select>
                      </div> 

                       <div class="col">
                        <select name="formative_second_phase" class="form-select progress-value"
                            data-phase="formative_second_phase"
                            data-student="{{ $student->id }}"
                            data-session="{{ $academic_session_id }}"
                            data-category="{{ $item_detail->progress_category }}">
                          <option value="">Select Value</option>
                          @foreach ($item_detail->pcategory as $pcategory_item)
                            <option value="{{ ucwords($pcategory_item->value) }}" {{ucwords($item_detail->formative_second_phase)==ucwords($pcategory_item->value)?"selected":""}}>{{ ucwords($pcategory_item->value) }}</option>
                          @endforeach
                        </select>
                      </div> 

                      <div class="col">
                        <select name="formative_third_phase" class="form-select progress-value"
                            data-phase="formative_third_phase"
                            data-student="{{ $student->id }}"
                            data-session="{{ $academic_session_id }}"
                            data-category="{{ $item_detail->progress_category }}">
                          <option value="">Select Value</option>
                          @foreach ($item_detail->pcategory as $pcategory_item)
                            <option value="{{ ucwords($pcategory_item->value) }}" {{ucwords($item_detail->formative_third_phase)==ucwords($pcategory_item->value)?"selected":""}}>{{ ucwords($pcategory_item->value) }}</option>
                          @endforeach
                        </select>
                      </div>
                    </div>
                  @endforeach --}}

                  @foreach ($student_progress_category as $fieldName => $values)
                    <div class="mb-4">
                      <h6 class="fw-bold mb-3">{{ ucwords($fieldName) }}</h6>

                      <div class="row">
                        @foreach ($values as $item)
                          <div class="col-md-6 mb-3">
                            <label class="form-label">{{ ucwords($item->value) }} (1-10)</label>
                           <input type="number"
                                  class="form-control progress-score-input"
                                  placeholder="Enter score"
                                  min="1" max="10"
                                  data-student="{{ $student->id }}"
                                  data-session="{{ $academic_session_id }}"
                                  data-category="{{ $fieldName }}"
                                  data-value="{{ $item->value }}"
                                  value="{{ $savedScores[ucwords($fieldName)][ucwords($item->value)] ?? '' }}">
                          </div>
                        @endforeach
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          @endforeach
            {{-- Comment textarea below table --}}
          <div class="mt-4">
            <label for="add_comments" class="form-label fw-bold">Add Comment</label>
            <textarea name="add_comments" id="add_comments" class="form-control" rows="3" placeholder="Enter comments...">{{ ucfirst($getDetails->first()->add_comments ?? '') }}</textarea>
            <button type="button" id="save_comment_btn" class="btn btn-primary mt-2">Save Comment</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('page-script')
<script>

  // for save fields and values
    // $(document).on('change', '.progress-value', function () {
    //   let phase = $(this).data('phase');
    //   let value = $(this).val();
    //   let student_id = $(this).data('student');
    //   let session_id = $(this).data('session');
    //   let category = $(this).data('category');

    //   if (value !== '') {
    //     $.ajax({
    //       url: "{{ route('admin.student.progress.update.phase') }}",
    //       method: "POST",
    //       data: {
    //         _token: '{{ csrf_token() }}',
    //         student_id: student_id,
    //         session_id: session_id,
    //         category: category,
    //         phase: phase,
    //         value: value
    //       },
    //       success: function (res) {
    //         if (res.success) {
    //           toastFire('success', 'Progress updated successfully!');
    //           console.log('Updated successfully');
    //         } else {
    //           toastFire('error', 'Progress update failed!');
    //           console.log('Update failed');
    //         }
    //       },
    //       error: function (err) {
    //         toastFire('error', 'Something went wrong while updating progress!');
    //         console.log('Error:', err);
    //       }
    //     });
    //   }
    // });

    // Score input auto-save
    $(document).on('change', '.progress-score-input', function () {
      let score = $(this).val();
      let category = $(this).data('category'); 
      let phase = $(this).data('value'); 
      let student_id = $(this).data('student');
      let session_id = $(this).data('session');


      if (score !== '' && score >= 1 && score <= 10) {
        $.ajax({
          url: "{{ route('admin.student.progress.update.phase') }}",
          method: "POST",
          data: {
            _token: '{{ csrf_token() }}',
            student_id: student_id,
            session_id: session_id,
            category: category,
            phase: phase, 
            value: score  
          },
          success: function (res) {
            if (res.success) {
              toastFire('success', 'Score saved successfully!');
            } else {
              toastFire('error', 'Failed to save score.');
            }
          },
          error: function () {
            toastFire('error', 'Something went wrong while saving the score.');
          }
        });
      } else {
        toastFire('warning', 'Please enter a score between 1 and 10.');
      }
    });



  //for save comments

  $('#save_comment_btn').on('click', function () {
    let comment = $('#add_comments').val();
    let student_id = "{{ $student->id }}";
    let session_id = "{{ $academic_session_id }}";

    console.log(session_id);
    if (comment.trim() === '') {
      toastFire('warning', 'Please enter a comment before saving.');
      return;
    }

    $.ajax({
      url: "{{ route('admin.student.progress.update.phase') }}",
      type: "POST",
      data: {
          _token: '{{ csrf_token() }}',
          student_id: student_id,
          session_id: session_id,
          add_comments: comment
      },
      success: function (res) {
          if (res.success) {
              toastFire('success', 'Comment saved successfully!');
          } else {
              toastFire('error', 'Failed to save comment.');
          }
      },
      error: function () {
          toastFire('error', 'Something went wrong while saving the comment.');
      }
    });
  });


</script>
@endsection
