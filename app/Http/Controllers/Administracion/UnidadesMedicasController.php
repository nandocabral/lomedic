<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\ControllerBase;
use App\Http\Models\Administracion\UnidadesMedicas;

class UnidadesMedicasController extends ControllerBase
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UnidadesMedicas $entity)
    {
        $this->entity = $entity;
    }
}