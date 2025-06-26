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
        <div>
            <a href="" 
                class="btn buttons-collection btn-outline-secondary dropdown-toggle waves-effect" 
                data-toggle="tooltip" title="Export Data">
                    Export
            </a>
        </div>
    </div>
    <div class="row mb-4">
        <div class="form-floating form-floating-outline col-md-3">
            <select id="session_id" class="form-select">
                <option value="">All Sessions</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                @endforeach
            </select>
            <label for="session_id">Session</label>
        </div>

        <div class="form-floating form-floating-outline col-md-3">
            <select id="class_id" class="form-select">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->class }}</option>
                @endforeach
            </select>
            <label for="class_id">Class</label>
        </div>

        <div class="form-floating form-floating-outline col-md-3">
            <select id="subject_id" class="form-select">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->sub_name }}</option>
                @endforeach
            </select>
            <label for="subject_id">Subject</label>
        </div>

        <div class="form-floating form-floating-outline col-md-3">
            <select id="student_id" class="form-select">
                <option value="">All Students</option>
                @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->student_name }}</option>
                @endforeach
            </select>
            <label for="student_id">Student</label>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3"> Academic Performance Trend</h5>
                <canvas id="lineChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 shadow-sm">
                <h5 class="mb-3"> Subject-wise Performance</h5>
                <canvas id="barChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Summary Stats --}}
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
    let lineChart, barChart;

    function fetchChartData() {
        $.get("{{ route('admin.fetchchartdata') }}", {
            session_id: $('#session_id').val(),
            class_id: $('#class_id').val(),
            subject_id: $('#subject_id').val(),
            student_id: $('#student_id').val(),
            time_period: 'last_6_months'
        }, function(response) {
            updateLineChart(response.trend);
            updateBarChart(response.subjectPerformance);
            updateStats(response.stats);
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

    function updateStats(stats) {
        $('#studentsTracked').text(stats.students_tracked);
        $('#subjectsMonitored').text(stats.subjects_monitored);
        $('#avgPerformance').text(stats.avg_performance + '%');
        $('#avgProgress').text(stats.avg_progress + '%');
    }

    $(document).ready(function () {
        fetchChartData();
        $('#session_id, #class_id, #subject_id, #student_id').on('change', fetchChartData);
    });
</script>