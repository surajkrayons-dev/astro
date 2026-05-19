@extends('layouts.master')

@section('title', 'Bulk Mails')

@section('content')

    {{-- PAGE HEADER --}}
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">

                <h4 class="mb-sm-0 font-size-18">
                    Bulk Mails
                </h4>

                <div class="page-title-right">
                    <a href="{{ route('admin.bulk_mails.create.index') }}" class="btn btn-soft-info">
                        <i class="fas fa-plus"></i> Send Mail
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="row">
        <div class="col-12">
            <div class="card border">

                <div class="card-body">

                    <table id="data-table"
                           class="table table-bordered dt-responsive nowrap w-100">

                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Send Date</th>
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

    $(function () {

        $('#data-table').DataTable({

            processing: true,
            serverSide: true,

            ajax: {
                url: "{{ route('admin.bulk_mails.list') }}"
            },

            columns: [

                {
                    data: 'email',
                    name: 'email'
                },

                {
                    data: 'created_at',
                    name: 'created_at'
                }

            ]

        });

    });

</script>

@endsection