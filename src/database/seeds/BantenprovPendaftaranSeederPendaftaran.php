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
                'user_id' => '1',
                'kegiatan_id' => '1',
                'label' => 'Kegiatan 1',
                'description' => 'Kegiatan satu'
            ],
            (object) [
                'user_id' => '2',
                'kegiatan_id' => '2',
                'label' => 'Kegiatan 2',
                'description' => 'Kegiatan dua',
            ]
        ];

        foreach ($pendaftarans as $pendaftaran) {
            $model = Pendaftaran::updateOrCreate(
                [
                    'user_id' => $pendaftaran->user_id,
                    'kegiatan_id' => $pendaftaran->kegiatan_id,
                    'label' => $pendaftaran->label,
                    'description' => $pendaftaran->description,
                ]
            );
            $model->save();
        }
	}
}
