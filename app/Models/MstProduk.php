<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstProduk extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'mst_produk';
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'merk',
        'harga',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'is_deleted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the transaksi detail records for this product.
     */
    public function transaksiDetails(): HasMany
    {
        return $this->hasMany(TrnTransaksiDetailToko::class, 'product_id', 'product_id');
    }
}
