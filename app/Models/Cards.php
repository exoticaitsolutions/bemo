<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cards extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(Items::class);
    }

        // this is a recommended way to declare event handlers
        public static function boot() {
            parent::boot();
    
            static::deleting(function($cards) { // before delete() method call this
                 $cards->items()->delete();
                 // do the rest of the cleanup...
            });
        }
}
