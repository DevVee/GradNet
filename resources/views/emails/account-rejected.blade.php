<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Application — ICCBI Alumni</title>
</head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:Arial,Helvetica,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f5;padding:40px 16px;">
  <tr>
    <td align="center">
      <table width="560" cellpadding="0" cellspacing="0"
             style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);max-width:560px;width:100%;">

        {{-- Header --}}
        <tr>
          <td style="background:#003087;padding:28px 32px;">
            <p style="margin:0;color:#c4972f;font-size:0.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;">ICCBI Alumni Network</p>
            <h1 style="margin:6px 0 0;color:#ffffff;font-size:1.4rem;font-weight:700;">Application Update</h1>
          </td>
        </tr>

        {{-- Body --}}
        <tr>
          <td style="padding:32px;">
            <p style="margin:0 0 16px;font-size:1rem;color:#1c1e21;">Hi <strong>{{ $user->first_name }}</strong>,</p>
            <p style="margin:0 0 16px;font-size:0.95rem;color:#3d3d3d;line-height:1.6;">
              Thank you for registering with the <strong>ICCBI Alumni Network</strong>. After review,
              we were unable to verify your alumni status at this time and your application has not been approved.
            </p>
            <p style="margin:0 0 16px;font-size:0.95rem;color:#3d3d3d;line-height:1.6;">
              If you believe this is a mistake, or if you would like to provide additional information,
              please contact the Alumni Office directly.
            </p>
            <p style="margin:0;font-size:0.95rem;color:#3d3d3d;line-height:1.6;">
              We appreciate your interest and encourage eligible alumni to reapply.
            </p>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f7f8fa;padding:16px 32px;border-top:1px solid #e0e0e0;">
            <p style="margin:0;font-size:0.75rem;color:#9a9ea5;">
              © {{ date('Y') }} ICCBI Alumni Network &mdash; This is an automated message, please do not reply.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
