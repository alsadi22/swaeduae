# 📜 SawaedUAE – Roadmap & Project Memoirs (as of 2025-08-11)

## Phase 1 – Core Foundation ✅ (Completed / Stable)
- Laravel backend installed on **shared hosting (Tasjeel.ae)**.
- Authentication system (login, register) with roles: **admin**, **organization**, **volunteer**.
- Blade layout with **sand/teal Bootstrap** UI.
- Multilingual base (Arabic 🇦🇪 / English 🇬🇧) with RTL support.
- Admin middleware & routes.
- Role seeding & default admin/org accounts.
- SEO routes (`sitemap.xml`, `robots.txt`).
- Admin dashboard placeholder.
- Manage users (CRUD + activate/deactivate).
- Manage organizations (owner linking ready).
- Manage opportunities (CRUD).
- Export CSV for applications & volunteer hours.
- Organization dashboard routes ready.
- Create/manage volunteer opportunities.
- Attendance QR code generation for events.
- Apply for opportunities.
- Attendance check-in & check-out via QR code.
- Certificates public verification page (basic).
- Profile page placeholder.

---

## Phase 2 – Volunteer System & Event Flow 🚧 (In Progress)
- ✅ Volunteer application to opportunities.
- ✅ Attendance tracking (check-in/check-out).
- ✅ Volunteer hours tracking.
- ✅ Volunteer profile dashboard:
  - /me/applications
  - /me/certificates
- ⏳ Volunteer badges/rewards.
- ⏳ Organization dashboard:
  - Manage events
  - View volunteers
  - Export lists

**Next Task:**  
- Public Opportunities Listing with Filters UI *(category, location, date, keyword; GET parameters for shareable links)*

---

## Phase 3 – Certificates, Reports & Public Engagement 📅 (Planned)
- Automatic certificate generation after event completion (PDF + QR verification).
- Public certificate verification page upgrade (searchable, responsive).
- Admin dashboard with analytics (volunteers, orgs, events, hours, charts).
- Leaderboards (volunteers, organizations).
- Public pages (About, Contact, FAQ, Partners).

---

## Phase 4 – Advanced Features & Polish 💡 (Planned)
- Responsive, mobile-first design optimizations.
- WhatsApp/email notifications for event reminders & certificates.
- Event categories/filters/regions for search (to complement Phase 2 UI).
- SEO optimization + sitemap.
- Accessibility compliance.
- Final UI refinements.
- Launch & public testing.

---

# 🗂 Project Memoirs & Reference

**Path:** `/home3/vminingc/swaeduae.ae/laravel-app`  
**Theme:** Sand/teal Bootstrap UI with RTL/Arabic support.  
**Hosting:** Shared hosting on Tasjeel.ae (No Docker, Redis, Gunicorn, Node.js build steps).  
**Frontend:** Pure PHP Blade templates, Bootstrap styling, no Vue/React.  
**Roles:** admin / organization / volunteer (via `spatie/laravel-permission`).  

## Admin Tools
- Users CRUD with activate/deactivate.
- Organizations CRUD.
- Opportunities CRUD.
- CSV exports.

## Volunteer Tools
- Apply to opportunities.
- View /me/applications.
- View /me/certificates.
- Attendance QR codes.
- Hours tracking.

## Certificates
- Basic verification page.
- Public-facing verification planned for Phase 3.

## Next Milestone
- Implement public opportunities filters UI *(Phase 2 final step before Phase 3)*.
