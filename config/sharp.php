<?php

return [
    'name' => 'Chrono Béton',
    'custom_url_segment' => 'sharp',
    'display_sharp_version_in_title' => true,
    'display_breadcrumb' => true,

    'entities' => [
        'consumers' => \App\Sharp\Entities\ConsumerEntity::class,
        'consumer_concrete_sessions' => \App\Sharp\Entities\ConsumerConcreteSessionEntity::class,
        'concrete_sessions' => \App\Sharp\Entities\ConcreteSessionEntity::class,
        'customers' => \App\Sharp\Entities\CustomerEntity::class,
    ],

    'dashboards' => [
    ],

    'menu' => \App\Sharp\AppSharpMenu::class,

    'uploads' => [
        'tmp_dir' => env('SHARP_UPLOADS_TMP_DIR', 'tmp'),
        'thumbnails_disk' => env('SHARP_UPLOADS_THUMBS_DISK', 'public'),
        'thumbnails_dir' => env('SHARP_UPLOADS_THUMBS_DIR', 'thumbnails'),
        'transform_keep_original_image' => true,
        'model_class' => \App\Models\Media::class,
    ],

    'markdown_editor' => [
        'tight_lists_only' => true,
        'nl2br' => false,
    ],

    'auth' => [
        'login_attribute' => 'email',
        'password_attribute' => 'password',
        'suggest_remember_me' => true,
        'display_attribute' => 'name',
        'check_handler' => \App\Sharp\Auth\SharpCheckHandler::class,
    ],

    'theme' => [
        'primary_color' => '#004c9b',
    ],

];
