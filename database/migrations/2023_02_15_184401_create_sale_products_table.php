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
        Schema::create('sale_products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references("id")->on("stores")->onDelete("cascade");
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->foreign('sale_id')->references("id")->on("sales")->onDelete("cascade");
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references("id")->on("products");
            $table->decimal('sellingPrice', 8, 2)->unsigned();
            $table->decimal('tax', 5, 2)->nullabel()->default(0);
            $table->decimal('costPrice', 8, 2)->unsigned();
            $table->integer('discount')->unsigned()->default(0);
            $table->integer('quantity')->unsigned();
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
        Schema::dropIfExists('sale_products');
    }
};
