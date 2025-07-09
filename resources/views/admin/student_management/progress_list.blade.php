<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@extends('layouts/contentNavbarLayout')

@section('title', 'Progress Marking Categories')

@section('content')
    <div class="container-fluid">
    <div class="row">
        <div class="col-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-primary">Progress Marking Categories</h5>
            </div>
            <div class="card-body">
            <table class="table table-sm table-hover">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Field</th>
                        <th>Values</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @php $grouped = $progressList->groupBy('field'); @endphp
                @forelse ($grouped as $field => $items)
                @php $first = $items->first(); @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                        <p class="text-muted mb-0">{{ ucwords($field) }}</p>
                        </td>
                        <td class="text-center">
                        <div class="d-flex flex-column align-items-center">
                            @foreach($items as $item)
                            <span class="badge bg-dark-primary text-primary mb-1">{{ ucwords($item->value) }}</span>
                            @endforeach
                        </div>
                        </td>
                        <td class="text-center">
                            <div class="form-check form-switch" data-bs-toggle="tooltip" title="Toggle status">
                                <input class="form-check-input ms-auto" type="checkbox" id="customSwitch{{ $first->id }}"
                                {{ $first->status ? 'checked' : '' }}
                                onclick="statusToggle('{{ route('admin.student.progressstatus', $first->id) }}', this)">
                                <label class="form-check-label" for="customSwitch{{ $first->id }}"></label>
                            </div>
                        </td>

                        <td>
                            <div class="btn-group" role="group" aria-label="Action Buttons">
                                {{-- Edit Button --}}
                                <a href="{{ route('admin.student.progresslist', ['update_id' => $first->id]) }}"
                                class="btn btn-sm btn-icon btn-outline-dark" data-bs-toggle="tooltip" title="Edit">
                                <i class="ri-pencil-line"></i>
                                </a>

                                {{-- Delete Button --}}
                                <button type="button"
                                    class="btn btn-sm btn-icon btn-outline-danger" onclick="deleteItem({{ $first->id }})"                              
                                    data-bs-toggle="tooltip" title="Delete">                                   
                                    <i class="ri-delete-bin-6-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                <tr>
                    <td colspan="100%" class="text-center">No records found</td>
                </tr>
                @endforelse
                </tbody>
            </table>
                <div class="d-flex justify-content-center">
                    {{-- {{ $progressList->links() }} --}}
                </div>  
            </div>
        </div>
        </div>


        <div class="col-4">
            @if(isset($updateData))
            <div class="card">
                <div class="card-header">
                <h4>Edit Progress Field & Values</h4>
                </div>
                <div class="card-body">
                <form action="{{ route('admin.student.progressupdate', [$updateData->id]) }}" method="post">
                    @csrf

                    <div class="form-floating form-floating-outline mb-3">
                    <input type="text" name="field" class="form-control" placeholder="Enter Field Name" value="{{ ucwords(old('field', $updateData->field)) }}">
                    <label>Field Name</label>
                    @error('field') <p class="text-danger small">{{ $message }}</p> @enderror
                    </div>

                    <input type="hidden" name="deleted_value_ids" id="deleted_value_ids" value="">

                    @foreach($updateDataValues as $valueItem)
                    <div class="input-group mb-2 existing-value">
                    <div class="form-floating form-floating-outline flex-grow-1">
                        <input type="text" name="existing_value[{{ $valueItem->id }}]" class="form-control" value="{{ ucwords($valueItem->value) }}" placeholder="Enter Value">
                        <label>Value</label>
                    </div>
                    <button type="button" class="btn btn-outline-danger remove-existing-value ms-2" data-id="{{ $valueItem->id }}">
                        <i class="ri-close-line"></i>
                    </button>
                    </div>
                    @endforeach

                    <div id="value-container">
                    <div class="input-group mb-2">
                        <div class="form-floating form-floating-outline flex-grow-1">
                        <input type="text" name="value[]" class="form-control" placeholder="Enter Value">
                        <label>Value</label>
                        </div>
                        <button type="button" class="btn btn-outline-secondary add-value ms-2">
                        <i class="ri-add-line"></i>
                        </button>
                    </div>
                    </div>

                    <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.student.progresslist') }}" class="btn btn-danger">Back</a>
                    <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
                </div>
            </div>

            @else

            <div class="card">
                <div class="card-header">
                    <h5>Add Progress Field & Values</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.student.progressstore') }}" method="post">
                        @csrf

                        {{-- <div class="form-floating form-floating-outline mb-3">
                            <input type="text" name="field" class="form-control" placeholder="Enter Field Name">
                            <label>Field Name</label>
                            @error('field') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div> --}}

                        <div class="form-floating form-floating-outline mb-3">
                            <select name="field" id="field" class="form-select">
                            <option value="identified condition">Identified Condition</option>
                            <option value="behavioural cognitive outcomes">Behavioural Cognitive Outcomes</option>
                            </select>
                            <label for="field" class="form-label">Field Name</label>
                            @error('field') <p class="text-danger small">{{ $message }}</p> @enderror
                        </div>

                        <div id="value-container">
                            @foreach(old('value', ['']) as $index => $value)
                                <div class="input-group mb-2">
                                    <div class="form-floating form-floating-outline flex-grow-1">
                                    <input type="text" name="value[]" class="form-control" placeholder="Enter Value" value="{{ $value }}">
                                    <label>Value</label>
                                    @error("value.$index")
                                    <p class="small text-danger"> {{ $message }} </p>
                                    @enderror
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary add-value ms-2">
                                    <i class="ri-add-line"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <template id="value-template">
            <div class="input-group mb-2">
                <div class="form-floating form-floating-outline flex-grow-1">
                    <input type="text" name="value[]" class="form-control" placeholder="Enter Value">
                    <label>Value</label>
                </div>
                <button type="button" class="btn btn-outline-danger remove-value ms-2">
                    <i class="ri-close-line"></i>
                </button>
            </div>
        </template>
    </div>
    </div>
@endsection

<script>
    $(document).ready(function () {
        let deletedValueIds = [];

        $(document).on('click', '.add-value', function () {
        let template = $('#value-template').html();
        $('#value-container').append(template);
        });

        $(document).on('click', '.remove-value', function () {
        $(this).closest('.input-group').remove();
        });

        $(document).on('click', '.remove-existing-value', function () {
        let id = $(this).data('id');
        deletedValueIds.push(id);
        $('#deleted_value_ids').val(deletedValueIds.join(','));
        $(this).closest('.input-group').remove();
        });
    });

    function statusToggle(url, checkbox) {
        $.get(url, function (res) {
            if (res.status !== 200) {
            $(checkbox).prop('checked', !$(checkbox).prop('checked'));
            toastFire('error', 'Failed to update status.');
            } else {
            toastFire('success', 'Status updated.');
            }
        });
    }

    function deleteItem(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
            $.post("{{ route('admin.student.progressdelete') }}", {
                _token: '{{ csrf_token() }}',
                id: id
            }, function (res) {
                if (res.status === 200) {
                toastFire('success', res.message);
                location.reload();
                } else {
                toastFire('error', res.message);
                }
            });
            }
        });
    }

</script>