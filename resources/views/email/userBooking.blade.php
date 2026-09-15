<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete payment to book seat - {{ $data['event'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .header {
            text-align: center;
            background-color: #f4f4f4;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
            color: #555;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .button {
            display: inline-block;
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
       {{-- <div class="header">
            <h1>Thank you!</h1>
            {{--<p>Thank you for registering for: <strong>{{ $data['event'] }} </strong></p>--}}
        </div> --}}

        <p>Dear {{ $data['name'] }},</p>


        <p>Thank you for expressing your interest in the <strong>{{ $data['event'] }}</strong>, scheduled to be held on 29 November 2025 (Saturday) in New Delhi.</p>
        <p><strong>To confirm your registration, please book your seat by completing the payment below.</strong></p>
        <p>
             <a class="button" target="_blank" href="https://bit.ly/4oeT4xL" color:"white">Pay Now</a>

        </p>

        <p>
            <strong>Limited seats! The Early Bird offer is open till 15 November 2025 only!</strong>
        </p>

        {{-- <h3>Booking Details:</h3> --}}
        {{-- <ul>
            <li><strong>Booking ID:</strong> {{ $data['booking_id'] }}</li>
            <li><strong>Name:</strong> {{ $data['name'] }}</li>
            <li><strong>Email:</strong> <a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></li>
            <li><strong>Phone:</strong> {{ $data['phone'] }}</li>
            <li><strong>Company:</strong> {{ $data['company'] ?? 'NA' }}</li>
            <li><strong>Designation:</strong> {{ $data['designation'] ?? 'NA' }}</li>
            <li><strong>Event Date:</strong> {{ $data['date'] }}</li>
             <li><strong>Venue:</strong> Constitution Club of India, Rafi Marg, New Delhi <br>
                (Nearest Metro Station: Central Secretariat / Patel Chowk)</li>
        </ul> --}}



        {{-- <p>
            <a class="button" href="https://maps.app.goo.gl/21boPJbsMyd4X1eH6" target="_blank">View Venue Location</a>
        </p> 

        <p>We look forward to being a part of your special occasion.</p> --}}
        <p>For any queries, please reach out to us at email: info@oakbridge.in or WhatsApp: <a href="https://www.wa.me/+918800337299">+91 88003 37299</a></p>

        <p>Warm regards,</p>
        <p><strong>Team OakBridge</strong><br>
            {{-- (An initiative of OakBridge Publishing)</p> --}}

        <div class="footer">
            <p>Email: <a href="mailto:info@oakbridge.in">info@oakbridge.in</a></p>
            <p>Website: <a href="https://www.oakbridge.events" target="_blank">https://www.oakbridge.events</a></p>
        </div>
    </div>
</body>

</html>
