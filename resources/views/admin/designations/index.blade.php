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

    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Designation List</h5>
            </div>
            <div class="px-3 py-2">
                <form method="GET" action="">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end">
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

            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($designations as $item)
                                <tr>
                                    <td>{{ $item->name}}</td>
                                    <td>
                                        <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                                            <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{$item->id}}"
                                            {{ $item->status ? 'checked' : ''}} onclick="statusToggle('{{route('admin.designation.status', $item->id)}}', this)">
                                            <label class="form-check-label" for="customSwitch{{$item->id}}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);">
                                            <span class="badge bg-label-danger mb-0 cursor-pointer">Permission</span>
                                        </a>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-icon btn-outline-dark"                     
                                            data-bs-toggle="tooltip"  title="Edit">
                                            <i class="ri-pencil-line"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3">No Designation found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- {{ $subjects->links() }} --}}
                </div>
            </div>
        </div>
    </div>

@endsection

