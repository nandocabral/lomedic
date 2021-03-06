<?php

namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;
use Illuminate\Support\HtmlString;

class MotivosDesviaciones extends ModelBase
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'gen_cat_motivos_desviaciones';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_motivo_desviacion';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['descripcion', 'activo'];

	protected $unique = ['descripcion'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'descripcion'	=> 'required|max:100|regex:/^[a-zA-Z\s]+/',
	];

	/**
	 * Los atributos que seran visibles en index-datable
	 * @var array
	 */
	protected $fields = [
		'descripcion' => 'Descripción',
		'activo_span' => 'Estatus',
	];
}
