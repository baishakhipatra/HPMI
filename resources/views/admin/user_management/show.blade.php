
@extends('layouts/contentNavbarLayout')

@section('title', 'Employee - Details')

@section('content')

<section class="content">
    <section class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header">
                        <h4 class="mb-0 text-primary">Employee Details</h5>
                        
                        {{-- Back Button --}}
                        <div class="text-end">
                        <a href="{{ route('admin.employee.index') }}" class="btn btn-sm btn-danger">
                            <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back</a>
                        </div>
                        
                    {{-- </div>

                    <div class="card-body pt-12"> --}}

                        <ul class="list-unstyled mb-4">

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
    </section>
</section>

@endsection
@section('scripts')
@endsection