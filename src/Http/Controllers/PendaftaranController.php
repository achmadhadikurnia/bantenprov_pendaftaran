<?php

namespace Bantenprov\Pendaftaran\Http\Controllers;

/* Require */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bantenprov\BudgetAbsorption\Facades\PendaftaranFacade;


/* Models */
use Bantenprov\Pendaftaran\Models\Bantenprov\Pendaftaran\Pendaftaran;
use Bantenprov\Kegiatan\Models\Bantenprov\Kegiatan\Kegiatan;
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

    public function __construct(Pendaftaran $pendaftaran, Kegiatan $kegiatan, User $user)
    {
        $this->pendaftaran      = $pendaftaran;
        $this->kegiatanModel    = $kegiatan;
        $this->user             = $user;
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
                $q->where('label', 'like', $value)
                    ->orWhere('description', 'like', $value);
            });
        }

        $perPage = request()->has('per_page') ? (int) request()->per_page : null;
        $response = $query->paginate($perPage);

        // foreach($response as $kegiatan){
        //     array_set($response->data, 'kegiatan', $kegiatan->kegiatan->label);
        // }

        // foreach($response as $user){
        //     array_set($response->data, 'user', $user->user->name);
        // }

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
        $kegiatan = $this->kegiatanModel->all();
        $users_special = $this->user->all();
        $users_standar = $this->user->find(\Auth::User()->id);
        
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

        $response['kegiatan'] = $kegiatan;        
        $response['status'] = true;

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
        $pendaftaran = $this->pendaftaran;

        $validator = Validator::make($request->all(), [
            'kegiatan_id' => 'required',
            'user_id' => 'required',
            'label' => 'required|max:16|unique:pendaftarans,label',
            'description' => 'max:255',
        ]);

        if($validator->fails()){
            $check = $pendaftaran->where('label',$request->label)->whereNull('deleted_at')->count();

            if ($check > 0) {
                $response['message'] = 'Failed, label ' . $request->label . ' already exists';
            } else {
                $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
                $pendaftaran->user_id = $request->input('user_id');
                $pendaftaran->label = $request->input('label');
                $pendaftaran->description = $request->input('description');                
                $pendaftaran->save();

                $response['message'] = 'success';
            }
        } else {
            $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
            $pendaftaran->user_id = $request->input('user_id');
            $pendaftaran->label = $request->input('label');
            $pendaftaran->description = $request->input('description');            
            $pendaftaran->save();
            $response['message'] = 'success';
        }

        $response['status'] = true;

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

        $response['pendaftaran'] = $pendaftaran;
        $response['kegiatan'] = $pendaftaran->kegiatan;
        $response['user'] = $pendaftaran->user;
        $response['status'] = true;

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
        $pendaftaran = $this->pendaftaran->findOrFail($id);

        array_set($pendaftaran->user, 'label', $pendaftaran->user->name);

        $response['pendaftaran'] = $pendaftaran;
        $response['kegiatan'] = $pendaftaran->kegiatan;
        $response['user'] = $pendaftaran->user;
        $response['status'] = true;

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
        $pendaftaran = $this->pendaftaran->findOrFail($id);

        if ($request->input('old_label') == $request->input('label'))
        {
            $validator = Validator::make($request->all(), [
                'label' => 'required|max:16',
                'description' => 'max:255',
                'kegiatan_id' => 'required',
                'user_id' => 'required',
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'label' => 'required|max:16|unique:pendaftarans,label',
                'description' => 'max:255',
                'kegiatan_id' => 'required',
                'user_id' => 'required',
            ]);
        }

        if ($validator->fails()) {
            $check = $pendaftaran->where('label',$request->label)->whereNull('deleted_at')->count();

            if ($check > 0) {
                $response['message'] = 'Failed, label ' . $request->label . ' already exists';
            } else {
                $pendaftaran->label = $request->input('label');
                $pendaftaran->description = $request->input('description');
                $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
                $pendaftaran->user_id = $request->input('user_id');
                $pendaftaran->save();                

                $response['message'] = 'success';                
            }
        } else {
            $pendaftaran->label = $request->input('label');
            $pendaftaran->description = $request->input('description');
            $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
            $pendaftaran->user_id = $request->input('user_id');
            $pendaftaran->save();

            $response['message'] = 'success';
        }

        $response['status'] = true;

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
