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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('invoice_no')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references("id")->on("customers");
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreign('store_id')->references("id")->on("stores")->onDelete("cascade");
            $table->integer('loyalty_points')->default(0);
            $table->enum("sales_mode",["online","walk_in"])->default("walk_in");
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
        Schema::dropIfExists('sales');
    }
};
