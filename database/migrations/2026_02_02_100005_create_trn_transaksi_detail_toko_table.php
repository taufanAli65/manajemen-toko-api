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
        Schema::create('trn_transaksi_detail_toko', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transaksi_id');
            $table->uuid('product_id');
            $table->integer('qty');
            $table->integer('price_at_moment');
            
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
            $table->foreign('transaksi_id')
                  ->references('transaksi_id')
                  ->on('trn_transaksi_toko')
                  ->onDelete('cascade');
                  
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('mst_produk')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trn_transaksi_detail_toko');
    }
};
