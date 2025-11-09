<?php

return [

    'column' => [

        'actions' => [
            'label' => 'Aksi',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Pilih/batalkan semua item untuk aksi massal.',
        ],

        'bulk_select_record' => [
            'label' => 'Pilih/batalkan item :key untuk aksi massal.',
        ],

        'bulk_select_group' => [
            'label' => 'Pilih/batalkan grup :title untuk aksi massal.',
        ],

        'search' => [
            'label' => 'Cari',
            'placeholder' => 'Cari',
            'indicator' => 'Cari',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Selesai menyusun ulang data',
        ],

        'enable_reordering' => [
            'label' => 'Susun ulang data',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grup',
        ],

        'open_bulk_actions' => [
            'label' => 'Aksi massal',
        ],

        'toggle_columns' => [
            'label' => 'Toggle kolom',
        ],

    ],

    'empty' => [

        'heading' => 'Tidak ada data',

        'description' => 'Buat :model untuk memulai.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Terapkan filter',
            ],

            'remove' => [
                'label' => 'Hapus filter',
            ],

            'remove_all' => [
                'label' => 'Hapus semua filter',
                'tooltip' => 'Hapus semua filter',
            ],

            'reset' => [
                'label' => 'Reset',
            ],

        ],

        'heading' => 'Filter',

        'indicator' => 'Filter aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',
        ],

        'trashed' => [

            'label' => 'Data terhapus',

            'only_trashed' => 'Hanya data terhapus',

            'with_trashed' => 'Dengan data terhapus',

            'without_trashed' => 'Tanpa data terhapus',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Kelompokkan berdasarkan',
                'placeholder' => 'Kelompokkan berdasarkan',
            ],

            'direction' => [

                'label' => 'Arah pengelompokan',

                'options' => [
                    'asc' => 'Menaik',
                    'desc' => 'Menurun',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Seret dan lepas data ke dalam urutan.',

    'selection_indicator' => [

        'selected_count' => '1 data dipilih.|:count data dipilih.',

        'actions' => [

            'select_all' => [
                'label' => 'Pilih semua :count',
            ],

            'deselect_all' => [
                'label' => 'Batalkan semua pilihan',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Urutkan berdasarkan',
            ],

            'direction' => [

                'label' => 'Arah pengurutan',

                'options' => [
                    'asc' => 'Menaik',
                    'desc' => 'Menurun',
                ],

            ],

        ],

    ],

];
