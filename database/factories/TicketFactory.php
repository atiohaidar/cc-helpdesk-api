<?php
namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(), // Secara otomatis buat user baru jika tidak ada
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'status' => 'open',
        ];
    }
}
