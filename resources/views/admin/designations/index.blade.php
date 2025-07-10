<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add your toastFire function if it's custom -->


@extends('layouts/contentNavbarLayout')

@section('title', 'Designation - List')

@section('content')

   @if(session('success'))
      <div class="alert alert-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">Designation List</h4>
                    <div class="px-3 py-2 col-8">
                        <form method="GET" action="">
                            <div class="row align-items-center">
                                <div class="col-md-12 p-0">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <div class="form-group me-2 mb-0">
                                            <input type="search" class="form-control form-control-sm" name="keyword" id="keyword" 
                                                value="{{ request()->input('keyword') }}" placeholder="Search something...">
                                        </div>
                                        <div class="form-group mb-0">
                                        <div class="btn-group">
                                            <button type="submit" class="btn btn-sm btn-primary">
                                                <i class="ri-filter-3-line"></i>
                                            </button>
                                            <a href="{{ url()->current() }}" class="btn btn-sm btn-light" title="Clear filter">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead>
                                <tr class="text-center">
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @forelse($designations as $item)
                                    <tr>
                                        <td class="text-center">{{ ucwords($item->name)}}</td>
                                        <td class="text-center align-middle">
                                            <div class="d-flex justify-content-center align-items-center">
                                                <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                                                    <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$item->id}}"
                                                        {{ $item->status ? 'checked' : '' }} 
                                                        onclick="statusToggle('{{ route('admin.designation.status', $item->id) }}', this)">
                                                    <label class="form-check-label" for="customSwitch{{$item->id}}"></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.designation.permissions', $item->id) }}">
                                                <span class="badge bg-label-danger mb-0 cursor-pointer">Permission</span>
                                            </a>
                                            <a href="{{ route('admin.designation.list', ['edit' => $item->id]) }}" class="btn btn-sm btn-icon btn-outline-dark" title="Edit">
                                                <i class="ri-pencil-line"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3">No Designation found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $designations->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- create designation --}}
        <div class="col-md-4">
            @if(!$editableDesignationDetails)
                {{-- Add Designation --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Add Designation</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.designation.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="name" class="form-control" placeholder="Designation Name" value="{{ old('name') }}">
                                    <label>Designation Name</label>
                                    @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary d-block ">Create</button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Edit Designation --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Edit Designation</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.designation.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id" value="{{ $editableDesignationDetails->id }}">

                            <div class="mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" name="name" class="form-control" placeholder="Designation Name" value="{{ old('name', $editableDesignationDetails->name) }}">
                                    <label>Designation Name</label>
                                    @error('name') <p class="text-danger small">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.designation.list') }}" class="btn btn-danger">
                                    <i class="ri-arrow-left-line"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>

    </div>
@endsection
