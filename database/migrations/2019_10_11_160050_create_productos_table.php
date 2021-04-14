<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('empresa_id'); // donde se vende
            $table->unsignedInteger('destino_empresa_id');
            $table->text('nombre');
            $table->string('nombre_interno')->nullable();
            $table->unsignedInteger('clase_id');
            $table->integer('quilates')->nullable();
            $table->text('caracteristicas')->nullable();
            $table->decimal('precio_coste', 10, 2)->default(0);
            $table->decimal('peso_gr', 10, 2)->default(0);
            $table->decimal('precio_venta', 10, 2)->default(0);
            $table->integer('stock')->default(1);
            $table->unsignedInteger('compra_id')->nullable()->index();
            $table->string('ref_pol',20)->nullable();
            $table->unsignedInteger('estado_id')->default(1);
            $table->unsignedInteger('etiqueta_id')->default(1);
            $table->string('referencia',20)->nullable()->index();
            $table->unsignedInteger('iva_id')->default(1);
            $table->boolean('online')->default(false);
            $table->string('univen',1)->default('G'); // Unidades - Gramos
            $table->unsignedInteger('cliente_id')->nullable(); // solo a efectos de importar para ubicar, se borrará el campo
            $table->unsignedInteger('garantia_id')->nullable();
            $table->smallInteger('meses_garantia')->nullable();
            $table->date('fecha_ultima_revision')->nullable();
            $table->unsignedInteger('almacen_id')->nullable();
            $table->text('notas')->nullable();
            $table->string('username',30)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('productos');
    }
}
