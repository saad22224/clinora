# Clinora SaaS — Comprehensive Architecture & Feature Readiness Audit

**Audit type:** Analysis only (no implementation, no migrations, no config/runtime changes).  
**Audited codebase:** `c:\laragon\www\clinora`  
**Runtime verified:** PHP `8.3.30`, Laravel Framework `13.18.0`, Database driver `mysql` (`php artisan about`)  
**Report date:** 2026-11-27

Evidence labels used throughout:

- **Existing** — present and usable in code
- **Partial** — present but incomplete / loosely integrated
- **Missing** — not found after source inspection
- **Confirmed** — verified from source / artisan output
- **Needs verification** — could not be fully proven from static inspection alone

---

## 1. Executive Summary

Clinora is a multi-tenant clinic SaaS built on **Laravel 13 + PHP 8.3 + MySQL**, with a **Blade + Vite + Tailwind CSS v4 + vanilla JS** frontend (Alpine is installed but not actively wired in `resources/js/app.js`). Tenant isolation is primarily **session/global-scope based** via `TenantScope` + `BelongsToClinic`, not package-based tenancy.

Clinically, the product today centers on:

- Clinic/patient CRUD and a patient medical-file page
- Appointments with **daily queue / voice calling / public tracking**
- Prescriptions with a global bilingual `drugs` catalog and browser print
- Medical file uploads (private disk)
- Diagnoses as patient-level records (not visit-scoped)
- Finance, reports, staff permissions, staff messaging, organizations/chains, admin/offline licensing

Critical architectural gap for the proposed roadmap:

> **There is no first-class medical Visit/Consultation aggregate.**  
> `VisitType` is a **priced service catalog** for booking, not a clinical encounter.  
> Diagnoses, prescriptions, and medical files are largely **patient-scoped**, not visit-scoped. Prescriptions have a nullable `appointment_id`, but create flow currently does **not** set it.

This means laboratory/radiology orders, historical visit editing, visit-linked prescriptions, and follow-ups that “reopen a visit” require either:

1. introducing a new `visits` (or equivalent) domain model, **or**
2. elevating `appointments` into a clinical encounter aggregate (with careful queue/payment/source semantics).

Prescription templates, multi-layout printing, and clinic language preference are **feasible with low/medium risk** on top of existing clinic settings + Blade print architecture. Lab/radiology and true historical visit workflows are **high complexity** because the visit layer is missing.

---

## 2. Verified Technology Stack

| Area | Finding | Status | Evidence |
|------|---------|--------|----------|
| PHP | `^8.3` / runtime `8.3.30` | Confirmed | `composer.json:12`, `php -v` |
| Laravel | `^13.8` / runtime `13.18.0` | Confirmed | `composer.json:14`, `php artisan --version` |
| Auth | Session auth (`AuthController`), `User` authenticatable | Existing | `app/Models/User.php`, `app/Http/Controllers/AuthController.php` |
| Permissions | Spatie `spatie/laravel-permission ^8` | Existing | `composer.json:17`, `User` uses `HasRoles` |
| DB | MySQL | Confirmed | `php artisan about` drivers |
| Frontend build | Vite 8 + Tailwind 4 + laravel-vite-plugin | Existing | `package.json` |
| JS interaction | Vanilla JS modal/theme system; clinic-queue/messages modules | Existing | `resources/js/app.js`, `clinic-queue.js`, `clinic-messages.js` |
| AlpineJS | Dependency present; **not imported/used in app JS** | Partial | `package.json:17`; `resources/js/app.js` has no Alpine |
| PDF libs | No DomPDF/Snappy/Browsershot in Composer require | Missing | `composer.json` require list |
| Printing | Browser `window.print()` + Blade print view | Existing | `resources/views/clinic/prescriptions-print.blade.php` |
| Images | `intervention/image ^4.1` | Existing | `composer.json:13` |
| QR | `simplesoftwareio/simple-qrcode` | Existing | `composer.json:16` |
| Tests | Pest 4 + RefreshDatabase feature suite | Existing | `composer.json` require-dev; `tests/Feature/*` |
| Locale config | `APP_LOCALE` default `en` | Confirmed | `config/app.php:81` |
| UI language | Clinic UI largely hard-coded Arabic + `dir="rtl"` | Existing / Partial | Blade layouts/views |
| Timezone | App timezone `UTC`; clinic queue uses `clinora.clinic_timezone` (Africa/Cairo default) | Confirmed | `config/app.php:68`; `config/clinora.php` |

