<?php

namespace App\Http\Controllers\Materiais;


use App\Http\Controllers\Controller;
use App\Demanda;
use App\Material;
use DB;
use Illuminate\Http\Request;
use Datatables;
use Validator;
use App\Exports\DemandasExport;
use App\Imports\DemandasImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;


class DemandaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('materiais.demanda.index');
    }

    public function getData(Request $request){
        
        $dataTable = Datatables::of(Demanda::query())
        ->editColumn('quantidade_material', function($demanda){
            return number_format($demanda->quantidade_material,2,',','.');
        })
        ->editColumn('data_programacao', function($demanda){
            return $demanda->data_programacao->diffForHumans();
        })
        ->addColumn('action', function($demanda){
            return '<a href="#" class="edit" id="'.$demanda->id.'">Editar</a>'. ' / '.
            '<a href="#" class="delete" id="'.$demanda->id.'"></i>Excluir</a>';
        });
        
        
        if($request->draw == 1) 
        {
            $codigo_material = Demanda::query()->distinct()->get(['codigo_material'])->pluck('codigo_material');
            $descricao_material = Demanda::query()->distinct()->get(['descricao_material'])->pluck('descricao_material');
            $quantidade_material = Demanda::query()->distinct()->get(['quantidade_material'])->pluck('quantidade_material');
            $centro_logistico = Demanda::query()->distinct()->get(['centro_logistico'])->pluck('centro_logistico');
            $regional = Demanda::query()->distinct()->get(['regional'])->pluck('regional');

            $dataTable->with([
                'allCodigoMaterial' => $codigo_material,
                'allDescricaoMaterial' => $descricao_material,
                'allQuantidade' => $quantidade_material,
                'allCentroLogistico' => $centro_logistico,
                'allRegional' => $regional

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
        $validation = Validator::make($request->all(), [
            'codigo_material' => 'required',
            'descricao_material' => 'required',
            'quantidade_material' => 'required',
            'centro_logistico' => 'required',
            'data_programacao' => 'required'
            ]);
        
        
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
            $request['data_programacao'] = Carbon::createFromFormat('d/m/Y', $request['data_programacao']);
                
            if($request->get('button_action') == 'insert')
            {
                $material = Demanda::create($request->only('codigo_material', 'descricao_material',
                    'quantidade_material', 'centro_logistico', 'regional', 'data_programacao'));
                $success_output = '<div class="alert alert-success">Demanda salva</div>';
            }
            if($request->get('button_action') == 'update')
            {
                
                $demanda = Demanda::findOrFail($request->get('demanda_id'));
                $input = $request->only(['codigo_material', 'descricao_material',
                                    'quantidade_material', 'centro_logistico', 'regional', 'data_programacao']);
                $demanda->fill($input)->save();
                $success_output = '<div class="alert alert-success">Demanda editada</div>';
            }

        }
        $output = array(
            'error' => $error_array, 
            'success' => $success_output
        );

        echo json_encode($output);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('materiais.demanda.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFile(Request $request)
    {
        //
        $this->validate($request,[
            'file' => 'required| max:10240',
        ]);
 
        $fileName = time().'.'.request()->file->getClientOriginalExtension();
        

        Excel::import(new DemandasImport, request()->file('file'));
        request()->file->move(public_path('files'), $fileName);
        
        return response()->json(['success'=>'You have successfully upload file']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_acrescimo(Request $request)
    {
        //
        $request->validate([
            'file' => 'required| max:10240',
        ]);
 
        $fileName = time().'.'.request()->file->getClientOriginalExtension();

        request()->file->move(public_path('files'), $fileName);
 
        return response()->json(['success'=>'You have successfully upload '. 
            public_path('files'). "/". $fileName]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Demanda  $demanda
     * @return \Illuminate\Http\Response
     */
    public function show(Demanda $demanda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Demanda  $demanda
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        //
        $id = $request->input('id');
        $demanda = Demanda::findOrFail($id);
        $output = array(
            'codigo_material' => $demanda->codigo_material, 
            'descricao_material' => $demanda->descricao_material,
            'quantidade_material' => $demanda->quantidade_material,
            'centro_logistico' => $demanda->centro_logistico,
            'regional' => $demanda->regional,
            'data_programacao' => $demanda->data_programacao->format('d/m/Y')
        );
        echo json_encode($output);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Demanda  $demanda
     * @return \Illuminate\Http\Response
     */
    //public function update(Request $request, Demanda $demanda)
    public function update(Request $request)
    {
        //
        $demanda = Demanda::findOrFail($request->id);

        //dd($request);
        if($request->ajax())
        {
            $data = array(
                $request->column_name => $request->column_value
            );

            DB::table('demandas')
                ->where('id', $request->id)
                ->update($data);
            echo '<div class="alert alert-success">Data Updated</div>';
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Demanda  $demanda
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //

        $id = $request->input('demanda_delete_id');
        $demanda = Demanda::findOrFail($id);
        

        if($demanda->delete()){
            $success_output = '<div class="alert alert-success">Demanda deletado</div>';
        }
        
        $output = array(
            'success' => $success_output
        );

        echo json_encode($output);
    }   

    public function export()
    {   
        $fields = ['codigo_material','descricao_material', 'quantidade_material', 'centro_logistico', 'regional', 'data_programacao'];
        $headings =  [
            'Codigo Material',
            'Descrição Material',
            'Quantidade',
            'Centro',
            'Regional',
            'Programação'

        ];
        $result = Demanda::query()->get($fields);
        $export = new DemandasExport($result,$headings);
        //dd($export);
        //return $export->download('demandas.xlsx');
        return Excel::download($export, 'demandas.xlsx');
    }

    private function import(Request $request) 
    {
        return Excel::import(new DemandasImport, 
            request()->file('file'));
    }

    public function getCodigoMaterial(){
        $materiais =  Material::query()->distinct()->get(['codigo_material'])->pluck('codigo_material');
        
        echo json_encode($materiais);
    }

    public function getDescricaoMaterial(Request $request){
        $codigo_material = $request->get('codigo_material');
        $descricao_material =  Material::select('descricao_material')
        ->where('codigo_material', $request->get('codigo_material'))
        ->first();
        echo json_encode($descricao_material);
    }
}
