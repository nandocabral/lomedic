<?php
namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\ControllerBase;
use App\Http\Models\Ventas\FacturasClientes;
use App\Http\Models\SociosNegocio\SociosNegocio;
use App\Http\Models\Administracion\Sucursales;
use App\Http\Models\Administracion\Monedas;
use App\Http\Models\Administracion\FormasPago;
use App\Http\Models\Administracion\MetodosPago;
use App\Http\Models\Proyectos\Proyectos;
use App\Http\Models\Administracion\UsosCfdis;
use App\Http\Models\Administracion\Empresas;
use App\Http\Models\Finanzas\CondicionesPago;
use App\Http\Models\Administracion\TiposRelacionesCfdi;
use App\Http\Models\Administracion\RegimenesFiscales;
use App\Http\Models\Administracion\SeriesDocumentos;
use App\Http\Models\Administracion\Municipios;
use App\Http\Models\Administracion\Estados;
use App\Http\Models\Administracion\Paises;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use File;
use App\Http\Models\Proyectos\ContratosProyectos;

class FacturasClientesController extends ControllerBase
{
    public function __construct(FacturasClientes $entity)
	{
		$this->entity = $entity;
	}

	public function getDataView($entity = null)
	{
        return [
            'empresas' => Empresas::where('activo',1)->where('eliminar',0)->orderBy('razon_social')->pluck('razon_social','id_empresa')->prepend('Selecciona una opcion...',''),
            'js_empresa' => Crypt::encryptString('"conditions": [{"where": ["id_empresa","$id_empresa"]}, {"where": ["eliminar","0"]}]'),
            'regimens' => RegimenesFiscales::select('regimen_fiscal','id_regimen_fiscal')->where('activo',1)->where('eliminar',0)->orderBy('regimen_fiscal')->pluck('regimen_fiscal','id_regimen_fiscal')->prepend('...',''),
            'series' => SeriesDocumentos::select('prefijo','id_serie')->where('activo',1)->where('fk_id_tipo_documento',4)->pluck('prefijo','id_serie'),
            'js_series' => Crypt::encryptString('"conditions": [{"where": ["fk_id_empresa",$id_empresa]}, {"where": ["activo",1]}]'),
            'municipios' => Municipios::select('municipio','id_municipio')->where('activo',1)->where('eliminar',0)->pluck('municipio','id_municipio')->prepend('...',''),
            'estados' => Estados::select('estado','id_estado')->where('activo',1)->where('eliminar',0)->pluck('estado','id_estado')->prepend('...',''),
            'paises' => Paises::select('pais','id_pais')->where('activo',1)->where('eliminar',0)->pluck('pais','id_pais')->prepend('...',''),
            'js_clientes' => Crypt::encryptString('"select": ["razon_social", "id_socio_negocio"], "conditions": [{"where": ["activo",1]}, {"where": ["eliminar",0]}, {"where": ["fk_id_tipo_socio_venta",1]}], "whereHas":[{"empresas":{"where":["id_empresa","$id_empresa"]}}]'),
            'clientes' => empty($entity) ? [] : SociosNegocio::where('fk_id_tipo_socio_venta',1)->whereHas('empresas', function ($query) use($entity) {
                $query->where('id_empresa','=',$entity->fk_id_empresa);
            })->orderBy('nombre_comercial')->pluck('nombre_comercial','id_socio_negocio')->prepend('Selecciona una opcion...',''),
            'js_cliente' => Crypt::encryptString('"conditions": [{"where": ["id_socio_negocio",$id_socio_negocio]}, {"where": ["eliminar",0]}], "limit": "1"'),
            'js_proyectos' => Crypt::encryptString('"select": ["proyecto", "id_proyecto"], "conditions": [{"where": ["fk_id_estatus",1]}, {"where": ["eliminar",0]}, {"where": ["fk_id_cliente","$fk_id_cliente"]}], "orderBy": [["proyecto", "ASC"]]'),
            'proyectos' => empty($entity) ? [] : Proyectos::where('id_proyecto',$entity->fk_id_proyecto)->pluck('proyecto','id_proyecto')->prepend('Selecciona una opcion...',''),
            'contratos' => empty($entity) ? [] : ContratosProyectos::where('id_contrato',$entity->fk_id_contrato)->pluck('num_contrato','id_contrato')->prepend('Selecciona una opcion...',''),
            'js_contratos' => Crypt::encryptString('"select":["id_proyecto"], "conditions":[{"where":["id_proyecto","$id_proyecto"]}], "with":["contratos:id_contrato,num_contrato,fk_id_proyecto"]'),
            'js_sucursales' => Crypt::encryptString('"select": ["sucursal", "id_sucursal"], "conditions": [{"where": ["activo",1]}, {"where": ["eliminar",0]}, {"where": ["fk_id_cliente","$fk_id_cliente"]}], "orderBy": [["sucursal", "ASC"]]'),
            'sucursales' => Sucursales::where('activo',1)->orderBy('sucursal')->pluck('sucursal','id_sucursal')->prepend('Selecciona una opcion...',''),
            'monedas' => Monedas::selectRaw("CONCAT(descripcion,' (',moneda,')') as moneda, id_moneda")->where('activo','1')->where('eliminar','0')->orderBy('moneda')->pluck('moneda','id_moneda')->prepend('Selecciona una opcion...',''),
            'metodospago' => MetodosPago::selectRaw("CONCAT(metodo_pago,' - ',descripcion) as metodo_pago, id_metodo_pago")->where('activo','1')->where('eliminar','0')->orderBy('metodo_pago')->pluck('metodo_pago','id_metodo_pago')->prepend('Selecciona una opcion...',''),
            'formaspago' => FormasPago::selectRaw("CONCAT(forma_pago,' - ',descripcion) as forma_pago, id_forma_pago")->where('activo','1')->where('eliminar','0')->orderBy('forma_pago')->pluck('forma_pago','id_forma_pago')->prepend('Selecciona una opcion...',''),
            'condicionespago' => CondicionesPago::select('condicion_pago','id_condicion_pago')->where('activo','1')->where('eliminar','0')->orderBy('condicion_pago')->pluck('condicion_pago','id_condicion_pago')->prepend('Selecciona una opcion...',''),
            'usoscfdi' => UsosCfdis::selectRaw("CONCAT(uso_cfdi,' - ',descripcion) as uso_cfdi, id_uso_cfdi")->where('activo','1')->where('eliminar','0')->orderBy('uso_cfdi')->pluck('uso_cfdi','id_uso_cfdi')->prepend('Selecciona una opcion...',''),
            'tiposrelacion' => TiposRelacionesCfdi::selectRaw("CONCAT(tipo_relacion,' - ',descripcion) as tipo_relacion, id_sat_tipo_relacion")->where('activo',1)->where('eliminar',0)->where('factura',1)->orderBy('tipo_relacion')->pluck('tipo_relacion','id_sat_tipo_relacion')->prepend('Selecciona una opcion...',''),
            'facturasrelacionadas' =>FacturasClientes::selectRaw("CONCAT(serie,'-',folio,'  [',uuid,']') as factura, id_documento")->whereNotNull('uuid')->orderBy('factura')->pluck('factura','id_documento')->prepend('Selecciona una opcion...',''),
        ];
    }
    
