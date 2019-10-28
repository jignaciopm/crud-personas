<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /**
     * Get the persons for the position.
     */
    public function persons()
    {
        return $this->hasMany('App\Person');
    }
}
