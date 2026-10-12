# ResolveDesk — College Complaint Management System

Premium SaaS-style portal for students to submit complaints and for admins to triage, respond and resolve them.
Stack: **Core PHP · MySQLi · PHP sessions · Custom CSS · Vanilla JS · Font Awesome · Inter**. No frameworks.

URL when installed correctly: `http://localhost/complaint-system/`

---

## 1. Folder structure

```
complaint-system/
  index.php               public landing page
  login.php / register.php / logout.php
  dashboard.php           student dashboard
  submit_complaint.php    student: new complaint
  my_complaints.php       student: history + search/filter/pagination
  complaint_details.php   student: detail + timeline (ownership enforced)
  complaint.php           alias → complaint_details.php (back-compat)
  database.sql            full schema + seed admin/student
  admin_seed.php          CLI-only admin creator (refuses HTTP)
  admin/
    dashboard.php         college-wide stats + activity
    complaints.php        search/filter/sort/paginate + open
    view_complaint.php    detail + student card + update form + archive
    update_status.php     POST handler (transactional + history)
    delete_complaint.php  POST soft-delete (archive)
    users.php             students + counts + activate/deactivate
  config/database.php     single MySQLi connection (utf8mb4)
  includes/
    functions.php         e(), CSRF, flash, categories, badges, time_ago
    auth.php              secure session start + require_login()
    student_auth.php      student-only guard
    admin_auth.php        admin-only guard (server-side)
    header.php / footer.php  app shell: sidebar, topbar, toasts
  css/style.css           design system + landing
  css/dashboard.css       sidebar/topbar/widgets/tables/timeline
  css/auth.css            login/register split screen
  css/responsive.css      tablet + mobile
  js/app.js               sidebar, toasts, confirms, toggles, submit feedback
  js/validation.js        client validation (server is authoritative)
```

---

## 2. XAMPP setup (Windows)

1. Install **XAMPP** (PHP 8.x + MySQL/MariaDB) from apachefriends.org.
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Copy this folder so the path is exactly:
   ```
   C:\xampp\htdocs\complaint-system\
   ```
   (If your repo lives elsewhere, copy — don't move — the `complaint-system` folder there.)
4. Confirm `http://localhost/` shows the XAMPP welcome page (Apache works).

## 3. Import the database (phpMyAdmin)

1. Open `http://localhost/phpmyadmin/`.
2. Click **Import** (top bar) → **Choose File** → select `complaint-system/database.sql` → **Go**.
3. You should see database `college_complaints` with tables `users`, `complaints`, `complaint_history`.
4. (Alternative, shell): `C:\xampp\mysql\bin\mysql.exe -u root < complaint-system\database.sql`

If connection fails, check `config/database.php` — defaults are `localhost / root / (empty) / college_complaints`, which matches stock XAMPP.

## 4. Run the app

Open `http://localhost/complaint-system/` — landing page → **Register** as a student, or **Login** with:

| Role | Email | Password |
|---|---|---|
| Admin | `admin@college.edu` | `Admin@123` |
| Student (demo) | `student@college.edu` | `Student@123` |

> Change both passwords after first login (register a new student normally; for admin, update via SQL `password_hash` or re-run `admin_seed.php` then delete demo rows).

### Create a real admin securely (no public admin registration exists)

```bat
cd C:\xampp\htdocs\complaint-system
C:\xampp\php\php.exe admin_seed.php "College Admin" admin2@college.edu "StrongPass123"
```

Then **delete or restrict `admin_seed.php`** before any shared/demo deployment.

### Create a student

Just use **Register** in the browser — role is forced to `student` on the server even if the form is tampered with.

---

## 5. Testing checklist

**Student**
- [ ] Register validates: empty, bad email, short/weak password, mismatch, duplicate email.
- [ ] Login wrong password → error; correct → lands on `/dashboard.php` with toast.
- [ ] Dashboard numbers equal actual rows (check against phpMyAdmin `SELECT status, COUNT(*) ...`).
- [ ] Submit with empty fields → inline errors (JS) and server errors (disable JS / curl POST to confirm).
- [ ] New complaint appears in My Complaints with `Pending` badge; details page shows timeline step 1.
- [ ] Search/filter/pagination works; visiting another student's `complaint_details.php?id=` → 403.
- [ ] Direct GET to `admin/dashboard.php` as student → 403/redirect.
- [ ] Logout → session destroyed; back button doesn't reveal dashboard (re-login required).

**Admin**
- [ ] Login as admin → `/admin/dashboard.php` with college-wide counts.
- [ ] Manage Complaints: search `Block B`, filter `Pending`, sort oldest, paginate.
- [ ] Open complaint → change status to `In Progress` + response → student view updates; history row added with admin name + timestamp.
- [ ] Resolve then Close, then reopen to `In Progress` (all transitions allowed, all logged).
- [ ] Archive (trash icon, confirm dialog) → disappears from queues, `is_deleted=1` in DB, history note added.
- [ ] Users page: counts correct, deactivate student → that student can't log in; reactivate → can.
- [ ] CSRF: POST without token (e.g. via curl) is rejected.
- [ ] XSS: submit `<script>alert(1)</script>` as subject → rendered as text, not executed.

**Responsive**: resize to 390px — sidebar becomes hamburger overlay, tables scroll, landing stacks.

---

## 6. Key concepts (for viva / demo)

- **PHP sessions**: `session_start()` creates a server-side store keyed by a cookie. We keep only `user_id/name/role` there — never the password. `session_regenerate_id(true)` after login stops fixation attacks.
- **password_hash / password_verify**: one-way bcrypt. DB holds `$2y$...` hashes; login verifies without ever decrypting. Cost factor makes brute force slow.
- **Prepared statements**: `prepare()` + `bind_param()` sends SQL and data separately, so `' OR '1'='1` can't break out — the fix for SQL injection. Every user value in this app goes through one.
- **CRUD**: Create (`INSERT` complaint/user), Read (`SELECT` dashboards/lists), Update (`UPDATE` status/response), Delete (soft `UPDATE is_deleted=1`, preserving audits).
- **AuthZ**: `student_auth.php` / `admin_auth.php` run on the server on every page; plus per-row ownership (`WHERE user_id = ?`) so students can't guess IDs.
- **CSRF tokens**: random per-session secret in a hidden field; state-changing POSTs verify it, so evil-site forms can't act as you.
- **Transactions**: `update_status.php` wraps `UPDATE complaints` + `INSERT history` in `begin/commit/rollback` — both succeed or neither does.
- **Soft delete**: archive flag instead of `DELETE`, so reports and history stay consistent.

---

## 7. Security notes & limits

- Development defaults use XAMPP `root` with no password — fine locally, **never** deploy as-is. Use a least-privilege MySQL user + HTTPS + `Secure` cookies in production.
- `admin_seed.php` refuses HTTP; delete it before sharing the folder publicly.
- Errors log to the server (`error_log`) and show generic messages — credentials never leak to the browser.
