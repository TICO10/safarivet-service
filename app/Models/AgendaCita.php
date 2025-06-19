<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgendaCita extends Model
{
    public $timestamps = false;
    protected $table = 'agenda_citas';

    protected $fillable = [
        'name',
        'lastName',
        'email',
        'phone',
        'petName',
        'petClase',
        'date',
        'hour',
        'dateCite',
        'hourCite',
        'regestxx',
        'regfecxx',
        'reghorxx',
        'regusrxx',
        'regfecmx',
        'reghormx',
        'regusrmx',
        'regstamp',
    ];
}