---

## 3. Multi-Tenant Architecture

### 3.1 Tenant resolution (Confirmed)

1. Middleware `App\Http\Middleware\IdentifyTenant` resolves clinic from:
   - `session('active_clinic_id')` if accessible, else
   - `Auth::user()->clinic_id`
2. Resolved clinic is bound as `app('tenant')`.
3. Controllers resolve via `Controller::currentClinic()` / `currentClinicId()`.

Evidence:

- `app/Http/Middleware/IdentifyTenant.php:19-47`
- `app/Http/Controllers/Controller.php:12-24`
- Clinic routes use `IdentifyTenant::class` in `routes/web.php` clinic group.

### 3.2 Query isolation (Confirmed)

`BelongsToClinic` trait:

- Adds global `TenantScope`
- Auto-fills `clinic_id` on create from session/user

`TenantScope`:

```php
$clinicId = session('active_clinic_id') ?: Auth::user()->clinic_id;
$builder->where($model->getTable().'.clinic_id', $clinicId);
```

Evidence: `app/Traits/BelongsToClinic.php`, `app/Models/Scopes/TenantScope.php:12-23`.

Models using clinic isolation include (non-exhaustive, confirmed by trait usage): `Patient`, `Appointment`, `Prescription`, `MedicalFile`, `PatientDiagnosis`, `Finance` (and related clinic-owned entities).

### 3.3 Organization / multi-clinic chains (Existing)

- `Organization`, `OrganizationUser` pivot with JSON-ish `permissions`
- Org users can enter a clinic via `ClinicSwitchController` setting `active_clinic_id`
- Soft deletes on clinics exist (`add_soft_deletes_to_clinics_table`)

Evidence: `app/Models/Organization.php`, `app/Http/Controllers/Organization/ClinicSwitchController.php`, migration `2026_10_12_204518_add_soft_deletes_to_clinics_table.php`.

### 3.4 Isolation guarantees for new features

New clinical tables **must**:

- include `clinic_id` FK
- use `BelongsToClinic` (or equivalent scoped queries)
- authorize with `abort_if($model->clinic_id !== $this->currentClinicId(), 403)` as currently done for patients/prescriptions/files

Cross-clinic access is actively tested for medical files (`tests/Feature/MedicalFilePrivacyTest.php`).

---

## 4. Authentication and Authorization

### 4.1 Roles (Confirmed)

`users.role` string field used in app logic, plus Spatie roles:

Observed roles: `superadmin`, `owner`, `doctor`, `secretary` (+ Spatie names like `clinic_owner`, `secretary`).

Evidence: `User::$fillable` includes `role`; seeders/tests create these roles.

### 4.2 Permission model (Confirmed — hybrid)

- **Role bypass:** `owner`, `doctor`, `superadmin` skip Spatie checks in `EnsureClinicPermission`.
- **Secretary:** must have explicit Spatie permission for gated routes.
- Permissions are **user-assigned** for secretaries via Staff UI (`syncPermissions`).

Permission catalog in `StaffController::$permissionsList`:

- `manage_appointments`
- `use_staff_messages`
- `manage_patients`
- `manage_prescriptions`
- `manage_records`
- `manage_billing`
- `manage_settings`

Evidence:

- `app/Http/Middleware/EnsureClinicPermission.php:22-28`
- `app/Http/Controllers/Clinic/StaffController.php:16-52`
- Route groups in `routes/web.php:140-195`

### 4.3 Policies

- `app/Policies` directory: **Missing / empty (0 files)**  
Authorization is middleware + controller `abort_if` checks.

### 4.4 Implication for proposed features

- Prescription templates / layout / language settings should gate under `manage_settings` and/or `manage_prescriptions`.
- Lab/radiology create likely `manage_prescriptions` or new permission; uploads can reuse `manage_records`.
- Follow-up appointment creation should reuse `manage_appointments` and patient access checks.
- Do **not** rely on Policies unless deliberately introducing them; current convention is middleware + clinic_id asserts.

---

## 5. Patient and Medical Visit Architecture

### 5.1 Patient (Existing)

Table/model fields (from migrations + controller validation):

- Identity: `name`, `phone`, `dob`, `gender`, `age`
- Clinical notes: `medical_history`, `surgical_history`, `notes`
- Tenant: `clinic_id`

Relations: appointments, prescriptions, medicalFiles, diagnoses.

Evidence:

