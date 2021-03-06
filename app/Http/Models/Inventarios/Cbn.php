<?php

namespace App\Http\Models\Inventarios;

use App\Http\Models\ModelBase;

class Cbn extends ModelBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_cat_cbn';
    
    /**
     * The primary key of the table
     * @var string
     */
    protected $primaryKey = 'id_cbn';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['clave_cbn','descripcion','precio_comision','precio_causes','precio_imss','vigencia','activo'];

	/**
	 * The validation rules
	 * @var array
	 */
	public $rules = [
		'clave_cbn' => ['required','max:90'],
		'descripcion' => ['max:150'],
        'vigencia' => 'required'
	];

    /**
     * Los atributos que seran visibles en index-datable
     * @var array
     */
    protected $fields = [
        'clave_cbn' => 'Clave',
        'descripcion' => 'Descripcion',
        'precio_comision' => 'Precio Comision',
        'precio_causes' => 'Precio Causes',
        'precio_imss' => 'Precio Imss',
        'activo_span'=> 'Estatus',
    ];

    public function skus()
    {
        return $this->hasMany(Productos::class,'fk_id_cbn','id_cbn');
    }
}
