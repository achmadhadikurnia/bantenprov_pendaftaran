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
use App\User;

/* Etc */
use Validator;

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

    public function __construct(Pendaftaran $pendaftaran, Kegiatan $kegiatan, User $user, Sekolah $sekolah)
    {
        $this->pendaftaran      = $pendaftaran;
        $this->kegiatanModel    = $kegiatan;
        $this->user             = $user;
        $this->sekolah          = $sekolah;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (request()->has('sort')) {
            list($sortCol, $sortDir) = explode('|', request()->sort);

            $query = $this->pendaftaran->with('kegiatan')->with('user')->orderBy($sortCol, $sortDir);
        } else {
            $query = $this->pendaftaran->with('kegiatan')->with('user')->orderBy('id', 'asc');
        }

        if ($request->exists('filter')) {
            $query->where(function($q) use($request) {
                $value = "%{$request->filter}%";
                $q->where('tanggal_pendaftaran', 'like', $value)
                    ->orWhere('tanggal_pendaftaran', 'like', $value);
            });
        }

        $perPage = request()->has('per_page') ? (int) request()->per_page : null;
        $response = $query->with(['sekolah'])->paginate($perPage);

        return response()->json($response)
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = [];

        $kegiatan       = $this->kegiatanModel->all();
        $sekolahs       = $this->sekolah->all();
        $users_special  = $this->user->all();
        $users_standar  = $this->user->find(\Auth::User()->id);
        $current_user   = \Auth::User();

        foreach($sekolahs as $sekolah){
            array_set($sekolah, 'label', $sekolah->nama);
        }

        $role_check = \Auth::User()->hasRole(['superadministrator','administrator']);

        if($role_check){
            $response['user_special'] = true;
            foreach($users_special as $user){
                array_set($user, 'label', $user->name);
            }
            $response['user'] = $users_special;
        }else{
            $response['user_special'] = false;
            array_set($users_standar, 'label', $users_standar->name);
            $response['user'] = $users_standar;
        }

        array_set($current_user, 'label', $current_user->name);

        $response['current_user']   = $current_user;
        $response['kegiatan']       = $kegiatan;
        //$response['user']           = $users;
        $response['sekolah']        = $sekolahs;
        $response['error']          = false;
        $response['message']        = 'Success';
        $response['status']         = true;

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $pendaftaran        = $this->pendaftaran;
        $current_user_id    = $request->user_id;

        $validator = Validator::make($request->all(), [
            'kegiatan_id'           => 'required',
            'user_id'               => "required|exists:{$this->user->getTable()},id",
            'tanggal_pendaftaran'   => 'required',
            'sekolah_id'            => 'required',
        ]);

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
        $pendaftaran = $this->pendaftaran->findOrFail($id);

        $response['pendaftaran']    = $pendaftaran;
        $response['kegiatan']       = $pendaftaran->kegiatan;
        $response['sekolah']        = $pendaftaran->sekolah;
        $response['user']           = $pendaftaran->user;
        $response['status']         = true;

        return response()->json($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pendaftaran  $pendaftaran
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $pendaftaran = $this->pendaftaran->with(['kegiatan', 'sekolah', 'user' ])->findOrFail($id);

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
         $pendaftaran = $this->pendaftaran->with(['kegiatan','sekolah','user'])->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'user_id'               => "required|exists:{$this->user->getTable()},id",
                'kegiatan_id'           => 'required',
                'tanggal_pendaftaran'   => 'required',
                'sekolah_id'            => 'required',
            ]);

        if ($validator->fails()) {
            $error      = true;
            $message    = $validator->errors()->first();

        } else {
            $pendaftaran->user_id               = $request->input('user_id');
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
        $pendaftaran = $this->pendaftaran->findOrFail($id);

        if ($pendaftaran->delete()) {
            $response['status'] = true;
        } else {
            $response['status'] = false;
        }

        return json_encode($response);
    }
}