- `app/Models/Patient.php`
- migrations `create_patients_table`, `add_gender_to_patients_table`, `add_medical_history_to_patients_table`
- `PatientController@store/update`

UI: `resources/views/clinic/patients.blade.php`, `patient-medical-file.blade.php`.

### 5.2 “Visit” concept (Critical finding)

| Concept | Reality | Status |
|---------|---------|--------|
| Clinical Visit model | **Not present** | Missing |
| `VisitType` | Service/price catalog for bookings (`name`, `price`, `description`) | Existing (different meaning) |
| Appointment | Scheduling + queue + payment/source fields | Existing |
| Diagnosis | Patient-level (`patient_diagnoses`), dated via `diagnosed_at` | Existing / not visit-linked |
| Prescription | Patient-level; optional `appointment_id` unused by create flow | Partial |
| MedicalFile | Patient-level; `type` free string (`xray`, `lab`, `document` comment only) | Partial |

Evidence:

- No `Visit` model under `app/Models`
- `VisitType` model/controller are pricing/services
- `PrescriptionController@store` creates prescription without `appointment_id` (`app/Http/Controllers/Clinic/PrescriptionController.php:80-84`)
- Diagnoses create path: `PatientDiagnosisController@store` patient-scoped only

### 5.3 Patient medical file page (Existing aggregation UI)

`PatientController@medicalFile` loads medicalFiles, prescriptions(+items), diagnoses and renders a rich RTL dashboard UI.

**Confirmed:** good UI integration point for history, follow-ups, and future visit timeline.  
**Missing:** appointment/visit timeline section as a first-class clinical encounter.

### 5.4 Completed visits / edit locks

Because there is no visit aggregate:

- Appointment “completed” is a **status/queue_status** on appointments (`AppointmentQueueService::complete` also sets appointment `status=completed`).
- Diagnoses can be added/deleted anytime (no completion lock).
- Prescriptions can be created anytime; **no update endpoint** found (create/delete/print only).
- Medical files can be uploaded anytime.

So “reopen old visit” is not currently a defined workflow; clinical artifacts are already mostly appendable at patient level.

---

## 6. Prescription and Medication Architecture

### 6.1 Data model (Existing)

**`prescriptions`**

- `clinic_id`, `patient_id`, `appointment_id` (nullable), `notes`, timestamps

**`prescription_items`**

- `prescription_id`, `medicine_name`, `dosage`, `instructions`

**`drugs` (global catalog, not clinic-scoped)**

- `commercial_name_en` (indexed)
- `commercial_name_ar` (nullable, indexed)
- `scientific_name`, `manufacturer`, `drug_class`, `route`, `price_egp`

Evidence: migrations `create_z_prescriptions_table`, `create_prescription_items_table`, `create_drugs_table`; models under `app/Models`.

### 6.2 Snapshot vs live drug reference (Confirmed)

Prescription items store **denormalized `medicine_name` strings**.  
There is **no `drug_id` FK** on `prescription_items`.

Therefore historical prescriptions are already **snapshots** relative to the drugs catalog — good foundation for templates (editing a template/drug later won’t rewrite old Rx text).

### 6.3 Workflow (Existing / Partial)

| Capability | Status | Evidence |
|------------|--------|----------|
| List + search prescriptions | Existing | `PrescriptionController@index` |
| Drug autocomplete search (EN/AR/scientific) | Existing | `searchDrugs` |
| Create Rx + items | Existing | `store` |
| Update/edit Rx | Missing | no `update` method |
| Delete Rx | Existing | `destroy` |
| Print | Existing (browser print) | `print` + `prescriptions-print.blade.php` |
| Link to appointment on create | Partial schema / Missing behavior | nullable column unused in store |
| Frequency/duration dedicated fields | Missing | only `dosage` + `instructions` |
| PDF generation package | Missing | Composer has no PDF engine |

### 6.4 Printing details (Confirmed)

- Single Blade layout, Arabic RTL hard-coded (`lang="ar" dir="rtl"`).
- Uses clinic branding fields (`doctor_title`, `name`, `address`, `phone`, optional `logo`).
- Doctor name currently taken from `$clinic->users->first()?->name` (fragile for multi-doctor clinics).

Evidence: `resources/views/clinic/prescriptions-print.blade.php:1-120`.

### 6.5 Historical edit risk

- Editing drugs catalog: **low risk** to old Rx (snapshot names).
- If an update endpoint is added later without versioning: **high risk**.
- Templates must copy into items at issue-time (recommended), never bind issued Rx to live template rows.

