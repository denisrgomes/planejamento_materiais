<?php

namespace App\Http\Controllers\Materiais;

//https://www.youtube.com/watch?v=npAr29_ArBU
use App\Http\Controllers\Controller;
use App\Material;
use Illuminate\Http\Request;
use Datatables;
use Validator;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('materiais.cadastro_materiais.index');
    }

    public function getData(Request $request){
        
        $dataTable = Datatables::of(Material::query())
        ->editColumn('created_at', function($material){
            return $material->created_at->diffForHumans();
        })
        ->editColumn('updated_at', function($material){
            return $material->updated_at->diffForHumans();
        })
        ->addColumn('action', function($material){
            return '<a href="#" class="edit" id="'.$material->id.'">Editar</a>'. ' / '.
            '<a href="#" class="delete" id="'.$material->id.'"></i>Excluir</a>';
        });
        
        
        if($request->draw == 1) 
        {
            $codigo_material = Material::query()->distinct()->get(['codigo_material'])->pluck('codigo_material');
            $descricao_material = Material::query()->distinct()->get(['descricao_material'])->pluck('descricao_material');
            $tipo = Material::query()->distinct()->get(['tipo'])->pluck('tipo');

            $dataTable->with([
                'allCodigoMaterial' => $codigo_material,
                'allDescricaoMaterial' => $descricao_material,
                'allTipo' => $tipo,
            ]);
        }
        
        return $dataTable->make(true);
        
    }
   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $validation;
        if($request->get('button_action') == 'insert'){
             $validation = Validator::make($request->all(), [
            'codigo_material' => 'required|unique:materials',
            'descricao_material' => 'required',
            ]);

        }

        if($request->get('button_action') == 'update'){
             $validation = Validator::make($request->all(), [
            'codigo_material' => 'required|unique:materials,codigo_material,' . $request->get('material_id'),
            'descricao_material' => 'required',
            ]);

        }
        
        $error_array = array();
        $success_output ="";
        if($validation->fails())
        {
            foreach ($validation->messages()->getMessages() as $messages) 
            {
                $error_array[] = $messages;
            }
        }
        else
        {
            if($request->get('button_action') == 'insert')
            {
                $material = Material::create($request->only('codigo_material', 'descricao_material' ,'tipo'));
                $success_output = '<div class="alert alert-success">Material salvo</div>';
            }
            if($request->get('button_action') == 'update')
            {
                
                $material = Material::findOrFail($request->get('material_id'));
                $input = $request->only(['codigo_material', 'descricao_material' ,'tipo']);
                $material->fill($input)->save();
                $success_output = '<div class="alert alert-success">Material editado</div>';
            }

        }
        $output = array(
            'error' => $error_array, 
            'success' => $success_output
        );

        echo json_encode($output);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->input('id');
        $material = Material::findOrFail($id);
        $output = array(
            'codigo_material' => $material->codigo_material, 
            'descricao_material' => $material->descricao_material,
            'tipo' => $material->tipo
        );

        echo json_encode($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Material  $material
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //
        $id = $request->input('material_delete_id');
        $material = Material::findOrFail($id);
        

        if($material->delete()){
            $success_output = '<div class="alert alert-success">Material deletado</div>';
        }
        
        $output = array(
            'success' => $success_output
        );

        echo json_encode($output);
    }
}
