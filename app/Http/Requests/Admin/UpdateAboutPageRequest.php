<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $section = $this->input('section');
        $sections = $this->sectionDefinitions();
        $selectedSection = is_string($section) ? ($sections[$section] ?? null) : null;

        if ($selectedSection === null) {
            return ['section' => ['required', 'in:banner-intro,mission,vision,founder,team,northstar']];
        }

        return array_merge([
            'section' => ['required', 'in:banner-intro,mission,vision,founder,team,northstar'],
            'content' => ['required', 'array:'.$selectedSection['content_keys']],
            'uploads' => ['nullable', 'array:'.$selectedSection['upload_keys']],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['nullable', 'boolean'],
        ], $selectedSection['rules']);
    }

    /**
     * @return array<string, array{content_keys: string, upload_keys: string, rules: array<string, array<int, string>>}>
     */
    private function sectionDefinitions(): array
    {
        return [
            'banner-intro' => [
                'content_keys' => 'banner_title,banner,intro',
                'upload_keys' => 'banner,intro',
                'rules' => [
                    'content.banner_title' => ['required', 'string', 'max:150'],
                    'content.banner' => ['required', 'array:image,image_alt'],
                    'content.banner.image' => ['nullable', 'string', 'max:255'],
                    'content.banner.image_alt' => ['nullable', 'string', 'max:255'],
                    'content.intro' => ['required', 'array:label,title,body,image'],
                    'content.intro.label' => ['required', 'string', 'max:100'],
                    'content.intro.title' => ['required', 'string', 'max:255'],
                    'content.intro.body' => ['nullable', 'string', 'max:10000'],
                    'content.intro.image' => ['nullable', 'string', 'max:255'],
                    'uploads.banner' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                    'uploads.intro' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                ],
            ],
            'mission' => [
                'content_keys' => 'mission',
                'upload_keys' => 'mission',
                'rules' => [
                    'content.mission' => ['required', 'array:label,title,cards'],
                    'content.mission.label' => ['required', 'string', 'max:100'],
                    'content.mission.title' => ['required', 'string', 'max:255'],
                    'content.mission.cards' => ['required', 'array', 'min:1', 'max:12'],
                    'content.mission.cards.*' => ['required', 'array:title,body,image'],
                    'content.mission.cards.*.title' => ['required', 'string', 'max:255'],
                    'content.mission.cards.*.body' => ['nullable', 'string', 'max:10000'],
                    'content.mission.cards.*.image' => ['nullable', 'string', 'max:255'],
                    'uploads.mission' => ['nullable', 'array:cards'],
                    'uploads.mission.cards.*' => ['nullable', 'array:image'],
                    'uploads.mission.cards.*.image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                ],
            ],
            'vision' => [
                'content_keys' => 'vision',
                'upload_keys' => 'vision',
                'rules' => [
                    'content.vision' => ['required', 'array:label,title,cards'],
                    'content.vision.label' => ['required', 'string', 'max:100'],
                    'content.vision.title' => ['required', 'string', 'max:255'],
                    'content.vision.cards' => ['required', 'array', 'min:1', 'max:12'],
                    'content.vision.cards.*' => ['required', 'array:title,body,image'],
                    'content.vision.cards.*.title' => ['required', 'string', 'max:255'],
                    'content.vision.cards.*.body' => ['nullable', 'string', 'max:10000'],
                    'content.vision.cards.*.image' => ['nullable', 'string', 'max:255'],
                    'uploads.vision' => ['nullable', 'array:cards'],
                    'uploads.vision.cards.*' => ['nullable', 'array:image'],
                    'uploads.vision.cards.*.image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                ],
            ],
            'founder' => [
                'content_keys' => 'founder',
                'upload_keys' => 'founder',
                'rules' => [
                    'content.founder' => ['required', 'array:label,title,name,signature,body,image'],
                    'content.founder.label' => ['required', 'string', 'max:100'],
                    'content.founder.title' => ['required', 'string', 'max:255'],
                    'content.founder.name' => ['required', 'string', 'max:150'],
                    'content.founder.signature' => ['nullable', 'string', 'max:500'],
                    'content.founder.body' => ['nullable', 'string', 'max:15000'],
                    'content.founder.image' => ['nullable', 'string', 'max:255'],
                    'uploads.founder' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                ],
            ],
            'team' => [
                'content_keys' => 'team_title,team',
                'upload_keys' => 'team',
                'rules' => [
                    'content.team_title' => ['required', 'string', 'max:150'],
                    'content.team' => ['required', 'array', 'min:1', 'max:32'],
                    'content.team.*' => ['required', 'array:name,role,organisation,bio,image'],
                    'content.team.*.name' => ['required', 'string', 'max:150'],
                    'content.team.*.role' => ['nullable', 'string', 'max:150'],
                    'content.team.*.organisation' => ['nullable', 'string', 'max:150'],
                    'content.team.*.bio' => ['nullable', 'string', 'max:5000'],
                    'content.team.*.image' => ['nullable', 'string', 'max:255'],
                    'uploads.team.*' => ['nullable', 'array:image'],
                    'uploads.team.*.image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                ],
            ],
            'northstar' => [
                'content_keys' => 'northstar_title,northstar',
                'upload_keys' => 'northstar',
                'rules' => [
                    'content.northstar_title' => ['required', 'string', 'max:150'],
                    'content.northstar' => ['required', 'array', 'min:1', 'max:32'],
                    'content.northstar.*' => ['required', 'array:name,image'],
                    'content.northstar.*.name' => ['required', 'string', 'max:150'],
                    'content.northstar.*.image' => ['nullable', 'string', 'max:255'],
                    'uploads.northstar.*' => ['nullable', 'array:image'],
                    'uploads.northstar.*.image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
                ],
            ],
        ];
    }
}
