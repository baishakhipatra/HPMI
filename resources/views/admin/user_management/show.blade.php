
@extends('layouts/contentNavbarLayout')

@section('title', 'Employee - Details')

@section('content')

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <!-- Card Header -->
                        <div class="card-header">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-auto">
                                    <h4 class="fw-bold mb-0">Employee Details</h5>
                                </div>
                                
                                {{-- Back Button --}}
                                <div class="col-auto">
                                    <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-danger">
                                        <i class="tf-icons ri-arrow-left-line"></i>Back
                                    </a>
                                </div>
                            </div>
                            
                        </div>

                        <div class="card-body pt-12">

                            <ul class="list-unstyled grid-list mb-4">

                                <li class="mb-2">
                                    <span class="h6 me-1">Full Name:</span>
                                    <span>{{ ucwords($employee->name ?? 'N/A') }}</span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6 me-1">Designation:</span>
                                    <span>{{ ucwords($employee->designationData->name ?? 'N/A') }}</span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6 me-1">Address:</span>
                                    <span>{{ ucwords($employee->address ?? 'N/A') }}</span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6 me-1">Date of Birth:</span>
                                    <span>{{date('d-m-Y',strtotime($employee->date_of_birth))}}</span>
                                    
                                </li>

                                <li class="mb-2">
                                    <span class="h6 me-1">Date of Joining:</span>
                                    <span>{{date('d-m-Y',strtotime($employee->date_of_joining))}}</span>
                                </li>

                                <li class="mb-2">
                                    <span class="h6 me-1">Qualifications:</span>
                                    <span>{{ ucwords($employee->qualifications ?? 'N/A') }}</span>
                                </li>

                            </ul>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
@section('scripts')
@endsection