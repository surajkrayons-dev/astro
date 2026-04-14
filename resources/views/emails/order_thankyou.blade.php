<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
</head>

<body style="margin:0;background:#fff3e0;font-family:Arial,sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="padding:10px;">
        <tr>
            <td align="center">

                <table width="100%" style="max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;">

                    <!-- HEADER -->
                    <tr>
                        <td
                            style="background:linear-gradient(135deg,#e65100,#fb8c00);padding:22px;text-align:center;color:#fff;">
                            <h2 style="margin:0;">🛍️ Astrotring</h2>
                            <p style="margin:5px 0 0;font-size:13px;">✨ Spiritual & Gemstone Store ✨</p>
                        </td>
                    </tr>

                    <!-- SUCCESS -->
                    <tr>
                        <td style="padding:20px;text-align:center;">
                            <h2 style="margin:0;color:#e65100;">🎉 Order Confirmed</h2>
                            <p style="color:#555;font-size:14px;">
                                Hi <b>{{ $order->user->name ?? 'User' }}</b>,<br>
                                Thank you for buying from <b>Astrotring Store</b> 🙏<br>
                                Your order has been successfully placed.
                            </p>
                            <p style="font-size:13px;color:#888;">Order ID: #{{ $order->order_number }}</p>
                        </td>
                    </tr>

                    <!-- PRODUCTS -->
                    <tr>
                        <td style="padding:10px 15px;">
                            @foreach($order->items as $item)
                            <table width="100%"
                                style="border:1px solid #f1f1f1;border-radius:10px;margin-bottom:10px;background:#fffaf5;">
                                <tr>

                                    <td width="80" style="padding:10px;">
                                        <img src="{{ env('APP_URL') }}/storage/product/{{ $item->product->image }}"
                                            width="65" style="border-radius:8px;">
                                    </td>

                                    <td style="font-size:14px;">
                                        <b>{{ $item->product_name }}</b><br>
                                        <span style="color:#777;">Qty: {{ $item->quantity }}</span>
                                    </td>

                                    <td align="right" style="color:#e65100;font-weight:bold;">
                                        ₹{{ $item->total }}
                                    </td>

                                </tr>
                            </table>
                            @endforeach
                        </td>
                    </tr>

                    <!-- TOTAL BOX -->
                    <tr>
                        <td style="padding:15px;">
                            <table width="100%" style="background:#fff8f0;border-radius:10px;padding:10px;">
                                <tr>
                                    <td>Total Paid</td>
                                    <td align="right" style="color:#e65100;font-size:18px;">
                                        <b>₹{{ $order->total_amount }}</b>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- FOOTER -->
                    <tr>
                        <td style="background:#ffe0b2;text-align:center;padding:15px;font-size:12px;">
                            🙏 Thank you for trusting Astrotring Store 💫<br>
                            We wish you positivity & success 🌟
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>