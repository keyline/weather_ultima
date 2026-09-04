<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Shankar Chatterjee', 'designation' => 'Retired Kolkata Police Officer', 'review' => 'It’s a wonderful platform of weather forecast & need to be more specific periodically.'],
            ['name' => 'Shrayan Sen', 'designation' => 'Tour Operator', 'review' => 'No one predicts weather better than Weather Ultima.'],
            ['name' => 'Vikash Jaiswal', 'designation' => 'Manufacturing Electrical Fans', 'review' => 'On time, accurate & reliable weather reporting.'],
            ['name' => 'Dr. Anandamay Mukhopadhyay', 'designation' => 'Govt. service', 'review' => 'It’s Accurate and Handy. Answers we get on predictions give us the best indication on our premonition.'],
        ];

        foreach ($testimonials as $index => $testimonial) {
            Testimonial::query()->firstOrCreate(
                ['name' => $testimonial['name']],
                [
                    'designation' => $testimonial['designation'],
                    'review' => $testimonial['review'],
                    'rating' => 5,
                    'display_order' => $index + 1,
                    'is_enabled' => true,
                ],
            );
        }
    }
}
