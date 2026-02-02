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
        Schema::create('trn_transaksi_toko', function (Blueprint $table) {
            $table->uuid('transaksi_id')->primary();
            $table->uuid('kasir_id');
            $table->uuid('toko_id');
            $table->integer('total_harga');
            
            // Standard fields - Not nullable
            $table->boolean('is_deleted')->default(false);
            $table->string('created_by'); // soft reference
            $table->timestamp('created_at')->useCurrent();
            
            // Standard fields - Nullable
            $table->uuid('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            // Foreign keys
            $table->foreign('kasir_id')
                  ->references('user_id')
                  ->on('mst_user')
                  ->onDelete('restrict');
                  
            $table->foreign('toko_id')
                  ->references('toko_id')
                  ->on('mst_toko')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trn_transaksi_toko');
    }
};