---

## 7. Prescription Templates Feasibility

### Verdict: **Feasible — Medium complexity**

### Recommended design (compatible with current snapshot model)

New clinic-scoped tables (proposed; **not existing**):

1. `prescription_templates`
   - `clinic_id`, `name`, `is_active`, `created_by`, timestamps, soft deletes optional
2. `prescription_template_items`
   - `template_id`, `medicine_name`, `dosage`, `instructions`, `sort_order`

Behavior:

- Selecting a template **copies** items into the current Rx draft UI.
- User can edit draft before save.
- General notes remain free-text per prescription (`prescriptions.notes`).
- Editing/deleting a template never mutates existing `prescriptions` / `prescription_items`.

### Integration points (Existing)

- UI: `resources/views/clinic/prescriptions.blade.php` create modal/form JS
- API/controller: extend `PrescriptionController` or add `PrescriptionTemplateController`
- Auth: `manage_prescriptions` (+ maybe `manage_settings` for clinic-wide defaults)
- Isolation: `BelongsToClinic` + clinic_id asserts

### What already helps

- Item shape already matches template item shape (`medicine_name`, `dosage`, `instructions`).
- Snapshot storage already protects history.

### Gaps

- No template models/migrations/UI today (**Missing**).
- No draft/update endpoint; create is one-shot.

---

## 8. Prescription Layout and Language Feasibility

### 8.1 Layout selection

### Verdict: **Feasible without new PDF dependency — Medium**

Recommended approach:

- Add clinic settings fields, e.g. `prescription_layout` enum/string (`classic`, `minimal`, `modern`, `compact`, `detailed`, `elegant`).
- Keep browser-print architecture.
- Implement layouts as Blade partials:
  - `resources/views/clinic/prescriptions/layouts/*.blade.php`
  - selected by settings in `prescriptions-print.blade.php`
- Preview route: render sample payload with chosen layout before save.

Why this fits:

- Current print is already Blade + CSS `@media print`.
- No DomPDF dependency to fight.
- Vite/Tailwind already available (print view can keep inline critical CSS like today).

Risks:

- Multi-doctor header currently weak (`users->first()`).
- Print CSS must be validated per layout for A4.
- Avoid loading heavy app chrome into print views.

### 8.2 Language selection (AR/EN)

### Verdict: **Feasible — Medium**, with clear separation from app locale

Existing:

- App locale default `en` (`config/app.php`).
- Clinic UI mostly Arabic hard-coded.
- Drugs already bilingual (`commercial_name_en`, `commercial_name_ar`).
- Print view hard-coded Arabic RTL.

Recommended:

- Clinic setting `prescription_language` = `ar|en` (clinic-level).
- Affect **print rendering only** (labels, direction, possibly preferred drug display name).
- Do **not** change `App::setLocale()` globally unless product explicitly wants full UI i18n.
- For English layout: `lang="en" dir="ltr"`; Arabic: `lang="ar" dir="rtl"`.
- Issued medicine names remain whatever was saved in `prescription_items.medicine_name` (snapshot). Optionally store both display names at issue time later.

Needs verification:

- Whether all clinics consistently populate `commercial_name_ar`.
- Exact desired behavior when a saved item name is Arabic but print language is English.

---

## 9. Laboratory and Radiology Architecture

### Current state

| Capability | Status | Evidence |
|------------|--------|----------|
| Lab order models | Missing | no models/migrations found |
| Radiology order models | Missing | no models/migrations found |
| Test/imaging catalogs | Missing | no catalog tables |
| Attachments | Existing (generic MedicalFile) | `medical_files.type` free text; comment mentions xray/lab |
| Result entry structured fields | Missing | file upload only |
| Order print workflow | Missing | only prescription print exists |
| Multiple orders per visit | Missing | no visit aggregate |

Search across PHP/Blade/JSON for laboratory/radiology/imaging order domains found no dedicated implementation (aside from generic file `type` values / UI wording).

### Feasibility of proposed workflow

Proposed steps 1–10 are **not implementable cleanly on appointments alone** without ambiguity, because:

- One appointment currently mixes booking, queue, payment, and optionally Rx.
- Files/diagnoses are patient-scoped, not encounter-scoped.
- Completed appointment semantics are queue-oriented.

### Recommended domain design (new)

Introduce clinical encounter first (name TBD: `visits` preferred to avoid overloading `appointments`):

