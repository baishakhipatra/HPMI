

@extends('layouts/contentNavbarLayout')

@section('title', 'Teacher - Details')

@section('content')

<section class="content">
    <section class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card Header -->
                    <div class="card-header mb-3">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-auto">
                                <h4 class="mb-0 text-primary">Teacher Details</h5>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('admin.teacher.index') }}" class="btn btn-sm btn-danger">
                                    <i class="menu-icon tf-icons ri-arrow-left-line"></i>Back
                                </a>
                            </div>
                        </div>
                        
                    </div>

                    <div class="card-body">

                        <ul class="common-list grid-list mb-4">

                            <li>
                                <span class="h6 me-1 label-color">Full Name:</span>
                                <span>{{ ucwords($teacher->name ?? 'N/A') }}</span>
                            </li>

                            <li>
                                <span class="h6 me-1 label-color">Address:</span>
                                <span>{{ ucwords($teacher->address ?? 'N/A') }}</span>
                            </li>

                            <li>
                                <span class="h6 me-1 label-color">Date of Birth:</span>
                                <span>{{date('d-m-Y',strtotime($teacher->date_of_birth))}}</span>
                                
                            </li>

                            <li>
                                <span class="h6 me-1 label-color">Date of Joining:</span>
                                <span>{{date('d-m-Y',strtotime($teacher->date_of_joining))}}</span>
                            </li>

                            <li>
                                <span class="h6 me-1 label-color">Qualifications:</span>
                                <span>{{ ucwords($teacher->qualifications ?? 'N/A') }}</span>
                            </li>

                            <li>
                                <span class="h6 me-1 label-color">Classes:</span>
                                @if(!empty($classes))
                                    <ul class="mb-0">
                                        @foreach($classes as $eachClassItem)
                                            <li>{{ $eachClassItem }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span>-</span>
                                @endif
                            </li>

                            <li>
                                <span class="h6 me-1 label-color">Subjects (Class-wise):</span>
                                @if(!empty($subjects))
                                    <ul class="mb-0">
                                        @foreach($subjects as $eachSubjectItem)
                                            <li>{{ $eachSubjectItem }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span>-</span>
                                @endif
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