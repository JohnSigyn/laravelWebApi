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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->string('proprietor');
            $table->string('address');
            $table->string('phone');
            $table->string('invoice');
            $table->string('gst')->nullable();
            $table->string('pan')->nullable();
            $table->decimal('loyalty_value', 8, 2)->nullable();
            $table->decimal('loyalty_given', 8, 2)->nullable();
            $table->boolean('gst_applicable');
            $table->integer('low_stock');
            $table->enum("license_type",["fully_paid","partially_paid"])->default("partially_paid");
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
        Schema::dropIfExists('stores');
    }
};
