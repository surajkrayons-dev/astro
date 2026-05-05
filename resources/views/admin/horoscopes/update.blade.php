@extends('layouts.master')

@section('title') Update Horoscope @endsection

@section('content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h4>Update Horoscope</h4>
        <a href="{{ route('admin.horoscopes.index') }}" class="btn btn-primary">
            ← Back
        </a>
    </div>
</div>

<form id="updateFrm">
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
                                @foreach($zodiacs as $zodiac)
                                <option value="{{ $zodiac->id }}"
                                    {{ $horoscope->zodiac_id == $zodiac->id ? 'selected' : '' }}>
                                    {{ $zodiac->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="fw-bold">Type <sup class="text-danger fs-5">*</sup> :</label>
                            <select name="type" class="form-control select2-class" data-placeholder="Choose Type..."
                                required>
                                @foreach(['today','yesterday','tomorrow','daily','weekly','monthly','yearly'] as $type)
                                <option value="{{ $type }}" {{ $horoscope->type == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }} Horoscope
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="fw-bold">Title <sup class="text-danger fs-5">*</sup> :</label>
                            <input type="text" name="title" class="form-control" value="{{ $horoscope->title }}"
                                placeholder="e.g. Aries Monthly Horoscope April 2026">
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="fw-bold">Overview :</label>
                            <textarea name="overview" class="form-control" rows="3"
                                placeholder="Write short summary...">{{ $horoscope->overview }}</textarea>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Career :</label>
                            <textarea name="career" class="form-control">{{ $horoscope->career }}</textarea>

                            <div class="mt-2">
                                <label class="fw-bold">Career Dates :</label>
                                <input type="text" id="career_date" class="form-control"
                                    value="{{ implode(',', $horoscope->career_date ?? []) }}"
                                    placeholder="e.g. 5,10,20">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Finance :</label>
                            <textarea name="finance" class="form-control"
                                placeholder="Describe financial predictions...">{{ $horoscope->finance }}</textarea>

                            <div class="mt-2">
                                <label class="fw-bold">Finance Dates :</label>
                                <input type="text" id="finance_date" class="form-control"
                                    value="{{ implode(',', $horoscope->finance_date ?? []) }}"
                                    placeholder="e.g. 3,14,25">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Love & Relationship :</label>
                            <textarea name="love" class="form-control"
                                placeholder="Describe love & relationship predictions...">{{ $horoscope->love }}</textarea>

                            <div class="mt-2">
                                <label class="fw-bold">Love & Relationship Dates :</label>
                                <input type="text" id="love_date" class="form-control"
                                    value="{{ implode(',', $horoscope->love_date ?? []) }}" placeholder="e.g. 7,18,29">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Health :</label>
                            <textarea name="health" class="form-control"
                                placeholder="Describe health insights...">{{ $horoscope->health }}</textarea>

                            <div class="mt-2">
                                <label class="fw-bold">Health Dates :</label>
                                <input type="text" id="health_date" class="form-control"
                                    value="{{ implode(',', $horoscope->health_date ?? []) }}"
                                    placeholder="e.g. 2,12,22">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Family :</label>
                            <textarea name="family" class="form-control"
                                placeholder="Family & home related predictions...">{{ $horoscope->family }}</textarea>

                            <div class="mt-2">
                                <label class="fw-bold">Family Dates :</label>
                                <input type="text" id="family_date" class="form-control"
                                    value="{{ implode(',', $horoscope->family_date ?? []) }}"
                                    placeholder="e.g. 4,15,26">
                            </div>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label class="fw-bold">Students :</label>
                            <textarea name="students" class="form-control"
                                placeholder="Education / students guidance...">{{ $horoscope->students }}</textarea>

                            <div class="mt-2">
                                <label class="fw-bold">Students Dates :</label>
                                <input type="text" id="students_date" class="form-control"
                                    value="{{ implode(',', $horoscope->students_date ?? []) }}"
                                    placeholder="e.g. 1,11,21">
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="fw-bold">Warning / Caution :</label>
                            <textarea name="warning" class="form-control"
                                placeholder="Things to avoid...">{{ $horoscope->warning }}</textarea>
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
                    <div class="col-lg-12">
                        <div class="form-group d-flex justify-content-between align-items-center">
                            <label class="form-label fw-bold">Status <sup class="text-danger fs-5">*</sup>
                                :</label>
                            <input type="hidden" name="status" value="0">
                            <div class="square-switch">
                                <input type="checkbox" id="statusSwitch" name="status" switch="status" value="1"
                                    {{ $horoscope->status ? 'checked' : '' }} />
                                <label for="statusSwitch" data-on-label="Yes" data-off-label="No"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">

                    <label class="fw-bold">Lucky Numbers</label>
                    <input type="text" id="lucky_numbers" class="form-control mb-2"
                        value="{{ implode(',', $horoscope->lucky_numbers ?? []) }}" placeholder="e.g. 3,7">

                    <label class="fw-bold">Lucky Colors</label>
                    <input type="text" id="lucky_colors" class="form-control"
                        value="{{ implode(',', $horoscope->lucky_colors ?? []) }}" placeholder="e.g. Red,Black">

                    <small class="text-muted">Comma separated values</small>

                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <button id="updateBtn" class="btn btn-success w-100">
                        Update Horoscope
                    </button>
                </div>
            </div>

        </div>

    </div>
</form>
@endsection


@section('script')
<script>
$('#updateBtn').click(function(e) {
    e.preventDefault();

    let formData = new FormData($('#updateFrm')[0]);

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

    appendArray('#career_date', 'career_date');
    appendArray('#finance_date', 'finance_date');
    appendArray('#love_date', 'love_date');
    appendArray('#health_date', 'health_date');
    appendArray('#family_date', 'family_date');
    appendArray('#students_date', 'students_date');

    appendArray('#lucky_numbers', 'lucky_numbers');
    appendArray('#lucky_colors', 'lucky_colors');

    $.ajax({
        url: "{{ route('admin.horoscopes.update', $horoscope->id) }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,

        beforeSend: function() {
            $('#updateBtn').prop('disabled', true).text('Updating...');
        },

        success: function(res) {
            toastr.success(res.message || 'Updated successfully');
            setTimeout(() => {
                window.location.href = "{{ route('admin.horoscopes.index') }}";
            }, 1000);
        },

        error: function(xhr) {
            let msg = xhr.responseJSON?.message || 'Something went wrong!';
            toastr.error(msg);
        },

        complete: function() {
            $('#updateBtn').prop('disabled', false).text('Update Horoscope');
        }
    });
});
</script>
@endsection