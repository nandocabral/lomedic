<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulos extends Model
{
	// use SoftDeletes;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'ges_cat_modulos';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_modulo';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['nombre', 'descripcion', 'url', 'icono', 'modulo_padre', 'accion_menu', 'accion_barra', 'accion_tabla', 'modulo_seguro'];

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'nombre' => 'required|unique:ges_cat_modulos',
		'descripcion' => 'required',
		'url' => 'required',
		'modulo_padre' => 'required',
		'empresas' => 'required',
	];

	/**
	 * Las empresas que relacionan al modulo.
	 */
	public function empresas()
	{
		return $this->belongsToMany('App\Http\Models\Empresas', 'ges_det_modulo_empresa', 'fk_id_modulo', 'fk_id_empresa');
	}

}
