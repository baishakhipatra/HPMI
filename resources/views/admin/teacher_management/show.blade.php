<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<!-- Select2 JS -->
@extends('layouts/contentNavbarLayout')

@section('title', 'Show - Teacher')

@section('content')

<section class="content">
    <section class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header">
                        <h5 class="border-bottom text-capitalize pb-4 mt-4 mb-8">Teacher Details</h5>
                        
                    {{-- </div>

                    <div class="card-body pt-12"> --}}

                        <ul class="list-unstyled mb-4">

                        <li class="mb-2">
                            <span class="h6 me-1">Full Name:</span>
                            <span>{{ ucwords($teacher->name ?? 'N/A') }}</span>
                        </li>

                        <li class="mb-2">
                            <span class="h6 me-1">Address:</span>
                            <span>{{ ucwords($teacher->address ?? 'N/A') }}</span>
                        </li>

                        <li class="mb-2">
                            <span class="h6 me-1">Date of Birth:</span>
                            <span>{{date('d-m-Y',strtotime($teacher->date_of_birth))}}</span>
                            
                        </li>

                        <li class="mb-2">
                            <span class="h6 me-1">Date of Joining:</span>
                            <span>{{date('d-m-Y',strtotime($teacher->date_of_joining))}}</span>
                        </li>

                        <li class="mb-2">
                            <span class="h6 me-1">Qualifications:</span>
                            <span>{{ ucwords($teacher->qualifications ?? 'N/A') }}</span>
                        </li>

                        </ul>

                        {{-- Back Button --}}
                        <div class="text-end">
                        <a href="{{ route('admin.teacher.index') }}" class="btn btn-sm btn-danger">
                            <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</section>

@endsection