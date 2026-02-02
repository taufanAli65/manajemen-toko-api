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
        Schema::create('mst_toko', function (Blueprint $table) {
            $table->uuid('toko_id')->primary();
            $table->string('name');
            $table->text('address');
            $table->enum('jenis_toko', ['pusat', 'cabang', 'retail']);
            
            // Standard fields - Not nullable
            $table->boolean('is_deleted')->default(false);
            $table->string('created_by'); // soft reference
            $table->timestamp('created_at')->useCurrent();
            
            // Standard fields - Nullable
            $table->uuid('deleted_by')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_toko');
    }
};
