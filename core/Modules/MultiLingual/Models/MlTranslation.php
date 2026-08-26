<?php

namespace Modules\MultiLingual\Models;

use Illuminate\Database\Eloquent\Model;

class MlTranslation extends Model
{
    protected $table = 'ml_translations';

    protected $fillable = [
        'tenant_id',
        'model_type',
        'model_id',
        'locale',
        'field',
        'value',
    ];
}
