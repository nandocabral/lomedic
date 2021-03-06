<?php

namespace App\Http\Models\Inventarios;

use App\Http\Models\Administracion\Sucursales;
use App\Http\Models\ModelBase;

class Almacenes extends ModelBase
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'inv_cat_almacenes';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_almacen';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['fk_id_sucursal', 'almacen', 'fk_id_tipo_almacen'];

	/**
	 * Los atributos que seran visibles en index-datable
	 * @var array
	 */
	protected $fields = [
		'almacen' => 'Almacen',
		'sucursal.sucursal' => 'Sucursal',
		'ubicaciones_total' => 'Ubicaciones',
		'activo_span' => 'Estatus'
	];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'fk_id_sucursal' => 'required',
		'almacen' => ['required','max:50'],
		'fk_id_tipo_almacen' => 'required',
	];

	/**
	 * Nice names to validator
	 * @var array
	 */
	public $niceNames = [
		'fk_id_sucursal' => 'sucursal',
		'fk_id_tipo_almacen' => 'tipo de almacen',
	];

	/**
	 * Obtenemos total de ubicaciones
	 * @return string
	 */
	public function getUbicacionesTotalAttribute() {
		return $this->ubicaciones->where('eliminar', 0)->count();
	}

	/**
	 * Obtenemos texto "Almacen - Sucursal" concatenado
	 * @return string
	 */
	public function getAlmacenSucursalConcatAttribute()
	{
		return $this->almacen . ' - ' . $this->sucursal->sucursal;
	}

	/**
	 * Obtenemos ubicaciones relacionadas a almacen
	 * @return @hasMany
	 */
	public function ubicaciones() {
		return $this->hasMany(Ubicaciones::class, 'fk_id_almacen', 'id_almacen');
	}

	/**
	 * Obtenemos sucursal relacionada a almacen
	 * @return @belongsTo
	 */
	public function sucursal() {
		return $this->belongsTo(Sucursales::class, 'fk_id_sucursal', 'id_sucursal');
	}

}
