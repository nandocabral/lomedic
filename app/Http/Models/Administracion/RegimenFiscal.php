<?php

namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;

class RegimenFiscal extends ModelBase
{
	/**
	 * The table associated with the model.
	 * @var string
	 */
	protected $table = 'sat_cat_regimenes_fiscales';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_regimen_fiscal';

	/**
	 * The attributes that are mass assignable.
	 * @var array
	 */
	protected $fillable = ['regimen_fiscal','activo'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'regimen_fiscal' => 'required|max:255'
	];

    protected $unique = ['regimen_fiscal'];

	/**
	 * Los atributos que seran visibles en smart-datatable
	 * @var array
	 */
	protected $fields = [
		'regimen_fiscal' => 'Regimen Fiscal',
	    'activo_span' => 'Estatus'
	];
}