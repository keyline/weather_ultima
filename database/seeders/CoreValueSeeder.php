<?php

namespace Database\Seeders;

use App\Models\CoreValue;
use Illuminate\Database\Seeder;

class CoreValueSeeder extends Seeder
{
    public function run(): void
    {
        $values = [
            ['icon' => 'R', 'title' => 'Reliability', 'description' => 'Because someone, somewhere, is planning their day around our forecast. We treat every forecast with the responsibility that trust deserves. Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem ipsum has been the industry’s standard dummy text ever since 1966.'],
            ['icon' => 'A', 'title' => 'Accuracy', 'description' => 'A few degrees. A few millimetres. Sometimes, they matter a lot. We stay focused on precision because small weather changes can influence big decisions. Lorem ipsum is simply dummy text of the printing and typesetting industry. Lorem ipsum has been the industry’s standard dummy text ever since 1966.'],
            ['icon' => 'I', 'title' => 'Innovation', 'description' => 'The atmosphere never stands still. Neither do we. We keep learning, upgrading and embracing smarter technology to understand weather better. Lorem ipsum is simply dummy text of the printing and typesetting industry.'],
            ['icon' => 'N', 'title' => 'Nature', 'description' => 'The sky is our workplace. Nature is our teacher. We observe, understand and respect the environment behind everything we do.'],
            ['icon' => 'B', 'title' => 'Better Decisions', 'description' => 'We don’t just say, “Rain is coming.” We help you decide what to do next. Our weather intelligence is designed for real people making real decisions.'],
            ['icon' => 'O', 'title' => 'Observation', 'description' => 'Sometimes, the best technology still begins by looking up. Instruments give us data. Experience, observation and science give that data meaning. Lorem ipsum has been the industry’s standard dummy text ever since 1966.'],
            ['icon' => 'W', 'title' => 'Wonder', 'description' => 'After all these years, we still look at the sky with curiosity. Because every cloud, storm, rainbow and changing season gives us another reason to keep learning. Lorem ipsum is simply dummy text of the printing and typesetting industry.'],
        ];

        foreach ($values as $index => $value) {
            CoreValue::query()->firstOrCreate(
                ['title' => $value['title']],
                ['icon' => $value['icon'], 'description' => $value['description'], 'display_order' => $index + 1, 'is_enabled' => true],
            );
        }
    }
}
