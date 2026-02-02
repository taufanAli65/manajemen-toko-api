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
        Schema::create('map_user_toko', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('toko_id');
            
            // Foreign keys
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('mst_user')
                  ->onDelete('cascade');
                  
            $table->foreign('toko_id')
                  ->references('toko_id')
                  ->on('mst_toko')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_user_toko');
    }
};
