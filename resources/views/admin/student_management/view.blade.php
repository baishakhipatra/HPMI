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
                        <h5 class="border-bottom text-capitalize pb-4 mt-4 mb-8">Student Details</h5>
                        
                    {{-- </div>

                    <div class="card-body pt-12"> --}}

                        <ul class="list-unstyled mb-4">

                            <li class="mb-2">
                                <span class="h6 me-1">Student Photo:</span>
                                @if($student->image)
                                    <img src="{{ asset($student->image) }}" alt="Student Photo" width="100">
                                @else
                                    <span>N/A</span>
                                @endif
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Full Name:</span>
                                <span>{{ ucwords($student->student_name ?? 'N/A') }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Date of Birth:</span>
                                <span>{{date('d-m-Y',strtotime($student->date_of_birth))}}</span>                               
                            </li>

                            
                            <li class="mb-2">
                                <span class="h6 me-1">Gender:</span>
                                <span>{{ $student->gender ?? 'N/A' }}</span>
                            </li>


                            <li class="mb-2">
                                <span class="h6 me-1">Aadhaar No.:</span>
                                <span>{{ $student->aadhar_no ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Blood Group:</span>
                                <span>{{ $student->blood_group ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Height:</span>
                                <span>{{ $student->height ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Weight:</span>
                                <span>{{ $student->weight ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Father's Name:</span>
                                <span>{{ ucwords($student->father_name ?? 'N/A') }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Mother's Name:</span>
                                <span>{{ ucwords($student->mother_name ?? 'N/A') }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Guardian's Name:</span>
                                <span>{{ ucwords($student->parent_name ?? 'N/A') }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Email address:</span>
                                <span>{{ $student->email ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Contact Number:</span>
                                <span>{{ $student->phone_number ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Student Address:</span>
                                <span>{{ ucwords($student->address ?? 'N/A') }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Divyang :</span>
                                <span>{{ $student->divyang ?? 'N/A' }}</span>
                            </li>

                            {{-- <li class="mb-2">
                                <span class="h6 me-1">Admission Date:</span>
                                <span>{{ optional($student->admission)->admission_date ? date('d-m-Y', strtotime($student->admission->admission_date)) : 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Class:</span>
                                <span>{{ optional($student->admission->classList)->class ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Section:</span>
                                <span>{{ $student->admission->section ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Roll Number:</span>
                                <span>{{ $student->admission->roll_number ?? 'N/A' }}</span>
                            </li>

                            <li class="mb-2">
                                <span class="h6 me-1">Academic Session:</span>
                                <span>{{ optional($student->admission->session)->session_name ?? 'N/A' }}</span>
                            </li> --}}

                        </ul>

                        {{-- Back Button --}}
                        <div class="text-end">
                        <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-danger">
                            <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back</a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>
</section>

@endsection