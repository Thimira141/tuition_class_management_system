<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class ClassroomControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_classrooms()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Classroom::factory()->count(3)->create();

        $response = $this->getJson(route('classrooms.ajax.dt.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['student__id', 'student__name', 'student__student_code', 'student__dob']
            ]
        ]);
    }


    #[Test]
    public function it_creates_a_classroom()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $data = [
            'classroom__class_code' => now()->format('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'classroom__name' => 'Grade 10 A',
            'classroom__grade' => '10',
            'classroom__remarks' => 'Science stream',
        ];

        $response = $this->postJson(route('classrooms.ajax.store'), $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('classes', ['name' => 'Grade 10 A']);

    }


    #[Test]
    public function it_updates_a_classroom()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();

        $response = $this->putJson(route('classrooms.ajax.update', $classroom), [
            'classroom__name' => 'Updated Name',
            'classroom__grade' => (string) $classroom->grade
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('classes', ['id' => $classroom->id, 'name' => 'Updated Name']);
    }


    #[Test]
    public function it_deletes_a_classroom()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $classroom = Classroom::factory()->create();

        $response = $this->deleteJson(route('classrooms.ajax.destroy', $classroom));

        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseMissing('classes', ['classroom__id' => $classroom->id]);
    }

}
