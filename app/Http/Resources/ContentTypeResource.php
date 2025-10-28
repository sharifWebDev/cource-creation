<?php
// app/Http/Resources/ContentTypeResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'schema' => $this->schema,
            'validation_rules' => $this->validation_rules,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'has_media' => $this->has_media,
            'has_url' => $this->has_url,
            'has_text' => $this->has_text,
            'icon_html' => $this->icon_html,
            'usage_count' => $this->when(isset($this->usage_count), $this->usage_count),
            'template' => $this->getTemplateStructure(),
        ];
    }

    /**
     * Get template structure for frontend form generation
     */
    private function getTemplateStructure(): array
    {
        $structure = [
            'fields' => [],
            'validation' => $this->validation_rules ?? [],
        ];

        switch ($this->slug) {
            case 'text':
                $structure['fields'] = [
                    [
                        'type' => 'textarea',
                        'name' => 'content',
                        'label' => 'Content',
                        'required' => true,
                        'rich_text' => true,
                    ],
                ];
                break;

            case 'image':
                $structure['fields'] = [
                    [
                        'type' => 'file',
                        'name' => 'image_path',
                        'label' => 'Image',
                        'required' => true,
                        'accept' => 'image/*',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'caption',
                        'label' => 'Caption',
                        'required' => false,
                    ],
                ];
                break;

            case 'video':
                $structure['fields'] = [
                    [
                        'type' => 'select',
                        'name' => 'source_type',
                        'label' => 'Video Source',
                        'required' => true,
                        'options' => [
                            ['value' => 'url', 'label' => 'URL'],
                            ['value' => 'upload', 'label' => 'Upload'],
                        ],
                    ],
                    [
                        'type' => 'url',
                        'name' => 'url',
                        'label' => 'Video URL',
                        'required' => false,
                        'conditional' => ['source_type' => 'url'],
                    ],
                    [
                        'type' => 'file',
                        'name' => 'file',
                        'label' => 'Upload Video',
                        'required' => false,
                        'accept' => 'video/*',
                        'conditional' => ['source_type' => 'upload'],
                    ],
                    [
                        'type' => 'text',
                        'name' => 'duration',
                        'label' => 'Video Length (HH:MM:SS)',
                        'required' => false,
                        'pattern' => '([0-1]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]',
                    ],
                ];
                break;

            case 'document':
                $structure['fields'] = [
                    [
                        'type' => 'file',
                        'name' => 'file_path',
                        'label' => 'Document',
                        'required' => true,
                        'accept' => '.pdf,.doc,.docx,.txt',
                    ],
                    [
                        'type' => 'text',
                        'name' => 'file_name',
                        'label' => 'File Name',
                        'required' => true,
                    ],
                ];
                break;

            case 'link':
                $structure['fields'] = [
                    [
                        'type' => 'url',
                        'name' => 'url',
                        'label' => 'URL',
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'name' => 'title',
                        'label' => 'Link Title',
                        'required' => true,
                    ],
                ];
                break;

            case 'quiz':
                $structure['fields'] = [
                    [
                        'type' => 'quiz-builder',
                        'name' => 'questions',
                        'label' => 'Questions',
                        'required' => true,
                    ],
                ];
                break;
        }

        return $structure;
    }
}
