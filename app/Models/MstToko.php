<?php

namespace App\Models;

use App\Enums\TokoType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstToko extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'mst_toko';
    protected $primaryKey = 'toko_id';
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
        'address',
        'jenis_toko',
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
            'jenis_toko' => TokoType::class,
            'is_deleted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the users associated with this toko.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(MstUser::class, 'map_user_toko', 'toko_id', 'user_id');
    }

    /**
     * Get the transaksi records for this toko.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TrnTransaksiToko::class, 'toko_id', 'toko_id');
    }
}
