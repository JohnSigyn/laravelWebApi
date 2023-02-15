<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->unsignedBigInteger('store_id');
            $table->foreign('store_id')->references("id")->on("stores")->onDelete(("cascade"));
            $table->string('hsn')->nullable();
            $table->string('bar')->nullable();
            $table->string('unit')->nullable();
            $table->decimal('sellingPrice', 8, 2)->unsigned();
            $table->decimal('tax', 5, 2)->nullabel()->default(0);
            $table->decimal('costPrice', 8, 2)->unsigned();
            $table->integer('discount')->unsigned()->nullable();
            $table->integer('quantity')->unsigned();
            $table->boolean('discountInPercent');
            $table->softDeletes();

       
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
