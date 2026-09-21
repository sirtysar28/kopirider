<?php
    /**
     * Shared HTML e-mail shell for Kopi Rider.
     *
     * Expects (optional) variables:
     *  - $preheader  string  small teaser line shown in the inbox preview
     *  - $title      string  big heading inside the card
     *
     * Wrapped content gets a cream card. Use $actionUrl + $actionText
     * to render the big call-to-action button.
     */
    $logo = mail_logo();
    $logoSrc = $logo['url'];
    // Inline-embedded images display even when the client blocks remote images
    if (! empty($logo['path']) && isset($message) && $message) {
        try { $logoSrc = $message->embed($logo['path']); } catch (\Throwable) {}
    }

    $contactEmail = setting('contact_email', 'hello@kopirider.id');
    $waNumber = preg_replace('/\D+/', '', setting('whatsapp_number', '6281234567890'));
    $instagram = setting('instagram_url');
    $tiktok = setting('tiktok_url');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="color-scheme" content="light" />
  <title>@yield('email.title', 'Kopi Rider')</title>
  <!--[if mso]><style>body,table,td{font-family:Arial,Helvetica,sans-serif !important;}</style><![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#EFE3C4;-webkit-text-size-adjust:100%;">

  {{-- preheader (hidden teaser text) --}}
  <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#EFE3C4;">
    {{ $preheader ?? 'Kopi Rider — Bali coffee cart & motorcycle sidecar experiences.' }}
  </div>

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#EFE3C4;padding:24px 12px;">
    <tr>
      <td align="center">

        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;">

          {{-- ============ HEADER — logo on dark brown ============ --}}
          <tr>
            <td style="background-color:#2A1B12;border-radius:24px 24px 0 0;padding:28px 36px;text-align:center;">
              <a href="{{ url('/') }}" target="_blank" rel="noopener" style="text-decoration:none;">
                <img src="{{ $logoSrc }}" width="56" height="56" alt="Kopi Rider"
                     style="display:inline-block;width:56px;height:56px;border-radius:14px;border:0;vertical-align:middle;" />
              </a>
              <div style="font-family:Georgia,'Times New Roman',serif;font-size:24px;line-height:1.2;color:#F6ECD9;padding-top:12px;font-weight:bold;letter-spacing:.5px;">
                KOPI&nbsp;RIDER
              </div>
              <div style="font-family:Arial,Helvetica,sans-serif;font-size:11px;letter-spacing:3px;color:#C98A34;padding-top:4px;text-transform:uppercase;">
                Bali &middot; Coffee &middot; Adventure
              </div>
            </td>
          </tr>

          {{-- ============ GOLD DIVIDER ============ --}}
          <tr><td style="height:4px;background-color:#C98A34;font-size:0;line-height:0;">&nbsp;</td></tr>

          {{-- ============ CONTENT CARD ============ --}}
          <tr>
            <td style="background-color:#FDFBF6;border-radius:0 0 24px 24px;padding:36px 36px 28px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                @hasSection('email.heading')
                  <tr>
                    <td style="font-family:Georgia,'Times New Roman',serif;font-size:24px;line-height:1.3;color:#2A1B12;padding-bottom:14px;font-weight:bold;">
                      @yield('email.heading')
                    </td>
                  </tr>
                @endif

                <tr>
                  <td style="font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.65;color:#4A382B;">
                    @yield('email.content')
                  </td>
                </tr>

                {{-- big CTA button --}}
                @if (! empty($actionUrl))
                  <tr>
                    <td align="center" style="padding:26px 0 8px;">
                      <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                        <tr>
                          <td style="background-color:#8B4226;border-radius:100px;">
                            <a href="{{ $actionUrl }}" target="_blank" rel="noopener"
                               style="display:inline-block;padding:15px 38px;font-family:Arial,Helvetica,sans-serif;font-size:15px;font-weight:bold;color:#FFFFFF;text-decoration:none;border-radius:100px;letter-spacing:.3px;">
                              {{ $actionText ?? 'Open link' }} &rarr;
                            </a>
                          </td>
                        </tr>
                      </table>
                      <div style="font-family:Arial,Helvetica,sans-serif;font-size:12px;color:#A6906F;padding-top:12px;word-break:break-all;">
                        {{ $actionUrl }}
                      </div>
                    </td>
                  </tr>
                @endif

                {{-- thin divider before the note --}}
                <tr><td style="padding-top:24px;"><table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td style="border-top:1px solid #EFE3C4;font-size:0;line-height:0;">&nbsp;</td></tr></table></td></tr>

                <tr>
                  <td style="font-family:Arial,Helvetica,sans-serif;font-size:12.5px;line-height:1.6;color:#A6906F;padding-top:14px;">
                    @yield('email.note')
                  </td>
                </tr>

              </table>
            </td>
          </tr>

          {{-- ============ FOOTER ============ --}}
          <tr>
            <td style="padding:26px 20px 8px;text-align:center;">
              <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin:0 auto 14px;">
                <tr>
                  <td style="padding:0 8px;">
                    <a href="https://wa.me/{{ $waNumber }}" target="_blank" rel="noopener"
                       style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;color:#8B4226;text-decoration:none;background-color:#F6ECD9;border-radius:40px;padding:8px 16px;display:inline-block;">&#128172; WhatsApp</a>
                  </td>
                  @if ($instagram)
                    <td style="padding:0 8px;">
                      <a href="{{ $instagram }}" target="_blank" rel="noopener"
                         style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;color:#8B4226;text-decoration:none;background-color:#F6ECD9;border-radius:40px;padding:8px 16px;display:inline-block;">&#128247; Instagram</a>
                    </td>
                  @endif
                  @if ($tiktok)
                    <td style="padding:0 8px;">
                      <a href="{{ $tiktok }}" target="_blank" rel="noopener"
                         style="font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:bold;color:#8B4226;text-decoration:none;background-color:#F6ECD9;border-radius:40px;padding:8px 16px;display:inline-block;">&#127916; TikTok</a>
                    </td>
                  @endif
                </tr>
              </table>
              <div style="font-family:Arial,Helvetica,sans-serif;font-size:12.5px;color:#5E4A38;line-height:1.7;">
                <b style="color:#2A1B12;font-family:Georgia,serif;font-size:14px;">Kopi Rider</b><br />
                {{ $contactEmail }} &middot; <a href="https://wa.me/{{ $waNumber }}" style="color:#8B4226;text-decoration:none;">+{{ $waNumber }}</a><br />
                Bali, Indonesia
              </div>
              <div style="font-family:Arial,Helvetica,sans-serif;font-size:11px;color:#A6906F;line-height:1.8;padding-top:12px;">
                &copy; {{ date('Y') }} Kopi Rider. All rights reserved.<br />
                You received this e-mail because it is linked to a Kopi Rider staff account.<br />
                Website by <a href="https://digimagine.web.id" target="_blank" rel="noopener" style="color:#8B4226;font-weight:bold;text-decoration:none;">Digimagine</a>
              </div>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
