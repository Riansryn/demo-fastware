<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;

    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'npk',
        'username',
        'password',
        'pass',
        'email',
        'telp',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'pass',
    ];

    public function roles(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function handings(): HasMany
    {
        return $this->hasMany(Handling::class);
    }

    public function schedule_visits(): HasMany
    {
        return $this->hasMany(ScheduleVisit::class);
    }

    public function sumbang_saran()
    {
        return $this->hasMany(SumbangSaran::class, 'id_user');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(PenilaianSS::class);
    }

    // Relasi dengan KmPengajuan
    public function kmPengajuan()
    {
        return $this->hasMany(KmPengajuan::class, 'id_user');
    }

    // Relasi dengan KmTransaksi
    public function kmTransaksi()
    {
        return $this->hasMany(KmTransaksi::class, 'id_user');
    } 
}
