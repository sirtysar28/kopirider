# ☕ Kopi Rider — Coffee Truck Booking & Lead Generation Platform

Built with **Laravel 11** (targets **PHP 8.2+**) and the light, This is **not** an e-commerce site: there is no cart, no checkout and no online ordering. The whole platform exists to turn visitors into
**qualified event enquiries** that flow into WhatsApp, where a human confirms every booking.

---

## ✨ Features

### Public website
| Page | Route | Notes |
|---|---|---|
| Landing page | `/` | Hero (photo/video slot), packages with starting prices, "Why us", social section |
| Check your date | `/check-date` | Standalone shareable booking flow (also opens as an overlay from any page, no reload) |
| Packages | `/packages` | Starting prices visible **before** anyone has to message |
| Free events | `/free-events` | Simple page explaining the free option + minimum guarantee — link it straight to organisers |
| Gallery | `/gallery` | Starts empty on purpose (no stock photos), lazy-loaded, built to grow to 30+ images |

### Booking flow (the heart of the system)
1. **Pick a date** — live calendar: 🟢 available · 🟡 someone is asking · 🔴 booked. Tap answers instantly.
2. **Event type + guest range** — buttons only, no typing.
3. **Dynamic step** — paid events show packages with *starting prices*; market/festival/community shows the **free option** explanation.
4. **Contact** — name + WhatsApp only, with a pre-written natural message preview.

### Lead capture (the part that must never lose a lead)
- **Partial leads are saved automatically** after every step — someone who quits halfway is still recorded for follow-up.
- On submit the lead is **committed to the database first**; the WhatsApp redirect only
  fires after the save is confirmed (`saved: true`).
- Even if WhatsApp never opens, the lead exists — reference number `KR-0001` shown to the customer.
- Requested dates automatically turn 🟡 *enquiry* on the public calendar.

### Admin panel (`/admin`, small by design)
- **Dashboard** — leads, revenue, booked days at a glance.
- **Leads** — completed & partial, filters, status workflow (new → contacted → negotiating → confirmed / lost), one-click "Open WhatsApp chat".
- **Calendar** — set any date available/enquiry/booked; notes; saves instantly.
- **Packages** — full CRUD with starting prices, features, paid/free audience.
- **Payments** — deposit/balance records per lead with **Midtrans payment link** field + bank transfer fallback (`pending → paid`).
- **Media** — upload hero image + gallery photos (stored on `public` disk).
- **Analytics** — conversion funnel with the exact events from the workflow doc.
- **Settings** — WhatsApp number, Instagram/TikTok URLs, hero badge text.

### Login
- Staff login at `/login` with a **password peek toggle** (eye button) — see what you typed before submitting.

### Analytics events tracked (`/api/analytics`)
`booking_flow_opened` → `date_selected` → `event_type_selected` → `guest_range_selected` → `package_viewed` → `package_selected` → `contact_screen_viewed` → `lead_submitted` → `whatsapp_opened`

### Safety rule (enforced by design)
The system **never confirms a booking or a final price** automatically. Green dates say
*"This date looks available. A member of our team will confirm it with you."*

---

## 🚀 Quick start

```bash
cd kopi-rider

# 1. install dependencies
composer install

# 2. configure
cp .env.example .env
php artisan key:generate

# 3. database (SQLite — zero setup) + seed demo packages/settings/calendar
touch database/database.sqlite
php artisan migrate --seed

# 4. storage link (gallery & hero uploads)
php artisan storage:link

# 5. run
php artisan serve
```

Open **http://127.0.0.1:8000**

### Default admin credentials
| | |
|---|---|
| URL | `/login` |
| E-mail | `admin@kopirider.id` |
| Password | `kopirider123` |

> ⚠️ Change the password before going live (register a new user + delete the seeder account, or run `php artisan tinker` → `User::first()->update(['password' => bcrypt('yours')])`).

