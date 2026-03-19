<?php

namespace App\Domains\Commercial\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractService extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'client_id',
        'contract_number',
        'start_date',
        'end_date',
        'administration_fee_percentage',
        'status', 
    ];

    /**
     * El cliente dueño de este contrato.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
