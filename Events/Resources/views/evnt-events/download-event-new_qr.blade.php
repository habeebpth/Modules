<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $EvntEvent->name }} Ticket</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .container { width: 80%; margin: auto; padding: 20px; border: 1px solid #ddd; }
        .logo { text-align: left; }
        .title { font-size: 20px; font-weight: bold; }
        .qr-code { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ $EvntEvent->icon_url }}" alt="{{ $EvntEvent->name }}" width="50">
        </div>
        <h2 class="mb-1 title">{{ $EvntEvent->name }}</h2>
        <p class="mb-1"><strong>Registration ID:</strong> {{ $registration->registration_code }}</p>
        <p class="mb-1">{{ strtoupper($registration->name) }} ({{ $registration->mobile }})</p>
        <h3>Scan the QR Code</h3>
        <div class="qr-code">
            <img src="{{ $qrCodeUrl }}" alt="QR Code" width="200">
        </div>
    </div>
</body>
</html>
