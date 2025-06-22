 <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student</th>
                        <th>Class</th>
                        <th>Subject</th>
                        <th>Term 1</th>
                        <th>Midterm</th>
                        <th>Term 2</th>
                        <th>Final</th>
                        <th>Total</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($marks as $mark)
                        <tr>
                            <td>{{ ucwords($mark->student->student_name ?? '-') }}</td>
                            <td>{{ $mark->class->class ?? '-' }}</td>
                            <td>{{ ucwords($mark->subjectlist->sub_name ?? '-') }}</td>
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
                                {{-- <span class="badge {{ calculateGrade(($mark->term_one_stu_marks ?? 0) + ($mark->term_two_stu_marks ?? 0) + ($mark->mid_term_stu_marks ?? 0) + ($mark->final_exam_stu_marks ?? 0)) == 'F' ? 'bg-danger' : 'bg-success' }}">
                                    {{ calculateGrade(($mark->term_one_stu_marks ?? 0) + ($mark->term_two_stu_marks ?? 0) + ($mark->mid_term_stu_marks ?? 0) + ($mark->final_exam_stu_marks ?? 0)) }}
                                </span> --}}
                            </td>
                            <td>
                                <a href="" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($marks->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center text-muted">No records found</td>
                        </tr>
                    @endif
                </tbody>

            </table>