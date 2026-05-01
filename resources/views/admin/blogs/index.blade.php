@extends('layouts.master')

@section('title') Blogs @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">All Blogs</h4>

            <div class="page-title-right">
                <a href="{{ route('admin.blogs.create.index') }}" class="btn btn-soft-info">
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
                        <label class="form-label fw-bold">Blog Category</label>
                        <select id="blog_category_id" class="form-control select2-class2"
                            data-placeholder="Select Category">
                            <option value=""></option>
                            @foreach(\App\Models\BlogCategory::orderBy('name')->get() as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col">
                        <label class="form-label fw-bold">Blog</label>
                        <select id="blog_id" class="form-control select2-class2" data-placeholder="Select Blog">
                            <option value=""></option>
                            @foreach(\App\Models\Blog::orderBy('name')->get() as $blog)
                            <option value="{{ $blog->id }}">
                                {{ $blog->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border">
            <div class="card-body">
                <table id="data-table" class="table table-bordered dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Blog Name</th>
                            <th>Slug</th>
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
            url: "{{ route('admin.blogs.list') }}",
            data: function(d) {
                d.blog_category_id = $('#blog_category_id').val();
                d.blog_id = $('#blog_id').val();
            }
        },
        columns: [{
                data: 'category_name',
                name: 'category.name'
            },
            {
                data: 'name',
                name: 'blogs.name'
            },
            {
                data: 'slug',
                name: 'blogs.slug'
            },
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                            <a href="{{ route('admin.blogs.update.index') }}/${row.id}"
                               class="btn btn-soft-info btn-sm waves-effect waves-light">
                                <i class="bx bx-pencil font-size-16"></i>
                            </a>
                            
                            <button type="button"
                                    class="btn btn-soft-danger btn-sm waves-effect waves-light delete-entry"
                                    data-href="{{ route('admin.blogs.delete') }}/${row.id}">
                                <i class="bx bx-trash font-size-16"></i>
                            </button>
                    `;
                }
            }
        ]
    });

    $('#blog_category_id, #blog_id').on('change', function() {
        reloadTable();
    });

    $('#reset-filter-btn').on('click', function() {
        $('#blog_category_id').val('').trigger('change');
        $('#blog_id').val('').trigger('change');
        reloadTable();
    });

});
</script>
@endsection