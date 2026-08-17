<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Classroom;
use App\Models\User;
use App\Traits\PrefixKeys;
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
            'classroom__payment_method' => 'monthly',
            'classroom__price' => '2000',
            'classroom__start_date' => '2026-06-12',
            'classroom__end_date' => '2026-12-31'
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

        $data = $classroom->toArray();
        $data['name'] = $name = "UPDATED-NAME";
        unset($data['class_code']);
        $data = PrefixKeys::prefixKeys($data, 'classroom__')->toArray();

        $response = $this->putJson(route('classrooms.ajax.update', $classroom), $data);
        // dd($data, $response->json(), $response->status());
        $response->assertStatus(200);
        $response->assertJsonFragment(['status' => 'success']);
        $this->assertDatabaseHas('classes', ['id' => $classroom->id, 'name' => $name]);
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
