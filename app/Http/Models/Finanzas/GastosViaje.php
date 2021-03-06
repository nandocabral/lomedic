<?php

namespace App\Http\Models\Finanzas;

use App\Http\Models\ModelBase;
use App\Http\Models\Administracion\Sucursales;
use App\Http\Models\RecursosHumanos\Departamentos;
use App\Http\Models\RecursosHumanos\Empleados;
use App\Http\Models\RecursosHumanos\Puestos;

class GastosViaje extends ModelBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fin_opr_gastos';

    /**
     * The primary key of the table
     * @var string
     */
    protected $primaryKey = 'id_gastos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fecha',
        'fk_id_empleado',
        'destino',
        'periodo_inicio',
        'periodo_fin',
        'motivo_viaje',
        'total_dias',
        'total_detalles',
        'subtotal_detalles'
    ];

    /**
     * Los atributos que seran visibles en index-datable
     * @var array
     */
    protected $fields = [
        'id_gastos' => 'Folio',
        'fecha' => 'Fecha',
        'nombre_empleado' => 'Empleado',
        'destino' => 'Destino',
        'total_dias'=>'Total de días',
        'motivo_viaje' => 'Motivo del viaje'
    ];

    /**
     * The validation rules
     * @var array
     */
    public $rules = [
        'fecha' => 'required',
        'fk_id_empleado' => 'required',
        'fecha' => 'required',
        'destino' => 'required|max:50',
        'periodo_inicio' => 'required',
        'periodo_fin' => 'required',
        'motivo_viaje' => 'required|max:255'

    ];

    public function sucursales()
    {
        return $this->belongsTo(Sucursales::class,'fk_id_sucursal','id_sucursal');
    }

    public function departamentos()
    {
        return $this->belongsTo(Departamentos::class,'fk_id_departamento','id_departamento');
    }
    public function getNombreEmpleadoAttribute()
    {
        return $this->empleados->nombre.' '.$this->empleados->apellido_paterno.' '.$this->empleados->apellido_materno;
    }
    public function empleados()
    {
        return $this->belongsTo(Empleados::class,'fk_id_empleado','id_empleado');
    }

    public function puestos()
    {
        return $this->belongsTo(Puestos::class,'fk_id_puesto','id_puesto');
    }
    public function detalle()
    {
        return $this->hasMany(DetalleGastosRelacionViajes::class, 'fk_id_gastos','id_gastos');
    }
}