- `visits`: `clinic_id`, `patient_id`, `doctor_id`, `appointment_id` nullable, `status`, `visited_at`, notes, etc.
- `lab_test_catalog` / `radiology_catalog` (global or clinic-scoped — product decision)
- `lab_orders` / `radiology_orders`: belong to visit + patient + clinic; status workflow
- `lab_order_items` / `radiology_order_items`
- `order_results`: morph or typed result rows; support file attachments + manual values
- Reuse private disk upload patterns from `MedicalFileController` (`DISK = local`)

Security:

- All tables clinic-scoped
- Authorize patient/visit clinic match
- Secretaries: upload results via `manage_records`; doctors create orders via clinical permission

Complexity: **High** (largest net-new clinical domain).

---

## 10. Historical Visit Editing

### Current behavior (Confirmed)

- No visit completion lock for diagnoses/files/prescriptions.
- Appointment completion locks queue transitions via `AppointmentQueueService::transition` allow-list.
- AuditLog exists but is used mainly for admin/org/offline-license actions — **not** for clinical edits.

Evidence:

- `AppointmentQueueService` transition matrix
- `AuditLog::record` call sites concentrated under Admin/Organization/OfflineLicense
- Diagnosis destroy/store unrestricted beyond clinic ownership

### Recommendation (least disruptive)

**Short term (compatible now):**

- Treat patient medical file as mutable longitudinal record (already true).
- Allow adding files/results/diagnoses anytime.
- Do not invent “reopen” until visit aggregate exists.

**When visits exist:**

- Allow “reopen for amendment” status or always-allow additive child records after completion.
- Prefer **append-only clinical events** over in-place overwrite of diagnoses/Rx.
- Prescriptions: keep immutable after print/issue; add addendum Rx if needed.
- Introduce clinical audit events via existing `AuditLog` morph (`subject_type/subject_id`) for reopen/add-result/edit-note.

Complexity: **Medium** after visit model exists; **High** if forcing visit semantics onto appointments.

---

## 11. Appointment and Scheduling Architecture

### Existing (Confirmed)

- Model: `Appointment` with patient, doctor, visitType, prescription(hasOne), queue fields, payment/source fields.
- Controllers: clinic appointment CRUD/status/queue actions; public booking + tracking.
- Queue service: number assignment per clinic+doctor+date, call/skip/complete/pause.
- Working hours stored on clinic JSON (`working_hours`) for landing/settings — **not enforced as hard scheduler constraints in AppointmentController store**.

### Same-time multi-patient requirement

**Confirmed current behavior aligns with requirement:**

- Manual clinic create validates `scheduled_at` as date (`after_or_equal:today`) and stores `startOfDay()` — no unique time-slot conflict check.
- No overlapping appointment validator found in `AppointmentController`.
- DB unique constraint is on queue numbers (`clinic_id, doctor_id, queue_date, queue_number`), **not** on scheduled datetime.

Therefore: **no change required to allow multiple patients at same datetime**, as long as new follow-up feature does not add exclusive slot validation.

### Follow-up appointments (proposed)

Status vs proposal:

| Requirement | Current | Gap |
|-------------|---------|-----|
| Create from patient profile | Not present as dedicated action | Missing UI/endpoint |
| Suggested periods (1w/2w/1m/custom) | Missing | Missing |
| Choose available time | Weak: day-level scheduling currently | Partial (no real slot engine) |
| Link to previous visit | No visit model; no `follow_up_of` / `parent_appointment_id` | Missing |
| Show in normal schedule | Appointments index/queue exists | Existing once created |
| Any authorized patient-access user | Permission `manage_appointments` exists | Existing pattern |

Recommended schema addition:

- `appointments.parent_appointment_id` nullable **or** `follow_up_of_visit_id` once visits exist.
- Keep allowing duplicate `scheduled_at`.

Complexity: **Medium** (UI + light schema); becomes cleaner after visit aggregate.

---

## 12. Clinic Settings Architecture

### Existing clinic-level settings (Confirmed)

`SettingsController` updates clinic fields:

- identity: `name`, `doctor_title`, `doctor_bio`, `phone`, `address`, geo
- branding: `theme_color`, `doctor_image`
- hours: `working_hours` JSON
- landing: `hero_title`, `hero_subtitle`, `about_us`, `social_links`

Evidence: `app/Http/Controllers/Clinic/SettingsController.php:19-80`, clinics migrations for doctor/landing fields.

### Settings scope convention

