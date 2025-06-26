<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Progress Chart')

@section('content')


<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">Progress Charts</h3>
            <small class="text-muted">Visual representation of student performance trends</small>
        </div>

    </div>
    <div class="row mb-4">
        <div class="form-floating form-floating-outline col-md-3">
            <select id="chart_type" class="form-select">
                <option value="academic">Academic Performance</option>
                <option value="qualitative">Qualitative Progress</option>
            </select>
            <label for="chart_type">Chart Type</label>
        </div>
        {{-- Session Dropdown --}}
        <div class="form-floating form-floating-outline col-md-2">

            <select id="session_id" class="form-select">
                <option value="">Select Session</option>
                @foreach($sessions as $session)
                    <option value="{{ $session['id'] }}">{{ $session['name'] }}</option>
                @endforeach
            </select>
            <label for="session_id">Session</label>
        </div>

        {{-- Student Dropdown (loaded via AJAX) --}}
        <div class="form-floating form-floating-outline col-md-2">
            <select id="student_id" class="form-select">
                <option value="">Select Student</option>
            </select>
            <label for="student_id">Student</label>
        </div>

        {{-- Class Dropdown (based on student + session) --}}
        <div class="form-floating form-floating-outline col-md-2">
            <select id="class_id" class="form-select">
                <option value="">Select Class</option>
            </select>
            <label for="class_id">Class</label>
        </div>

        {{-- Subject Dropdown (based on class) --}}
        <div class="form-floating form-floating-outline col-md-2">
            <select id="subject_id" class="form-select">
                <option value="">Select Subject</option>
            </select>
            <label for="subject_id">Subject</label>
        </div>

    </div>

    <div id="academicCharts" class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3">Academic Performance Trend</h5>
                <canvas id="lineChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3">Subject-wise Performance</h5>
                <canvas id="barChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div id="qualitativeCharts" class="row mb-4 d-none">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3">Qualitative Progress Trend</h5>
                <canvas id="qualLineChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3">Current Progress Assessment</h5>
                <canvas id="radarChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm">
                <p class="text-muted mb-1"> Students Tracked</p>
                <h4 id="studentsTracked">0</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm">
                <p class="text-muted mb-1"> Subjects Monitored</p>
                <h4 id="subjectsMonitored">0</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm">
                <p class="text-muted mb-1"> Avg Performance</p>
                <h4 id="avgPerformance">0%</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center p-3 shadow-sm">
                <p class="text-muted mb-1"> Avg Progress</p>
                <h4 id="avgProgress">0%</h4>
            </div>
        </div>
    </div>
</div>

@endsection
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    //for fetch session,student, subject, class
    $(document).ready(function () {
        //  Load students when session changes
        $('#session_id').change(function () {
            const sessionId = $(this).val();

            // Clear and disable dependent dropdowns
            $('#student_id').empty().append('<option value="">Loading...</option>');
            $('#class_id').empty().append('<option value="">Select Class</option>').prop('disabled', false);
            $('#subject_id').empty().append('<option value="">Select Subject</option>').prop('disabled', false);

            if (!sessionId) return;

            $.ajax({
                url: '{{ route("admin.getStudentsBySession") }}',
                type: 'GET',
                data: { sessionId },
                success: function (res) {
                    $('#student_id').empty().append('<option value="">Select Student</option>');
                    if (res.success) {
                        res.students.forEach(stu => {
                            $('#student_id').append(`<option value="${stu.id}">${stu.name}</option>`);
                        });
                    }
                }
            });
        });

        //  Load class + subjects when student changes
        $('#student_id').change(function () {
            const sessionId = $('#session_id').val();
            const studentId = $(this).val();

            $('#class_id').empty().append('<option value="">Loading...</option>');
            $('#subject_id').empty().append('<option value="">Loading...</option>');

            if (!sessionId || !studentId) return;

            $.ajax({
                url: '{{ route("admin.getClassBySessionAndStudent") }}',
                type: 'GET',
                data: {
                    session_id: sessionId,
                    student_id: studentId
                },
                success: function (res) {
                    $('#class_id').empty().append('<option value="">Select Class</option>').prop('disabled', false);
                    $('#subject_id').empty().append('<option value="">Select Subject</option>').prop('disabled', false);

                    if (res.success) {
                        res.classes.forEach(cls => {
                            $('#class_id').append(`<option value="${cls.id}">${cls.name}</option>`);
                        });

                        res.subjects.forEach(sub => {
                            $('#subject_id').append(`<option value="${sub.id}">${sub.name}</option>`);
                        });
                    }
                }
            });
        });
    });
    let lineChart, barChart, qualLineChart, radarChart;

    function fetchChartData() {
        const chartType = $('#chart_type').val();

        $.ajax({
            url: "{{ route('admin.fetchchartdata') }}",
            type: "GET",
            data: {
                chart_type: chartType,
                session_id: $('#session_id').val(),
                class_id: $('#class_id').val(),
                subject_id: $('#subject_id').val(),
                student_id: $('#student_id').val(),
                time_period: 'last_6_months'
            },
            success: function(response) {
                if (chartType === 'academic') {
                    $('#academicCharts').removeClass('d-none');
                    $('#qualitativeCharts').addClass('d-none');
                    updateLineChart(response.trend);
                    updateBarChart(response.subjectPerformance);
                } else {
                    $('#qualitativeCharts').removeClass('d-none');
                    $('#academicCharts').addClass('d-none');
                    updateQualitativeLineChart(response.qualitativeTrend);
                    updateRadarChart(response.assessment);
                }
                updateStats(response.stats);
            },
            error: function(err) {
                console.log("Error loading data", err);
            }
        });
    }

    function updateLineChart(data) {
        if (lineChart) lineChart.destroy();
        lineChart = new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Average Marks',
                    data: Object.values(data),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    tension: 0.4,
                    fill: true
                }]
            }
        });
    }

    function updateBarChart(data) {
        if (barChart) barChart.destroy();
        barChart = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Average Marks',
                    data: Object.values(data),
                    backgroundColor: '#36b9cc'
                }]
            }
        });
    }

    function updateQualitativeLineChart(data) {
        if (qualLineChart) qualLineChart.destroy();
        qualLineChart = new Chart(document.getElementById('qualLineChart'), {
            type: 'line',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Qualitative Score',
                    data: Object.values(data),
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            }
        });
    }

    function updateRadarChart(data) {
        if (radarChart) radarChart.destroy();
        radarChart = new Chart(document.getElementById('radarChart'), {
            type: 'radar',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Current Assessment',
                    data: Object.values(data),
                    backgroundColor: 'rgba(251, 191, 36, 0.3)',
                    borderColor: '#f59e0b'
                }]
            },
            options: {
                scales: {
                    r: {
                        suggestedMin: 0,
                        suggestedMax: 10
                    }
                }
            }
        });
    }

    function updateStats(stats) {
        $('#studentsTracked').text(stats.students_tracked);
        $('#subjectsMonitored').text(stats.subjects_monitored);
        $('#avgPerformance').text(stats.avg_performance + '%');
        $('#avgProgress').text(stats.avg_progress + '%');
    }

    $(document).ready(function () {
        fetchChartData();
        $('#session_id, #class_id, #subject_id, #student_id, #chart_type').on('change', fetchChartData);
    });
</script>