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
        Schema::create('sale_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_id')->nullable();
            $table->foreign('sales_id')->references("id")->on("sales")->onDelete("cascade");
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references("id")->on("stores")->onDelete("cascade");
            $table->decimal("paid_amount",9,2)->required();
            $table->enum("payment_mode",["cash","upi","gpay","cheque","card"])->default("cash");
            $table->timestamps();
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
        Schema::dropIfExists('sale_histories');
    }
};
