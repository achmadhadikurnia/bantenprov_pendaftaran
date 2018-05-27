<?php

namespace Bantenprov\Pendaftaran\Http\Controllers;

/* Require */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
/* Models */
use Bantenprov\Pendaftaran\Models\Bantenprov\Pendaftaran\Pendaftaran;
use Bantenprov\Kegiatan\Models\Bantenprov\Kegiatan\Kegiatan;
use Bantenprov\Sekolah\Models\Bantenprov\Sekolah\Sekolah;
use Bantenprov\Sekolah\Models\Bantenprov\Sekolah\AdminSekolah;
use App\User;
use Bantenprov\Siswa\Models\Bantenprov\Siswa\Siswa;
use Bantenprov\Sktm\Models\Bantenprov\Sktm\MasterSktm;
use Bantenprov\Sktm\Models\Bantenprov\Sktm\Sktm;
use Bantenprov\Prestasi\Models\Bantenprov\Prestasi\JenisPrestasi;
use Bantenprov\Prestasi\Models\Bantenprov\Prestasi\MasterPrestasi;
use Bantenprov\Prestasi\Models\Bantenprov\Prestasi\Prestasi;
use Bantenprov\Prestasi\Http\Controllers\MasterPrestasiController;

/* Etc */
use Validator;
use Auth;

/**
 * The PendaftaranController class.
 *
 * @package Bantenprov\Pendaftaran
 * @author  bantenprov <developer.bantenprov@gmail.com>
 */
