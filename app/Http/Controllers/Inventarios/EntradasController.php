<?php
/**
 * Created by PhpStorm.
 * User: ihernandezt
 * Date: 11/10/2017
 * Time: 12:00
 */

namespace App\Http\Controllers\Inventarios;

use App\Http\Controllers\ControllerBase;
use App\Http\Models\Administracion\Localidades;
use App\Http\Models\Administracion\Empresas;
use App\Http\Models\Administracion\Proveedores;
use App\Http\Models\Administracion\Sucursales;
use App\Http\Models\Administracion\TiposDocumentos;
use App\Http\Models\Administracion\Usuarios;
use App\Http\Models\Compras\DetalleSolicitudes;
use App\Http\Models\Inventarios\Entradas;
use App\Http\Models\Inventarios\EntradaDetalle;
use App\Http\Models\Compras\Ordenes;
use App\Http\Models\SociosNegocio\SociosNegocio;
use function MongoDB\BSON\toJSON;
use Illuminate\Http\Request;
use Carbon\Carbon;
//use App\Http\Models\Compras\Solicitudes;
//use Milon\Barcode\DNS2D;
//use Milon\Barcode\DNS1D;
//use App\Http\Models\Finanzas\CondicionesPago;
//use App\Http\Models\Proyectos\Proyectos;
//use App\Http\Models\SociosNegocio\SociosNegocio;
//use App\Http\Models\SociosNegocio\TiposEntrega;
//use Barryvdh\DomPDF\Facade as PDF;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Expr\Cast\Object_;
use PhpParser\Node\Scalar\String_;

//use Illuminate\Support\Facades\Response;


class EntradasController extends ControllerBase
{
    public function __construct(Entradas $entity)
    {
        $this->entity = $entity;
    }

    public function index($company, $attributes = [])
    {
        $attributes = ['where'=>[]];
        return parent::index($company, $attributes);
    }

    public function getDataView($entity = null)
    {
//        dd($entity);

        if($entity == null)
        {
            $productos_entrada = '';
        }
        else
        {
            $datos_entrada = $entity->datosEntrada($entity->numero_documento,$entity->fk_id_tipo_documento);

            switch ($entity->fk_id_tipo_documento)
            {
                case 1:
                    break;
                case 2:
                    break;
                case 3:
                    $productos_entrada = $datos_entrada->detalleOrdenes;

                    foreach( $productos_entrada  as $index => $producto )
                    {
                        $productos_entrada[$index]->sku = $datos_entrada->detalleSku()->where('id_sku',$productos_entrada[$index]->fk_id_sku)->pluck('sku')->first();
                        $productos_entrada[$index]->upc = $datos_entrada->detalleUpc()->where('id_upc',$productos_entrada[$index]->fk_id_sku)->pluck('upc')->first();
                        $productos_entrada[$index]->descripcion = $datos_entrada->detalleSku()->where('id_sku',$productos_entrada[$index]->fk_id_sku)->pluck('descripcion')->first();
                    }
                    break;
            }

            $entity->nombre_sucursal = $datos_entrada->sucursales->sucursal;
            $entity->nombre_proveedor = $datos_entrada->proveedor->nombre_comercial;
            $entity->productos = $datos_entrada;
        }

        return [
            'tipo_documento' => TiposDocumentos::pluck('nombre_documento','id_tipo_documento')->sortBy('nombre_documento')->prepend('Selecciona una opcion...',''),
            'productos_entrada' =>  $productos_entrada,
        ];
    }

    public function create($company, $attributes =[])
    {

        $attributes = $attributes+['dataview'=>[
                'sucursales' => Sucursales::where('activo',true)->where('eliminar',false)->pluck('sucursal','id_sucursal'),
                'tipo_documento' => DB::table('gen_cat_tipo_documento')->pluck('nombre_documento','id_tipo_documento'),
        ]];
        return parent::create($company,$attributes);
    }

    public function store(Request $request, $company)
    {
//        $this->validate($request, $this->entity->rules, [], $this->entity->niceNames);
//        $numero_docmento = Entradas::where('numero_documento',$_POST['numero_documento'])
//            ->where('fk_id_tipo_documento',$_POST['fk_id_tipo_documento'])
//            ->first();
//        if(!$numero_docmento)
//        {
        $nueva_entrada = Entradas::create(['fk_id_tipo_documento'=>$_POST['fk_id_tipo_documento'],
            'numero_documento'=> $_POST['numero_documento'],
            'referencia_documento'=> $_POST['referencia_documento'],
            'fecha_entrada'=> date("Y-m-d H:i:s")
        ]);
        parse_str($_POST['detalle_entrada'] , $datos_detalle);
        foreach ($datos_detalle["datos_entradas"] as $detalle)
        {
//                if($detalle['caducidad'] == '')
//                {
//                    $detalle['caducidad'] = null;
//                }

            if( $detalle['ingresar'] != 0 )
            {
                EntradaDetalle::create(['fk_id_entrada_almacen' => $nueva_entrada->id_entrada_almacen ,
                    'fk_id_sku' => $detalle['id_sku'],
                    'fk_id_upc' => $detalle['id_upc'],
                    'cantidad_surtida' => $detalle['ingresar'],
                    'lote' => $detalle['lote'],
                    'fecha_caducidad' => $detalle['caducidad'],
                    'fk_id_detalle_documento' => $detalle['id_detalle_documento'],
                ]);
            }
        }

//        dd($this);
//        }
        return $this->redirect('store');
    }