**Clinic-level** is the established convention for branding/print identity.  
No per-user prescription preference store was found.

### Best integration points for new prefs

Add to `clinics` table (clinic-level):

- `prescription_layout` (string)
- `prescription_language` (`ar`/`en`)

Optionally later:

- `prescription_header_mode` (clinic owner vs attending doctor)

UI: `resources/views/clinic/settings.blade.php` under existing settings permission.

Preview: settings page modal/iframe hitting print preview route with dummy prescription.

---

## 13. Database Changes Required

### Reuse as-is

- `patients`, `patient_diagnoses`, `prescriptions`, `prescription_items`, `drugs`
- `medical_files` (+ private disk patterns)
- `appointments` (+ queue tables/fields)
- `clinics` settings columns
- `audit_logs`
- permission tables (Spatie)

### Likely new tables (for proposed features)

| Feature | Proposed tables | Necessity |
|---------|-----------------|-----------|
| Rx templates | `prescription_templates`, `prescription_template_items` | Required |
| Rx layout/language | columns on `clinics` | Required (no new tables) |
| Clinical visits | `visits` (+ maybe visit notes) | Strongly recommended foundation |
| Lab/Radiology | catalogs, orders, order_items, results, result_files | Required |
| Follow-ups | `parent_appointment_id` or visit link column | Required small schema |

### Integrity / indexing notes

- All new clinical tables: `clinic_id` indexed + FK cascade strategy consistent with existing (`constrained()->cascadeOnDelete()` common).
- Order/result queries: composite indexes `(clinic_id, visit_id, status)`, `(clinic_id, patient_id)`.
- Keep prescription item snapshot fields; avoid FK from issued items to templates.
- Soft deletes: clinics soft-delete exists; most clinical rows hard-delete today — decide explicitly for orders/results.

### Historical compatibility

- Existing prescriptions remain valid snapshots.
- Existing medical files can be optionally linked later via nullable `visit_id`/`order_id` without breaking old rows.
- Diagnoses can gain nullable `visit_id` later.

---

## 14. Existing Components That Can Be Reused

| Component | Reuse for |
|-----------|-----------|
| `x-layouts.clinic` + nav permission gates | All new clinic pages |
| `resources/js/app.js` CModal / flash / confirm | Template CRUD modals, order modals |
| `form-control` CSS utility + dark select fixes | Settings + clinical forms |
| Drug search endpoint pattern | Template item picker |
| `prescriptions-print.blade.php` pattern | Lab/Radiology order print |
| `MedicalFileController` private disk upload/download/preview | Result attachments |
| Patient medical file page sections | Visit timeline / follow-up CTA |
| `EnsureClinicPermission` + Staff permissions UI | New permission keys if needed |
| `BelongsToClinic` / `TenantScope` | All new models |
| `AuditLog::record` | Clinical amendment auditing |
| Appointment queue UI | Not ideal for clinical visit charting; keep separate |

---

## 15. Security and Data Isolation Considerations

Confirmed strengths:

- Tenant global scope + explicit clinic asserts on sensitive controllers
- Medical files on private `local` disk with cross-tenant download test
- Secretary least-privilege via Spatie permissions
- Org clinic enter checks via subscription/access service

Risks / gaps for new work:

1. **No Policy layer** — easy to forget `abort_if` on new controllers.
2. **Global `drugs` table** is shared across tenants (OK for catalog; do not put clinic secrets there).
3. Prescription print currently may expose wrong doctor name in multi-doctor clinics.
4. Route model binding + TenantScope usually hides cross-tenant IDs, but JSON/public endpoints must remain carefully scoped (public tracking already tokenized).
5. Uploads: keep MIME/size limits; virus scanning **Needs verification / likely Missing**.
6. Staff messaging permission (`use_staff_messages`) is a good precedent for feature-specific grants.

---

## 16. Testing and Regression Risks

### Existing relevant tests (Confirmed file list)

Under `tests/Feature`:

- `ClinicPermissionTest.php` — secretary permission gating
- `MedicalFilePrivacyTest.php` — private disk + cross-tenant denial
- `AppointmentQueueTest.php`, `AppointmentTrackingTest.php`, `AppointmentSourceTest.php`
- `StaffMessageTest.php`
- `PublicClinicLandingTest.php`
- `OrganizationEntitlementTest.php`, admin/subscription/offline license suites
- `FactoryDatasetTest.php` includes prescription/item factory creation
- `ComprehensiveAuditRegressionTest.php` mentions permission names including `manage_prescriptions`

