@extends('layouts.master')

@section('title') Add Horoscope @endsection

@section('content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Add Horoscope</h4>
        <a href="{{ route('admin.horoscopes.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<form id="createFrm">
    @csrf

    <div class="row">

        <!-- LEFT -->
        <div class="col-lg-8">

            <div class="card">
                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">
                            <label class="fw-bold">Zodiac <sup class="text-danger fs-5">*</sup> :</label>
                            <select name="zodiac_id" class="form-control select2-class"
                                data-placeholder="Choose Zodiac Sign..." required>
                                <option value=""></option>
                                @foreach($zodiacs as $zodiac)
                                <option value="{{ $zodiac->id }}">{{ $zodiac->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Type <sup class="text-danger fs-5">*</sup> :</label>
                            <select name="type" class="form-control select2-class" data-placeholder="Choose Type..."
                                required>
                                <option value=""></option>
                                <option value="today">Today Horoscope</option>
                                <option value="yesterday">Yesterday Horoscope</option>
                                <option value="tomorrow">Tomorrow Horoscope</option>
                                <option value="daily">Daily Horoscope</option>
                                <option value="weekly">Weekly Horoscope</option>
                                <option value="monthly">Monthly Horoscope</option>
                                <option value="yearly">Yearly Horoscope</option>
                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="fw-bold">Title <sup class="text-danger fs-5">*</sup> :</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="e.g. Aries Monthly Horoscope April 2026">
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="fw-bold">Overview :</label>
                            <textarea name="overview" class="form-control" rows="3"
                                placeholder="Write a short summary about this horoscope..."></textarea>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Career :</label>
                            <textarea name="career" class="form-control" rows="3"
                                placeholder="Describe career predictions..."></textarea>
                            <div class="col-md-12 mt-2">
                                <label class="fw-bold">Career Dates :</label>
                                <input type="text" id="career_date" class="form-control" placeholder="e.g. 5,10,20">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Finance :</label>
                            <textarea name="finance" class="form-control" rows="3"
                                placeholder="Describe financial predictions..."></textarea>
                            <div class="col-md-12 mt-2">
                                <label class="fw-bold">Finance Dates :</label>
                                <input type="text" id="finance_date" class="form-control" placeholder="e.g. 3,14,25">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Love & Relationship :</label>
                            <textarea name="love" class="form-control" rows="3"
                                placeholder="Describe love & relationship predictions..."></textarea>
                            <div class="col-md-12 mt-2">
                                <label class="fw-bold">Love & Relationship Dates :</label>
                                <input type="text" id="love_date" class="form-control" placeholder="e.g. 7,18,29">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Health :</label>
                            <textarea name="health" class="form-control" rows="3"
                                placeholder="Describe health insights..."></textarea>
                            <div class="col-md-12 mt-2">
                                <label class="fw-bold">Health Dates :</label>
                                <input type="text" id="health_date" class="form-control" placeholder="e.g. 2,12,22">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Family :</label>
                            <textarea name="family" class="form-control" rows="3"
                                placeholder="Family & home related predictions..."></textarea>
                            <div class="col-md-12 mt-2">
                                <label class="fw-bold">Family Dates :</label>
                                <input type="text" id="family_date" class="form-control" placeholder="e.g. 4,15,26">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Students :</label>
                            <textarea name="students" class="form-control" rows="3"
                                placeholder="Education / students guidance..."></textarea>
                            <div class="col-md-12 mt-2">
                                <label class="fw-bold">Students Dates :</label>
                                <input type="text" id="students_date" class="form-control" placeholder="e.g. 1,11,21">
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="fw-bold">Warning / Caution :</label>
                            <textarea name="warning" class="form-control" rows="3"
                                placeholder="Things to avoid or be careful about..."></textarea>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-4">

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Status</h4>
                </div>

                <div class="card-body">
                    <div class="form-group d-flex justify-content-between align-items-center">
                        <label class="form-label fw-bold">Status <sup class="text-danger fs-5">*</sup> :</label>
                        <input type="hidden" name="status" value="0">

                        <div class="square-switch">
                            <input type="checkbox" id="statusSwitch" name="status" switch="status" value="1" checked>
                            <label for="statusSwitch" data-on-label="Yes" data-off-label="No"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">

                    <label class="fw-bold">Lucky Numbers :</label>
                    <input type="text" id="lucky_numbers" class="form-control mb-2" placeholder="e.g. 3, 7, 9">

                    <label class="fw-bold">Lucky Colors :</label>
                    <input type="text" id="lucky_colors" class="form-control" placeholder="e.g. Red, Black, Blue">

                    <small class="text-muted d-block mt-2">
                        Enter values separated by commas.
                    </small>

                </div>
            </div>

            <!-- Buttons -->
            <div class="card mt-3">
                <div class="card-body">
                    <button type="submit" id="createBtn" class="btn btn-success w-100 mb-2">
                        Save Horoscope
                    </button>
                    <button type="reset" class="btn btn-warning w-100">
                        Clear Form
                    </button>
                </div>
            </div>

        </div>

    </div>
</form>
@endsection


@section('script')
<script>
$('#createFrm').submit(function(e) {
    e.preventDefault();

    let formData = new FormData(this);

    let status = $('#statusSwitch').is(':checked') ? 1 : 0;
    formData.set('status', status);

    function appendArray(inputId, fieldName) {
        let val = $(inputId).val();
        if (val) {
            val.split(',').forEach(v => {
                if (v.trim() !== '') {
                    formData.append(fieldName + '[]', v.trim());
                }
            });
        }
    }

    // section dates
    appendArray('#career_date', 'career_date');
    appendArray('#finance_date', 'finance_date');
    appendArray('#love_date', 'love_date');
    appendArray('#health_date', 'health_date');
    appendArray('#family_date', 'family_date');
    appendArray('#students_date', 'students_date');

    // lucky
    appendArray('#lucky_numbers', 'lucky_numbers');
    appendArray('#lucky_colors', 'lucky_colors');

    $.ajax({
        url: "{{ route('admin.horoscopes.create') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function() {
            $('#createBtn').prop('disabled', true).text('Saving...');
        },

        success: function(res) {
            toastr.success(res.message || 'Created successfully');
            setTimeout(() => {
                window.location.href = "{{ route('admin.horoscopes.index') }}";
            }, 1000);
        },

        error: function(xhr) {
            let msg = xhr.responseJSON?.message || 'Something went wrong!';
            toastr.error(msg);
        },

        complete: function() {
            $('#createBtn').prop('disabled', false).text('Save Horoscope');
        }
    });
});
</script>
@endsection