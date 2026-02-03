<?php

namespace App\Models;

use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrnTransaksiToko extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasAuditFields;

    protected $table = 'trn_transaksi_toko';
    protected $primaryKey = 'transaksi_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kasir_id',
        'toko_id',
        'total_harga',
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
            'total_harga' => 'integer',
            'is_deleted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the kasir (user) that processed this transaction.
     */
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(MstUser::class, 'kasir_id', 'user_id');
    }

    /**
     * Get the toko where this transaction occurred.
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(MstToko::class, 'toko_id', 'toko_id');
    }

    /**
     * Get the detail records for this transaction.
     */
    public function details(): HasMany
    {
        return $this->hasMany(TrnTransaksiDetailToko::class, 'transaksi_id', 'transaksi_id');
    }
}
