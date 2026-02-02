<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapUserToko extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'map_user_toko';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'toko_id',
    ];

    /**
     * Get the user that owns this mapping.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(MstUser::class, 'user_id', 'user_id');
    }

    /**
     * Get the toko that owns this mapping.
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(MstToko::class, 'toko_id', 'toko_id');
    }
}
