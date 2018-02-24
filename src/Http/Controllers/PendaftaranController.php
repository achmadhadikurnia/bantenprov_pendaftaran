<?php namespace Bantenprov\Pendaftaran\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bantenprov\Pendaftaran\Facades\Pendaftaran;
use Bantenprov\Pendaftaran\Models\PendaftaranModel;

/**
 * The PendaftaranController class.
 *
 * @package Bantenprov\Pendaftaran
 * @author  bantenprov <developer.bantenprov@gmail.com>
 */
class PendaftaranController extends Controller
{
    public function demo()
    {
        return Pendaftaran::welcome();
    }
}
