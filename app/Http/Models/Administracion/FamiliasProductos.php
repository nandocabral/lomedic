<?php

namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;
use App\Http\Models\Administracion\TiposProductos;

class FamiliasProductos extends ModelBase
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'gen_cat_familias_productos';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_familia';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['descripcion', 'tipo_presentacion','nomenclatura','fk_id_tipo_producto','activo'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'descripcion' => 'required|max:100',
//		'tipo_presentacion' => 'required',
		'nomenclatura' => 'required|max:3'
//		'fk_id_tipo_producto' => 'required',
	];

	protected $unique = ['descripcion','nomenclatura'];

	/**
	 * Los atributos que seran visibles en index-datable
	 * @var null|array
	 */
	protected $fields = [
		'descripcion' => 'Familia',
		'tipo_presentacion' => 'Presentación',
		'tipo_producto.tipo_producto' => 'Tipo',
		'nomenclatura' => 'Nomenclatura',
		'activo_span' => 'Estatus',
	];

    // public function getTipoProductoDescripcionAttribute()
    // {
    //     return $this->tipo_producto->nomenclatura;
    // }

	public function usuario()
	{
		return $this->hasOne('app\Http\Models\Administracion\Usuarios');
	}

	public function empresa()
	{
		return $this->hasOne('app\Http\Models\Administracion\Empresas');
	}

	public function tipo_producto()
    {
        return $this->belongsTo('App\Http\Models\Administracion\TiposProductos','fk_id_tipo_producto','id_tipo');
    }
}
