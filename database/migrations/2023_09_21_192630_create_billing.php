<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * {name: jjj, surname: jjj, email: jjj, phoneNumber: jjj, city: jjj, country: jjj, shippingAddress: jjj, zipCode; jjj}
     */
    public function up(): void
    {
        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->string('name', "256");
            $table->string('surname', "256");
            $table->string('email', "256");
            $table->string('phoneNumber', "256");
            $table->string('city', "256");
            $table->string('country', "256");
            $table->string('shippingAddress', "256");
            $table->string('zipCode', "256");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billing');
    }
};
