<?php

return [

    'fields' => [

        'color_picker' => [

            'actions' => [

                'clear' => [
                    'label' => 'Hapus',
                ],

            ],

        ],

        'file_upload' => [

            'editor' => [

                'actions' => [

                    'cancel' => [
                        'label' => 'Batal',
                    ],

                    'drag_crop' => [
                        'label' => 'Mode seret "potong"',
                    ],

                    'drag_move' => [
                        'label' => 'Mode seret "pindah"',
                    ],

                    'flip_horizontal' => [
                        'label' => 'Balik gambar horizontal',
                    ],

                    'flip_vertical' => [
                        'label' => 'Balik gambar vertikal',
                    ],

                    'move_down' => [
                        'label' => 'Pindah gambar ke bawah',
                    ],

                    'move_left' => [
                        'label' => 'Pindah gambar ke kiri',
                    ],

                    'move_right' => [
                        'label' => 'Pindah gambar ke kanan',
                    ],

                    'move_up' => [
                        'label' => 'Pindah gambar ke atas',
                    ],

                    'reset' => [
                        'label' => 'Reset',
                    ],

                    'rotate_left' => [
                        'label' => 'Putar gambar ke kiri',
                    ],

                    'rotate_right' => [
                        'label' => 'Putar gambar ke kanan',
                    ],

                    'set_aspect_ratio' => [
                        'label' => 'Atur rasio aspek ke :ratio',
                    ],

                    'save' => [
                        'label' => 'Simpan',
                    ],

                    'zoom_100' => [
                        'label' => 'Perbesar gambar ke 100%',
                    ],

                    'zoom_in' => [
                        'label' => 'Perbesar',
                    ],

                    'zoom_out' => [
                        'label' => 'Perkecil',
                    ],

                ],

                'fields' => [

                    'height' => [
                        'label' => 'Tinggi',
                        'unit' => 'px',
                    ],

                    'rotation' => [
                        'label' => 'Rotasi',
                        'unit' => 'deg',
                    ],

                    'width' => [
                        'label' => 'Lebar',
                        'unit' => 'px',
                    ],

                    'x_position' => [
                        'label' => 'X',
                        'unit' => 'px',
                    ],

                    'y_position' => [
                        'label' => 'Y',
                        'unit' => 'px',
                    ],

                ],

                'aspect_ratios' => [

                    'label' => 'Rasio aspek',

                    'no_fixed' => [
                        'label' => 'Bebas',
                    ],

                ],

                'svg' => [

                    'messages' => [
                        'confirmation' => 'Tidak disarankan mengedit file SVG karena dapat mengakibatkan kehilangan kualitas saat scaling.\n Apakah Anda yakin ingin melanjutkan?',
                        'disabled' => 'Mengedit file SVG dinonaktifkan karena dapat mengakibatkan kehilangan kualitas saat scaling.',
                    ],

                ],

            ],

        ],

        'key_value' => [

            'actions' => [

                'add' => [
                    'label' => 'Tambah baris',
                ],

                'delete' => [
                    'label' => 'Hapus baris',
                ],

                'reorder' => [
                    'label' => 'Susun ulang baris',
                ],

            ],

            'fields' => [

                'key' => [
                    'label' => 'Kunci',
                ],

                'value' => [
                    'label' => 'Nilai',
                ],

            ],

        ],

        'markdown_editor' => [

            'toolbar_buttons' => [
                'attach_files' => 'Lampirkan file',
                'blockquote' => 'Blockquote',
                'bold' => 'Tebal',
                'bullet_list' => 'Daftar bullet',
                'code_block' => 'Blok kode',
                'heading' => 'Heading',
                'italic' => 'Miring',
                'link' => 'Tautan',
                'ordered_list' => 'Daftar nomor',
                'redo' => 'Ulangi',
                'strike' => 'Coret',
                'table' => 'Tabel',
                'undo' => 'Batalkan',
            ],

        ],

        'radio' => [

            'boolean' => [
                'true' => 'Ya',
                'false' => 'Tidak',
            ],

        ],

        'repeater' => [

            'actions' => [

                'add' => [
                    'label' => 'Tambah ke :label',
                ],

                'add_between' => [
                    'label' => 'Sisipkan',
                ],

                'delete' => [
                    'label' => 'Hapus',
                ],

                'clone' => [
                    'label' => 'Gandakan',
                ],

                'reorder' => [
                    'label' => 'Pindah',
                ],

                'move_down' => [
                    'label' => 'Pindah ke bawah',
                ],

                'move_up' => [
                    'label' => 'Pindah ke atas',
                ],

                'collapse' => [
                    'label' => 'Ciutkan',
                ],

                'expand' => [
                    'label' => 'Perluas',
                ],

                'collapse_all' => [
                    'label' => 'Ciutkan semua',
                ],

                'expand_all' => [
                    'label' => 'Perluas semua',
                ],

            ],

        ],

        'rich_editor' => [

            'dialogs' => [

                'link' => [

                    'actions' => [
                        'link' => 'Tautan',
                        'unlink' => 'Hapus tautan',
                    ],

                    'label' => 'URL',

                    'placeholder' => 'Masukkan URL',

                ],

            ],

            'toolbar_buttons' => [
                'attach_files' => 'Lampirkan file',
                'blockquote' => 'Blockquote',
                'bold' => 'Tebal',
                'bullet_list' => 'Daftar bullet',
                'code_block' => 'Blok kode',
                'h1' => 'Judul',
                'h2' => 'Heading',
                'h3' => 'Subjudul',
                'italic' => 'Miring',
                'link' => 'Tautan',
                'ordered_list' => 'Daftar nomor',
                'redo' => 'Ulangi',
                'strike' => 'Coret',
                'underline' => 'Garis bawah',
                'undo' => 'Batalkan',
            ],

        ],

        'select' => [

            'actions' => [

                'create_option' => [

                    'modal' => [

                        'heading' => 'Buat',

                        'actions' => [

                            'create' => [
                                'label' => 'Buat',
                            ],

                            'create_another' => [
                                'label' => 'Buat & buat lagi',
                            ],

                        ],

                    ],

                ],

                'edit_option' => [

                    'modal' => [

                        'heading' => 'Ubah',

                        'actions' => [

                            'save' => [
                                'label' => 'Simpan',
                            ],

                        ],

                    ],

                ],

            ],

            'boolean' => [
                'true' => 'Ya',
                'false' => 'Tidak',
            ],

            'loading_message' => 'Memuat...',

            'max_items_message' => 'Hanya :count yang dapat dipilih.',

            'no_search_results_message' => 'Tidak ada opsi yang cocok dengan pencarian Anda.',

            'placeholder' => 'Pilih opsi',

            'searching_message' => 'Mencari...',

            'search_prompt' => 'Mulai mengetik untuk mencari...',

        ],

        'tags_input' => [
            'placeholder' => 'Tag baru',
        ],

        'text_input' => [

            'actions' => [

                'hide_password' => [
                    'label' => 'Sembunyikan kata sandi',
                ],

                'show_password' => [
                    'label' => 'Tampilkan kata sandi',
                ],

            ],

        ],

        'toggle_buttons' => [

            'boolean' => [
                'true' => 'Ya',
                'false' => 'Tidak',
            ],

        ],

        'wizard' => [

            'actions' => [

                'previous_step' => [
                    'label' => 'Sebelumnya',
                ],

                'next_step' => [
                    'label' => 'Berikutnya',
                ],

            ],

        ],

    ],

    'components' => [

        'button' => [

            'messages' => [
                'submitting' => 'Mengirim...',
            ],

        ],

    ],

];