    public function store(Request $request, $company, $compact = true)
    {
        $return = parent::store($request, $company, $compact);
        
        $datos = $return["entity"];
        
        if($datos) {
            $id = $datos->id_documento;
            $xml = generarXml($this->datos_cfdi($id));
            
            if(!empty($xml)) {
                $request->request->add(['xml_original'=>$xml]);
            }
            
            if($request->timbrar == true && !empty($xml))
                $timbrado = timbrar($xml);
                
            if(isset($timbrado) && $timbrado->status == '200') {
                if(in_array($timbrado->resultados->status,['200','307'])) {
                    $request->request->add([
                        'cadena_original'=>$timbrado->resultados->cadenaOriginal,
                        'certificado_sat'=>$timbrado->resultados->certificadoSAT,
                        'xml_timbrado'=>$timbrado->resultados->cfdiTimbrado,
                        'fecha_timbrado'=>str_replace('T',' ',substr($timbrado->resultados->fechaTimbrado,0,19)),
                        'sello_sat'=>$timbrado->resultados->selloSAT,
                        'uuid'=>$timbrado->resultados->uuid,
                        'version_tfd'=>$timbrado->resultados->versionTFD,
                        'codigo_qr'=>base64_encode($timbrado->resultados->qrCode),
                    ]);
                }
            }
            $request->request->set('save',true);
            
            $return = parent::update($request, $company, $id, $compact);
        }
        return $return["redirect"];
    }
    
