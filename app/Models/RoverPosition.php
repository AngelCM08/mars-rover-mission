<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoverPosition extends Model
{
    use HasFactory;
    
    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'session_id',
        'x',
        'y',
        'direction',
    ];
    
    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'x' => 'integer',
        'y' => 'integer',
    ];
}
