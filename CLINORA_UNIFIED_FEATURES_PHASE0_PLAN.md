# Clinora Online + Offline — Phase 0 Implementation Plan

**Status:** READ-ONLY / AWAITING EXPLICIT APPROVAL  
**Date:** 2026-11-27  
**Online root:** `C:\laragon\www\clinora`  
**Offline root:** `C:\laragon\www\clinora_offline`  

Evidence labels: **Existing** / **Partial** / **Missing** / **Confirmed** / **Needs verification**

This document does **not** authorize implementation. Work starts only after explicit approval of this plan and the unresolved decisions below.

---

## 1. Executive Summary

Both editions share a Laravel 13 / PHP 8.3 / Blade / Vite / Tailwind v4 / Spatie / Pest clinical core, but they are **not** identical products.

| Area | Online | Offline |
|------|--------|---------|
| Database | MySQL | SQLite (`DB_CONNECTION=sqlite`) |
| Tenancy | Multi-clinic SaaS + organizations | Single clinic install; same `TenantScope` code retained |
| Public booking / landing | **Existing** | **Disabled** via `edition.offline` (no public HTTP booking) |
| Doctor identity on Rx | Uses `$clinic->users->first()` on print (**Confirmed** bug/smell) | **`doctor_id` + `DoctorProfile` + `ClinicDoctorService`** already on prescriptions |
| Patient delete | Hard delete | SoftDeletes + medical archive patterns |
| Packaging | N/A (web deploy) | Inno Setup + .NET Agent/Control Center/Client Assistant |
| Clinical Visit aggregate | **Missing** | **Missing** |
| Lab/Radiology orders | **Missing** | **Missing** |
| Medication templates | **Missing** | **Missing** |
| Rx layouts / print language settings | **Missing** | **Missing** |
| Appointment hour selection | Date only; `startOfDay()` strips time | Same |
| Age | DOB calc in medical-file header; editable stored `age` field remains | Same pattern (header already prefers DOB) |

**Recommended implementation order (after approval):** Phase 1 Age+Time → Phase 2 Rx templates/layouts/language → Phase 3 Visit foundation → Phase 4 Follow-ups → Phase 5 Lab/Radiology → Phase 6 Regression + Offline EXE.

---

## 2. Verified Stack Comparison

### 2.1 Online (`clinora`) — Confirmed

- PHP `^8.3`, Laravel `^13.8` (runtime previously verified 13.18 / PHP 8.3.30).
- MySQL.
- Blade + Vite + Tailwind v4 + vanilla JS (Alpine dependency unused).
- Spatie permissions; `EnsureClinicPermission`; no Policies.
- `TenantScope` + `BelongsToClinic` + `IdentifyTenant`.
- Browser print for prescriptions; no DomPDF.
- Prior audit: `CLINORA_FEATURES_ARCHITECTURE_AUDIT.md`.

### 2.2 Offline (`clinora_offline`) — Confirmed

