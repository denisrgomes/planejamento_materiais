<?php

use Faker\Generator as Faker;
use App\Material;

$factory->define(App\Demanda::class, function (Faker $faker) {

	$material =Material::all()->random();
    return [
        'codigo_material' => $material->codigo_material,
        'descricao_material' =>$material->descricao_material, // secret
        'quantidade_material' => $faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 1000), //48.8932
        'centro_logistico' => $faker->randomElement($array = array ('F1B1','F1B2','F1B3')),
        'regional' => $faker->randomElement($array = array ('Norte','Sul','Leste')),
        'data_programacao'=> $faker->dateTimeInInterval($startDate = 'now',  $endDate = '+ 30 days',$interval = '+ 7 days', $timezone = null)
    ];
});
