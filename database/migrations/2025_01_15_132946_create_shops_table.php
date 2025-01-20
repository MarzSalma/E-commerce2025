<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('address');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->enum('status', ['actif', 'inactif']);
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
