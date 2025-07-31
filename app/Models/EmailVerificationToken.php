<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class EmailVerificationToken extends Model
{
    protected $fillable = ['email', 'token', 'expires_at'];
    
    protected $dates = ['expires_at'];

    public static function createToken($email)
    {
        // Delete any existing tokens for this email
        self::where('email', $email)->delete();
        
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        
        return self::create([
            'email' => $email,
            'token' => $token, // Store the original token
            'expires_at' => Carbon::now()->addHours(24), // 24-hour expiry
        ]);
    }

    public static function validateToken($email, $token)
    {
        $record = self::where('email', $email)
                     ->where('token', $token) // Compare with original token
                     ->where('expires_at', '>', Carbon::now())
                     ->first();
        
        if ($record) {
            $record->delete(); // Delete token after use
            return true;
        }
        
        return false;
    }
}