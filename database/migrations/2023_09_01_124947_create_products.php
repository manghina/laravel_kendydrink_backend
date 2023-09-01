<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', "100");
            $table->decimal('price');
            $table->string('description', "200");
            $table->string('short_description', "200");
            $table->integer('category_id');
            $table->integer('sales');
            $table->string('stripe_id', "200");
            $table->string('card_id', "200");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
