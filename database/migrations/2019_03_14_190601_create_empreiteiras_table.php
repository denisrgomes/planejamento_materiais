<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpreiteirasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empreiteiras', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cnpj');
            $table->string('nome_fantasia');
            $table->string('razao_social');
            $table->integer('almoxarifado_id')->unsigned();

            $table->foreign('almoxarifado_id')
                ->references('id')->on('almoxarifados')
                ->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('empreiteiras');
    }
}
