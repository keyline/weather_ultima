<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $images = $this->seedImages();

        foreach ($this->services() as $index => $data) {
            $service = Service::query()->firstOrCreate(
                ['name' => $data['name']],
                [
                    'category' => $data['category'],
                    'tags' => $data['tags'],
                    'statement' => $data['statement'],
                    'body' => $data['body'],
                    'result' => $data['result'],
                    'display_order' => $index + 1,
                    'is_enabled' => true,
                ],
            );

            if ($service->images()->doesntExist() && $images !== []) {
                foreach ($images as $order => $path) {
                    $service->images()->create([
                        'image' => $path,
                        'alt_text' => $data['name'],
                        'display_order' => $order + 1,
                    ]);
                }
            }
        }
    }

    /**
     * Copy the theme service images onto the public disk once.
     *
     * @return list<string>
     */
    private function seedImages(): array
    {
        $paths = [];

        foreach (['service_tab1_img1.png', 'service_tab1_img2.png', 'service_tab1_img3.png', 'service_tab1_img4.png'] as $file) {
            $source = public_path('material/images/'.$file);

            if (! File::exists($source)) {
                continue;
            }

            $target = 'services/'.$file;

            if (! Storage::disk('public')->exists($target)) {
                Storage::disk('public')->put($target, File::get($source));
            }

            $paths[] = $target;
        }

        return $paths;
    }

    /**
     * @return list<array{name: string, category: string, tags: string, statement: string, body: string, result: string}>
     */
    private function services(): array
    {
        return [
            [
                'name' => 'SkyWatch Live',
                'category' => 'Weather Forecasting & Intelligence',
                'tags' => 'Sports • Agriculture • Mountaineering • Outdoor Operations',
                'statement' => "Know the Weather. Make Your Move.\nThe sky rarely sends a calendar invite before changing its mind.",
                'body' => implode("\n\n", [
                    'A cricket match can be interrupted by rain. A golfer may need to rethink the next shot because of wind. A farmer may wait for the right precipitation window before sowing or harvesting. And on a mountain, a sudden fall in temperature, visibility or atmospheric pressure can change an expedition completely. Different situations. One common variable – weather.',
                    'SkyWatch Live combines meteorological expertise, atmospheric observations, forecasting technology and data interpretation to provide actionable weather intelligence for people whose plans depend on the sky.',
                    'For cricket, golf and other outdoor sports, we look at rainfall probability, wind speed and direction, temperature, humidity, dew, cloud cover and changing atmospheric conditions.',
                    'For agriculture, weather intelligence supports smarter decisions around irrigation, sowing, spraying and harvesting. For mountaineering and outdoor operations, wind, precipitation, visibility, pressure and temperature trends can become critical safety information.',
                    'We don’t want to simply tell you what the weather might be. We want to help you understand what that weather means for your next move.',
                ]),
                'result' => 'The result: The weather may change its mind. Your plan doesn’t have to lose its head.',
            ],
            [
                'name' => 'StationCraft',
                'category' => 'Weather Station Solutions',
                'tags' => 'Installation • Monitoring • Instruments • Data',
                'statement' => "Your Location. Your Weather. Your Data.\nYour weather app may know your city. But does it know your field?",
                'body' => implode("\n\n", [
                    'It can be raining at one end of a district while another stays completely dry. Temperature, wind, humidity and rainfall can change significantly across surprisingly short distances.',
                    'That is why sometimes the weather that matters most is the weather exactly where you stand.',
                    'StationCraft is Weather Ultima’s end-to-end weather station solution, covering system planning, instrument selection, installation, configuration, monitoring and technical support.',
                    'Depending on the requirement, stations can measure meteorological parameters such as air temperature, relative humidity, rainfall, wind speed, wind direction, atmospheric pressure, solar radiation and other environmental variables.',
                    'Our solutions are designed with actual field conditions in mind, summer heat, humidity, monsoon rainfall, dust and remote locations where continuous observation may be challenging.',
                    'StationCraft can support agricultural estates, research organisations, industries, educational institutions, infrastructure projects and other weather-sensitive operations.',
                    'Because accurate meteorology isn’t only about sophisticated sensors. Correct siting, positioning, calibration and reliable data continuity matter just as much.',
                    'We take care of the instruments and technology.',
                ]),
                'result' => 'The result: Your own weather station, watching the sky 24/7, no tea breaks, no weekends, just data.',
            ],
            [
                'name' => 'SolarSphere',
                'category' => 'Solar Products & Solutions',
                'tags' => 'Solar Technology • Products • Systems • Sustainable Energy',
                'statement' => "Put Every Ray to Work.\nThe sun clocks in every morning. No attendance reminder required.",
                'body' => implode("\n\n", [
                    'And behind that everyday sunshine lies an extraordinary source of energy waiting to be used intelligently.',
                    'SolarSphere is Weather Ultima’s solar solutions vertical, combining solar technology, products, systems and environmental understanding to help homes, institutions and organisations move towards cleaner energy.',
                    'Solar isn’t completely separate from meteorology. Solar radiation, cloud cover, atmospheric conditions, temperature, geographical location and seasonal climate variability can all influence solar-energy potential and system performance.',
                    'That is where our weather understanding adds another dimension.',
                    'Our approach begins with the requirement rather than the product catalogue. We consider the application, operating environment and energy objective before identifying an appropriate solar solution.',
                    'SolarSphere supports residential, commercial, institutional and project-specific applications, with a focus on efficiency, reliability, practical implementation and long-term sustainability.',
                    'Because going solar isn’t simply about placing panels under an open sky.',
                    'It is about understanding the available resource, choosing the right technology and designing a solution that makes practical and economic sense. The sunshine is already doing its part.',
                ]),
                'result' => 'The result: The sun shows up for free. We help you put it on the payroll.',
            ],
            [
                'name' => 'MetEdge Consulting',
                'category' => 'Meteorological Consulting',
                'tags' => 'Weather Intelligence • Climate Insights • Technical Advisory • Travel Advisory',
                'statement' => "When Weather Matters, Ask a Meteorologist.\nThe forecast says “40% chance of rain.” Your question is much simpler: “Should we go ahead?”",
                'body' => implode("\n\n", [
                    'That difference is exactly where MetEdge Consulting comes in.',
                    'Data can tell you what the atmosphere is doing. Experience helps interpret what that information means for your project, journey, operation or decision.',
                    'Our meteorological consultants analyse precipitation probability, temperature, humidity, atmospheric pressure, wind patterns, visibility, severe-weather potential, seasonal behaviour and longer-term climate trends to provide practical, context-specific guidance.',
                    'For agriculture, it could mean identifying a suitable weather window. For infrastructure or construction, it may involve monsoon and extreme-weather risk. For an outdoor event, it could mean deciding whether contingency planning is necessary.',
                    'Our Travel Advisory adds another human dimension. Whether you’re heading towards mountains, snow, tropical rainfall, extreme heat or an unfamiliar climate zone, we help you understand the weather conditions you may actually encounter.',
                    'Consulting engagements can range from a focused assessment to ongoing technical advisory.',
                    'No unnecessary meteorological jargon. No generic report simply forwarded to your inbox.',
                    'We read the atmosphere, interpret the science and tell you what matters.',
                ]),
                'result' => 'The result: Less staring at weather apps. More knowing exactly what your next move should be.',
            ],
            [
                'name' => 'WeatherWise Academy',
                'category' => 'Learning & Training',
                'tags' => 'Seminars • Workshops • Classes • Hands-on Weather Learning',
                'statement' => 'Come Curious. Leave Weather-Wise.',
                'body' => implode("\n\n", [
                    'Twinkle, twinkle, little star, ever wondered what you really are?',
                    'Most of us started learning about the sky long before we knew words like meteorology, atmospheric pressure or climatology.',
                    'We looked up. We asked questions. And that’s exactly where the art of science begins.',
                    'WeatherWise Academy brings weather, climate and atmospheric science out of textbooks and into a world students and professionals can see, touch, measure, question and enjoy.',
                    'Why do clouds float? How does a rain gauge measure rainfall? Why does wind change direction? What happens to atmospheric pressure before a storm? How do meteorologists know there could be rain tomorrow?',
                    'Through seminars, workshops, classes, demonstrations and hands-on sessions, participants explore cloud formation, precipitation, thunderstorms, temperature, humidity, wind, climate change, forecasting, weather instruments and Automatic Weather Stations.',
                    'Programs can be created for schools, colleges, universities, institutions, professionals and technical teams, from fun introductory sessions to advanced training.',
                    'We don’t want someone to simply memorize what an anemometer is. We’d rather let them see one, understand it, use it and ask another question. And yes, you can ask why yesterday’s forecast ruined your picnic.',
                ]),
                'result' => 'The result: Students walk in asking, “Will it rain?” and walk out asking, “What’s the atmospheric pressure?”',
            ],
            [
                'name' => 'GreenHorizon',
                'category' => 'Environmental Solutions',
                'tags' => 'Environmental Monitoring • Biogas • EV Charging • EIA • Sustainability',
                'statement' => "Measure Better. Live Greener.\nA greener future doesn’t begin with the word “sustainable” in a presentation.",
                'body' => implode("\n\n", [
                    'It begins by understanding what is happening around us today. The air we breathe. The energy we consume. The waste we generate. The emissions we release. And the environmental footprint we leave behind.',
                    'GreenHorizon is Weather Ultima’s environmental solutions vertical, extending our meteorological and instrumentation expertise into environmental monitoring, assessment, clean technology and sustainability.',
                    'Our services include air and environmental quality monitoring, Environmental Impact Assessment (EIA), biogas solutions, EV charging infrastructure, sustainability initiatives and environmental advisory.',
                    'Weather and environment are deeply connected. Wind direction, atmospheric stability, rainfall, temperature, humidity and seasonal climate patterns can influence how environmental conditions develop and how environmental data should be interpreted.',
                    'That is why our philosophy is simple: Measure before you assume.',
                    'GreenHorizon supports industries, infrastructure developers, municipalities, institutions and organisations seeking credible environmental insights, regulatory support or practical pathways towards cleaner operations. Whether you’re measuring air quality, exploring waste-to-energy through biogas or building EV charging infrastructure, we connect environmental responsibility with practical technology.',
                    'Because sustainability isn’t about looking green. It’s about doing better and being able to measure the difference.',
                ]),
                'result' => 'The result: Less green talk. More green action you can actually measure.',
            ],
            [
                'name' => 'WaterSphere',
                'category' => 'Water Treatment & Management',
                'tags' => 'Treatment • Purification • Water Quality • Sustainable Water Solutions',
                'statement' => "Better Water Begins with Better Understanding.\nThe water in your glass has probably travelled farther than you did today.",
                'body' => implode("\n\n", [
                    'Cloud. Rain. River. Soil. Groundwater. Pipeline. Tap. Every stage of that journey can influence the water that eventually reaches us.',
                    'WaterSphere is Weather Ultima’s water treatment and management vertical, bringing scientific understanding, appropriate technology and sustainable thinking together to address real-world water requirements.',
                    'Our solutions cover water treatment, purification, water-quality management and sustainable water systems for residential, institutional, commercial, industrial and project-specific applications.',
                    'And water has an inseparable relationship with weather and climate.',
                    'Changing precipitation patterns, intense rainfall events, prolonged dry periods, groundwater stress, rising temperatures and seasonal climate variability are changing how communities and organisations need to think about water availability and water security.',
                    'That’s why our approach doesn’t begin with selling equipment. It begins by understanding the source, quality, application and treatment requirement of the water before identifying an appropriate technological solution.',
                    'Because two water problems may look similar and still require very different answers.',
                    'We want every system to make sense for the water it is designed to manage.',
                    'Water may fall freely from the sky. Clean, usable water does not.',
                ]),
                'result' => 'The result: Because every drop counts and the right technology makes every drop work smarter.',
            ],
        ];
    }
}
