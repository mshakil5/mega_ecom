<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Order Confirmation</title>
    <style>
        body, table, td, a { margin:0; padding:0; border-collapse: collapse; }
        img { border:0; height:auto; line-height:100%; outline:none; text-decoration:none; }
        a { text-decoration:none; }
        body { height:100% !important; width:100% !important; font-family:Arial, Helvetica, sans-serif; background:#f8f8f8; }

        @media (max-width: 600px) {
            .container { width:100% !important; }
            .stack { display:block !important; width:100% !important; }
            .p-32 { padding:20px !important; }
            .text-center-sm { text-align:center !important; }
        }
    </style>
</head>
<body style="background-color:#f8f8f8; margin:0; padding:0;">

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f8f8f8;">
    <tr>
        <td align="center" style="padding:20px;">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="container" style="max-width:600px;background-color:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e0e0e0;">
                
                <!-- Header -->
                <tr>
                    <td style="background-color:#000;padding:24px;text-align:center;">
                        <h2 style="margin:0;color:#fff;font-size:20px;font-weight:700;">
                            @if($recipientType === 'admin')
                                New Order Received
                            @else
                                Order Confirmation
                            @endif
                        </h2>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px 24px;" class="p-32">
                        <p style="margin:0 0 20px 0;font-size:14px;line-height:1.6;color:#555;">
                            @if($recipientType === 'admin')
                                You have received a new order. Order details are below:
                            @else
                                Thank you for your order! Here are your order details:
                            @endif
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                    <p style="margin:0;font-size:13px;">
                                        <strong style="color:#333;">Order Invoice:</strong>
                                        <span style="color:#666;">{{ $orderData['invoice'] }}</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                    <p style="margin:0;font-size:13px;">
                                        <strong style="color:#333;">Order Date:</strong>
                                        <span style="color:#666;">{{ $orderData['purchase_date'] }}</span>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- Customer Details -->
                        <div style="margin:20px 0;">
                            <p style="margin:0 0 12px 0;font-size:13px;font-weight:700;color:#333;">Customer Details:</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;">
                                            <strong style="color:#333;">Name:</strong>
                                            <span style="color:#666;">{{ $orderData['customer_name'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;">
                                            <strong style="color:#333;">Email:</strong>
                                            <span style="color:#666;">
                                                <a href="mailto:{{ $orderData['customer_email'] }}" style="color:#0066cc;text-decoration:none;">{{ $orderData['customer_email'] }}</a>
                                            </span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;">
                                            <strong style="color:#333;">Phone:</strong>
                                            <span style="color:#666;">
                                                <a href="tel:{{ $orderData['customer_phone'] }}" style="color:#0066cc;text-decoration:none;">{{ $orderData['customer_phone'] }}</a>
                                            </span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;">
                                            <strong style="color:#333;">Address:</strong>
                                            <span style="color:#666;">{{ $orderData['address'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Payment Details -->
                        <div style="margin:20px 0;">
                            <p style="margin:0 0 12px 0;font-size:13px;font-weight:700;color:#333;">Payment Summary:</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;display:flex;justify-content:space-between;">
                                            <strong style="color:#333;">Subtotal:</strong>
                                            <span style="color:#666;">{{ $orderData['subtotal'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;display:flex;justify-content:space-between;">
                                            <strong style="color:#333;">Shipping:</strong>
                                            <span style="color:#666;">{{ $orderData['shipping'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;display:flex;justify-content:space-between;">
                                            <strong style="color:#333;">VAT (20%):</strong>
                                            <span style="color:#666;">{{ $orderData['vat'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 0;background-color:#f9f9f9;border-radius:6px;padding:12px;">
                                        <p style="margin:0;font-size:14px;font-weight:700;display:flex;justify-content:space-between;">
                                            <strong style="color:#000;">Total Amount:</strong>
                                            <span style="color:#000;">{{ $orderData['total'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Payment Method -->
                        <div style="margin:20px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                        <p style="margin:0;font-size:13px;">
                                            <strong style="color:#333;">Payment Method:</strong>
                                            <span style="color:#666;">{{ $orderData['payment_method'] }}</span>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        @if($orderData['order_notes'])
                        <!-- Order Notes -->
                        <div style="background-color:#f9f9f9;padding:16px;border-radius:6px;margin:20px 0;">
                            <p style="margin:0 0 8px 0;font-size:12px;color:#999;text-transform:uppercase;font-weight:600;">Order Notes:</p>
                            <p style="margin:0;font-size:14px;line-height:1.8;color:#444;white-space: pre-wrap;">{{ $orderData['order_notes'] }}</p>
                        </div>
                        @endif

                        <div style="margin:20px 0;padding:16px;background-color:#f0f8ff;border-left:4px solid #0066cc;border-radius:4px;">
                            <p style="margin:0;font-size:13px;color:#0066cc;font-weight:600;">
                                📎 Your order invoice PDF is attached to this email.
                            </p>
                        </div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background-color:#f8f8f8;padding:20px;font-size:12px;color:#666;text-align:center;border-top:1px solid #eee;">
                        <p style="margin:0 0 8px 0;">
                            &copy; {{ date('Y') }} All rights reserved.
                        </p>
                        <p style="margin:0;font-size:11px;color:#999;">
                            @if($recipientType === 'admin')
                                This is an automated email notification from your order management system.
                            @else
                                Thank you for your purchase!
                            @endif
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>