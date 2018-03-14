<?php

namespace Bantenprov\Pendaftaran\Http\Controllers;

/* Require */
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Bantenprov\Pendaftaran\Facades\PendaftaranFacade;

/* Models */
use Bantenprov\Pendaftaran\Models\Bantenprov\Pendaftaran\Pendaftaran;
use Bantenprov\Kegiatan\Models\Bantenprov\Kegiatan\Kegiatan;

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

    public function __construct(Pendaftaran $pendaftaran, Kegiatan $kegiatan)
    {
        $this->pendaftaran = $pendaftaran;
        $this->kegiatanModel = $kegiatan;
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

            $query = $this->pendaftaran->orderBy($sortCol, $sortDir);
        } else {
            $query = $this->pendaftaran->orderBy('id', 'asc');
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
       
        foreach($response as $kegiatan){   
            array_set($response->data, 'kegiatan_id', $kegiatan->kegiatan->label);           
        }
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
            'label' => 'required|max:16|unique:pendaftarans,label',
            'description' => 'max:255',
        ]);

        if($validator->fails()){
            $check = $pendaftaran->where('label',$request->label)->whereNull('deleted_at')->count();

            if ($check > 0) {
                $response['message'] = 'Failed, label ' . $request->label . ' already exists';
            } else {
                $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
                $pendaftaran->label = $request->input('label');
                $pendaftaran->description = $request->input('description');
                $pendaftaran->save();

                $response['message'] = 'success';
            }
        } else {
            $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
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

        $response['pendaftaran'] = $pendaftaran;
        $response['kegiatan'] = $pendaftaran->kegiatan;
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
                'kegiatan_id' => 'required'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'label' => 'required|max:16|unique:pendaftarans,label',
                'description' => 'max:255',
                'kegiatan_id' => 'required'
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
                $pendaftaran->save();

                $response['message'] = 'success';
            }
        } else {
            $pendaftaran->label = $request->input('label');
            $pendaftaran->description = $request->input('description');
            $pendaftaran->kegiatan_id = $request->input('kegiatan_id');
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
