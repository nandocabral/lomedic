<?php

namespace App\Http\Models\Compras;

// use App\Http\Models\Administracion\EstatusDocumentos;
use App\Http\Models\ModelCompany;
use DB;

class CondicionesAutorizacion extends ModelCompany
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'com_cat_condiciones_autorizacion';

    /**
     * The primary key of the table
     * @var string
     */
    protected $primaryKey = 'id_condicion';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre','campo','rango_de','rango_hasta','consulta_sql','tipo_documento','activo','eliminar'];

    public $niceNames =[
    ];

    protected $dataColumns = [
        'campo'
    ];
    /**
     * Los atributos que seran visibles en index-datable
     * @var array
     */
    // protected $fields = [
    // ];

    // protected $eagerLoaders = ['autorizacionOrden'];

    /**
     * The validation rules
     * @var array
     */
    public $rules = [
        'nombre' => 'required'
    ];


}