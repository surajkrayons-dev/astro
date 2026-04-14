<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Astrologer Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="padding:40px 0;">

    <div class="container">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">New Astrologer Registration Request</h4>
            </div>

            <div class="card-body p-4">
                <p class="text-muted">
                    A new astrologer has submitted a registration request. Please review the details below:
                </p>

                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th width="30%">Name</th>
                            <td>{{ $name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $email }}</td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td>{{ $username }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $country_code }} {{ $mobile }}</td>
                        </tr>
                        <tr>
                            <th>Experience</th>
                            <td>{{ $experience }} Years</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ implode(', ', $category ?? []) }}</td>
                        </tr>
                        <tr>
                            <th>Expertise</th>
                            <td>{{ implode(', ', $expertise ?? []) }}</td>
                        </tr>
                        <tr>
                            <th>Qualification</th>
                            <td>{{ implode(', ', $astro_education ?? []) }}</td>
                        </tr>
                        <tr>
                            <th>Languages</th>
                            <td>{{ implode(', ', $languages ?? []) }}</td>
                        </tr>
                        <tr>
                            <th>Daily Available Hours</th>
                            <td>{{ $daily_available_hours }}</td>
                        </tr>
                        <tr>
                            <th>Is Family Astrologer</th>
                            <td>{{ $is_family_astrologer ? 'Yes' : 'No' }}</td>
                        </tr>
                        @if ($is_family_astrologer)
                        <tr>
                            <th>Family Astrology Details</th>
                            <td>{{ $family_astrology_details }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-warning text-dark">
                                    Pending (0)
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="text-align:center; margin-top:20px;">
                    <a href="{{ url('/astrologers') }}"
                        style="background:#0d6efd; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;">
                        Review Astrologers
                    </a>
                </div>
            </div>

            <div class="card-footer text-center text-muted small">
                This is an automated notification from your Astrologer Platform.
            </div>
        </div>
    </div>

    <script>
    console.log("Admin Registration Notification Loaded");
    </script>

</body>

</html>