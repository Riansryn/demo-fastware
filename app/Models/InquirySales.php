<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquirySales extends Model
{
    use HasFactory;

    protected $table = 'inquiry_sales'; // Pastikan nama tabel benar
    protected $fillable = [
        'id_type',
        'id_customer',
        'kode_inquiry',
        'jenis_inquiry',
        'supplier',
        'order_from',
        'create_by',
        'to_approve',
        'to_validate',
        'note',
        'attach_file',
        'status',
        'modified_by',
    ];

    public function details()
    {
        return $this->hasMany(DetailInquiry::class, 'id_inquiry');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }
}
