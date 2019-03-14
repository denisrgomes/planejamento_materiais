<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Demanda;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DemandasImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        var_dump(new Carbon("1899-12-30 + ". round($row['data_programacao'] * 86400) . " seconds"));
        return new Demanda([
                'codigo_material' => $row['codigo_material'],
                'descricao_material' => $row['descricao_material'],
                'quantidade_material' => $row['quantidade'],
                'centro_logistico' => $row['centro'],
                'regional' => $row['regional'],
                'data_programacao' => new Carbon("1899-12-30 + ". round($row['data_programacao'] * 86400) . " seconds"),
        ]);
    }
}
