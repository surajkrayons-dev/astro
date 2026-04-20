<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Order Delivered</title>
</head>

<body style="font-family: Arial; background: #f5f5f5; padding: 20px;">

    <div style="max-width:600px; margin:auto; background:#fff; padding:20px; border-radius:8px;">

        <h2 style="color:green;">🎉 Order Delivered!</h2>

        <p>Hi {{ $order->name ?? 'User' }},</p>

        <p>Your order <strong>#{{ $order->order_number }}</strong> has been successfully delivered.</p>

        <hr>

        <h4>📦 Order Details:</h4>

        <ul>
            @foreach($order->items as $item)
            <li>
                {{ $item->product_name }} × {{ $item->quantity }}
            </li>
            @endforeach
        </ul>

        <p><strong>Total:</strong> ₹{{ $order->total_amount }}</p>

        <p><strong>Delivered At:</strong> {{ $order->delivered_at }}</p>

        <hr>

        <p>Thank you for shopping with us ❤️</p>

        <p>— Astrotring Team</p>

    </div>

</body>

</html>