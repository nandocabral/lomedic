<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\ControllerBase;
use App\Http\Models\Administracion\TiposDocumentos;

class TipoDocumentosController extends ControllerBase
{
	/**
	 * Create a new controller instance.
	 * @return void
	 */
	public function __construct(TiposDocumentos $entity)
	{
		$this->entity = $entity;
	}
}