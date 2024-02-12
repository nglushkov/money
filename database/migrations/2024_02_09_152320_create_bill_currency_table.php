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
        Schema::create('bill_currency_initial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->foreignId('currency_id')->constrained('currencies')->onDelete('cascade');
            $table->decimal('amount', 8, 2)->unsigned();
            $table->unique(['bill_id', 'currency_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_currency_initial');
    }
};
