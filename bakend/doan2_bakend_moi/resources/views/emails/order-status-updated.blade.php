<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Status Updated</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            text-align: center;
        }

        h1 {
            color: #333333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            color: #555555;
            font-size: 16px;
            margin-bottom: 10px;
        }

        strong {
            color: #007bff;
        }

        footer {
            margin-top: 20px;
            font-size: 14px;
            color: #888888;
        }

        .footer-link {
            color: #007bff;
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Dear {{ $order->user->name }},</h1>
        <p>Your order (ID: {{ $order->id }}) status has been updated to <strong>{{ $order->status }}</strong>.</p>
        <p>Thank you for shopping with us!</p>
        <footer>
            <p>If you have any questions, feel free to <a href="mailto:support@example.com" class="footer-link">contact
                    our support team</a>.</p>
        </footer>
    </div>
</body>

</html>
