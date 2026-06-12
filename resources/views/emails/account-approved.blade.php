<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Approved — GradNet</title>
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
            <p style="margin:0;color:#c4972f;font-size:0.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;">GradNet</p>
            <h1 style="margin:6px 0 0;color:#ffffff;font-size:1.4rem;font-weight:700;">Account Approved ✓</h1>
          </td>
        </tr>

        {{-- Body --}}
        <tr>
          <td style="padding:32px;">
            <p style="margin:0 0 16px;font-size:1rem;color:#1c1e21;">Hi <strong>{{ $user->first_name }}</strong>,</p>
            <p style="margin:0 0 16px;font-size:0.95rem;color:#3d3d3d;line-height:1.6;">
              Great news! Your application to join the <strong>GradNet</strong> has been
              <span style="color:#1a7f4b;font-weight:700;">approved</span>. You can now log in and
              connect with your batchmates, view events, read the latest news, and more.
            </p>

            {{-- CTA Button --}}
            <table cellpadding="0" cellspacing="0" style="margin:24px 0;">
              <tr>
                <td style="background:#003087;border-radius:9999px;">
                  <a href="{{ url('/login') }}"
                     style="display:inline-block;padding:12px 32px;color:#ffffff;font-size:0.95rem;font-weight:700;text-decoration:none;">
                    Log In Now →
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:0;font-size:0.85rem;color:#65676b;line-height:1.5;">
              If the button above doesn't work, copy and paste this link into your browser:<br>
              <a href="{{ url('/login') }}" style="color:#003087;">{{ url('/login') }}</a>
            </p>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f7f8fa;padding:16px 32px;border-top:1px solid #e0e0e0;">
            <p style="margin:0;font-size:0.75rem;color:#9a9ea5;">
              © {{ date('Y') }} GradNet &mdash; This is an automated message, please do not reply.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
