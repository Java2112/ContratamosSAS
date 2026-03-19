<?php

namespace App\Domains\Commercial\Models;

use App\Models\User;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'document_type', // NIT, CC, CE
        'document_number',
        'business_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'industry_sector',
        'status', // active, inactive, pending
    ];

    /**
     * El usuario principal asociado al cliente para que inicie sesión en el Portal.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Los contratos comerciales asociados a este cliente.
     */
    public function contracts()
    {
        return $this->hasMany(ContractService::class);
    }
}
