

@extends('layouts/contentNavbarLayout')

@section('title', 'Dashboard')

@section('vendor-style')
@vite('resources/assets/vendor/libs/apex-charts/apex-charts.scss')
@endsection

@section('vendor-script')
@vite('resources/assets/vendor/libs/apex-charts/apexcharts.js')
@endsection


@section('content')
 <div class="container-xxl flex-grow-1 container-p-y">

  <h4 class="fw-bold py-3 mb-4">Welcome to Holy Palace Multipurpose Institute</h4>

  <div class="row">
    <!-- Total Students -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
      <div class="card bg-primary text-white">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <small class="text-white">Total Students</small>
            <h4 class="text-white fw-bold mb-0">{{ $totalStudents ?? 0 }}</h4>
          </div>
          <i class="ri-group-line fs-2 text-white"></i>
        </div>
      </div>
    </div>

    <!-- Total Classes -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
      <div class="card bg-success text-white">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <small class="text-white">Total Classes</small>
            <h4 class="text-white fw-bold mb-0">{{ $totalClasses ?? 0 }}</h4>
          </div>
          <i class="ri-book-open-line fs-2 text-white"></i>
        </div>
      </div>
    </div>

    <!-- Teachers -->
    <div class="col-lg-4 col-md-6 col-12 mb-4">
      <div class="card bg-purple text-white" style="background-color: #9b59b6;">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <small class="text-white">Teachers</small>
            <h4 class="text-white fw-bold mb-0">{{ $totalTeachers ?? 0 }}</h4>
          </div>
          <i class="ri-line-chart-line fs-2 text-white"></i>
        </div>
      </div>
    </div>

  </div>

  <div class="card">
  <div class="card-header">
    <h5 class="mb-0">Student Admission Chart</h5>
  </div>
  <div class="card-body">
    <canvas id="admissionChart" height="120"></canvas>
  </div>
</div>

</div>
@endsection
@section('scripts')
@vite('resources/assets/js/dashboards-analytics.js')

<script>
  $(document).ready(function () {
    $.ajax({
      url: "{{ route('admin.dashboard.chart-data') }}",
      type: "GET",
      dataType: "json",
      success: function (response) {
        const ctx = document.getElementById('admissionChart').getContext('2d');
        new Chart(ctx, {
          type: 'bar',
          data: {
            labels: response.labels,
            datasets: [{
              label: 'Total Admissions',
              data: response.data,
              backgroundColor: '#42a5f5',
              borderColor: '#1e88e5',
              borderWidth: 1,
              borderRadius: 5
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                }
              }
            }
          }
        });
      },
      error: function (xhr) {
        console.error('Error fetching chart data:', xhr);
      }
    });
  });
</script>

@endsection
