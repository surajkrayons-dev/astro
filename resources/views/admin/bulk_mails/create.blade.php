@extends('layouts.master')

@section('title', 'Send Bulk Mail')

@section('content')

    <div class="row">
        <div class="col-12">

            <div class="page-title-box d-flex justify-content-between">

                <h4>
                    Send Bulk Mail
                </h4>

                <a href="{{ route('admin.bulk_mails.index') }}" class="btn btn-primary">

                    <i class="fas fa-arrow-left"></i>
                    Back

                </a>

            </div>

        </div>
    </div>

    <form id="createFrm">

        @csrf

        <div class="card">

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label fw-bold">
                        Email IDs
                        <sup class="text-danger">*</sup>
                    </label>

                    <textarea name="emails" class="form-control" rows="12"
                        placeholder="test1@gmail.com
test2@gmail.com
test3@gmail.com"></textarea>

                    <small class="text-muted">
                        Enter one email per line
                    </small>

                </div>

            </div>

            <div class="card-footer text-end">

                <button type="reset" class="btn btn-warning">

                    Clear

                </button>

                <button type="button" id="createBtn" class="btn btn-success">

                    Send Mail

                </button>

            </div>

        </div>

    </form>

@endsection

@section('script')

    <script>
        $(document).ready(function() {

            $('#createBtn').click(function(e) {

                e.preventDefault();

                let btn = $(this);

                $.ajax({

                    url: "{{ route('admin.bulk_mails.create') }}",

                    type: "POST",

                    data: $('#createFrm').serialize(),

                    beforeSend: () => {

                        btn.prop('disabled', true);

                        showToastr('info', 'Sending mail...');

                    },

                    success: res => {

                        showToastr('success', res.message);

                        window.location.href =
                            "{{ route('admin.bulk_mails.index') }}";

                    },

                    error: xhr => {

                        btn.prop('disabled', false);

                        showToastr('error', formatErrorMessage(xhr));

                    }

                });

            });

        });
    </script>

@endsection
