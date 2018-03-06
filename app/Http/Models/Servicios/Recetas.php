<?php

namespace App\Http\Models\Servicios;

use App\Http\Models\Administracion\Dependencias;
use App\Http\Models\Administracion\Parentescos;
use App\Http\Models\ModelCompany;
use App\Http\Models\Administracion\Afiliaciones;
use App\Http\Models\Administracion\Diagnosticos;
use App\Http\Models\Administracion\Sucursales;
use App\Http\Models\Administracion\Medicos;
use App\Http\Models\Administracion\Programas;
use App\Http\Models\Proyectos\Proyectos;
use DB;

class Recetas extends ModelCompany
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rec_opr_recetas';

    /**
     * The primary key of the table
     * @var string
     */
    protected $primaryKey = 'id_receta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'folio',
        'fk_id_sucursal',
        'fecha',
        'fk_id_afiliacion',
        'fk_id_dependiente',
        'fk_id_medico',
        'fk_id_diagnostico',
        'fk_id_programa',
        'fk_id_estatus_receta',
        'fk_id_area',
        'nombre_paciente_no_afiliado',
        'observaciones',
        'fecha_modificacion',
        'peso',
        'altura',
        'presion_sistolica',
        'presion_diastolica',
        'fk_id_proyecto',
        'fk_id_parentesco',
        'fk_id_afiliado',
    ];

    /**
     * Los atributos que seran visibles en index-datable
     * @var array
     */

    public $niceNames =[
        'fk_id_cliente'=>'cliente',
        'fecha_contrato' =>'fecha de contrato',
        'fecha_inicio_contrato' => 'fecha de inicio de contrato',
        'fecha_fin_contrato' => 'fecha de fin de contrato',
        'numero_contrato' => 'número de contrato',
        'numero_proyecto' => 'número de proyecto',
        'monto_adjudicado' => 'monto adjudicado',
        'fk_id_clasificacion_proyecto' => 'clasificación proyecto',
        'representante_legal' => 'representante legal',
        'numero_fianza' => 'número de fianza',
        'num_evento' => 'número de evento',
        'fk_id_tipo_evento' => 'tipo evento',
        'fk_id_dependencia' => 'dependencia',
        'fk_id_subdependencia' => 'subdependencia',
        'fk_id_sucursal' => 'sucursal',
        'fk_id_caracter_evento' => 'caracter evento',
        'fk_id_forma_adjudicacion' => 'forma_adjudicacion',
        'fk_id_modalidad_entrega' => 'modalidad_entrega'
    ];

    protected $fields = [
        'id_receta'=>'#',
        'folio' => 'Folio',
        'unidad_medica'=>'Unidad medica',
        'tipo_servicio' => 'Tipo de servicio',
        'numero_afiliado' => 'N. de afiliacion',
        'nombre_completo_paciente' => 'Paciente',
        'fecha_formated' => 'Fecha Captura',
        'estatus_formated' => 'Estatus de la receta'
    ];

    public function getNombreCompletoMedicoAttribute()
    {
        return $this->medico->nombre.' '.$this->medico->paterno.' '.$this->medico->materno;
    }

    public function getNombreCompletoPacienteAttribute()
    {
        if($this->fk_id_afiliado != '' && $this->fk_id_afiliado != null){
            return $this->afiliacion->paterno.' '.$this->afiliacion->materno.' '.$this->afiliacion->nombre;
        }else{
            return $this->nombre_paciente_no_afiliado;
        }
    }

    public function getNumeroAfiliadoAttribute()
    {
        return $this->afiliacion['id_afiliacion'];
    }

    public function getTipoServicioAttribute(){
        if($this->fk_id_afiliado != '' || $this->fk_id_afiliado != null){
            return 'Afiliado';
        }else{
            return 'Externo';
        }
    }


    public function getUnidadMedicaAttribute(){
        return $this->sucursal->sucursal;
    }

    public function getFechaFormatedAttribute(){
        return date("d-m-Y",strtotime($this->fecha));
    }

    public function getEstatusFormatedAttribute(){

        return $this->estatus->estatus_receta;
    }

    public function afiliacion()
    {
        return $this->belongsTo(Afiliaciones::class,'fk_id_afiliado','id_afiliado');
    }

    public function diagnostico()
    {
        return $this->belongsTo(Diagnosticos::class,'fk_id_diagnostico','id_diagnostico');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursales::class,'fk_id_sucursal','id_sucursal');
    }

    public function medico()
    {
        return $this->belongsTo(Medicos::class,'fk_id_medico','id_medico');
    }

    public function programa()
    {
        return $this->belongsTo(Programas::class,'fk_id_programa','id_programa');
    }

    public function estatus()
    {
        return $this->belongsTo(EstatusRecetas::class,'fk_id_estatus_receta','id_estatus_receta');
    }
    public function proyecto()
    {
        return $this->hasMany(Proyectos::class,'fk_id_proyecto','id_proyecto');
    }
    public function dependiente($fk_id_afiliacion,$id_dependiente)
    {
        return Afiliaciones::select(DB::raw("CONCAT(nombre,' ',paterno,' ',materno) AS nombre,genero,fecha_nacimiento"))->where('id_afiliacion',$fk_id_afiliacion)->where('id_dependiente',$id_dependiente)->first();
    }
    public function parentesco()
    {
        return $this->hasOne(Parentescos::class,'id_parentesco','fk_id_parentesco');
    }
    public function detalles()
    {
        return $this->hasMany(RecetasDetalle::class,'fk_id_receta','id_receta');
    }

}
