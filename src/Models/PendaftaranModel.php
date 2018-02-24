<?php namespace Bantenprov\Pendaftaran\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * The PendaftaranModel class.
 *
 * @package Bantenprov\Pendaftaran
 * @author  bantenprov <developer.bantenprov@gmail.com>
 */
class PendaftaranModel extends Model
{
    /**
    * Table name.
    *
    * @var string
    */
    protected $table = 'pendaftaran';

    /**
    * The attributes that are mass assignable.
    *
    * @var mixed
    */
    protected $fillable = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];
}
