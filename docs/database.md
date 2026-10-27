# قاعدة البيانات

المخطط تحت فيه الجداول الأساسية بس. جداول Laravel الجاهزة (`migrations`، `password_resets`، `failed_jobs`، `personal_access_tokens`، `notifications`) وجداول المحتوى العام (`events`، `news`، `abouts`) مش مرسومة لأنها ما إلها علاقات مع باقي الجداول.

```mermaid
erDiagram
    roles ||--o{ users : "role_id"
    roles }o--o{ abilities : "ability_role"
    users ||--o| students : "user_id"
    users ||--o| teachers : "user_id"
    categories ||--o{ teachers : "categorie_id"
    teachers ||--o{ courses : "teacher_id"

    students ||--o{ registrations : ""
    courses ||--o{ registrations : ""

    courses ||--o{ course_slots : "مواعيد"
    courses ||--o{ course_units : "وحدات"
    courses ||--o{ lessons : "دروس"
    course_units |o--o{ lessons : "course_unit_id"
    lessons ||--o{ lesson_student : "خلّص الدرس"
    students ||--o{ lesson_student : ""

    courses ||--o{ class_sessions : "حصص"
    class_sessions ||--o{ attendances : ""
    students ||--o{ attendances : ""

    courses ||--o{ assignments : "واجبات"
    assignments ||--o{ submissions : ""
    students ||--o{ submissions : ""

    users |o--o{ contact_messages : "user_id"

    users {
        int id PK
        string name
        string email
        int role_id
    }
    students {
        int id PK
        int user_id FK
        string name
        string major
        string no_phone
    }
    teachers {
        int id PK
        int user_id FK
        int categorie_id FK
        string name
        string major
    }
    courses {
        int id PK
        int teacher_id FK
        string title
        string description
    }
    registrations {
        int id PK
        int student_id FK
        int course_id FK
    }
    course_slots {
        int id PK
        int course_id FK
        int day "0 = الأحد"
        time starts_at
        time ends_at
        string room
    }
    course_units {
        int id PK
        int course_id FK
        string title
        int position
    }
    lessons {
        int id PK
        int course_id FK
        int course_unit_id FK
        string title
        string type "video, file, link, text"
        string url
        string file
        text body
        bool is_published
    }
    class_sessions {
        int id PK
        int course_id FK
        datetime starts_at
        string room
    }
    attendances {
        int id PK
        int class_session_id FK
        int student_id FK
        string status "present, late, absent, excused"
        string note
    }
    assignments {
        int id PK
        int course_id FK
        string title
        datetime due_at
        int max_grade
    }
    submissions {
        int id PK
        int assignment_id FK
        int student_id FK
        string file
        decimal grade
        text feedback
    }
```

## ملاحظات على التصميم

- **المستخدم والطالب والمدرّس:** كل واحد بيسجّل دخول من جدول `users`. الطالب إلو صف بجدول `students`، والمدرّس إلو صف بجدول `teachers`، والربط بينهم عن طريق `user_id`. كل الجداول الأكاديمية بتأشّر على `students.id`، مش على `users.id`.
- **التسجيل بكورس:** جدول `registrations` فيه بس الطالب والكورس، ومعهم قيد `unique` حتى ما يتسجّل الطالب مرتين. مدرّس الكورس بيتجاب من `courses.teacher_id`، فما في داعي نخزّنه مرة تانية.
- **الحضور:** كل حصة إلها صف بجدول `class_sessions`. أول ما المدرّس يحفظ الحضور بتنعمل الحصة، وبعدها كل طالب إلو صف بجدول `attendances`. على الجدول قيد `unique (class_session_id, student_id)`، يعني إذا حفظنا مرة تانية بيتعدّل الصف القديم وما بيتكرر.
- **الواجبات:** كل طالب إلو تسليم واحد لكل واجب، بقيد `unique (assignment_id, student_id)`. إذا رفع ملف جديد بيتبدّل القديم، إلا إذا الواجب انصحّح.
- **الملفات:** ملفات الدروس والتسليمات محفوظة بـ `storage/app`، مش بـ `public`. ما بتنزل إلا عن طريق الكنترولر، بعد ما نتأكد إنه الشخص مسجّل بالكورس أو إنه مدرّسه.
- **المواعيد والتعارض:** كل موعد أسبوعي للكورس إلو صف بجدول `course_slots` (اليوم، البداية، النهاية، القاعة). موعدين بيتعارضوا إذا كانوا بنفس اليوم، وبداية كل واحد قبل نهاية التاني. هيك منفحص قبل ما يتسجّل الطالب بكورس، وقبل ما المدرّس يضيف موعد.
- **الإشعارات:** محفوظة بجدول `notifications` الجاهز من Laravel. كل إشعار فيه نوعه، ومعلوماته، ورابط بيفتح الصفحة المناسبة. إعدادات الإيميل لكل مستخدم محفوظة بعمود `users.email_settings`. وعمود `assignments.reminded_at` بيمنع إنه التذكير ينبعت مرتين.
- **نسبة التقدّم:** هي عدد الدروس المنشورة اللي الطالب علّم عليها بجدول `lesson_student`، مقسوم على عدد كل الدروس المنشورة بالكورس.