### Notable coverage gaps (for proposed features)

- No dedicated Prescription feature tests for store/print/authorization
- No visit model tests (model absent)
- No lab/radiology tests
- No settings prescription-layout/language tests
- No follow-up appointment tests
- No clinical AuditLog assertions

### Recommended tests to add (when implementing)

1. Template CRUD isolation across clinics; issued Rx unaffected by template edit.
2. Print layout/language settings render correct `dir`/`lang` and labels.
3. Order create restricted by clinic; multiple orders per visit.
4. Result upload after visit completion.
5. Follow-up creation allows identical `scheduled_at` for two patients.
6. Secretary permission matrix for new endpoints.
7. Cross-tenant 403 on all new show/download/print routes.

### Coverage note

Exact percentage coverage was **not measured** in this audit (no coverage run executed, to avoid environment side effects). Qualitatively: queue/org/admin areas are better covered than prescriptions/settings clinical UX.

### Test inventory snapshot (Confirmed)

- **Feature:** 21 files under `tests/Feature` (plus broad regression in `ComprehensiveAuditRegressionTest.php`).
- **Unit:** essentially placeholder only (`tests/Unit/ExampleTest.php`).
- PHPUnit/Pest config uses sqlite `:memory:` for tests (not production MySQL) — safe for local suite runs.
- Dedicated prescription print/settings Feature suites remain thin; coverage is often indirect via factories/QA/regression.

---

## 17. Recommended Implementation Sequence

1. **Clinic prescription settings** (layout + language) + print partials + preview  
   - Fast win; low coupling; validates print architecture.
2. **Prescription templates** (clinic-scoped snapshot copy into Rx form)  
   - Builds on current item model.
3. **Introduce Visit aggregate** (link optional appointment; migrate UX gradually on patient medical file)  
   - Foundation for everything clinical.
4. **Wire prescriptions/diagnoses/files optionally to visit_id** (nullable backfill)  
5. **Follow-up appointments** from patient/visit page (parent link; no exclusive slot validation)
6. **Lab/Radiology catalogs + orders + print + results upload**  
7. **Historical amendment UX + clinical audit events**

Do **not** start with lab/radiology before visits; it will create irreversible schema ambiguity.

---

## 18. Estimated Complexity

| Workstream | Complexity | Notes |
|------------|------------|-------|
| Rx templates | **Medium** | Clean fit to snapshot items |
| Rx layouts (6+) + preview | **Medium** | Blade/CSS effort; no PDF package needed |
| Rx language AR/EN (print-only) | **Medium** | Drugs bilingual already helps |
| Follow-up appointments | **Medium** | Scheduler is day-oriented today |
| Visit aggregate introduction | **High** | Conceptual migration of “encounter” |
| Lab/Radiology full workflow | **High** | Net-new domain + files + statuses |
| Historical visit editing (proper) | **Medium–High** | Depends on visit model + audit rules |

---

## 19. Unresolved Questions and Architectural Risks

1. **Should clinical encounters replace appointments, or sit beside them?**  
   Appointments are deeply tied to queue/public booking/payments. Recommendation: **beside** (Visit linked to Appointment).

2. **Multi-doctor prescription header identity:** clinic brand doctor vs attending doctor per visit? Current print uses first clinic user — risk.

3. **Catalog ownership for lab/radiology:** global platform catalogs vs per-clinic custom lists vs hybrid?

4. **Are English UI translations desired**, or only English prescription print?

5. **Prescription edit-after-issue policy:** currently delete-only; product must decide immutability rules before templates/print language changes.

6. **Alpine dependency** appears unused — frontend standard is vanilla JS; new UI should follow `CModal` conventions unless Alpine is intentionally adopted.

7. **Working hours vs true slot booking:** follow-ups asking for “available time” may require a new availability engine not present today.

8. **Audit completeness:** clinical mutations mostly unaudited; regulatory expectations may require expanding `AuditLog` usage.

9. **`VisitType` naming collision:** high confusion risk in code reviews/docs; keep naming explicit (`service type` vs `clinical visit`).

10. **Needs verification in target deployments:** volume/quality of `drugs.commercial_name_ar` data; whether all environments use MySQL with same migrations applied.

11. **Spatie teams disabled** (`config/permission.php` → `'teams' => false`): secretary permissions are global package-wise; clinic isolation depends on `clinic_id` + TenantScope + controller asserts, not Spatie team scoping.