### MySQL / MariaDB (production)
The `.env.example` contains the commented MySQL block — set it, create the database,
then `php artisan migrate --seed`.

---

## 🗂 Structure

```
app/
├── Http/Controllers/
│   ├── HomeController, PageController        # public pages
│   ├── CalendarApiController                 # GET  /api/calendar
│   ├── LeadApiController                     # POST /api/leads (partial & complete)
│   ├── AnalyticsController                   # POST /api/analytics
│   ├── AuthController                        # login/logout with password peek
│   └── Admin/                                # dashboard, leads, calendar, packages,
│                                             # payments, gallery, settings, analytics
├── Models/  Package, Lead, DateStatus, Payment, AnalyticsEvent, Media, Setting
└── Support/helpers.php                       # setting(), wa_link()

database/migrations   packages · date_statuses · leads · payments
                      · analytics_events · media · settings
database/seeders      admin user, 3 packages, settings, sample calendar days

public/
├── css/app.css        public theme (light cream / clay / gold, smooth animations)
├── css/admin.css      admin theme
├── js/app.js          scroll-reveal animations
└── js/booking-flow.js 4-step flow, partial saves, save-before-WhatsApp

resources/views/
├── layouts/public.blade.php, layouts/admin.blade.php
├── partials/booking-flow.blade.php           # shared modal
├── home, check-date, packages, free-events, gallery, auth/login
└── admin/ (dashboard, leads, calendar, packages, gallery, settings, analytics)
```

## 🔌 Payment flow (deposit → balance)

### Midtrans — enable/disable from the admin
**Admin → Settings → Payments** has a master switch:

| Setting | Purpose |
|---|---|
| Enable Midtrans payment links | ON/OFF — when OFF, no API calls are made and payment records default to bank transfer |
| Environment | Sandbox (testing) / Production (live) |
| Merchant ID, Client key, Server key | From your Midtrans dashboard |
| Bank transfer details | Fallback text shown on the lead page for easy copy to WhatsApp |

When enabled, staff get a **⚡ Generate Midtrans link** button on each pending payment —
it calls the Midtrans **Payment Link API** and stores the hosted URL on the record.

### Automatic status updates (webhook)
Set the notification URL in the Midtrans dashboard to:
```
https://your-domain/api/midtrans/notification
```
When a customer pays: signature verified → payment becomes **paid** → deposit/full
payments auto-**confirm the lead** and **lock the event date as booked** 🔒 on the public calendar.

1. Discuss details on WhatsApp (button on every lead).
2. Admin → Lead → Payments → create a *deposit* → generate (or paste) the Midtrans link.
3. Send the link via WhatsApp; the webhook flips it to paid, or staff can mark it manually.
4. Repeat with a *balance* record before the event.

## 🛣 Roadmap (from the workflow document)
- Phase 2: WhatsApp Business API integration — automated first reply that can check
  the calendar (but still never confirms bookings).

## 🎨 Branding & identity

| File | Use |
|---|---|
| `public/img/logo.svg` | Full logo (icon + wordmark) — for print, invoices, decks |
| `public/img/logo-icon.svg` | Icon used in header, footer, admin sidebar, login page |
| `public/favicon.ico` + `img/favicon.svg` + `img/favicon-96.png` | Browser tab icons |
| `public/img/apple-touch-icon.png` | iOS home-screen icon |

Colours: cream `#F6ECD9` · ink `#2A1B12` · clay `#8B4226` · gold `#C98A34` · sage `#5B6B45`.
Fonts: **Fraunces** (headings) + **Work Sans** (body) via Google Fonts.

Every page carries the footer credit **“Developed by [Digimagine](https://digimagine.web.id)”**
(public site, admin panel and login page).

## 🧰 Tech stack
Laravel 11 · PHP 8.2 · SQLite (dev) / MySQL-MariaDB (prod) · Vanilla JS + Blade —
no build step required, everything is mobile responsive with smooth animations.
# kopirider
