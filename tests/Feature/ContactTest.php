<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->makeRoles();
    }

    public function test_contact_message_is_saved()
    {
        $this->actingAs($this->makeStudent())->post('/contact', [
            'name' => 'سارة',
            'mail' => 'sara@example.com',
            'major' => 'علوم',
            'message' => 'بدي أعرف موعد الفصل الجاي',
        ])->assertOk();

        $this->assertEquals(1, ContactMessage::count());
        $this->assertEquals('sara@example.com', ContactMessage::first()->email);
    }

    public function test_message_needs_a_valid_email()
    {
        $this->actingAs($this->makeStudent())->post('/contact', [
            'name' => 'سارة',
            'mail' => 'not-an-email',
            'message' => 'مرحبا',
        ])->assertSessionHasErrors('mail');

        $this->assertEquals(0, ContactMessage::count());
    }
}
