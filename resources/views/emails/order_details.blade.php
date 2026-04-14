<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0;background:#fff3e0;font-family:Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:10px;">
        <tr>
            <td align="center">

                <table width="100%" style="max-width:650px;background:#ffffff;border-radius:14px;overflow:hidden;">

                    <!-- HEADER -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#e65100,#fb8c00);color:#fff;padding:18px;text-align:center;">
                            <h2 style="margin:0;">📦 Order Details</h2>
                        </td>
                    </tr>

                    <!-- USER -->
                    <tr>
                        <td style="padding:18px;font-size:14px;">
                            Hi <b>{{ $order->user->name ?? 'User' }}</b>,<br>
                            Here’s everything about your order 👇
                        </td>
                    </tr>

                    <!-- INFO CARD -->
                    <tr>
                        <td style="padding:0 15px;">
                            <table width="100%" style="background:#fff8f0;border-radius:10px;padding:10px;">
                                <tr>
                                    <td>🧾 Order ID</td>
                                    <td align="right">#{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td>📅 Date</td>
                                    <td align="right">{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td>💳 Payment</td>
                                    <td align="right">{{ $order->payment?->payment_mode ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- PRODUCTS -->
                    <tr>
                        <td style="padding:15px;">
                            <h3 style="color:#e65100;margin-bottom:10px;">🛒 Products</h3>

                            @foreach($order->items as $item)
                            <table width="100%" style="border-bottom:1px solid #eee;margin-bottom:10px;">
                                <tr>

                                    <td width="70">
                                        <img src="{{ env('APP_URL') }}/storage/product/{{ $item->product->image }}"
                                            width="60">
                                    </td>

                                    <td style="font-size:13px;">
                                        <b>{{ $item->product_name }}</b><br>
                                        Qty: {{ $item->quantity }}<br>
                                        Price: ₹{{ $item->price }}
                                    </td>

                                    <td align="right" style="color:#e65100;font-weight:bold;">
                                        ₹{{ $item->total }}
                                    </td>

                                </tr>
                            </table>
                            @endforeach

                        </td>
                    </tr>

                    <!-- PAYMENT -->
                    <tr>
                        <td style="padding:15px;">
                            <h3 style="color:#e65100;">💰 Payment Summary</h3>

                            <table width="100%" style="font-size:13px;">
                                <tr>
                                    <td>Subtotal</td>
                                    <td align="right">₹{{ $order->subtotal }}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td align="right">- ₹{{ $order->discount }}</td>
                                </tr>
                                <tr>
                                    <td>Delivery Charge</td>
                                    <td align="right">₹{{ $order->delivery_charge }}</td>
                                </tr>
                                <tr>
                                    <td>Wallet Used</td>
                                    <td align="right">- ₹{{ $order->wallet_used }}</td>
                                </tr>
                                <tr>
                                    <td><b>Total</b></td>
                                    <td align="right" style="color:#e65100;font-size:16px;">
                                        <b>₹{{ $order->total_amount }}</b>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- ADDRESS -->
                    <tr>
                        <td style="padding:15px;">
                            <h3 style="color:#e65100;">📍 Delivery Address</h3>
                            <p style="font-size:13px;line-height:1.6;">
                                {{ $order->name }}<br>
                                {{ $order->mobile }}<br>
                                {{ $order->address }}<br>
                                {{ $order->pincode }}
                            </p>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#ffe0b2;text-align:center;padding:15px;font-size:12px;">
                            🚚 Your order will be shipped soon<br>
                            🙏 Thank you for shopping with Astrotring Store
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>