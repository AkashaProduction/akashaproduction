<?php
declare(strict_types=1);

return [
    'site' => [
        'name'          => 'Akasha Production',
        'domain'        => 'akashaproduction.com',
        'contact_email' => 'contact@akashaproduction.com',
    ],
    'admin' => [
        'email'         => 'admin@akashaproduction.com',
        // bcrypt hash of the admin password — verify via password_verify().
        'password_hash' => '$2y$12$Mx2qOyR8Q57OgO8uNHYlneqRziNkLACdIKlJmc3pplpizt8XxiVWm',
    ],
    'paths' => [
        'data'             => __DIR__ . '/../data',
        'uploads'          => __DIR__ . '/../assets/users-projects/images',
        'uploads_url_path' => '/assets/users-projects/images',
    ],
    'mail' => [
        'recipients_contact' => ['contact@akashaproduction.com', 'admin@akashaproduction.com'],
        'recipients_project' => ['contact@akashaproduction.com', 'admin@akashaproduction.com'],
    ],
];
