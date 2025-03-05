<?php
namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $admin;
    protected $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat user biasa
        $this->user = User::factory()->create([
            'role' => 'user'
        ]);

        // Buat admin
        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);

        // Buat tiket
        $this->ticket = Ticket::factory()->create([
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function user_can_create_a_ticket()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/tickets', [
            'title' => 'Test Ticket',
            'description' => 'This is a test ticket'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tickets', ['title' => 'Test Ticket']);
    }

    /** @test */
    public function user_can_view_their_own_tickets()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->getJson('/api/tickets');

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $this->ticket->title]);
    }

    /** @test */
    public function admin_can_view_all_tickets()
    {
        $this->actingAs($this->admin, 'sanctum');

        $response = $this->getJson('/api/tickets');

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $this->ticket->title]);
    }

    /** @test */
    public function user_can_add_a_comment_to_a_ticket()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson("/api/tickets/{$this->ticket->id}/comments", [
            'comment' => 'This is a test comment'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('ticket_comments', ['comment' => 'This is a test comment']);
    }

    /** @test */
    public function admin_can_update_ticket_status()
    {
        $this->actingAs($this->admin, 'sanctum');

        $response = $this->putJson("/api/tickets/{$this->ticket->id}/status", [
            'status' => 'resolved'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', ['id' => $this->ticket->id, 'status' => 'resolved']);
    }

    /** @test */
    public function user_cannot_update_ticket_status()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->putJson("/api/tickets/{$this->ticket->id}/status", [
            'status' => 'resolved'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_delete_a_ticket()
    {
        $this->actingAs($this->admin, 'sanctum');

        $response = $this->deleteJson("/api/tickets/{$this->ticket->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tickets', ['id' => $this->ticket->id]);
    }

    /** @test */
    public function user_cannot_delete_a_ticket()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->deleteJson("/api/tickets/{$this->ticket->id}");

        $response->assertStatus(403);
    }
}
