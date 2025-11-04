# Assessment Platform Backend API Documentation

This document provides a **comprehensive overview** of the backend architecture and API design for the **Online Assessment Platform**, built using **Laravel 11 (Sanctum)** for the backend and **React + shadcn** for the frontend. The backend supports **multi-tenancy**, **role-based access control (RBAC)**, **OTP-based authentication**, and **assessment workflows** including MCQs, open-ended, and rubric-based evaluations.

---

## 1. Stack Overview

| Component  | Technology                                          |
| ---------- | --------------------------------------------------- |
| Backend    | Laravel 11 (Sanctum, Policies, Resources, Requests) |
| Database   | MySQL                                               |
| Auth       | Sanctum (session-based), OTP for signup/login       |
| Storage    | Local or S3-compatible                              |
| Mail / SMS | Laravel Mail + Custom SMS service                   |

---

## 2. Multi-Tenancy Structure

Each college/institution is represented as a **tenant**. Tenant ID (`tenant_id`) is attached to every record to ensure data isolation.

### Tenancy Middleware

* Extracts `X-Tenant-ID` from the request header or from the authenticated user.
* Sets `app('tenant.id')` globally.
* All models must use `tenant_id` in queries.

---

## 3. Database Schema Overview

### Core Tables

| Table                   | Purpose                                |
| ----------------------- | -------------------------------------- |
| tenants                 | Holds college/institution info         |
| users                   | Global user registry with role linkage |
| roles / model_has_roles | Managed via Spatie Permissions         |

### Academic Entities

| Table       | Purpose                               |
| ----------- | ------------------------------------- |
| students    | Student records (linked to tenant)    |
| modules     | Academic courses or subjects          |
| assessments | Exams/quizzes/tests linked to modules |
| questions   | Questions bank (MCQ or open-ended)    |
| options     | Answer options for MCQs               |

### Assessment Flow

| Table            | Purpose                                           |
| ---------------- | ------------------------------------------------- |
| attempts         | Student submissions of assessments                |
| responses        | Question-level answers during an attempt          |
| rubrics          | Evaluation framework for rubric-based assessments |
| rubric_criteria  | Criteria and weights within rubrics               |
| evaluators       | Teachers or staff authorized to grade             |
| criterion_scores | Scores given per rubric criterion                 |

### Security & Logs

| Table      | Purpose                                         |
| ---------- | ----------------------------------------------- |
| otp_tokens | Stores OTPs for authentication and verification |
| audit_logs | Records all admin or critical system actions    |

---

## 4. Authentication Endpoints

### `POST /api/v1/login`

Authenticate via email/password or phone/password.

```json
{
  "email": "admin@college.ac.ke",
  "password": "StrongPass@123"
}
```

**Response:**

```json
{
  "message": "ok",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "admin@college.ac.ke",
    "tenant_id": 12,
    "roles": ["CollegeAdmin"]
  }
}
```

### `POST /api/v1/logout`

Logs out and invalidates the session.

### OTP Authentication

| Endpoint                   | Description                         |
| -------------------------- | ----------------------------------- |
| `POST /api/v1/otp/request` | Request OTP via SMS or email        |
| `POST /api/v1/otp/verify`  | Verify OTP code for signup or login |

**Request example:**

```json
{
  "channel": "sms",
  "identifier": "+254712345678",
  "purpose": "signup"
}
```

---

## 5. Students API

| Endpoint                       | Description                          |
| ------------------------------ | ------------------------------------ |
| `GET /api/v1/students`         | List all students for current tenant |
| `POST /api/v1/students`        | Create new student                   |
| `POST /api/v1/students/import` | Bulk import via CSV/Excel            |
| `GET /api/v1/students/{id}`    | Show details                         |
| `PATCH /api/v1/students/{id}`  | Update student                       |
| `DELETE /api/v1/students/{id}` | Delete student                       |

**Sample Response:**

```json
{
  "id": 34,
  "reg_no": "KEMU-2025-012",
  "branch": "Nairobi Campus",
  "cohort": "2025",
  "meta": {"gender": "F"}
}
```

---

## 6. Modules API

| Endpoint                      | Description                             |
| ----------------------------- | --------------------------------------- |
| `GET /api/v1/modules`         | List all modules                        |
| `POST /api/v1/modules`        | Create new module                       |
| `GET /api/v1/modules/{id}`    | Get module detail with assessment count |
| `PATCH /api/v1/modules/{id}`  | Update                                  |
| `DELETE /api/v1/modules/{id}` | Delete                                  |

---

## 7. Assessments API

| Endpoint                          | Description                             |
| --------------------------------- | --------------------------------------- |
| `GET /api/v1/assessments`         | List all assessments (filter by module) |
| `POST /api/v1/assessments`        | Create new assessment                   |
| `GET /api/v1/assessments/{id}`    | View with questions + rubrics           |
| `PATCH /api/v1/assessments/{id}`  | Update                                  |
| `DELETE /api/v1/assessments/{id}` | Delete                                  |

