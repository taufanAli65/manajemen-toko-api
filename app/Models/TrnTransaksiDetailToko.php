<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrnTransaksiDetailToko extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'trn_transaksi_detail_toko';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'transaksi_id',
        'product_id',
        'qty',
        'price_at_moment',
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
            'qty' => 'integer',
            'price_at_moment' => 'integer',
            'is_deleted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the transaksi that owns this detail.
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TrnTransaksiToko::class, 'transaksi_id', 'transaksi_id');
    }

    /**
     * Get the product for this detail.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(MstProduk::class, 'product_id', 'product_id');
    }
}
