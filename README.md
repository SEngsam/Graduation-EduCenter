# EduCenter — نظام إدارة مركز تعليمي

مشروع تخرج. موقع لمركز تعليمي فيه ثلاث أنواع مستخدمين:

- **الطالب:** بيتصفّح الكورسات وبيسجّل فيها، وبيتابع دروسه، وبيسلّم الواجبات، وبيشوف حضوره وعلاماته.
- **المدرّس:** بينزّل الدروس، وبيحدد مواعيد الكورس، وبيسجّل الحضور، وبيعمل واجبات وبيصححها.
- **الإدارة:** بتدير الأقسام والكورسات والمدرّسين والفعاليات والأخبار والمستخدمين والصلاحيات، وبتقرأ رسائل التواصل.

الموقع بالعربي والإنجليزي، وفيه وضع ليلي، وبيشتغل على الموبايل.

![الرئيسية](docs/screenshots/01-home.jpg)

## التقنيات

- Laravel 8 و PHP 8
- MySQL، أو SQLite للتجربة السريعة
- Blade و CSS عادي. الألوان والمسافات معرّفة كمتغيرات بملف `public/css/tokens.css`
- `mcamara/laravel-localization` للغتين بلوحة التحكم
- `tymon/jwt-auth` للـ API

## التشغيل

```bash
git clone <repo-url> educenter
cd educenter
composer install
cp .env.example .env
php artisan key:generate
```

عدّل إعدادات قاعدة البيانات بملف `.env`، وبعدها:

```bash
php artisan migrate --seed
php artisan serve
```

وافتح `http://127.0.0.1:8000`.

إذا بدك تجرب بسرعة بدون MySQL:

```bash
touch database/database.sqlite
# بملف .env:
# DB_CONNECTION=sqlite
# DB_DATABASE=/المسار/الكامل/database/database.sqlite
php artisan migrate --seed
```

## حسابات التجربة

كلمة السر لكل الحسابات: `password`

| الدور | الإيميل |
|---|---|
| مدير النظام | `super_admin@example.com` |
| مدرّس | `teacher@example.com` |
| طالب | `student@example.com` |
| طلاب إضافيين | `student1@example.com` لحد `student5@example.com` |
| مدير أقسام / كورسات / فعاليات / أخبار | `category_manager@example.com`، `course_manager@example.com`، `event_manager@example.com`، `news_manager@example.com` |

الـ seeder اسمه `AcademicSeeder`، وبيعبّي كورس الفيزياء بدروس ومواعيد وحصص سابقة وواجبات وتسليمات، حتى تكون الصفحات مليانة من أول تشغيل.

## الاختبارات

```bash
php artisan test
```

فيه 28 اختبار، بيشتغلوا على SQLite بالذاكرة وما بيلمسوا قاعدة بياناتك. بيغطّوا:

- التسجيل بكورس: ما بينقبل التسجيل مرتين، ولا التسجيل بكورس ما إلو مدرّس.
- الصلاحيات: الطالب ما بيفوت على بوابة المدرّس، والمدرّس ما بيفتح كورس مش إله.
- الدروس: المسودة مخفية عن الطالب، ونسبة التقدّم بتنحسب صح.
- الحضور: إعادة الحفظ بتعدّل وما بتكرر، والطالب من كورس تاني ما بينسجّل.
- الواجبات: التسليم، واستبدال الملف، والعلامة ما بتتجاوز الحد، والواجب المصحّح ما بيتعدّل، ومدرّس تاني ما بيقدر يصحح.
- نموذج التواصل.

## هيكل المشروع

```
app/Http/Controllers/
├── SiteController.php            الموقع العام
├── StudentController.php         لوحة الطالب والملف الشخصي
├── StudentCourseController.php   صفحة الكورس عند الطالب: الدروس والتسليم
├── Teach/                        بوابة المدرّس
│   ├── DashboardController.php
│   ├── CourseController.php      الوحدات والدروس والمواعيد
│   ├── AttendanceController.php
│   └── AssignmentController.php  الواجبات والتصحيح
├── Admin/                        لوحة التحكم
└── Api/                          الـ API

resources/views/
├── site/      الموقع العام
├── student/   صفحات الطالب
├── teach/     صفحات المدرّس
├── admin/     لوحة التحكم
└── includes/  أجزاء مشتركة: كروت، أيقونات، رسائل، نافذة التأكيد
```

## الروابط المهمة

| الرابط | الصفحة |
|---|---|
| `/` | الرئيسية |
| `/courses` | الكورسات، مع بحث وفلترة حسب المدرّس |
| `/my` | لوحة الطالب |
| `/my/courses/{id}` | الكورس عند الطالب |
| `/teach` | لوحة المدرّس |
| `/teach/courses/{id}` | محتوى الكورس |
| `/teach/courses/{id}/attendance` | الحضور |
| `/teach/courses/{id}/assignments` | الواجبات |
| `/ar/admin` | لوحة التحكم |

## قاعدة البيانات

المخطط وشرح الجداول موجودين بملف [docs/database.md](docs/database.md).

## صور

| | |
|---|---|
| ![لوحة الطالب](docs/screenshots/04-student-dashboard.jpg) | ![الكورس عند الطالب](docs/screenshots/05-student-course.jpg) |
| ![لوحة المدرّس](docs/screenshots/06-teacher-dashboard.jpg) | ![محتوى الكورس](docs/screenshots/07-course-content.jpg) |
| ![الحضور](docs/screenshots/08-attendance.jpg) | ![الواجبات](docs/screenshots/09-assignments.jpg) |
| ![لوحة التحكم](docs/screenshots/10-admin.jpg) | ![الوضع الليلي](docs/screenshots/11-dark-mode.jpg) |
