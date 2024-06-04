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
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_coin_id')->constrained('coins');
            $table->foreignId('to_coin_id')->constrained('coins');
            $table->decimal('from_amount', 16, 8);
            $table->decimal('to_amount', 16, 8);
            $table->foreignId('wallet_id')->constrained('wallets');
            $table->decimal('fee', 16, 8)->default(0);
            $table->string('note', 255);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');
    }
};
