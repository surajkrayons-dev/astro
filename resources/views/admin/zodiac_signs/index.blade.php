@extends('layouts.master')

@section('title') Zodiac Signs @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">All Zodiac Signs</h4>

            <div class="page-title-right">
                <a href="javascript:void(0);" data-href="{{ route('admin.zodiac_signs.create.index') }}"
                    class="btn btn-soft-info waves-effect waves-light open-remote-modal">
                    <i class="fas fa-plus"></i> Create
                </a>
            </div>
        </div>
    </div>
</div>

{{-- FILTER SECTION --}}
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Filter</h4>
                <button id="reset-filter-btn" type="button" class="btn btn-light">
                    <i class="fa fa-undo"></i> Reset
                </button>
            </div>
            <div class="card-body">
                <div class="row">

                    <div class="col">
                        <label class="form-label fw-bold">Zodiac</label>
                        <select id="zodiac_id" class="form-control select2-class2" data-placeholder="Select Zodiac">
                            @foreach(\App\Models\ZodiacSign::all() as $zodiac)
                            <option value=""></option>
                            <option value="{{ $zodiac->id }}">
                                {{ $zodiac->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="col">
                        <label class="form-label fw-bold">Type</label>
                        <select id="type" class="form-control select2-class2" data-placeholder="Select Type">
                            <option value=""></option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="tomorrow">Tomorrow</option>
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div> --}}

                    <div class="col">
                        <div class="form-group">
                            <label for="status" class="form-label fw-bold">Status :</label>
                            <select class="form-control select2-class2" id="status" data-placeholder="Choose Status">
                                <option value=""></option>
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABLE --}}
<div class="row">
    <div class="col-12">
        <div class="card border">
            <div class="card-body">
                <table id="data-table" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {

    const reloadTable = () => {
        $('#data-table').DataTable().ajax.reload();
    };

    $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.zodiac_signs.list') }}",
            data: function(d) {
                d.zodiac_id = $('#zodiac_id').val();
                d.status = $('#status').val();
            }
        },
        columns: [{
                data: 'icon',
                name: 'icon',
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (!data) {
                        return '<span class="text-muted">N/A</span>';
                    }

                    return `<img src="${data}" width="30" height="30" style="object-fit:contain;" />`;
                }
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'slug',
                name: 'slug'
            },
            {
                data: 'status_text',
                name: 'status',
                className: 'text-center'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                                <a href="javascript:void(0);" data-href="{{ route('admin.zodiac_signs.update.index') }}/${row.id}" class="btn btn-soft-info btn-sm waves-effect waves-light open-remote-modal">
                                    <i class="bx bx-pencil font-size-16"></i>
                                </a>

                                <button class="btn btn-soft-danger btn-sm delete-entry"
                                    data-href="{{ route('admin.zodiac_signs.delete') }}/${row.id}">
                                    <i class="bx bx-trash"></i>
                                </button>
                        `;
                }
            }
        ]
    });

    $('#zodiac_id, #status').on('change', function() {
        reloadTable();
    });

    $('#reset-filter-btn').on('click', function() {
        $('#zodiac_id').val('').trigger('change');
        $('#status').val('1').trigger('change');
        reloadTable();
    });

});
</script>
@endsection