<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->integer('stock_quantity');
        $table->string('image')->nullable();
        $table->unsignedBigInteger('category_id');
        $table->unsignedBigInteger('shop_id');
        $table->enum('status', ['actif', 'inactif'])->default('actif');
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
    });
}

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
