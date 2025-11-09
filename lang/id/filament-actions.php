<?php

return [

    'single' => [

        'label' => 'Aksi',

        'modal' => [

            'heading' => ':label',

            'actions' => [

                'cancel' => [
                    'label' => 'Batal',
                ],

            ],

        ],

        'notifications' => [

            'success' => [
                'title' => 'Berhasil',
            ],

        ],

        'messages' => [
            'delete' => 'Apakah Anda yakin ingin menghapus ini?',
        ],

    ],

    'multiple' => [

        'label' => 'Aksi',

        'modal' => [

            'heading' => ':label',

            'actions' => [

                'cancel' => [
                    'label' => 'Batal',
                ],

            ],

        ],

        'notifications' => [

            'success' => [
                'title' => 'Berhasil',
            ],

        ],

        'messages' => [
            'delete' => 'Apakah Anda yakin ingin menghapus item yang dipilih?',
        ],

    ],

    'create' => [

        'label' => 'Buat',

        'modal' => [

            'heading' => 'Buat :label',

            'actions' => [

                'create' => [
                    'label' => 'Buat',
                ],

                'create_another' => [
                    'label' => 'Buat & buat lagi',
                ],

            ],

        ],

        'notifications' => [

            'created' => [
                'title' => 'Berhasil dibuat',
            ],

        ],

    ],

    'edit' => [

        'label' => 'Ubah',

        'modal' => [

            'heading' => 'Ubah :label',

            'actions' => [

                'save' => [
                    'label' => 'Simpan perubahan',
                ],

            ],

        ],

        'notifications' => [

            'saved' => [
                'title' => 'Berhasil disimpan',
            ],

        ],

    ],

    'view' => [

        'label' => 'Lihat',

        'modal' => [

            'heading' => 'Lihat :label',

            'actions' => [

                'close' => [
                    'label' => 'Tutup',
                ],

            ],

        ],

    ],

    'delete' => [

        'label' => 'Hapus',

        'modal' => [

            'heading' => 'Hapus :label',

            'actions' => [

                'delete' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Berhasil dihapus',
            ],

        ],

    ],

    'force_delete' => [

        'label' => 'Hapus permanen',

        'modal' => [

            'heading' => 'Hapus permanen :label',

            'actions' => [

                'delete' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Berhasil dihapus',
            ],

        ],

    ],

    'restore' => [

        'label' => 'Pulihkan',

        'modal' => [

            'heading' => 'Pulihkan :label',

            'actions' => [

                'restore' => [
                    'label' => 'Pulihkan',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Berhasil dipulihkan',
            ],

        ],

    ],

    'replicate' => [

        'label' => 'Gandakan',

        'modal' => [

            'heading' => 'Gandakan :label',

            'actions' => [

                'replicate' => [
                    'label' => 'Gandakan',
                ],

            ],

        ],

        'notifications' => [

            'replicated' => [
                'title' => 'Berhasil digandakan',
            ],

        ],

    ],

    'attach' => [

        'label' => 'Lampirkan',

        'modal' => [

            'heading' => 'Lampirkan :label',

            'fields' => [

                'record_id' => [
                    'label' => 'Data',
                ],

            ],

            'actions' => [

                'attach' => [
                    'label' => 'Lampirkan',
                ],

                'attach_another' => [
                    'label' => 'Lampirkan & lampirkan lagi',
                ],

            ],

        ],

        'notifications' => [

            'attached' => [
                'title' => 'Berhasil dilampirkan',
            ],

        ],

    ],

    'detach' => [

        'label' => 'Lepaskan',

        'modal' => [

            'heading' => 'Lepaskan :label',

            'actions' => [

                'detach' => [
                    'label' => 'Lepaskan',
                ],

            ],

        ],

        'notifications' => [

            'detached' => [
                'title' => 'Berhasil dilepaskan',
            ],

        ],

        'single' => [

            'modal' => [
                'heading' => 'Lepaskan :label',
            ],

        ],

        'multiple' => [

            'modal' => [
                'heading' => 'Lepaskan :label yang dipilih',
            ],

        ],

    ],

    'associate' => [

        'label' => 'Asosiasikan',

        'modal' => [

            'heading' => 'Asosiasikan :label',

            'fields' => [

                'record_id' => [
                    'label' => 'Data',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'Asosiasikan',
                ],

                'associate_another' => [
                    'label' => 'Asosiasikan & asosiasikan lagi',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'Berhasil diasosiasikan',
            ],

        ],

    ],

    'dissociate' => [

        'label' => 'Batalkan asosiasi',

        'modal' => [

            'heading' => 'Batalkan asosiasi :label',

            'actions' => [

                'dissociate' => [
                    'label' => 'Batalkan asosiasi',
                ],

            ],

        ],

        'notifications' => [

            'dissociated' => [
                'title' => 'Asosiasi berhasil dibatalkan',
            ],

        ],

        'single' => [

            'modal' => [
                'heading' => 'Batalkan asosiasi :label',
            ],

        ],

        'multiple' => [

            'modal' => [
                'heading' => 'Batalkan asosiasi :label yang dipilih',
            ],

        ],

    ],

    'import' => [

        'label' => 'Impor',

        'modal' => [

            'heading' => 'Impor :label',

            'form' => [

                'file' => [

                    'label' => 'File',

                    'placeholder' => 'Unggah file CSV',

                    'rules' => [
                        'duplicate_columns' => '{0} File tidak boleh berisi lebih dari satu header kolom kosong.|{1,*} File tidak boleh berisi header kolom duplikat: :columns.',
                    ],

                ],

                'columns' => [
                    'label' => 'Kolom',
                    'placeholder' => 'Pilih kolom',
                ],

            ],

            'actions' => [

                'download_example' => [
                    'label' => 'Unduh contoh file CSV',
                ],

                'import' => [
                    'label' => 'Impor',
                ],

            ],

        ],

        'notifications' => [

            'completed' => [

                'title' => 'Impor selesai',

                'actions' => [

                    'download_failed_rows_csv' => [
                        'label' => 'Unduh informasi tentang baris yang gagal|Unduh informasi tentang baris yang gagal',
                    ],

                ],

            ],

            'max_rows' => [
                'title' => 'File CSV yang diunggah terlalu besar',
                'body' => 'Anda tidak dapat mengimpor lebih dari 1 baris sekaligus.|Anda tidak dapat mengimpor lebih dari :count baris sekaligus.',
            ],

            'started' => [
                'title' => 'Impor dimulai',
                'body' => 'Impor Anda telah dimulai dan 1 baris akan diproses di latar belakang.|Impor Anda telah dimulai dan :count baris akan diproses di latar belakang.',
            ],

        ],

        'example_csv' => [

            'file_name' => ':importer-contoh',

        ],

        'failure_csv' => [

            'file_name' => 'import-:import_id-:csv_name-baris-gagal',

            'columns' => [
                'name' => 'Nama',
                'body' => 'Isi',
            ],

        ],

    ],

    'export' => [

        'label' => 'Ekspor',

        'modal' => [

            'heading' => 'Ekspor :label',

            'form' => [

                'columns' => [

                    'label' => 'Kolom',

                    'form' => [

                        'is_enabled' => [
                            'label' => ':column diaktifkan',
                        ],

                        'label' => [
                            'label' => 'Label :column',
                        ],

                    ],

                ],

            ],

            'actions' => [

                'export' => [
                    'label' => 'Ekspor',
                ],

            ],

        ],

        'notifications' => [

            'completed' => [

                'title' => 'Ekspor selesai',

                'actions' => [

                    'download_csv' => [
                        'label' => 'Unduh .csv',
                    ],

                    'download_xlsx' => [
                        'label' => 'Unduh .xlsx',
                    ],

                ],

            ],

            'max_rows' => [
                'title' => 'Ekspor terlalu besar',
                'body' => 'Anda tidak dapat mengekspor lebih dari 1 baris sekaligus.|Anda tidak dapat mengekspor lebih dari :count baris sekaligus.',
            ],

            'started' => [
                'title' => 'Ekspor dimulai',
                'body' => 'Ekspor Anda telah dimulai dan 1 baris akan diproses di latar belakang.|Ekspor Anda telah dimulai dan :count baris akan diproses di latar belakang.',
            ],

        ],

        'file_name' => 'ekspor-:export_id-:model',

    ],

];
