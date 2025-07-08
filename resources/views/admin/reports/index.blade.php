<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Class - List')

@section('content')

<div class="container-xxl">
    <h4 class="fw-bold">Academic Reports</h4>
    <p>Comprehensive academic and performance reports</p>

    <div class="row mb-4">
        @php
          $widgets = [
            ['icon' => 'ri-user-3-fill', 'count' => $studentsCount, 'label' => 'Total Students'],
            ['icon' => 'ri-book-open-fill', 'count' => $classesCount, 'label' => 'Total Classes'],
            ['icon' => 'ri-bar-chart-fill', 'count' => $subjectsCount, 'label' => 'Total Subjects'],
            ['icon' => 'ri-file-list-3-fill', 'count' => $marksCount, 'label' => 'Total Marks Recorded'],
          ];
        @endphp

        @foreach ($widgets as $widget)
        <div class="col-md-3">
            <div class="card text-center p-3">
                <i class="ri {{ $widget['icon'] }} fs-2 mb-2"></i>
                <h4>{{ $widget['count'] }}</h4>
                <p>{{ $widget['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Filters for both tabs --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="filterSession" class="form-label">Session</label>
            <select id="filterSession" class="form-select">
                <option value="">All Sessions</option>
                @foreach($sessions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterClass" class="form-label">Class</label>
            <select id="filterClass" class="form-select">
                <option value="">All Classes</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="filterSubject" class="form-label">Subject</label>
            <select id="filterSubject" class="form-select">
                <option value="">All Subjects</option>
            </select>
        </div>
        {{-- Student Filter for Report Card Tab --}}
        <div class="col-md-3">
            <label for="filterStudent" class="form-label">Student</label>
            <select id="filterStudent" class="form-select">
                <option value="">Select Student</option>
            </select>
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" id="classComparisonTab" data-bs-toggle="tab" href="#classComparison">Class-Wise Comparison</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="studentReportTab" data-bs-toggle="tab" href="#studentReports">Student Report Cards</a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- Class-Wise Comparison Tab Content (existing) --}}
        <div class="tab-pane fade show active" id="classComparison">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Class Performance Comparison</h5>
                <button class="btn btn-outline-primary"><i class="ri-download-2-line"></i> Export Report</button>
            </div>
            <canvas id="performanceChart" height="100"></canvas>
            <p id="noDataMessage" class="text-center mt-3 alert alert-info" style="display: none;"></p>
        </div>

        {{-- Student Report Cards Tab Content (NEW) --}}
        <div class="tab-pane fade" id="studentReports">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Individual Student Reports</h5>
                <a href=""
                    id="exportReportBtn"
                    class="btn btn-outline-primary">
                    <i class="ri-download-2-line"></i> Export Report Card
                </a>
            </div>

            <div id="studentReportCardContainer" style="display: none;">
                <h4 id="reportStudentName" class="mb-3"></h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Session:</strong> <span id="reportSessionName"></span></p>
                        <p><strong>Class:</strong> <span id="reportClassName"></span></p>
                    </div>
                    {{-- <div class="col-md-6">
                        <p><strong>Roll Number:</strong> <span id="reportRollNumber"></span></p>
                    </div> --}}
                </div>

                <h6>Subject-Wise Performance</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Mid-Term (Marks/Out Of)</th>
                                <th>Final Exam (Marks/Out Of)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="studentMarksTableBody">
                            
                        </tbody>
                    </table>
                </div>

                <h6 class="mt-4">Overall Summary</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Total Marks Obtained:</strong> <span id="reportTotalMarks"></span></p>
                        <p><strong>Overall Percentage:</strong> <span id="reportAveragePercentage"></span>%</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Overall Status:</strong> <span id="reportOverallStatus"></span></p>
                    </div>
                </div>
            </div>

            <div id="selectStudentMessage" class="text-center mt-5">
                <h5 class="mt-3">Select a Student</h5>
                <p>Choose a student from the filter above to view their detailed report card.</p>
            </div>
             <p id="studentReportErrorMessage" class="text-center mt-3 alert alert-danger" style="display: none;"></p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Add Bootstrap 5 JS if not already loaded in your layout, for tabs functionality --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> --}}

    <script>
    $(function () {
        let performanceChartInstance = null; // For Class Comparison Chart


        function fetchChartData() {
            const sessionId = $('#filterSession').val();
            const classId = $('#filterClass').val();
            const subjectId = $('#filterSubject').val();

            $.ajax({
                url: "{{ route('admin.report.getChartData') }}",
                method: 'GET',
                data: {
                    session_id: sessionId,
                    class_id: classId,
                    subject_id: subjectId
                },
                success: function (res) {
                    // console.log("Chart Data Response:", res);
                    if (res.success && res.data.length > 0) {
                        $('#noDataMessage').hide();
                        $('#performanceChart').show();
                        drawChart(res.data);
                    } else {
                        $('#performanceChart').hide();
                        $('#noDataMessage').text('No performance data available for the selected filters.').show();
                        if (performanceChartInstance) {
                            performanceChartInstance.destroy();
                            performanceChartInstance = null;
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error fetching chart data:", textStatus, errorThrown, jqXHR);
                    $('#performanceChart').hide();
                    $('#noDataMessage').text('Error loading data. Please check console for network issues.').show();
                    if (performanceChartInstance) {
                        performanceChartInstance.destroy();
                        performanceChartInstance = null;
                    }
                }
            });
        }

        // Function to draw the chart (remains the same)
        function drawChart(data) {
            const labels = data.map(item => item.class);
            const avgMarks = data.map(item => item.avg_marks);
            const passPercent = data.map(item => item.pass_percentage);

            const ctx = document.getElementById('performanceChart').getContext('2d');

            if (performanceChartInstance) {
                performanceChartInstance.destroy();
            }

            performanceChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Average Marks %', data: avgMarks, backgroundColor: '#3b82f6' },
                        { label: 'Pass Percentage %', data: passPercent, backgroundColor: '#10b981' } 
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: 'Class Performance Comparison'
                        }
                    }
                }
            });
        }



        function populateClasses() {
            const sessionId = $('#filterSession').val();
            $('#filterClass').html('<option value="">All Classes</option>');
            $('#filterSubject').html('<option value="">All Subjects</option>');
            $('#filterStudent').html('<option value="">Select Student</option>');

            if (sessionId) {
                $.ajax({
                    url: "{{ route('admin.report.getClassesBySession') }}",
                    method: 'GET',
                    data: { session_id: sessionId },
                    success: function (res) {
                        let options = '<option value="">All Classes</option>';
                        $.each(res.classes, function (i, c) {
                            options += `<option value="${c.id}">${c.name}</option>`;
                        });
                        $('#filterClass').html(options);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error fetching classes:", textStatus, errorThrown, jqXHR);
                        alert('Failed to load classes.');
                    }
                });
            }
        }

       
        function populateSubjects() {
            const sessionId = $('#filterSession').val();
            const classId = $('#filterClass').val();
            $('#filterSubject').html('<option value="">All Subjects</option>');
            $('#filterStudent').html('<option value="">Select Student</option>');

            if (sessionId && classId && classId !== '') { 
                $.ajax({
                    url: "{{ route('admin.report.getSubjectsByClassAndSession') }}",
                    method: 'GET',
                    data: {
                        session_id: sessionId,
                        class_id: classId
                    },
                    success: function (res) {
                        let options = '<option value="">All Subjects</option>';
                        $.each(res.subjects, function (i, s) {
                            options += `<option value="${s.id}">${s.name}</option>`;
                        });
                        $('#filterSubject').html(options);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error fetching subjects:", textStatus, errorThrown, jqXHR);
                        alert('Failed to load subjects.');
                    }
                });
            }
        }

        function populateStudents() {
            const sessionId = $('#filterSession').val();
            const classId = $('#filterClass').val();
            $('#filterStudent').html('<option value="">Select Student</option>'); 

       
            if (sessionId && sessionId !== '' && classId && classId !== '') {
                $.ajax({
                    url: "{{ route('admin.report.getStudentsByClassAndSession') }}",
                    method: 'GET',
                    data: {
                        session_id: sessionId,
                        class_id: classId
                    },
                    success: function (res) {
                        let options = '<option value="">Select Student</option>';
                        $.each(res.students, function (i, s) {
                            options += `<option value="${s.id}">${s.name}</option>`;
                        });
                        $('#filterStudent').html(options);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error("AJAX Error fetching students:", textStatus, errorThrown, jqXHR);
                        alert('Failed to load students.');
                    }
                });
            }
        }

        // function fetchStudentReportCard() {
        //     const sessionId = $('#filterSession').val();
        //     const classId = $('#filterClass').val();
        //     const studentId = $('#filterStudent').val();
        //     const subjectId = $('#filterSubject').val(); 

     
        //     $('#studentReportCardContainer').hide();
        //     $('#selectStudentMessage').show();
        //     $('#studentReportErrorMessage').hide().text('');

         
        //     if (!sessionId || sessionId === '' || !classId || classId === '' || !studentId || studentId === '') {
        //         $('#selectStudentMessage').show(); 
        //         return;
        //     } else {
        //         $('#selectStudentMessage').hide(); 
        //     }

        //     $.ajax({
        //         url: "{{ route('admin.report.getStudentReportCard') }}",
        //         method: 'GET',
        //         data: {
        //             session_id: sessionId,
        //             class_id: classId,
        //             student_id: studentId,
        //             subject_id: subjectId 
        //         },
        //         success: function (res) {
        //             console.log("Student Report Card Response:", res);
        //             if (res.success && res.data) {
        //                 const data = res.data;
        //                 $('#reportStudentName').text(data.student_name);
        //                 $('#reportSessionName').text(data.session_name);
        //                 $('#reportClassName').text(data.class_name);
        //                 $('#reportRollNumber').text(data.roll_number || 'N/A');

        //                 let marksHtml = '';
        //                 if (data.marks.length > 0) {
        //                     $.each(data.marks, function(i, mark) {
        //                         marksHtml += `
        //                             <tr>
        //                                 <td>${mark.subject}</td>
        //                                 <td>${mark.mid_term_marks || 'N/A'}/${mark.mid_term_out_off || 'N/A'}</td>
        //                                 <td>${mark.final_exam_marks || 'N/A'}/${mark.final_exam_out_off || 'N/A'}</td>
        //                                 <td><span class="badge bg-${mark.status === 'Pass' ? 'success' : 'danger'}">${mark.status}</span></td>
        //                             </tr>
        //                         `;
        //                     });
        //                 } else {
        //                     marksHtml = '<tr><td colspan="4" class="text-center">No marks recorded for this student in the selected criteria.</td></tr>';
        //                 }
        //                 $('#studentMarksTableBody').html(marksHtml);

                     
        //                 $('#reportTotalMarks').text(data.summary.total_marks_obtained || 'N/A');
        //                 $('#reportAveragePercentage').text(data.summary.overall_percentage || '0');
        //                 $('#reportOverallStatus').html(`<span class="badge bg-${data.summary.overall_status === 'Pass' ? 'success' : 'danger'}">${data.summary.overall_status}</span>`);

        //                 $('#studentReportCardContainer').show();
        //             } else {
        //                 $('#studentReportErrorMessage').text(res.message || 'Failed to load student report card.').show();
        //             }
        //         },
        //         error: function (jqXHR, textStatus, errorThrown) {
        //             console.error("AJAX Error fetching student report card:", textStatus, errorThrown, jqXHR);
        //             $('#studentReportErrorMessage').text('Error fetching report card. Check console for details.').show();
        //         }
        //     });
        // }

        function fetchStudentReportCard() {
            const sessionId = $('#filterSession').val();
            const classId = $('#filterClass').val();
            const studentId = $('#filterStudent').val();
            const subjectId = $('#filterSubject').val();

            $('#studentReportCardContainer').hide();
            $('#selectStudentMessage').show();
            $('#studentReportErrorMessage').hide().text('');

            if (!sessionId || sessionId === '' || !classId || classId === '' || !studentId || studentId === '') {
                $('#selectStudentMessage').show();
                return;
            } else {
                $('#selectStudentMessage').hide();
            }

            $.ajax({
                url: "{{ route('admin.report.getStudentReportCard') }}",
                method: 'GET',
                data: {
                    session_id: sessionId,
                    class_id: classId,
                    student_id: studentId,
                    subject_id: subjectId
                },
                success: function (res) {
                    console.log("Student Report Card Response:", res);
                    if (res.success && res.data) {
                        const data = res.data;
                        $('#reportStudentName').text(data.student_name);
                        $('#reportSessionName').text(data.session_name);
                        $('#reportClassName').text(data.class_name);
                        $('#reportRollNumber').text(data.roll_number || 'N/A');

                        let marksHtml = '';
                        if (data.marks.length > 0) {
                            $.each(data.marks, function(i, mark) {
                                marksHtml += `
                                    <tr>
                                        <td>${mark.subject}</td>
                                        <td>${mark.mid_term_marks || 'N/A'}/${mark.mid_term_out_off || 'N/A'}</td>
                                        <td>${mark.final_exam_marks || 'N/A'}/${mark.final_exam_out_off || 'N/A'}</td>
                                        <td><span class="badge bg-${mark.status === 'Pass' ? 'success' : 'danger'}">${mark.status}</span></td>
                                    </tr>
                                `;
                            });
                        } else {
                            marksHtml = '<tr><td colspan="4" class="text-center">No marks recorded for this student in the selected criteria.</td></tr>';
                        }
                        $('#studentMarksTableBody').html(marksHtml);
                        $('#reportTotalMarks').text(data.summary.total_marks_obtained); 
                        $('#reportAveragePercentage').text(data.summary.overall_percentage || '0'); 
                        $('#reportOverallStatus').html(`<span class="badge bg-${data.summary.overall_status === 'Pass' ? 'success' : 'danger'}">${data.summary.overall_status}</span>`);

                        $('#studentReportCardContainer').show();
                    } else {
                        $('#studentReportErrorMessage').text(res.message || 'Failed to load student report card.').show();
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error fetching student report card:", textStatus, errorThrown, jqXHR);
                    $('#studentReportErrorMessage').text('Error fetching report card. Check console for details.').show();
                }
            });
        }


   
        $('#filterSession').on('change', function () {
            if ($(this).val()) {
                populateClasses();
                populateStudents();
            } else {
                $('#filterClass').html('<option value="">All Classes</option>');
                $('#filterSubject').html('<option value="">All Subjects</option>');
                $('#filterStudent').html('<option value="">Select Student</option>');
            }
            if ($('#studentReportTab').hasClass('active')) {
                fetchStudentReportCard();
            } else {
                fetchChartData();
            }
        });

        $('#filterClass').on('change', function () {
            populateSubjects();
            populateStudents();
            
            if ($('#studentReportTab').hasClass('active')) {
                fetchStudentReportCard();
            } else {
                fetchChartData();
            }
        });

        $('#filterSubject').on('change', function () {
         
            if ($('#studentReportTab').hasClass('active')) {
                fetchStudentReportCard();
            } else {
                fetchChartData(); 
            }
        });

        
        $('#filterStudent').on('change', function () {
            if ($('#studentReportTab').hasClass('active')) {
                fetchStudentReportCard();
            }
        });

  
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.id === 'classComparisonTab') {
                fetchChartData();
            } else if (e.target.id === 'studentReportTab') {
                populateStudents();
                fetchStudentReportCard(); 
            }
        });

        
        fetchChartData(); 
     
        $('#studentReportCardContainer').hide();
        $('#selectStudentMessage').show();
    });

    function updateExportLink() {
        const sessionId = $('#filterSession').val();
        const classId = $('#filterClass').val();
        const studentId = $('#filterStudent').val();
        const subjectId = $('#filterSubject').val();

        const url = `{{ route('admin.report.export') }}?session_id=${sessionId}&class_id=${classId}&student_id=${studentId}&subject_id=${subjectId}`;
        $('#exportReportBtn').attr('href', url);
    }

    $('#filterSession, #filterClass, #filterStudent, #filterSubject').on('change', updateExportLink);

    $(document).ready(updateExportLink);

    </script>
@endsection