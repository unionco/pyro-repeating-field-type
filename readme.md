Sample Field Assignment

```php

<?php

'content' => [
    'type' => 'repeating',
    'settings' => [
        'field_groups' => [
            [
                'name' => 'Heading',
                'slug' => 'heading',
                'elements' => [
                    [
                        'label' => 'Main Heading',
                        'slug' => 'main_heading',
                        'type' => 'text',
                    ],

                    [
                        'label' => 'Sub Heading',
                        'slug' => 'sub_heading',
                        'type' => 'text',
                    ],
                ]
            ],
            [
                'name' => 'Related Link',
                'slug' => 'related_link',
                'elements' => [
                    [
                        'label' => 'Title',
                        'slug' => 'title',
                        'type' => 'text',
                    ],
                    [
                        'label' => 'URL',
                        'slug' => 'url',
                        'type' => 'url',
                    ],
                ]
            ],
            [
                'name' => 'Text Content',
                'slug' => 'text_content',
                'elements' => [
                    [
                        'label' => '',
                        'slug' => 'text_content',
                        'type' => 'wysiwyg',
                    ],
                ]
            ],
        ],
    ],
],
```