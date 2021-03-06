<?php

namespace App\Http\Models\Inventarios;

use App\Http\Models\Inventarios\Productos;
use App\Http\Models\Administracion\Presentaciones;
use App\Http\Models\Administracion\Sales;
use App\Http\Models\ModelCompany;

class DetallePresentacionesSku extends ModelCompany
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'inv_det_sku_presentaciones';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_detalle';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'fk_id_presentaciones',
		'fk_id_sal'
	];

	/**
	 * Obtenemos upc relacionadas a detalle
	 * @return @belongsTo
	 */
	public function sku()
	{
		return $this->belongsTo(Productos::class, 'fk_id_sku');
	}
	public function presentacion(){
		return $this->belongsTo(Presentaciones::class, 'fk_id_presentaciones');
	}
	public function sal(){
		return $this->belongsTo(Sales::class, 'fk_id_sal');
	}
}