12. **Models with `clinic_id` but without `BelongsToClinic` / `TenantScope`:** confirmed examples include `VisitType`, `ClinicNotification`, `StaffMessage`, `ClinicQueueControl`. New features on similar tables must keep explicit clinic asserts (current controller pattern).

13. **Staff management routes** (`clinic.staff.*`) are **role-gated inside the controller** (`owner|doctor|superadmin`) and are **not** wrapped in `clinic.permission` middleware — secretaries cannot manage staff even if given Spatie permissions.

14. **Impersonation code smell:** `Admin\ClinicController::impersonate()` falls back to `$clinic->organization?->owner()`, but `Organization` has **no** `owner()` method — source-confirmed risk if that path is hit.

15. **Laravel `lang/` translations:** **Missing**; UI strings are hard-coded Arabic. App `locale` defaults to `en` and is unused for clinic UI.

16. **Drug import path exists:** `app/Console/Commands/ImportEgyptianDrugs.php` (`drugs:import`) loads bilingual catalog data; Rx create UI still prefers `commercial_name_en` for the stored item name.

17. **Clinic `logo` column exists**, but `SettingsController` updates `doctor_image` rather than `logo` — print header logo support is only partial.

18. **Dual public styling path:** some public/marketing pages still load CDN Tailwind alongside Vite — relevant if public print/booking pages are extended.

---

## Appendix A — Key File Index (Confirmed)

### Tenancy / Auth

- `app/Models/Scopes/TenantScope.php`
- `app/Traits/BelongsToClinic.php`
- `app/Http/Middleware/IdentifyTenant.php`
- `app/Http/Middleware/EnsureClinicPermission.php`
- `app/Http/Controllers/Controller.php`
- `app/Http/Controllers/Clinic/StaffController.php`
- `routes/web.php`

### Clinical core

- `app/Models/Patient.php`
- `app/Models/PatientDiagnosis.php`
- `app/Models/Prescription.php`
- `app/Models/PrescriptionItem.php`
- `app/Models/Drug.php`
- `app/Models/MedicalFile.php`
- `app/Models/Appointment.php`
- `app/Models/VisitType.php` (**service catalog**)
- `app/Http/Controllers/Clinic/PatientController.php`
- `app/Http/Controllers/Clinic/PatientDiagnosisController.php`
- `app/Http/Controllers/Clinic/PrescriptionController.php`
- `app/Http/Controllers/Clinic/MedicalFileController.php`
- `app/Http/Controllers/Clinic/AppointmentController.php`
- `app/Services/AppointmentQueueService.php`

### Settings / Print / UI

- `app/Http/Controllers/Clinic/SettingsController.php`
- `resources/views/clinic/settings.blade.php`
- `resources/views/clinic/prescriptions.blade.php`
- `resources/views/clinic/prescriptions-print.blade.php`
- `resources/views/clinic/patient-medical-file.blade.php`
- `resources/js/app.js`

### Tests of interest

- `tests/Feature/ClinicPermissionTest.php`
- `tests/Feature/MedicalFilePrivacyTest.php`
- `tests/Feature/AppointmentQueueTest.php`
- `tests/Feature/StaffMessageTest.php`
- `tests/Feature/FactoryDatasetTest.php`

---

## Appendix B — Feature Readiness Matrix

| Proposed feature | Existing foundation | Missing foundation | Ready to implement now? |
|------------------|---------------------|--------------------|-------------------------|
| Saved medication templates | Rx items snapshot model | template tables/UI | Yes |
| 6 print layouts + preview | Blade print | layout pack + clinic setting | Yes |
| Prescription language AR/EN | bilingual drugs; RTL print | clinic setting + EN layout | Yes (print-scoped) |
| Lab/Radiology orders | file uploads; print pattern | visit + order domain | Not cleanly |
| Results after completion | files already appendable | structured order/result link | Partial only |
| Historical visit editing | patient-level mutability | visit aggregate + audit rules | Not as specified |
| Follow-up appointments | appointments + permissions | parent link + patient UI + periods | Mostly yes |

---

## Concise Bottom Line

Clinora’s tenancy, permissions, patient file, prescription snapshotting, private uploads, and Blade printing are solid building blocks. The largest architectural blocker for the requested clinical roadmap is the **absence of a visit/encounter aggregate**; lab/radiology and true historical visit workflows should not be bolted onto queue appointments without that decision. Templates, multi-layout printing, and print-language settings can proceed first with comparatively low risk.
