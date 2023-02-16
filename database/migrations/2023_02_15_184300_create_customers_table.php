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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name")->required();
            $table->date("dob")->nullable();
            $table->string("phone",10)->nullable();
            $table->string("address")->nullable();
            $table->enum("sex",["M","F"])->default("M");
            $table->boolean("member");
            $table->unsignedBigInteger("store_id");
            $table->foreign('store_id')->references("id")->on("stores")->onDelete(("cascade"));
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
        Schema::dropIfExists('customers');
    }
};
