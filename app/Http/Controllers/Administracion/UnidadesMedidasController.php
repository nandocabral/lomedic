<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\ControllerBase;
use App\Http\Models\Administracion\UnidadesMedidas;
use Illuminate\Http\Request;

class UnidadesMedidasController extends ControllerBase
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
	public function __construct()
	{
	    $this->entity = new UnidadesMedidas;
	}

}