    public function getProveedores()
    {
        $ordenes_compra = SociosNegocio::where('fk_id_sucursal',$_POST['id_sucursal'])
            ->select('id_orden')
            ->pluck('id_orden')
            ->toJson();

        return $ordenes_compra;
    }

//    public function getOrdenes()
//    {
//
//        $entrada_almacen = Entradas::where('fk_id_tipo_documento',$_POST['fk_id_tipo_documento'])
//                            ->where('numero_documento',$_POST['numero_documento'])
//                            ->firts();
//        if(!$entrada_almacen)
//        {
//            switch ($_POST['fk_id_tipo_documento'])
//            {
//                case 1:
//                    $orden = 1;
//                    break;
//
//                case 2:
//                    $orden = 2;
//                    break;
//
//                case 3:
//                    $orden = Ordenes::where('id_orden',$_POST['id_entrada'])
//                        ->get()
//                        ->toJson();
//                    break;
//            }
//        }
//
//        return $_POST['fk_id_tipo_documento'];
//    }

    public function getDetalleEntrada()
    {

        if($_POST['fk_id_tipo_documento'])
        {
            switch ($_POST['fk_id_tipo_documento'])
            {
                case 1:
                    $datos_documento = '';
                    break;
                case 2:
                    $datos_documento = '';
                    break;
                case 3:
                    $datos_documento = Ordenes::where('id_orden',$_POST['numero_documento'])
                        ->first();
                    break;
            }
        }

        if($datos_documento != '')
        {
//
//            $datos_entrada = Entradas::Join('inv_det_entrada_almacen','inv_opr_entrada_almacen.id_entrada_almacen','=','inv_det_entrada_almacen.fk_id_entrada_almacen')
//                ->where('inv_opr_entrada_almacen.fk_id_tipo_documento',$_POST['fk_id_tipo_documento'])
//                ->where('inv_opr_entrada_almacen.numero_documento',$_POST['numero_documento'])
//                ->get();

            $datos_orden['sucursales'] = $datos_documento->sucursales;
            $datos_orden['proveedor'] = $datos_documento->proveedor;
//            $datos_orden['entrada_detalle'] = $datos_entrada;
            $detalle_documento = $datos_documento->detalleOrdenes;
//            $sku = $datos_documento->detalleSku;
//            $upc = $datos_documento->detalleUpc;

            foreach ( $datos_documento->detalleOrdenes as $id_row => $detalle)
            {
                $detalle_productos[$id_row]['id_sku'] = $detalle->fk_id_sku;
                $detalle_productos[$id_row]['sku'] = $detalle->sku->sku;
                $detalle_productos[$id_row]['sku_descripcion'] = $detalle->sku->descripcion;
                $detalle_productos[$id_row]['id_upc'] = $detalle->fk_id_upc;
                $detalle_productos[$id_row]['upc'] = $detalle->upc['upc'];
                $detalle_productos[$id_row]['id_cliente'] = $detalle->fk_id_cliente;
                $detalle_productos[$id_row]['nombre_cliente'] = $detalle->cliente['nombre_comercial'];
                $detalle_productos[$id_row]['fk_id_proyecto'] = $detalle->fk_id_proyecto;
                $detalle_productos[$id_row]['nombre_proyecto'] = $detalle->proyecto['proyecto'];
                $detalle_productos[$id_row]['cantidad'] = $detalle->cantidad;
                $detalle_productos[$id_row]['id_detalle'] = $detalle->id_orden_detalle;
                $detalle_productos[$id_row]['cantidad_surtida'] = $detalle->sumatoriaCentidad($_POST['fk_id_tipo_documento'],$_POST['numero_documento'],$detalle->fk_id_sku,$detalle->fk_id_upc,$detalle->id_orden_detalle);
//                $detalle_productos[$id_row]['cantidad_surtida'] = $detalle->entradaDetalle['cantidad_surtida'];
                $detalle_productos[$id_row]['lote'] = $detalle->entradaDetalle['lote'];
                $detalle_productos[$id_row]['fecha_caducidad'] = $detalle->entradaDetalle['fecha_caducidad'];
            }

            $data = [
                    'datos_documento' => $datos_orden ,
                    'detalle_documento' => $detalle_documento,
                    'company_route'=>companyRoute('store'),
                    'dato_entrada'=>$datos_documento,
                    'detalle_entrada'=>$detalle_productos];

        }
        else
        {
            $data = [];
        }

        return $data ;
    }

//    public function guardarEntrada(Request $request, $company)
//    {
//
////        $numero_docmento = Entradas::where('numero_documento',$_POST['numero_documento'])
////            ->where('fk_id_tipo_documento',$_POST['fk_id_tipo_documento'])
////            ->first();
////        if(!$numero_docmento)
////        {
//            $nueva_entrada = Entradas::create(['fk_id_tipo_documento'=>$_POST['fk_id_tipo_documento'],
//                            'numero_documento'=> $_POST['numero_documento'],
//                            'referencia_documento'=> $_POST['referencia_documento'],
//                            'fecha_entrada'=> date("Y-m-d H:i:s")
//                            ]);
//            parse_str($_POST['detalle_entrada'] , $datos_detalle);
//            foreach ($datos_detalle["datos_entradas"] as $detalle)
//            {
////                if($detalle['caducidad'] == '')
////                {
////                    $detalle['caducidad'] = null;
////                }
//
//                if( $detalle['ingresar'] != 0 )
//                {
//                    EntradaDetalle::create(['fk_id_entrada_almacen' => $nueva_entrada->id_entrada_almacen ,
//                        'fk_id_sku' => $detalle['id_sku'],
//                        'fk_id_upc' => $detalle['id_upc'],
//                        'cantidad_surtida' => $detalle['ingresar'],
//                        'lote' => $detalle['lote'],
//                        'fecha_caducidad' => $detalle['caducidad'],
//                        'fk_id_detalle_documento' => $detalle['id_detalle_documento'],
//                    ]);
//                }
//            }
//
//
////        }
//        return $this->redirect('store');
//
//    }



}