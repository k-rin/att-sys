<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveReport>
 */
class LeaveReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => fake()->numberBetween(1, 100),
            'start_at' => fake()->dateTime('2022-09-22 09:00:00'),
            'end_at' => fake()->dateTime('2022-12-25 18:00:00'),
            'days' => fake()->randomDigitNotNull(),
            'type' => fake()->numberBetween(1, 7),
            'reason' => fake()->sentence(),
            'approval_id' => fake()->numberBetween(1, 100),
            'status' => fake()->numberBetween(0, 2),
        ];
    }
}
