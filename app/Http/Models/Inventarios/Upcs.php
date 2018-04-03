<?php

namespace App\Http\Models\Inventarios;

use App\Http\Models\ModelBase;
use App\Http\Models\Administracion\PresentacionVenta;
use App\Http\Models\Administracion\Laboratorios;
use App\Http\Models\Administracion\Paises;
use App\Http\Models\Proyectos\ClaveClienteProductos;
use App\Http\Models\SociosNegocio\SociosNegocio;

class Upcs extends ModelBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_cat_upcs';

    /**
     * The primary key of the table
     * @var string
     */
    protected $primaryKey = 'id_upc';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'upc','registro_sanitario','nombre_comercial','marca','fk_id_presentacion_venta','fk_id_laboratorio','peso','longitud','ancho','altura','descontinuado','fk_id_pais_origen','activo','descripcion'
    ];

    /**
     * Los atributos que seran visibles en index-datable
     * @var array
     */
    protected $fields = [
        'upc' => 'UPC',
        'descripcion' => 'Descripcion',
        'nombre_comercial' => 'Nombre Comercial',
        'registro_sanitario' => 'Registro Sanitario',
        'marca' => 'Marca',
        'presentacion.presentacion_venta' => 'Presentacion',
        'laboratorio.laboratorio' => 'Laboratorio',
        'pais.pais' => 'Pais Origen',
    ];

    /**
     * The validation rules
     * @var array
     */
    public $rules = [
        'upc' => 'max:25|regex:/^([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-])+((\s*)+([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-]*)*)+$/',
        'descripcion' => 'max:255|regex:/^([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-])+((\s*)+([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-]*)*)+$/',
        'nombre_comercial' => 'max:255|regex:/^([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-])+((\s*)+([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-]*)*)+$/',
        'registro_sanitario' => 'max:255|regex:/^([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-])+((\s*)+([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-]*)*)+$/',
        'marca' => 'max:255|regex:/^([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-])+((\s*)+([0-9a-zA-ZÃ±Ã‘Ã¡Ã©Ã­Ã³ÃºÃ�Ã‰Ã�Ã“Ãš_-]*)*)+$/',
    ];

    public function presentacion()
    {
        return $this->belongsTo(PresentacionVenta::class,'fk_id_presentacion_venta','id_presentacion_venta');
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorios::class,'fk_id_laboratorio','id_laboratorio');
    }

    public function pais()
    {
        return $this->belongsTo(Paises::class,'fk_id_pais_origen','id_pais');
    }

    public function skus()
    {
        return $this->belongsToMany(Productos::class,getSchema(request()->company).'.inv_det_sku_upc','fk_id_upc','fk_id_sku','id_upc','id_sku')->withPivot('fk_id_upc','fk_id_sku','cantidad');
    }

    public function clavesclientes()
    {
        return $this->hasMany(ClaveClienteProductos::class,'fk_id_upc','id_upc');
    }

    public function clientes()
    {
        return $this->hasManyThrough(SociosNegocio::class,ClaveClienteProductos::class,'fk_id_upc','id_socio_negocio','id_upc','fk_id_cliente');
    }
}
