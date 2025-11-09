<?php

return [

    'direction' => 'ltr',

    'actions' => [

        'billing' => [
            'label' => 'Kelola langganan',
        ],

        'logout' => [
            'label' => 'Keluar',
        ],

        'open_database_notifications' => [
            'label' => 'Buka notifikasi',
        ],

        'open_user_menu' => [
            'label' => 'Menu pengguna',
        ],

        'sidebar' => [

            'collapse' => [
                'label' => 'Ciutkan sidebar',
            ],

            'expand' => [
                'label' => 'Perluas sidebar',
            ],

        ],

        'theme_switcher' => [

            'dark' => [
                'label' => 'Aktifkan mode gelap',
            ],

            'light' => [
                'label' => 'Aktifkan mode terang',
            ],

            'system' => [
                'label' => 'Gunakan tema sistem',
            ],

        ],

    ],

    'avatar' => [
        'alt' => 'Avatar :name',
    ],

    'breadcrumbs' => [

        'actions' => [
            'toggle' => [
                'label' => 'Toggle breadcrumbs',
            ],
        ],

    ],

    'logo' => [
        'alt' => 'Logo :name',
    ],

    'notifications' => [

        'title' => 'Notifikasi',

        'database' => [

            'empty' => [
                'heading' => 'Tidak ada notifikasi',
                'description' => 'Silakan periksa kembali nanti.',
            ],

            'mark_all_as_read' => [
                'label' => 'Tandai semua sudah dibaca',
            ],

        ],

    ],

    'pagination' => [

        'label' => 'Navigasi paginasi',

        'overview' => [
            'single' => 'Menampilkan :first hingga :last dari :total hasil',
            'multiple' => 'Menampilkan :first hingga :last dari :total hasil',
        ],

        'fields' => [

            'records_per_page' => [

                'label' => 'per halaman',

                'options' => [
                    'all' => 'Semua',
                ],

            ],

        ],

        'actions' => [

            'first' => [
                'label' => 'Pertama',
            ],

            'go_to_page' => [
                'label' => 'Ke halaman :page',
            ],

            'last' => [
                'label' => 'Terakhir',
            ],

            'next' => [
                'label' => 'Berikutnya',
            ],

            'previous' => [
                'label' => 'Sebelumnya',
            ],

        ],

    ],

    'widgets' => [

        'account' => [

            'heading' => 'Selamat datang, :name',

            'links' => [

                'profile' => [
                    'label' => 'Kelola profil',
                ],

            ],

        ],

        'filament_info' => [

            'actions' => [

                'open_documentation' => [
                    'label' => 'Buka dokumentasi',
                ],

                'open_github' => [
                    'label' => 'Buka di GitHub',
                ],

                'open_website' => [
                    'label' => 'Buka website',
                ],

            ],

            'description' => 'Anda dapat menemukan lebih banyak informasi tentang Filament di sini:',
            'heading' => 'Selamat datang di Filament',

        ],

    ],

];
