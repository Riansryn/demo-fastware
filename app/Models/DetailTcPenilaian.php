<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTcPenilaian extends Model
{
    use HasFactory;

    protected $table = 'detail_penilaian_tcs';

    protected $fillable = [
        'id_job_position',
        'keterangan_detail',
        'modified_at',
    ];

    public function tcPenilaian()
    {
        return $this->belongsTo(TrsPenilaianTc::class, 'id_job_position');
    }

}
