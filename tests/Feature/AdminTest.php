<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Course;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\AbilityRoleSeeder;
use Database\Seeders\RolesDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolesDataSeeder::class, AbilityRoleSeeder::class]);
        Storage::fake('uploads');
    }

    private function admin()
    {
        return $this->makeUser(Role::ROLE_ADMIN, 'Admin');
    }

    public function test_student_cannot_open_admin_forms()
    {
        $student = $this->makeStudent();

        $this->actingAs($student)->get(route('admin.courses.create'))->assertRedirect('not_allowed');
        $this->actingAs($student)->get(route('admin.categories.create'))->assertRedirect('not_allowed');
    }

    public function test_student_cannot_create_a_course()
    {
        $this->actingAs($this->makeStudent())->post(route('admin.courses.store'), [
            'title' => 'Hacked',
            'description' => 'x',
            'btn_text' => 'x',
            'btn_link' => 'x',
            'image' => UploadedFile::fake()->image('c.jpg'),
        ]);

        $this->assertEquals(0, Course::count());
    }

    public function test_admin_can_create_a_course_with_image()
    {
        $teacher = $this->makeTeacher();

        $this->actingAs($this->admin())->post(route('admin.courses.store'), [
            'title' => 'Chemistry',
            'description' => 'Basics',
            'btn_text' => 'More',
            'btn_link' => '#',
            'teacher_id' => $teacher->teacher->id,
            'image' => UploadedFile::fake()->image('chem.jpg'),
        ])->assertRedirect(route('admin.courses.index'));

        $course = Course::first();
        $this->assertEquals('Chemistry', $course->title);
        $this->assertEquals($teacher->teacher->id, $course->teacher_id);
        Storage::disk('uploads')->assertExists('courses/' . $course->image);
    }

    public function test_updating_image_removes_the_old_one()
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.courses.store'), [
            'title' => 'Chemistry', 'description' => 'x', 'btn_text' => 'x', 'btn_link' => '#',
            'image' => UploadedFile::fake()->image('old.jpg'),
        ]);
        $course = Course::first();
        $old = $course->image;

        $this->actingAs($admin)->put(route('admin.courses.update', $course->id), [
            'title' => 'Chemistry 2', 'description' => 'x', 'btn_text' => 'x', 'btn_link' => '#',
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

        $course->refresh();
        $this->assertEquals('Chemistry 2', $course->title);
        Storage::disk('uploads')->assertMissing('courses/' . $old);
        Storage::disk('uploads')->assertExists('courses/' . $course->image);
    }

    public function test_upload_must_be_an_image()
    {
        $this->actingAs($this->admin())->post(route('admin.events.store'), [
            'location' => 'Hall',
            'description' => 'Fair',
            'image' => UploadedFile::fake()->create('virus.php', 10),
        ])->assertSessionHasErrors('image');
    }

    public function test_category_with_teachers_cannot_be_deleted()
    {
        $teacher = $this->makeTeacher();
        $category = Category::first();

        $this->actingAs($this->admin())->delete(route('admin.categories.destroy', $category->id));

        $this->assertNotNull(Category::find($category->id));
    }

    public function test_teacher_with_courses_cannot_be_deleted()
    {
        $teacher = $this->makeTeacher();
        $this->makeCourse($teacher);

        $this->actingAs($this->admin())->delete(route('admin.teachers.destroy', $teacher->teacher->id));

        $this->assertNotNull(Teacher::find($teacher->teacher->id));
    }

    public function test_new_teacher_gets_a_login_account()
    {
        $this->makeTeacher();

        $this->actingAs($this->admin())->post(route('admin.teachers.store'), [
            'name' => 'Rana',
            'major' => 'Programming',
            'category_id' => Category::first()->id,
            'email' => 'rana@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'image' => UploadedFile::fake()->image('rana.jpg'),
        ]);

        $user = User::where('email', 'rana@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(Role::ROLE_TEACHER, $user->role_id);
        $this->assertEquals('Rana', $user->teacher->name);
    }

    public function test_user_update_is_validated()
    {
        $other = $this->makeStudent();

        $this->actingAs($this->admin())->put(route('admin.users.update', $other->id), [
            'name' => '',
            'email' => 'not-an-email',
            'role_id' => 999,
        ])->assertSessionHasErrors(['name', 'email', 'role_id']);
    }

    public function test_admin_cannot_delete_own_account()
    {
        $admin = $this->admin();

        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin->id));

        $this->assertNotNull(User::find($admin->id));
    }

    public function test_built_in_roles_cannot_be_deleted()
    {
        $this->actingAs($this->admin())->delete(route('admin.roles.destroy', Role::ROLE_STUDENT));

        $this->assertNotNull(Role::find(Role::ROLE_STUDENT));
    }

    public function test_admin_can_create_a_role_with_abilities()
    {
        $this->actingAs($this->admin())->post(route('admin.roles.store'), [
            'name' => 'Assistant',
            'ability' => [1, 2],
        ]);

        $role = Role::where('name', 'Assistant')->first();
        $this->assertEquals(2, $role->abilities()->count());
    }

    public function test_super_admin_has_every_permission()
    {
        $admin = $this->admin();

        $this->assertTrue($admin->hasAbility('edit_news'));
        $this->assertTrue($admin->hasAbility('anything_new'));
        $this->assertFalse($this->makeStudent()->hasAbility('edit_news'));
    }
}
