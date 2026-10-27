<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Registration;
use App\Models\Role;
use App\Models\Student;
use App\Models\Submission;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AcademicSeeder extends Seeder
{
    public function run()
    {
        $teacher = Teacher::first();
        if (!$teacher) {
            return;
        }

        $courses = Course::orderBy('id')->take(2)->get();
        foreach ($courses as $course) {
            if (!$course->teacher_id) {
                $course->teacher_id = $teacher->id;
                $course->save();
            }
        }

        $names = ['عمر خالد', 'لين يوسف', 'كريم ناصر', 'ريم سليمان', 'يزن حداد'];
        $students = Student::orderBy('id')->take(1)->get();

        foreach ($names as $i => $name) {
            $email = 'student' . ($i + 1) . '@example.com';
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role_id' => Role::ROLE_STUDENT,
                ]);
            }

            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                $student = Student::create([
                    'name' => $name,
                    'major' => 'علوم',
                    'no_phone' => '09' . rand(10000000, 99999999),
                    'user_id' => $user->id,
                ]);
            }
            $students->push($student);
        }

        foreach ($courses as $course) {
            foreach ($students as $student) {
                $exists = Registration::where('student_id', $student->id)->where('course_id', $course->id)->exists();
                if (!$exists) {
                    Registration::create([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                    ]);
                }
            }
        }

        $physics = $courses->first();
        if ($physics->units()->count() > 0) {
            return;
        }

        foreach (Registration::orderBy('id')->get() as $i => $registration) {
            $registration->created_at = now()->subMonths($i % 6)->subDays(($i * 5) % 20);
            $registration->save();
        }

        $unit1 = $physics->units()->create(['title' => 'الوحدة 1: الحركة والقوى', 'position' => 1]);
        $unit2 = $physics->units()->create(['title' => 'الوحدة 2: الشغل والطاقة', 'position' => 2]);

        $lessons = [
            [$unit1, 'شو هي الحركة؟', 'video', 'https://www.youtube.com/watch?v=ZM8ECpBuQYE'],
            [$unit1, 'قوانين نيوتن الثلاثة', 'text', null],
            [$unit1, 'أمثلة على القوة والتسارع', 'video', 'https://www.youtube.com/watch?v=kKKM8Y-u7ds'],
            [$unit1, 'محاكاة الحركة على PhET', 'link', 'https://phet.colorado.edu/en/simulations/forces-and-motion-basics'],
            [$unit2, 'مفهوم الشغل', 'video', 'https://www.youtube.com/watch?v=w4QFJb9a8vo'],
            [$unit2, 'الطاقة الحركية والكامنة', 'text', null],
        ];

        foreach ($lessons as $i => $row) {
            Lesson::create([
                'course_id' => $physics->id,
                'course_unit_id' => $row[0]->id,
                'title' => $row[1],
                'type' => $row[2],
                'url' => $row[3],
                'body' => $row[2] == 'text' ? "القانون الأول: الجسم بيضل ساكن أو متحرك بسرعة ثابتة إذا ما أثرت عليه قوة.\nالقانون الثاني: القوة = الكتلة × التسارع.\nالقانون الثالث: لكل فعل رد فعل مساوي إله بالمقدار ومعاكس بالاتجاه." : null,
                'position' => $i + 1,
                'is_published' => $i < 5,
            ]);
        }

        $today = now()->dayOfWeek;
        $physics->slots()->create(['day' => $today, 'starts_at' => '09:00', 'ends_at' => '10:30', 'room' => 'قاعة A']);
        $physics->slots()->create(['day' => ($today + 2) % 7, 'starts_at' => '09:00', 'ends_at' => '10:30', 'room' => 'قاعة A']);
        if ($courses->count() > 1) {
            $courses[1]->slots()->create(['day' => $today, 'starts_at' => '13:00', 'ends_at' => '14:30', 'room' => 'المرسم']);
        }

        $statuses = ['present', 'present', 'present', 'late', 'absent', 'excused'];
        foreach ($courses as $c => $course) {
            for ($week = 1; $week <= 8; $week++) {
                $session = ClassSession::create([
                    'course_id' => $course->id,
                    'starts_at' => now()->subWeeks($week)->setTime(9 + $c * 4, 0),
                    'room' => $c == 0 ? 'قاعة A' : 'المرسم',
                ]);

                foreach ($students as $j => $student) {
                    $status = $statuses[($j + $week + $c) % count($statuses)];
                    if ($j == 0 || ($c == 0 && $week % 3 == 0)) {
                        $status = 'present';
                    }

                    Attendance::create([
                        'class_session_id' => $session->id,
                        'student_id' => $student->id,
                        'status' => $status,
                    ]);
                }
            }
        }

        $old = Assignment::create([
            'course_id' => $physics->id,
            'title' => 'حل مسائل الحركة',
            'description' => 'حل المسائل من 1 لـ 10 بالكتاب، وارفع الحل كملف PDF.',
            'due_at' => now()->subDays(5)->setTime(23, 59),
            'max_grade' => 20,
        ]);

        $current = Assignment::create([
            'course_id' => $physics->id,
            'title' => 'تقرير تجربة البندول',
            'description' => 'اكتب تقرير عن تجربة البندول البسيط: الأدوات، الخطوات، جدول القياسات، والعلاقة بين طول الخيط وزمن الدورة.',
            'due_at' => now()->addDays(2)->setTime(23, 59),
            'max_grade' => 20,
        ]);

        Storage::put('submissions/sample.pdf', '%PDF-1.4 sample');

        foreach ($students as $j => $student) {
            if ($j < 5) {
                Submission::create([
                    'assignment_id' => $old->id,
                    'student_id' => $student->id,
                    'file' => 'submissions/sample.pdf',
                    'original_name' => 'motion-' . ($j + 1) . '.pdf',
                    'grade' => $j == 0 ? 17 : 12 + $j,
                    'feedback' => $j == 0 ? 'شغل مرتب، انتبه للوحدات بالمسألة 7.' : null,
                    'graded_at' => now()->subDays(3),
                    'created_at' => now()->subDays(6)->setTime(18, $j * 7),
                ]);
            }

            if ($j > 0 && $j < 4) {
                Submission::create([
                    'assignment_id' => $current->id,
                    'student_id' => $student->id,
                    'file' => 'submissions/sample.pdf',
                    'original_name' => 'pendulum-' . $j . '.pdf',
                    'created_at' => now()->subHours($j * 3),
                ]);
            }
        }
    }
}
