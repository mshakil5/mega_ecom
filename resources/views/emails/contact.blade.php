<!doctype html>
<html lang="en" style="margin:0;padding:0;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $contactSubject }}</title>
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
                        <h2 style="margin:0;color:#fff;font-size:20px;font-weight:700;">New Contact Message</h2>
                    </td>
                </tr>

                <!-- Content -->
                <tr>
                    <td style="padding:32px 24px;" class="p-32">
                        <p style="margin:0 0 20px 0;font-size:14px;line-height:1.6;color:#555;">
                            You have received a new contact message:
                        </p>

                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                            <tr>
                                <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                    <p style="margin:0;font-size:13px;">
                                        <strong style="color:#333;">Name:</strong>
                                        <span style="color:#666;">{{ $contactName }}</span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                    <p style="margin:0;font-size:13px;">
                                        <strong style="color:#333;">Email:</strong>
                                        <span style="color:#666;">
                                            <a href="mailto:{{ $contactEmail }}" style="color:#0066cc;text-decoration:none;">{{ $contactEmail }}</a>
                                        </span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                    <p style="margin:0;font-size:13px;">
                                        <strong style="color:#333;">Phone:</strong>
                                        <span style="color:#666;">
                                            <a href="tel:{{ $contactPhone }}" style="color:#0066cc;text-decoration:none;">{{ $contactPhone }}</a>
                                        </span>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 0;border-bottom:1px solid #eee;">
                                    <p style="margin:0;font-size:13px;">
                                        <strong style="color:#333;">Subject:</strong>
                                        <span style="color:#666;">{{ $contactSubject }}</span>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <div style="background-color:#f9f9f9;padding:16px;border-radius:6px;margin:20px 0;">
                            <p style="margin:0 0 8px 0;font-size:12px;color:#999;text-transform:uppercase;font-weight:600;">Message:</p>
                            <p style="margin:0;font-size:14px;line-height:1.8;color:#444;white-space: pre-wrap;">{{ $contactMessage }}</p>
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
                            This is an automated email from your contact form.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>