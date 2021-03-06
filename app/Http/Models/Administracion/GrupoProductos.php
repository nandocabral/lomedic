<?php

namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;

class GrupoProductos extends ModelBase
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'gen_cat_grupo_productos';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_grupo';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['grupo','sales','especificaciones','activo'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'grupo' => 'required|max:100|regex:/^[a-zA-Z\s]+/'
	];

	protected $unique = ['grupo'];

	/**
	 * Los atributos que seran visibles en index-datable
	 * @var null|array
	 */
	protected $fields = [
		'grupo' => 'Grupo',
		'activo_span' => 'Estatus',
	];
}