**Assessment Types:** `MCQ`, `RUBRIC`

---

## 8. Questions & Options API

| Endpoint                              | Description               |
| ------------------------------------- | ------------------------- |
| `GET /api/v1/questions`               | List question bank        |
| `POST /api/v1/questions`              | Create question + options |
| `GET /api/v1/questions/{id}`          | Retrieve question detail  |
| `PATCH /api/v1/questions/{id}`        | Update                    |
| `DELETE /api/v1/questions/{id}`       | Delete                    |
| `POST /api/v1/questions/{id}/options` | Add new MCQ option        |
| `DELETE /api/v1/options/{id}`         | Remove option             |

---

## 9. Attempts & Responses API

| Endpoint                                 | Description                      |
| ---------------------------------------- | -------------------------------- |
| `POST /api/v1/assessments/{id}/attempts` | Start attempt                    |
| `POST /api/v1/attempts/{id}/save`        | Save progress per question       |
| `POST /api/v1/attempts/{id}/submit`      | Submit attempt (auto-score MCQs) |

**Sample Save Payload:**

```json
{
  "question_id": 15,
  "option_id": 42,
  "text_answer": null
}
```

**Submit Response:**

```json
{
  "id": 101,
  "assessment_id": 12,
  "score": 85,
  "submitted_at": "2025-11-03T15:30:00Z"
}
```

---

## 10. Rubrics & Evaluation API

| Endpoint                               | Description               |
| -------------------------------------- | ------------------------- |
| `GET /api/v1/rubrics`                  | List rubrics              |
| `POST /api/v1/rubrics`                 | Create new rubric         |
| `GET /api/v1/rubrics/{id}`             | Show rubric with criteria |
| `PATCH /api/v1/rubrics/{id}`           | Update                    |
| `DELETE /api/v1/rubrics/{id}`          | Delete                    |
| `POST /api/v1/rubrics/criteria`        | Add criterion             |
| `PATCH /api/v1/rubrics/criteria/{id}`  | Update criterion          |
| `DELETE /api/v1/rubrics/criteria/{id}` | Delete criterion          |

### Evaluation Scoring

| Endpoint                            | Description                          |
| ----------------------------------- | ------------------------------------ |
| `GET /api/v1/evaluate/queue`        | List pending attempts for evaluation |
| `POST /api/v1/attempts/{id}/scores` | Submit rubric scores                 |

**Scoring Payload:**

```json
{
  "scores": [
    {"criterion_id": 11, "score": 4, "comment": "Good clarity"},
    {"criterion_id": 12, "score": 3, "comment": "Minor grammar errors"}
  ]
}
```

---

## 11. Reports API

| Endpoint                           | Description                                |
| ---------------------------------- | ------------------------------------------ |
| `GET /api/v1/reports/student/{id}` | Student performance history                |
| `GET /api/v1/reports/overview`     | Aggregated insights (per module or cohort) |

---

## 12. Authorization (Policies)

Each resource implements policies for `view`, `update`, and `delete`, ensuring tenant isolation:

```php
public function view(User $user, Model $model): bool
{
    return $user->hasRole('SuperAdmin') || $user->tenant_id === $model->tenant_id;
}
```

Roles:

* **SuperAdmin:** Full global access
* **CollegeAdmin:** CRUD within tenant
* **Evaluator:** Read & score assigned assessments
* **Student:** Limited to own attempts

---

## 13. Seeder Overview

```php
php artisan db:seed --class=RolesAndAdminSeeder
```

### Example Roles Seeder

```php
use Spatie\Permission\Models\Role;

Role::create(['name'=>'SuperAdmin']);
Role::create(['name'=>'CollegeAdmin']);
Role::create(['name'=>'Evaluator']);
Role::create(['name'=>'Student']);
```

### Example Admin Seeder

```php
User::create([
  'name' => 'Platform Admin',
  'email' => 'admin@assessment.io',
  'password' => bcrypt('Strong@1234')
])->assignRole('SuperAdmin');
```

---

## 14. API Response Format

```json
{
  "data": { ... },
  "meta": {
    "success": true,
    "timestamp": "2025-11-04T08:00:00Z"
  }
}
```

Errors:

```json
{
  "message": "Invalid credentials",
  "errors": {
    "email": ["These credentials do not match our records."]
  }
}
```

---

## 15. Development Commands

```bash
php artisan migrate:fresh --seed
php artisan serve
npm run dev
```

---

## 16. Next Steps

* Implement unit & feature tests for tenant isolation.
* Add Swagger/OpenAPI generation via `knuckleswtf/scribe`.
* Add mailer & Twilio SMS integration.
* Extend analytics (per module, per cohort, per tenant).

---

© 2025 — **Arden Bouet** | Assessment Platform API | Laravel + React + shadcn
