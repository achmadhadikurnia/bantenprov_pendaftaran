<?php

/* Require */
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

/* Models */
use Bantenprov\Pendaftaran\Models\Bantenprov\Pendaftaran\Pendaftaran;

class BantenprovPendaftaranSeederPendaftaran extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
	public function run()
	{
        Model::unguard();

        $pendaftarans = (object) [
            (object) [
                'kegiatan_id' => '1',
                'label' => 'Pendaftaran 1',
                'description' => 'Pendaftaran satu'
            ],
            (object) [
                'kegiatan_id' => '2',
                'label' => 'Pendaftaran 2',
                'description' => 'Pendaftaran dua',
            ]
        ];

        foreach ($pendaftarans as $pendaftaran) {
            $model = Pendaftaran::updateOrCreate(
                [
                    'kegiatan_id' => $pendaftaran->kegiatan_id,
                ],
                [
                    'label' => $pendaftaran->label,
                ],
                [
                    'description' => $pendaftaran->description,
                ]
            );
            $model->save();
        }
	}
}
