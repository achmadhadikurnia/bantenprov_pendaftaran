<?php

namespace Bantenprov\Pendaftaran\Models\Bantenprov\Pendaftaran;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pendaftaran extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'pendaftarans';
    protected $dates = [
        'deleted_at'
    ];
    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'label',
        'description'
    ];

    public function kegiatan()
    {
        return $this->belongsTo('Bantenprov\Kegiatan\Models\Bantenprov\Kegiatan\Kegiatan','kegiatan_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }
}