    public function update(Request $request, $company, $id, $compact = true)
    {
        $return = parent::update($request, $company, $id, $compact);
        
        $datos = $return["entity"];
        
        if($datos && $request->save !== true)
        {
            $xml = generarXml($this->datos_cfdi($id));
            
            if(!empty($xml)) {
                $request->request->add(['xml_original'=>$xml]);
            }

            #dd($xml['xml']);
            
            if($request->timbrar == true && !empty($xml))
                $timbrado = timbrar($xml);
            
            if(isset($timbrado) && $timbrado->status == '200') {
                if(in_array($timbrado->resultados->status,['200','307'])) {
                    $request->request->add([
                        'cadena_original'=>$timbrado->resultados->cadenaOriginal,
                        'certificado_sat'=>$timbrado->resultados->certificadoSAT,
                        'xml_timbrado'=>$timbrado->resultados->cfdiTimbrado,
                        'fecha_timbrado'=>str_replace('T',' ',substr($timbrado->resultados->fechaTimbrado,0,19)),
                        'sello_sat'=>$timbrado->resultados->selloSAT,
                        'uuid'=>$timbrado->resultados->uuid,
                        'version_tfd'=>$timbrado->resultados->versionTFD,
                        'codigo_qr'=>base64_encode($timbrado->resultados->qrCode),
                    ]);
                }
                else
                    dd($timbrado);
            }
            $request->request->set('save',true);
            $return = parent::update($request, $company, $id, $compact);
        }
        return $return["redirect"];
    }
    
    public function destroy(Request $request, $company, $idOrIds, $attributes = ['fk_id_estatus'=>3])
    {
        $ids = !is_array($idOrIds) ? [$idOrIds] : $idOrIds;
        
        foreach ($ids as $id)
        {
            $entity = $this->entity->where('fk_id_estatus','<>',3)->find($id);
            
            if(!empty($entity)) {
                $rfc = $entity->empresa->rfc;
                $uuid = $entity->uuid;
                $cer = $this->getfile($entity->empresa->conexion,$entity->certificado->certificado);
                $key = $this->getfile($entity->empresa->conexion,$entity->certificado->key);
                $pass = decrypt($entity->certificado->password);
                $email = $entity->empresa->email;
                
                $estatusCancelacion = confirmar_cancelacion($uuid);
                if($estatusCancelacion->status == 200) {
                    $entity->update($attributes);
                }
                
                $cancelacion = cancelar($rfc,$uuid,$cer,$key,$pass,$email);
                
                if($cancelacion->status == 200)
                {
                    $estatusCancelacion = confirmar_cancelacion($uuid);
                    if($estatusCancelacion->status == 200)
                        $entity->update($attributes);
                    else
                        dd($cancelacion);
                }
                else
                    dd($cancelacion);
            }
        }
        
        return parent::destroy($request, $company, $idOrIds, $attributes);
    }
    
    protected function getfile($empresa,$archivo)
    {
        $return = null;
        $file = Storage::disk('certificados')->getDriver()->getAdapter()->getPathPrefix().$empresa.'/'.$archivo;
        if (File::exists($file)) {
            $return = file_get_contents($file);
        }
        return $return;
    }
    
