 <tr>
                            <td class="mark-student-name">{{ ucwords($mark->student->student_name ?? '-') }}</td>
                            <td class="mark-class-name">{{ $mark->class->class ?? '-' }}</td>
                            <td class="mark-subject-name">{{ ucwords($mark->subjectlist->sub_name ?? '-') }}</td>
                            <td>{{ $mark->term_one_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->mid_term_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->term_two_stu_marks ?? '-' }}</td>
                            <td>{{ $mark->final_exam_stu_marks ?? '-' }}</td>
                            <td>
                                <strong>
                                    {{
                                        ($mark->term_one_stu_marks ?? 0) +
                                        ($mark->term_two_stu_marks ?? 0) +
                                        ($mark->mid_term_stu_marks ?? 0) +
                                        ($mark->final_exam_stu_marks ?? 0)
                                    }}
                                </strong>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ri-more-2-line"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        {{-- <a class="dropdown-item" href="#" title="Edit" data-bs-toggle="tooltip">
                                            <i class="ri-pencil-line me-1"></i> Edit
                                        </a> --}}
                                        <button type="button" class="dropdown-item editMarksBtn" 
                                                data-id="{{ $mark->id }}"
                                                data-session-id="{{ $mark->studentAdmission->session_id ?? '' }}"
                                                data-student-id="{{ $mark->studentAdmission->student_id ?? '' }}"
                                                data-student-name="{{ $mark->studentAdmission->student->name ?? '' }}"
                                                data-class-id="{{ $mark->studentAdmission->class_id ?? '' }}"
                                                data-class-name="{{ $mark->studentAdmission->class->class_name ?? '' }}"
                                                data-subject-id="{{ $mark->subject_id ?? '' }}"
                                                data-subject-name="{{ $mark->subject->subject_name ?? '' }}"
                                                
                                                data-term-one-out-off="{{ $mark->term_one_out_off }}"
                                                data-term-one-stu-marks="{{ $mark->term_one_stu_marks }}"
                                                
                                                data-term-two-out-off="{{ $mark->term_two_out_off }}"
                                                data-term-two-stu-marks="{{ $mark->term_two_stu_marks }}"
                                                
                                                data-mid-term-out-off="{{ $mark->mid_term_out_off }}"
                                                data-mid-term-stu-marks="{{ $mark->mid_term_stu_marks }}"
                                                
                                                data-final-exam-out-off="{{ $mark->final_exam_out_off }}"
                                                data-final-exam-stu-marks="{{ $mark->final_exam_stu_marks }}"
                                                
                                                data-bs-toggle="modal" data-bs-target="#editMarksModal"><i class="ri-pencil-line me-1"></i>
                                            Edit
                                        </button>

                                        <a class="dropdown-item" href="javascript:void(0);" title="Delete" data-bs-toggle="tooltip" onclick="deleteMark({{ $mark->id }})">
                                            <i class="ri-delete-bin-6-line me-1"></i> Delete
                                        </a>
                                    </div>
                                </div>
                                </form>
                            </td>
                        </tr>