class PendaftaranController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $kegiatanModel;
    protected $pendaftaran;
    protected $user;
    protected $sekolah;
    protected $admin_sekolah;
    protected $siswa;
    protected $mastersktm;
    protected $sktm;
    protected $jenisprestasi;
    protected $masterprestasi;
    protected $masterprestasicont;
    protected $prestasi;

    public function __construct()
    {
        $this->pendaftaran          = new Pendaftaran;
        $this->kegiatanModel        = new Kegiatan;
        $this->user                 = new User;
        $this->sekolah              = new Sekolah;
        $this->admin_sekolah        = new AdminSekolah;
        $this->siswa                = new Siswa;
        $this->sktm                 = new Sktm;
        $this->mastersktm           = new MasterSktm;
        $this->jenisprestasi        = new JenisPrestasi;
        $this->masterprestasi       = new MasterPrestasi;
        $this->masterprestasicont   = new MasterPrestasiController;
        $this->prestasi             = new Prestasi;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin_sekolah = $this->admin_sekolah->where('admin_sekolah_id', Auth::user()->id)->first();

        if(is_null($admin_sekolah) && $this->checkRole(['superadministrator']) === false){
            $response = [];
            return response()->json($response)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET');
        }else{
            $page  					= 10;
            $data 					= $this->pendaftaran
                                    ->select(
                                        'pendaftarans.id',
                                        'users.name',
                                        'pendaftarans.tanggal_pendaftaran',
                                        'sekolahs.nama',
                                        'kegiatans.label',
                                        'siswas.nama_siswa'
                                    )
                                    ->leftjoin(
                                        'sekolahs',
                                        'pendaftarans.sekolah_id','=','sekolahs.id'
                                    )
                                    ->leftjoin(
                                        'kegiatans',
                                        'pendaftarans.kegiatan_id','=','kegiatans.id'
                                    )
                                    ->leftjoin(
                                        'users',
                                        'pendaftarans.user_id','=','users.id'
                                    )
                                    ->leftjoin(
                                        'siswas',
                                        'users.name','=','siswas.nomor_un'
                                    )
                                    ->where('pendaftarans.sekolah_id','=',$admin_sekolah->sekolah_id)
                                    ->paginate($page);
            return response()->json($data)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET');            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $admin_sekolah = $this->admin_sekolah->where('admin_sekolah_id', Auth::user()->id)->first();

        $pendaftaran        = $this->pendaftaran;
        $current_user_id    = $request->user_id;

        $validator = Validator::make($request->all(), [
            'kegiatan_id'           => 'required',
            'user_id'               => "required|exists:{$this->user->getTable()},id",
            'tanggal_pendaftaran'   => 'required',
            'sekolah_id'            => 'required',
        ]);

        if($admin_sekolah->sekolah_id != $request->sekolah_id && $this->checkRole(['superadministrator']) === false){
            $response['error']      = true;
            $response['message']    = 'Terjadi kesalahan, mohon ulangi lagi pengisian data yang benar.';
            $response['status']     = true;

            return response()->json($response);
        }

        if ($validator->fails()) {
            $error      = true;
            $message    = $validator->errors()->first();

            } else {
                $pendaftaran->kegiatan_id           = $request->input('kegiatan_id');
                $pendaftaran->user_id               = $current_user_id;
                $pendaftaran->tanggal_pendaftaran   = $request->input('tanggal_pendaftaran');
                $pendaftaran->sekolah_id            = $request->input('sekolah_id');
                $pendaftaran->save();
                $error      = false;
                $message    = 'Success';
            }

        $response['error']      = $error;
        $response['message']    = $message;
        $response['status']     = true;

        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $admin_sekolah = $this->admin_sekolah->where('admin_sekolah_id', Auth::user()->id)->first();
        // if($this->checkRole(['superadministrator'])){
        //     $pendaftaran = $this->pendaftaran->findOrFail($id);
        // }else{
        //     $pendaftaran = $this->pendaftaran->where('sekolah_id', $admin_sekolah->sekolah_id)->findOrFail($id);
        // }
        


        $admin_sekolah = $this->admin_sekolah->where('admin_sekolah_id', Auth::user()->id)->first();

        if($this->checkRole(['superadministrator'])){
//            $pendaftaran = $this->pendaftaran->findOrFail($id);
        }else{
            $data 					= $this->pendaftaran
                                    ->select(
                                        'siswas.nama_siswa',
                                        'kegiatans.label as jenis_pendaftaran',
                                        'pendaftarans.id',
                                        'users.name',
                                        'pendaftarans.tanggal_pendaftaran',
                                        'sekolahs.nama as sekolah_tujuan',
                                        'master_sktms.nama as jenis_sktm',
                                        'sktms.no_sktm',
                                        'prestasis.nama_lomba',
                                        'jenis_prestasis.nama as jenis_prestasi',
                                        'master_prestasis.juara',
                                        'master_prestasis.tingkat'                                        
                                    )
                                    ->leftjoin(
                                        'sekolahs',
                                        'pendaftarans.sekolah_id','=','sekolahs.id'
                                    )
                                    ->leftjoin(
                                        'kegiatans',
                                        'pendaftarans.kegiatan_id','=','kegiatans.id'
                                    )
                                    ->leftjoin(
                                        'users',
                                        'pendaftarans.user_id','=','users.id'
                                    )
                                    ->leftjoin(
                                        'siswas',
                                        'users.name','=','siswas.nomor_un'
                                    )
                                    ->leftjoin(
                                        'sktms',
                                        'pendaftarans.nomor_un','=','sktms.nomor_un'
                                    )                                    
                                    ->leftjoin(
                                        'master_sktms',
                                        'sktms.master_sktm_id','=','master_sktms.id'
                                    )                                    
                                    ->leftjoin(
                                        'prestasis',
                                        'pendaftarans.nomor_un','=','prestasis.nomor_un'
                                    )
                                    ->leftjoin(
                                        'master_prestasis',
                                        'prestasis.master_prestasi_id','=','master_prestasis.id'
                                    )
                                    ->leftjoin(
                                        'jenis_prestasis',
                                        'master_prestasis.jenis_prestasi_id','=','jenis_prestasis.id'
                                    )
                                    ->where('pendaftarans.id','=',$id)
                                    ->first();
                // $result         = array();
                // foreach((array)$data as $key => $val){
                //     if($key == '*attributes'){
                //         dd($val);
                //         foreach($val as $key1 => $val1){
                //             if($key1 == 'juara'){
                //                 $result['juara']    = $this->masterprestasi->prestasi_label($val1);
                //             }elseif($key1 == 'tingkat'){
                //                 $result['tingkat']  = $this->masterprestasi->tingkat_label($val1);
                //             }else{
                //                 $result[$key1]      = $val1;
                //             }
                //         }
                //     }
                // }
                return response()->json($data)
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET');            
        }

        // $response['pendaftaran']    = $pendaftaran;
        // $response['kegiatan']       = $pendaftaran->kegiatan;
        // $response['sekolah']        = $pendaftaran->sekolah;
        // $response['user']           = $pendaftaran->user;
        // $response['siswa']          = $pendaftaran->siswa;
        // /*
        // // $response['prestasi']       = $pendaftaran->sekolah;
        // // $response['sktm']          = $pendaftaran->sekolah;
        // */

        // $response['nama_siswa']             = $data->nama_siswa;
        // $response['jenis_pendaftaran']      = $data->label;
        // $response['sekolah_tujuan']         = $data->nama;
        // $response['tanggal_pendaftaran']    = $data->tanggal_pendaftaran;
        // $response['jenis_sktm']             = $data->nama;
        // $response['no_sktm']                = $data->no_sktm;
        // $response['prestasi']               = $data->nama_lomba;
        // $response['jenis_prestasi']         = $data->nama;
        // $response['juara']                  = $data->juara;
        // $response['tingkat']                = $data->tingkat;
        // $response['status']                 = true;
        // return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $admin_sekolah = $this->admin_sekolah->where('admin_sekolah_id', Auth::user()->id)->first();

        if($this->checkRole(['superadministrator'])){
            $pendaftaran = $this->pendaftaran->with(['kegiatan', 'sekolah', 'user' ])->findOrFail($id);
        }else{
            $pendaftaran = $this->pendaftaran->where('sekolah_id', $admin_sekolah->sekolah_id)->with(['kegiatan', 'sekolah', 'user' ])->findOrFail($id);
        }


        $response['pendaftaran']['user']     = array_add($pendaftaran->user, 'label', $pendaftaran->user->name);



        $response['pendaftaran']    = $pendaftaran;
        //$response['kegiatan']       = $pendaftaran->kegiatan;
        //$response['sekolah']        = $pendaftaran->sekolah->nama;
        //$response['user']           = $pendaftaran->user;
        $response['error']          = false;
        $response['message']        = 'Success';
        $response['status']         = true;

        return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $admin_sekolah = $this->admin_sekolah->where('admin_sekolah_id', Auth::user()->id)->first();

        if($this->checkRole(['superadministrator'])){
            $pendaftaran = $this->pendaftaran->with(['kegiatan', 'sekolah', 'user' ])->findOrFail($id);
        }else{
            $pendaftaran = $this->pendaftaran->where('sekolah_id', $admin_sekolah->sekolah_id)->with(['kegiatan', 'sekolah', 'user' ])->findOrFail($id);

            if(is_null($pendaftaran)){
                $response['error']      = true;
                $response['message']    = 'Terjadi kesalahan saat update data.';
                $response['status']     = true;

                return response()->json($response);
            }
        }


        $validator = Validator::make($request->all(), [
            'kegiatan_id'           => 'required',
            'tanggal_pendaftaran'   => 'required',
            'sekolah_id'            => 'required',
        ]);

        if ($validator->fails()) {
            $error      = true;
            $message    = $validator->errors()->first();

        } else {
            $pendaftaran->kegiatan_id           = $request->input('kegiatan_id');
            $pendaftaran->tanggal_pendaftaran   = $request->input('tanggal_pendaftaran');
            $pendaftaran->sekolah_id            = $request->input('sekolah_id');
            $pendaftaran->save();

            $error      = false;
            $message    = 'Success';
        }

        $response['error']      = $error;
        $response['message']    = $message;
        $response['status']     = true;

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!$this->checkRole(['superadministrator'])){
            $response['status'] = false;
            return json_encode($response);
        }

        $pendaftaran = $this->pendaftaran->findOrFail($id);

        if ($pendaftaran->delete()) {
            $response['status'] = true;
        } else {
            $response['status'] = false;
        }

        return json_encode($response);
    }

    protected function checkRole($role = array())
    {
        return Auth::user()->hasRole($role);
    }    
}
