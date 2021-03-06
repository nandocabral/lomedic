<?php
namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;

class MetodosValoracion extends ModelBase
{
	/**
	 * The table associated with the model.
	 * @var string
	 */
	protected $table = 'inv_cat_metodos_valoracion';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_metodo_valoracion';

	/**
	 * The attributes that are mass assignable.
	 * @var array
	 */
	protected $fillable = ['metodo_valoracion','activo'];

    protected $unique = ['metodo_valoracion'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'metodo_valoracion' => 'required|max:90|regex:/^[a-zA-Z\s]+/'
	];

	/**
	 * Los atributos que seran visibles en smart-datatable
	 * @var array
	 */
	protected $fields = [
		'metodo_valoracion' => 'Metodo Valoracion',
	    'activo_span' => 'Estatus'
	];
}