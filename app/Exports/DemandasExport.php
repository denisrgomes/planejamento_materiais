<?php

namespace App\Exports;

use App\Demanda;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DemandasExport implements FromCollection, WithHeadings, WithMapping
{

	/**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($result, $headings = array())
    {
        $this->result = $result;
        $this->headings = $headings;
    }


    public function collection()
	{
	  
	    return $this->result;


	}
/*
    public function query()
    {
        return Demanda::query()->get($this->fields);
    }
*/
    public function headings(): array
    {
        return $this->headings;
    }

     /**
    * @var Invoice $invoice
    */
    public function map($demanda): array
    {
       
        return [
        	$demanda->codigo_material,
        	$demanda->descricao_material,
        	$demanda->quantidade_material,
        	$demanda->centro_logistico,
        	$demanda->regional,
            Date::dateTimeToExcel($demanda->data_programacao)
        ];
    }
}
