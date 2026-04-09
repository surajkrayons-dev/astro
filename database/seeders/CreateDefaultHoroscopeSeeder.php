<?php

namespace Database\Seeders;

use App\Models\Horoscope;
use App\Models\ZodiacSign;
use Illuminate\Database\Seeder;

class CreateDefaultHoroscopeSeeder extends Seeder
{
    public function run(): void
    {
        $zodiacs = ZodiacSign::all();

        $horoscopeData = [
            'monthly' => [
                'title_suffix' => 'Monthly Horoscope',
                'overview' => 'This month brings stability and clarity in life.',
                'love' => 'Emotional understanding will increase.',
                'career' => 'Career growth is likely this month.',
                'health' => 'Focus on long-term wellness.',
                'finance' => 'Good month for savings and investments.',
                'family' => 'Family support will remain strong.',
                'students' => 'Students will stay focused on studies.',
                'warning' => 'Avoid overconfidence this month.',
            ],
            'yearly' => [
                'title_suffix' => 'Yearly Horoscope',
                'overview' => 'This year opens doors to major transformation.',
                'love' => 'Strong emotional connections will develop.',
                'career' => 'Major career milestones are expected.',
                'health' => 'Overall health improves gradually.',
                'finance' => 'Financial growth and stability are indicated.',
                'family' => 'Family bonding will improve.',
                'students' => 'Students will achieve good results.',
                'warning' => 'Avoid unnecessary risks this year.',
            ],
        ];

        $colors = ['Red', 'Blue', 'Green', 'Yellow', 'Purple'];

        foreach ($zodiacs as $zodiac) {
            foreach ($horoscopeData as $type => $data) {

                Horoscope::create([
                    'zodiac_id' => $zodiac->id,
                    'type' => $type,

                    'title' => $zodiac->name . ' ' . $data['title_suffix'],
                    'overview' => $data['overview'],

                    'love' => $data['love'],
                    'career' => $data['career'],
                    'health' => $data['health'],
                    'finance' => $data['finance'],
                    'family' => $data['family'],
                    'students' => $data['students'],
                    'warning' => $data['warning'],

                    // 🔥 SECTION DATE ARRAYS
                    'career_date' => [rand(1, 28), rand(1, 28)],
                    'finance_date' => [rand(1, 28), rand(1, 28)],
                    'love_date' => [rand(1, 28), rand(1, 28)],
                    'health_date' => [rand(1, 28), rand(1, 28)],
                    'family_date' => [rand(1, 28), rand(1, 28)],
                    'students_date' => [rand(1, 28), rand(1, 28)],

                    // 🔥 LUCKY
                    'lucky_numbers' => [
                        rand(1, 9),
                        rand(1, 9)
                    ],

                    'lucky_colors' => [
                        $colors[array_rand($colors)],
                        $colors[array_rand($colors)]
                    ],

                    'status' => 1,
                    'created_by' => 1,
                    'modified_by' => 1,
                ]);
            }
        }
    }
}