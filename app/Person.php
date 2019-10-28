<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    /**
     * Get the position that owns the person.
     */
    public function position()
    {
        return $this->belongsTo('App\Position');
    }
}
