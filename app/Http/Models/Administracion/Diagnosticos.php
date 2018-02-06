<?php

namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;

class Diagnosticos extends ModelBase
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'maestro.gen_cat_diagnosticos';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_diagnostico';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['clave_diagnostico', 'diagnostico', 'medicamento_sugerido','activo'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'clave_diagnostico' => 'required',
		'diagnostico' => 'required',
		'medicamento_sugerido' => 'required',
	];

	/**
	 * Los atributos que seran visibles en smart-datatable
	 * @var array
	 */
	protected $fields = [
		'clave_diagnostico' => 'Clave',
		'diagnostico' => 'Diagnostico',
		'medicamento_sugerido' => 'Medicamento Sugerido'
	];

}
