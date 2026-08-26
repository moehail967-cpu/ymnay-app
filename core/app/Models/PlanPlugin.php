<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class PlanPlugin extends Model
{
    use CentralConnection;

    protected $fillable = ['plan_id', 'plugin_id'];
}
