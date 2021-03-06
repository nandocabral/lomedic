<?php

namespace App\Http\Models\Administracion;

use App\Http\Models\ModelBase;
use App\Http\Models\Administracion\Usuarios;

class Correos extends ModelBase
{
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'adm_det_correos';

	/**
	 * The primary key of the table
	 * @var string
	 */
	protected $primaryKey = 'id_correo';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['correo', 'fk_id_empresa', 'fk_id_usuario','activo'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'correo' => 'required|email|max:255',
	];

	protected $unique = ['correo'];

	/**
	 * Los atributos que seran visibles en index-datable
	 * @var array
	 */
	protected $fields = [
		'correo' => 'Correo',
		'empresa.nombre_comercial' => 'Empresa',
		'usuario.nombre_corto' => 'Usuario',
		'activo_span' => 'Estatus',
	];

	/**
	 * Obtenemos usuario relacionado
	 * @return Usuario
	 */
	public function usuario()
	{
		return $this->belongsTo(Usuarios::class, 'fk_id_usuario','id_usuario');
	}

	/**
	 * Obtenemos empresa relacionada
	 * @return Empresa
	 */
	public function empresa()
	{
		return $this->belongsTo(Empresas::class, 'fk_id_empresa', 'id_empresa');
	}


}
