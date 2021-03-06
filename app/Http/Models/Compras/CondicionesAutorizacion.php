<?php

namespace App\Http\Models\Compras;

// use App\Http\Models\Administracion\EstatusDocumentos;
use App\Http\Models\Administracion\TiposDocumentos;
use App\Http\Models\Administracion\Usuarios;
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
    protected $fillable = ['nombre','campo','rango_de','rango_hasta','consulta_sql','fk_id_tipo_documento','activo'];

    public $niceNames =[
    ];

    protected $dataColumns = [
        'campo',
    ];
    /**
     * Los atributos que seran visibles en index-datable
     * @var array
     */
    // protected $fields = [
    // ];

    /**
     * The validation rules
     * @var array
     */
    public $rules = [
        'nombre' => 'required'
    ];

    public function tipodocumento()
    {
        return $this->hasOne(TiposDocumentos::class,'id_tipo_documento','fk_id_tipo_documento');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuarios::class,'com_det_usuarios_autorizados','fk_id_condicion','fk_id_usuario');
    }

    public function autorizaciones()
    {
        return $this->belongsTo(Autorizaciones::class,'fk_id_condicion','id_condicion');
    }
}
