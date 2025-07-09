

@extends('layouts/contentNavbarLayout')

@section('title', 'Student - Details')

@section('content')

<section class="content">
    <section class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow border-0">
                    <!-- Card Header -->
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-primary">Student Details</h5>
                        <a href="{{ route('admin.studentlist') }}" class="btn btn-sm btn-danger">
                            <i class="ri-arrow-left-line"></i> Back
                        </a>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-4 text-center">
                                @if($student->image)
                                    <img src="{{ asset($student->image) }}" alt="Student Photo" class="img-fluid rounded shadow" style="max-height: 150px;">
                                @else
                                    <img src="{{ asset('assets/img/placeholder.jpg') }}" alt="no-image" class="img-fluid rounded shadow" style="max-height: 150px;">
                                @endif
                                <div class="mt-2 fw-semibold">{{ ucwords($student->student_name ?? 'N/A') }}</div>
                            </div>

                            <div class="col-md-9">
                                <div class="row">
                                    @php
                                        $details = [
                                            'Date of Birth'      => $student->date_of_birth ? date('d-m-Y', strtotime($student->date_of_birth)) : 'N/A',
                                            'Gender'             => $student->gender ?? 'N/A',
                                            'Aadhaar No.'        => $student->aadhar_no ?? 'N/A',
                                            'Blood Group'        => $student->blood_group ?? 'N/A',
                                            'Height'             => $student->height ?? 'N/A',
                                            'Weight'             => $student->weight ?? 'N/A',
                                            "Father's Name"      => ucwords($student->father_name ?? 'N/A'),
                                            "Mother's Name"      => ucwords($student->mother_name ?? 'N/A'),
                                            "Guardian's Name"    => ucwords($student->parent_name ?? 'N/A'),
                                            'Email Address'      => $student->email ?? 'N/A',
                                            'Contact Number'     => $student->phone_number ?? 'N/A',
                                            'Student Address'    => ucwords($student->address ?? 'N/A'),
                                            'Divyang'            => $student->divyang ?? 'N/A',
                                            'Admission Date'     => optional($student->admission)->admission_date ? date('d-m-Y', strtotime($student->admission->admission_date)) : 'N/A',
                                            'Class'              => strToUpper(optional($student->admission->class)->class ?? 'N/A'),
                                            'Section'            => strToUpper($student->admission->section ?? 'N/A'),
                                            'Roll Number'        => $student->admission->roll_number ?? 'N/A',
                                            'Academic Session'   => optional($student->admission->session)->session_name ?? 'N/A',
                                        ];
                                    @endphp

                                    @foreach($details as $label => $value)
                                        <div class="col-md-3 mb-3">
                                            <strong class="label-color">{{ $label }}:</strong>
                                            <div>{{ $value }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</section>

@endsection

@section('scripts')
@endsection