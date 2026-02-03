<?php

namespace App\Models;

use App\Enums\UserRole;
use App\Traits\HasAuditFields;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class MstUser extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasUuids, SoftDeletes, HasAuditFields;

    protected $table = 'mst_user';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const DELETED_AT = 'deleted_at';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'email',
        'password',
        'full_name',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'is_deleted' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the tokos associated with this user.
     */
    public function tokos(): BelongsToMany
    {
        return $this->belongsToMany(MstToko::class, 'map_user_toko', 'user_id', 'toko_id');
    }

    /**
     * Get the transaksi records for this kasir.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TrnTransaksiToko::class, 'kasir_id', 'user_id');
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role->value,
        ];
    }
}
