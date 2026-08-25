<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SmsConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'sender_id',
        'api_url',
        'api_key',
        'username',
        'password',
        'enabled',
        'last_known_balance',
        'balance_checked_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'last_known_balance' => 'decimal:2',
        'balance_checked_at' => 'datetime',
    ];

    protected $hidden = ['api_key', 'password'];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? Crypt::encryptString($value) : null;
    }

    public function getPasswordAttribute($value)
    {
        if (!$value) {
            return null;
        }
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            return null;
        }
    }
}