- Same Composer clinical stack (`composer.json`: PHP ^8.3, Laravel ^13.8, Spatie, Intervention, QR, Pest).
- `.env`: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`.
- `config/clinora.php`: `edition` env (`CLINORA_EDITION`, default `online` in config; production installs set offline).
- Extra models: `DoctorProfile`, `RegisteredDevice`, `PairedClient`, `ClientPairingRequest`, archive-related services.
- Middleware extras: `EnsureRegisteredDevice`, `EnsureOfflineLicense`, edition gates.
- Desktop: `desktop/Clinora.Agent`, `Clinora.ControlCenter`, `Clinora.ClientAssistant`, shared libs (.NET).
- Installer: `installer/build-release.ps1` + `installer/clinora.iss`.
- Existing dist artifacts under `dist/` (e.g. `Clinora-Setup-1.0.0.exe`) — **do not overwrite until new build verified**.
- Public routes wrapped in `edition.online`; Offline redirects `/` → login; setup wizard replaces SaaS registration.

---

## 3. Feature-by-Feature Gap Matrix

| # | Feature | Online | Offline | Shared notes |
|---|---------|--------|---------|--------------|
| 1 | Clinical visit timeline + reopen for results | **Missing** Visit model | **Missing** Visit model | Appointments ≠ visits; VisitType = price catalog |
| 2 | Lab / radiology orders + catalogs + print + results | **Missing** | **Missing** | Closest: `MedicalFile.type` free text |
| 3 | Clinic medication templates | **Missing** | **Missing** | Must snapshot-copy into Rx items |
| 4 | ≥6 Rx print layouts + settings + preview | **Missing** (1 Blade print) | **Partial** doctor identity better; still 1 layout | Keep browser print |
| 5 | Rx print language AR/EN (print-only) | **Missing** | **Missing** | Drugs catalog bilingual; Rx item stores text snapshot |
| 6 | Follow-up from medical file | **Missing** visit link | **Missing** visit link | Appointments exist; no `follow_up_of_visit_id` |
| 7 | Appointment hour selection; allow duplicates | **Partial** date only + `startOfDay()` | **Partial** same; **no public booking** | Offline already has day-level doctor schedule/daily_limit — keep; do **not** add slot exclusivity |
| 8 | Automatic age from DOB | **Partial** display calc; form still stores age | **Partial** same | Prefer accessor + JS; fallback to stored age if no DOB |

---

## 4. Critical Architecture Differences Affecting Implementation

### 4.1 Do not copy blindly Online → Offline

1. **Public booking time selector** applies to Online only. Offline Feature 7 = clinic dashboard appointment form only.
2. **Doctor resolution:** Offline already has `ClinicDoctorService` and `prescriptions.doctor_id`. Online must add equivalent `doctor_id` (nullable for history) before fixing print identity — Offline mostly needs layout/language wiring to existing doctor relation.
3. **Deletion:** Offline archives medical records (`MedicalRecordArchiveService`); Online hard-deletes prescriptions. New visit/order delete behavior must follow each edition’s convention.
4. **Patients SoftDeletes** Offline only — queries/validators must use `withoutTrashed` / edition-aware exists rules (Offline already does this in Rx store).
5. **SQLite vs MySQL:** avoid MySQL-only indexes/JSON operators; keep migrations portable (Laravel schema builder).
6. **Device/license middleware** Offline — new write endpoints must remain compatible with read-only license mode (`EnsureOfflineLicense`).
7. **AI / WhatsApp** disabled Offline — do not couple new clinical flows to them.

### 4.2 Appointment time vs queue (both)

Confirmed both editions:

```php
// AppointmentController::store — Online ~L94, Offline ~L101
$validated['scheduled_at'] = Carbon::parse($validated['scheduled_at'])->startOfDay();
```

Queue services also normalize queue date to start of day. **Plan:** stop stripping time on create/update; keep queue date derived from the **calendar date** of `scheduled_at` (existing `queueDateFor` / `startOfDay` for queue_date only). Display hour in appointment lists. **Do not** add unique constraints on `(doctor_id, scheduled_at)`.

`created_at` already provides second-precision booking order — **no new timestamp column** unless verification shows otherwise.

### 4.3 Offline existing booking constraints (must preserve, not expand)

`ClinicDoctorService::ensureCanBeBooked()` rejects:

- doctor not working that weekday;
- doctor `daily_limit` reached for that **date**.

These are **not** same-hour exclusivity. Feature 7 forbids hiding/disabling hours and overlap engines. Plan: add hour UI + persist time; leave day-level doctor rules as-is unless you approve changing them.

---

## 5. Proposed Domain Design (both editions)

### 5.1 Clinical Visit (Feature 1 foundation)

**New table `visits` (name TBD — recommend `clinical_visits` only if `visits` conflicts; prefer `visits`):**

| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| clinic_id | FK | Tenant |
| patient_id | FK | |
| appointment_id | nullable FK | Link when visit started from appointment |
| doctor_id | nullable FK → users | Treating doctor |
| visited_at | datetime | Clinical date/time |
| status | string | e.g. `open`, `completed` — **completion must NOT block** adding results/attachments/orders |
| chief_complaint | nullable text | Optional |
| clinical_notes | nullable text | Doctor notes for this encounter |
| created_by | nullable FK | |
| timestamps | | |

**Relationships:**

- `Patient` hasMany `Visit`
- `Visit` belongsTo Patient, Clinic, optional Appointment, optional Doctor
- Nullable FKs on existing clinical tables:
  - `patient_diagnoses.visit_id` nullable
  - `prescriptions.visit_id` nullable (keep `appointment_id` as-is)
  - `medical_files.visit_id` nullable
- **Do not** backfill historical rows into fabricated visits.

**UI:**

- Patient medical file: chronological visit timeline.
- Visit show page: diagnoses, notes, prescriptions, lab/rad orders, results, attachments for that visit.
- Create visit: from medical file and/or from appointment action (“بدء زيارة”) — **needs product decision** (see §11).

### 5.2 Lab & Radiology (Feature 2)

**Catalog tables (recommended):**

- `lab_tests` — global seeded catalog (like `drugs`), columns: `code` nullable, `name_ar`, `name_en`, `category`, `is_active`, indexes on names.
- `radiology_exams` — same shape.

Ownership recommendation (needs approval): **global seed catalogs** shipped in both editions (Offline must work offline; Online must not depend on remote API). Clinics may later get clinic-specific favorites — **out of scope unless approved**.

**Order tables:**

- `lab_orders`: clinic_id, patient_id, visit_id, ordered_by, status (`ordered|partial|completed|cancelled`), notes, ordered_at
- `lab_order_items`: lab_order_id, lab_test_id nullable, name_snapshot, status
- `radiology_orders` + `radiology_order_items` (mirror)

**Results:**

- Prefer result rows linked to order/item + optional `medical_files` attachment (private disk), plus optional free-text result.
- Multiple orders per visit allowed.
- Print: Blade + `window.print()`, parallel to prescriptions-print conventions.

**Catalog content:** Start with a **modest practical seed** (common CBC/chemistry/urine + common X-ray/US/CT/MRI labels in AR/EN). Exact list requires approval — will not invent a huge fabricated formulary.

### 5.3 Medication templates (Feature 3)

**Tables:**

- `prescription_templates`: clinic_id, name, created_by nullable, timestamps (+ soft delete optional)
- `prescription_template_items`: template_id, medicine_name, dosage, instructions (mirror `prescription_items`; **no notes field**)

**Behavior:**

- CRUD under `manage_prescriptions` (or new permission — see §11).
- On Rx create: AJAX/load template → **copy** items into client draft → user edits → existing `PrescriptionController::store` saves snapshots.
- Template edit/delete never touches issued prescriptions.

### 5.4 Prescription layouts + language (Features 4–5)

**Clinic columns (clinic-level settings):**

- `prescription_layout` string default `classic` (values: `classic`, `minimal`, `modern`, `compact`, `detailed`, `elegant` — ≥6)
- `prescription_print_locale` string default `ar` (`ar`|`en`)

Store on `clinics` table via SettingsController (with `manage_settings`). Preview route renders sample data without saving a real prescription.

Print controller selects Blade partial: `resources/views/clinic/prescriptions/print/{layout}.blade.php` + shared data + locale labels array. Direction: `dir=rtl` for ar, `dir=ltr` for en. Medication name: use stored snapshot; for **new** items optionally prefer AR/EN from drug search at create time; **never rewrite** historical `medicine_name`.

**Doctor identity:**

- Online: add nullable `prescriptions.doctor_id`; set on create via practicing-doctor resolution (port Offline `ClinicDoctorService` pattern in a minimal form, or simpler owner/doctor picker). Print uses `$prescription->doctor` then safe fallback (`clinic.doctor_title` + clinic name / primary owner — **not** arbitrary `users->first()` when a doctor is known).
- Offline: keep existing doctor_id path; replace legacy fallback only when doctor null.

### 5.5 Follow-up appointments (Feature 6)

Add nullable `appointments.follow_up_of_visit_id` (FK visits).  
UI on medical file / visit page: period presets (7/14/30 days) + custom date + **time** (Phase 1 time selector).  
Uses existing `AppointmentController::store` path (or dedicated action that calls same service) with `manage_appointments` + patient access. Multiple follow-ups per visit allowed unless you forbid it.

### 5.6 Age (Feature 8)

- Add `Patient::ageYears(): ?int` (or accessor) computing from `dob` when present; else fallback to stored `age`.
- Forms: DOB change auto-fills displayed age via JS; optionally stop requiring manual age when DOB present; keep storing age only as fallback for DOB-less legacy rows (or stop writing age when DOB present — decision §11).
- Replace display sites (lists, medical file, print, AI context Online) to use calculator.
- Birthday transitions automatic when DOB exists.

---

## 6. Routes / Permissions / UI Integration Points

### Permissions (proposed default — needs approval)

Reuse existing keys where possible:

| Feature | Permission |
|---------|------------|
| Templates CRUD + apply | `manage_prescriptions` |
| Layout / print language settings | `manage_settings` |
| Visits create/view/amend notes | `manage_patients` (view/edit medical file) + clinical writes as today |
| Lab/Rad order create/print | `manage_prescriptions` **or** `manage_patients` — **decision needed** |
| Result upload | `manage_records` |
| Follow-up create | `manage_appointments` (+ patient visible under `manage_patients` UI) |

Do **not** grant secretaries new powers beyond assigned Spatie permissions. Owner/doctor bypass remains per `EnsureClinicPermission`.

### UI placement

| Feature | Online UI | Offline UI |
|---------|-----------|------------|
| Age auto | `patients.blade.php`, `patient-medical-file.blade.php` | Same |
| Appointment time | `appointments.blade.php` + public booking Blade | `appointments.blade.php` only |
| Templates | Prescriptions page + settings subsection optional | Same |
| Layout/language | `settings.blade.php` + preview | Same (offline settings already diverge slightly — follow local) |
| Visit timeline | `patient-medical-file.blade.php` + new visit show | Same |
| Follow-up | Medical file / visit | Same |
| Lab/Rad | Visit show | Same |

Keep Arabic-first RTL clinic chrome; print language independent.

---

## 7. Database Changes Summary

### Shared new migrations (portable)

1. `add_prescription_print_preferences_to_clinics_table`
2. `create_prescription_templates_tables`
3. `add_doctor_id_to_prescriptions_table` (**Online only if missing**; Offline already has it)
4. `create_visits_table`
5. `add_visit_id_to_patient_diagnoses_prescriptions_medical_files`
6. `add_follow_up_of_visit_id_to_appointments_table`
7. `create_lab_tests_table` + `create_radiology_exams_table` + seeders
8. `create_lab_orders_tables` + `create_radiology_orders_tables`
9. Optional: `add_duration_to_prescription_items` — **only if approved** (currently dosage + instructions only)

### Backward compatibility

- All new FKs nullable for historical rows.
- No mass assignment of old diagnoses/Rx/files into visits.
- Appointment time change is behavioral: existing rows remain midnight; new rows store hour.
- Offline SoftDeletes / archive flows preserved.
- Offline release package must include seed catalogs + `drugs:import` already in build smoke.

---

## 8. Testing Strategy

### Online

Pest Feature tests for each requirement in the master prompt (§PART 6), plus full suite:

```bash
php artisan test --compact
```

Emphasize tenant isolation, secretary 403s, duplicate `scheduled_at` accepted, public booking keeps hour, template isolation, print locale/layout smoke responses, visit amend after completed status.

### Offline

Equivalent Pest tests under `tests/Feature`, plus existing offline suites (`SqliteFileDatabaseTest`, license, devices). Commands:

```bash
php artisan test --compact
```

Desktop/.NET tests remain unchanged unless packaging scripts break.

### Manual

Per master prompt checklist; Offline additionally EXE launch.

---

## 9. Offline Packaging & EXE Rebuild Strategy

**Build entrypoint (Confirmed):**

```powershell
powershell -ExecutionPolicy Bypass -File installer\build-release.ps1 -Version X.Y.Z
```

Script steps (from header + body): verify components → npm build → copy Laravel app → `composer install --no-dev` → runtime PHP/Apache → smoke migrate/seed/`drugs:import`/`clinora:health`/`httpd -t` → `dotnet publish` Agent/ControlCenter/ClientAssistant → Inno Setup → `dist\Clinora-Setup-X.Y.Z.exe`.

**Rules for Phase 6:**

1. Bump version (do not silently overwrite verified `1.0.0` until new version verified).
2. Ensure components directory / pinned SHA present (or approved `-DevelopmentPhpDirectory` for internal only — never clinic).
3. Verify installer path exists after build; launch; Agent/Web services; login; clinical smoke; license/device still OK.
4. LAN client check if environment permits.
5. Document exact version, SHA-256, and paths in final delivery report.

---

## 10. Risks

| Risk | Severity | Mitigation |
|------|----------|------------|
| Confusing Appointment with Visit | High | Separate `visits` table; link optionally |
| Breaking queue by storing times | Medium | Queue date from date part only |
| Online Rx doctor wrong on print | High | Add doctor_id + print fix |
| Template linked to live Rx | High | Copy-on-use snapshots only |
| Cross-clinic IDOR on new routes | High | clinic_id asserts on every action |
| SQLite migration incompatibility | Medium | Portable Blueprint; test Offline suite |
| Catalog medical liability / inventing tests | Medium | Small approved seed list |
| Offline license read-only blocking writes | Medium | Respect middleware; test |
| Overwriting working EXE | High | New version folder + verify before replace |
| Scope creep (availability engine) | High | Explicitly out of scope |

---

## 11. Unresolved Product Decisions — APPROVAL REQUIRED

Please answer before implementation:

1. **Visit creation UX:**  
   A) Create visit only from medical file (standalone), optional later link to appointment  
   B) Also “start visit” from an appointment (auto-link `appointment_id`)  
   C) Both  

2. **Lab/Radiology catalog ownership:**  
   A) Global seeded catalogs (recommended)  
   B) Per-clinic catalogs managed in settings  
   C) Global seed + clinic can add custom items  

3. **Initial catalog size:** Approve a short practical AR/EN list (~20–40 lab + ~15–25 radiology) vs provide your own CSV/list.

4. **Order permission:** Orders under `manage_prescriptions`, `manage_patients`, or new `manage_orders`?

5. **Template permission:** Reuse `manage_prescriptions` or new `manage_prescription_templates`?

6. **Prescription item duration field:** Add new `duration` column, or keep dosage+instructions only?

7. **Stored `patients.age` when DOB present:** Stop persisting age / auto-sync on save / keep both with DOB winning for display only?

8. **Visit completion semantics:** Keep `completed` as soft status with unlimited result/attachment/order adds (recommended), or also allow editing clinical notes/diagnoses after completion?

9. **Online multi-doctor:** Port Offline `DoctorProfile` system, or minimal `doctor_id` on prescriptions/visits without full doctor profiles?

10. **Follow-up doctor default:** Same as previous visit’s doctor, appointment form choice, or logged-in doctor?

11. **Offline Feature 7:** Confirm public booking N/A; dashboard time only. Confirm existing weekday/daily_limit checks remain.

12. **Edition sync process:** Implement Online first then port, Offline first, or parallel feature branches with shared migration names?

---

## 12. Proposed Implementation Sequence (post-approval)

Aligned with master prompt Part 8:

1. **Phase 1** — Age calculator + appointment time (Online public + both dashboards); duplicate times allowed; tests.
2. **Phase 2** — Templates; 6 layouts; print language; doctor identity (Online doctor_id); settings preview; tests.
3. **Phase 3** — `visits` + medical file timeline + nullable links + amend-friendly status; tests.
4. **Phase 4** — Follow-up from medical file/visit; tests.
5. **Phase 5** — Catalogs, orders, print, results/attachments after completion; tests.
6. **Phase 6** — Full regression both editions; Offline EXE build + launch verification; final delivery report.

---

## 13. Complexity Estimate

| Phase | Complexity |
|-------|------------|
| 1 Age + time | Low–Medium |
| 2 Rx templates/layouts/language/doctor | Medium |
| 3 Visit foundation | High |
| 4 Follow-ups | Low–Medium (depends on Visit) |
| 5 Lab/Radiology | High |
| 6 Dual regression + EXE | High (process/ops) |

Overall program: **High**.

---

## 14. Explicit Non-Goals (this roadmap)

- Availability / capacity / exclusive slot engine
- PDF library introduction for Rx/orders
- Frontend framework migration
- Cloud dependency for Offline clinical core
- Fabricating historical visits for old data
- Doctor-specific medication templates
- App-wide language switch via Rx locale setting

---

## 15. Gate

**PHASE 0 COMPLETE — NO IMPLEMENTATION STARTED.**

Awaiting your explicit approval of:

1. This plan overall  
2. Answers to §11 decisions  

Only then will implementation begin in small phases across both editions.
