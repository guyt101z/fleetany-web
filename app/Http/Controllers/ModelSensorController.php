<?php

namespace App\Http\Controllers;

use Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Repositories\ModelSensorRepositoryEloquent;
use App\Entities\ModelSensor;
use Log;
use Input;
use Lang;
use Session;
use Redirect;
use Response;
use Illuminate\Support\Facades\View;
use Prettus\Validator\Exceptions\ValidatorException;

class ModelSensorController extends Controller
{
    
    protected $repository;
    
    public function __construct(ModelSensorRepositoryEloquent $repository) 
    {
        $this->middleware('auth');
        $this->repository = $repository;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $modelsensors = $this->repository->all();
        if(Request::isJson()) {
            return $modelsensors;
        }

        return View::make("modelsensor.index", compact('modelsensors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $modelsensor = new ModelSensor();
        return view("modelsensor.edit", compact('modelsensor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $this->repository->validator();
            $this->repository->create( Input::all() );
            Session::flash('message', Lang::get('general.succefullcreate', 
                  ['table'=> Lang::get('general.ModelSensor')]));
            return Redirect::to('modelsensor');
        } catch (ValidatorException $e) {
            return Redirect::back()->withInput()
                   ->with('errors',  $e->getMessageBag());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $modelsensor= $this->repository->find($id);
        return View::make("modelsensor.show", compact('modelsensor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $modelsensor = $this->repository->find($id);
        return View::make("modelsensor.edit", compact('modelsensor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $this->repository->validator();
            $this->repository->update(Input::all(), $id);
            Session::flash('message', Lang::get('general.succefullupdate', 
                       ['table'=> Lang::get('general.ModelSensor')]));
            return Redirect::to('modelsensor');
         }
         catch (ValidatorException $e) {
            return Redirect::back()->withInput()
                    ->with('errors',  $e->getMessageBag());
         }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        Log::info('Delete field: '.$id);
        if($this->repository->find($id)) {
            $this->repository->delete($id);
            Session::flash('message', Lang::get("general.deletedregister"));
         }

        return Redirect::to('modelsensor');
    }
}