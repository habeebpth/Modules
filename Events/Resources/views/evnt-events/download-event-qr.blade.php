<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $EvntEvent->name }} Ticket</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

    body {
        font-family: Arial, sans-serif;
        text-align: center;
        margin: 0;
        padding: 0;
    }

    .page {
        position: relative;
        width: 21cm;
        height: 29.7cm;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .background-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('{{ public_path("img/event/shared image.jpg") }}');
        background-size: cover;
        background-position: center;
        z-index: -1;
    }

    .container {
        padding: 75px; /* increased */
        text-align: center;
    }

    .title {
        font-size: 30px; /* increased */
        font-weight: bold;
        margin-bottom: 20px;
    }

    p {
        font-size: 20px; /* increased */
        margin: 10px 0;
    }

    h3 {
        font-size: 20px;
        margin-top: 10px;
    }

    .qr-code img {
        width: 200px; /* increased size */
        height: 175px;
        margin-top: 10px;
    }
</style>
</head>
<body>
    <div class="page">
        <div class="background-image"></div>
        <div class="container">
            {{-- <div class="logo">
                @if($EvntEvent->icon_url)
                    <img src="{{ $EvntEvent->icon_url }}" alt="{{ $EvntEvent->name }}" width="50">
                @endif
            </div> --}}
            <h2 class="title">{{ $EvntEvent->name }}</h2>
            <p><strong>Student ID:</strong> {{ $registration->student_id }}</p>
            <p class="mb-1"> {{ strtoupper($student->student_name) }}</p>
            <p><strong>Ref Name:</strong>{{ strtoupper($registration->name) }}</p>
            <p><strong>Total Tickets:</strong> {{ $registration->no_of_participants }}</p>
            {{-- <p><strong>Allowed Seats:</strong>
                {{ implode(', ', range($registration->allotted_seats_start, $registration->allotted_seats_start + $registration->no_of_participants - 1)) }}
            </p> --}}
            <h3>Scan the QR Code</h3>
            <div class="qr-code">
                <img src="{{ $qrCodeUrl }}" alt="QR Code">
            </div>
        </div>
    </div>
</body>
</html>
