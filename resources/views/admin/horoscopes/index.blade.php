@extends('layouts.master')

@section('title') Horoscopes @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">All Horoscopes</h4>

            @if (Can::is_accessible('horoscope', 'create'))
            <div class="page-title-right">
                <a href="{{ route('admin.horoscopes.create') }}" class="btn btn-soft-info">
                    <i class="fas fa-plus"></i> Create
                </a>
            </div>
            @endif
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

                    <div class="col">
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
                    </div>

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
                            <th>Zodiac</th>
                            <th>Type</th>
                            <th>Title</th>
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

    $('#data-table').on('change', '.change-status', function(e) {
        e.preventDefault();

        const id = $(this).data("id");
        const checkbox = $(this);
        checkbox.prop("disabled", true);

        $.get(`{{ route('admin.horoscopes.change.status') }}/${id}`, function(response) {
            reloadTable('#data-table');
            checkbox.prop("disabled", false);
        }).fail(function() {
            checkbox.prop("disabled", false);
        });
    });

    $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.horoscopes.list') }}",
            data: function(d) {
                d.zodiac_id = $('#zodiac_id').val();
                d.type = $('#type').val();
                d.status = $('#status').val();
            }
        },
        columns: [{
                data: 'zodiac_name',
                name: 'zodiac.name'
            },
            {
                data: 'type',
                name: 'type'
            },
            {
                data: 'title',
                name: 'title',
                render: function(data, type, row) {
                    if (!data) return '';

                    let limit = 40;

                    return data.length > limit ?
                        data.substring(0, limit) + '.......' :
                        data;
                }
            },
            {
                data: null,
                name: 'status',
                className: 'text-center',
                mRender: (data, type, row) => {
                    return `
                            <div class="square-switch">
                                <input type="checkbox"
                                       id="status-switch-${row.id}"
                                       class="change-status"
                                       switch="status"
                                       data-id="${row.id}"
                                       ${row.status == 1 ? 'checked' : ''} />
                                <label for="status-switch-${row.id}"
                                       data-on-label="Yes"
                                       data-off-label="No"></label>
                            </div>
                        `;
                }
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                            @if (Can::is_accessible('horoscope', 'update'))
                                <a href="{{ route('admin.horoscopes.update.index') }}/${row.id}" class="btn btn-soft-info btn-sm waves-effect waves-light"><i class="bx bx-pencil font-size-16"></i></a>
                            @endif

                            @if (Can::is_accessible('horoscope', 'delete'))
                                <button type="button" class="btn btn-soft-danger btn-sm waves-effect waves-light delete-entry" data-href="{{ route("admin.horoscopes.delete") }}/${row.id}" data-tbl="data"><i class="bx bx-trash font-size-16"></i></button>
                            @endif
                        `;
                }
            }
        ]
    });

    $('#zodiac_id, #type, #status').on('change', function() {
        reloadTable();
    });

    $('#reset-filter-btn').on('click', function() {
        $('#zodiac_id').val('').trigger('change');
        $('#type').val('').trigger('change');
        $('#status').val('1').trigger('change');
        reloadTable();
    });

});
</script>
@endsection