    protected function datos_cfdi($id)
    {
        $return = [];
        $entity = $this->entity->find($id);
        
        if(!empty($entity))
        {
            $return['certificado'] = $entity->certificado->cadena_cer;
            $return['key'] = $entity->certificado->cadena_key;
            $return['cfdi'] = [
                'Version'=>'3.3',
                'Serie' => $entity->serie,
                'Folio' => $entity->folio,
                'Fecha' => str_replace(' ','T',substr($entity->fecha_creacion,0,19)),
                'FormaPago' => $entity->formapago->forma_pago,
                'NoCertificado' => $entity->certificado->no_certificado,
                'CondicionesDePago' => $entity->condicionpago->condicion_pago,
                'Moneda' => $entity->moneda->moneda,
                'TipoCambio' => round($entity->tipo_cambio,4),
                'TipoDeComprobante' => $entity->tipocomprobante->tipo_comprobante,
                'MetodoPago' => $entity->metodopago->metodo_pago,
                'LugarExpedicion' => '64000',
            ];
            
            foreach ($entity->relaciones as $i=>$row)
            {
                $return['relacionados'][$row->tiporelacion->tipo_relacion][] = ['UUID'=>$row->documento->uuid];
            }
        
            $return['emisor'] = [
                'Rfc' => $entity->empresa->rfc,
                'Nombre' => $entity->empresa->razon_social,
                'RegimenFiscal' => $entity->empresa->fk_id_regimen_fiscal,
            ];
        
            $return['receptor'] = [
                'Rfc' =>  $entity->cliente->rfc,
                'Nombre' => $entity->cliente->razon_social,
                #'ResidenciaFiscal' => 'MXN',
                #'NumRegIdTrib' => '121585958',
                'UsoCFDI' => $entity->usocfdi->uso_cfdi,
            ];
        
            foreach ($entity->detalle as $i=>$row)
            {
                $impuesto = [];
                if($row->impuestos->retencion) {
                    $impuesto['retencion'] = [
                        'Impuesto' => $row->impuestos->numero_impuesto,
                        'TipoFactor' => $row->impuestos->tipo_factor,
                        'TasaOCuota' => $row->impuestos->tasa_o_cuota,
                        'Importe' => number_format($row->impuesto,2,'.',''),
                        'Base' => number_format(($row->importe),2,'.',''),
                    ];
                }
                else {
                    $impuesto['traslado'] = [
                        'Impuesto' => $row->impuestos->numero_impuesto,
                        'TipoFactor' => $row->impuestos->tipo_factor,
                        'TasaOCuota' => $row->impuestos->tasa_o_cuota,
                        'Importe' => number_format($row->impuesto,2,'.',''),
                        'Base' => number_format(($row->importe),2,'.','') - number_format(($row->descuento),2,'.',''),
                    ];
                }
                
                $concepto = [
                    'ClaveProdServ' => $row->claveproducto->clave_producto_servicio,
                    'NoIdentificacion' => $row->clavecliente->clave_producto_cliente,
                    'Cantidad' => $row->cantidad,
                    'ClaveUnidad' => $row->unidadmedida->clave_unidad,
                    'Unidad' => $row->unidadmedida->descripcion,
                    'Descripcion' => $row->descripcion,
                    'ValorUnitario' => number_format($row->precio_unitario,2,'.',''),
                    'Importe' => number_format($row->cantidad * $row->precio_unitario,2,'.',''),
                    'impuestos' => [$impuesto]
                ];
                if($row->descuento > 0)
                    $concepto['Descuento'] = number_format($row->descuento,2,'.','');
                
                if(!empty($row->cuenta_predial))
                    $concepto['cuentapredial'] = $row->cuenta_predial;
                
                if(!empty($row->pedimento))
                    $concepto['pedimento'] = $row->pedimento;
                
                $return['conceptos'][] = $concepto;
            }
        }
        return $return;
    }
}