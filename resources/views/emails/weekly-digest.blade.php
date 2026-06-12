<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Update — GradNet</title>
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
            <h1 style="margin:6px 0 0;color:#ffffff;font-size:1.4rem;font-weight:700;">Your Weekly Update 📬</h1>
            <p style="margin:6px 0 0;color:#a8bcd8;font-size:0.8rem;">{{ now()->format('F j, Y') }}</p>
          </td>
        </tr>

        {{-- Body --}}
        <tr>
          <td style="padding:28px 32px 0;">
            <p style="margin:0 0 24px;font-size:0.95rem;color:#3d3d3d;line-height:1.6;">
              Here's what's happening in the GradNet community this week.
            </p>

            {{-- ── Upcoming Events ──────────────────────────────── --}}
            @if ($upcomingEvents->isNotEmpty())
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
              <tr>
                <td style="padding-bottom:10px;border-bottom:2px solid #003087;">
                  <h2 style="margin:0;font-size:0.95rem;font-weight:700;color:#003087;text-transform:uppercase;letter-spacing:0.5px;">
                    📅 Upcoming Events
                  </h2>
                </td>
              </tr>
              @foreach ($upcomingEvents as $event)
              <tr>
                <td style="padding:12px 0;border-bottom:1px solid #eaecef;">
                  <p style="margin:0 0 3px;font-size:0.9rem;font-weight:700;color:#1c1e21;">{{ $event->title }}</p>
                  <p style="margin:0 0 3px;font-size:0.8rem;color:#65676b;">
                    🕐 {{ \Carbon\Carbon::parse($event->event_datetime)->format('D, M j · g:i A') }}
                    @if($event->location)&nbsp;&nbsp;📍 {{ $event->location }}@endif
                  </p>
                  <a href="{{ url('/events/' . $event->id) }}"
                     style="font-size:0.8rem;color:#003087;text-decoration:none;font-weight:600;">
                    View Event →
                  </a>
                </td>
              </tr>
              @endforeach
            </table>
            @endif

            {{-- ── Latest News ──────────────────────────────────── --}}
            @if ($latestNews->isNotEmpty())
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
              <tr>
                <td style="padding-bottom:10px;border-bottom:2px solid #c4972f;">
                  <h2 style="margin:0;font-size:0.95rem;font-weight:700;color:#c4972f;text-transform:uppercase;letter-spacing:0.5px;">
                    📰 Latest News
                  </h2>
                </td>
              </tr>
              @foreach ($latestNews as $item)
              <tr>
                <td style="padding:12px 0;border-bottom:1px solid #eaecef;">
                  <p style="margin:0 0 3px;font-size:0.9rem;font-weight:700;color:#1c1e21;">{{ $item->title }}</p>
                  <p style="margin:0 0 4px;font-size:0.8rem;color:#65676b;line-height:1.5;">
                    {{ \Illuminate\Support\Str::limit($item->description, 100) }}
                  </p>
                  <a href="{{ url('/news/' . $item->id) }}"
                     style="font-size:0.8rem;color:#003087;text-decoration:none;font-weight:600;">
                    Read More →
                  </a>
                </td>
              </tr>
              @endforeach
            </table>
            @endif

            {{-- ── New Alumni ───────────────────────────────────── --}}
            @if ($newAlumni->isNotEmpty())
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
              <tr>
                <td style="padding-bottom:10px;border-bottom:2px solid #10b981;">
                  <h2 style="margin:0;font-size:0.95rem;font-weight:700;color:#065f46;text-transform:uppercase;letter-spacing:0.5px;">
                    👋 New Alumni This Week
                  </h2>
                </td>
              </tr>
              <tr>
                <td style="padding:12px 0;">
                  @foreach ($newAlumni as $alumnus)
                    <span style="display:inline-block;margin:3px 6px 3px 0;background:#e8eef8;color:#003087;font-size:0.8rem;font-weight:600;padding:4px 10px;border-radius:9999px;">
                      {{ $alumnus->first_name }} {{ $alumnus->last_name }}
                      @if($alumnus->program) · {{ $alumnus->program }} {{ $alumnus->graduation_year }} @endif
                    </span>
                  @endforeach
                </td>
              </tr>
            </table>
            @endif

            @if ($upcomingEvents->isEmpty() && $latestNews->isEmpty() && $newAlumni->isEmpty())
            <p style="color:#65676b;font-size:0.9rem;margin:0 0 24px;">
              No new activity this week. Check back soon — the alumni community is always growing!
            </p>
            @endif

          </td>
        </tr>

        {{-- CTA --}}
        <tr>
          <td style="padding:0 32px 28px;">
            <table cellpadding="0" cellspacing="0">
              <tr>
                <td style="background:#003087;border-radius:9999px;">
                  <a href="{{ url('/dashboard') }}"
                     style="display:inline-block;padding:11px 28px;color:#ffffff;font-size:0.9rem;font-weight:700;text-decoration:none;">
                    Visit Alumni Network →
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        {{-- Footer --}}
        <tr>
          <td style="background:#f7f8fa;padding:16px 32px;border-top:1px solid #e0e0e0;">
            <p style="margin:0;font-size:0.75rem;color:#9a9ea5;">
              © {{ date('Y') }} GradNet &mdash; You are receiving this because you are a verified alumnus.
              This is a weekly automated digest.
            </p>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
