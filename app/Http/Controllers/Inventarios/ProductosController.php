<?php

namespace App\Http\Controllers\Inventarios;

use App\Http\Controllers\ControllerBase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Models\Inventarios\Productos;
use App\Http\Models\Administracion\GrupoProductos;
use App\Http\Models\Administracion\SubgrupoProductos;
use App\Http\Models\Administracion\SeriesSkus;
use App\Http\Models\Administracion\Impuestos;
use App\Http\Models\Administracion\FamiliasProductos;
use App\Http\Models\Administracion\Presentaciones;
use App\Http\Models\Administracion\Sales;
use App\Http\Models\Administracion\FormaFarmaceutica;
use App\Http\Models\SociosNegocio\TiposSocioNegocio;
use App\Http\Models\Inventarios\Cbn;
use App\Http\Models\Inventarios\Upcs;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use DB;
use App\Http\Models\SociosNegocio\SociosNegocio;
use App\Http\Models\Administracion\Periodos;
use App\Http\Models\Administracion\MetodosValoracion;

class ProductosController extends ControllerBase
{
    public function __construct()
    {
        $this->entity = new Productos;
    }

    public function update(Request $request, $company, $id, $compact = false)
    {
        # ¿Usuario tiene permiso para actualizar?
        #$this->authorize('update', $this->entity);

        # Validamos request, si falla regresamos atras
        $this->validate($request, $this->entity->rules, [], $this->entity->niceNames);

        DB::beginTransaction();
        $entity = $this->entity->findOrFail($id);
        $entity->fill($request->all());

        if ($entity->save()) {
            if(isset($request->detalles)) {
                foreach ($request->detalles as $detalle) {
                    $sync[$detalle['fk_id_upc']] = $detalle;
                }
                $entity->findOrFail($id)->upcs()->sync($sync);
            }
            DB::commit();

            # Eliminamos cache
            Cache::tags(getCacheTag('index'))->flush();
            #$this->log('update', $id);
            return $this->redirect('update');
        } else {
            DB::rollBack();
            #$this->log('error_update', $id);
            return $this->redirect('error_update');
        }
    }

    public function getDataView($entity = null)
    {
        $grupos = GrupoProductos::where('activo',1)->pluck('grupo','id_grupo')->sortBy('grupo');

        foreach ($grupos as $id => $grupo) {
            $subgrupo = SubgrupoProductos::where('fk_id_grupo',$id)->where('activo',1)->pluck('subgrupo','id_subgrupo')->sortBy('subgrupo')->toArray();
            if(!empty($subgrupo))
            { $subgrupos[$grupo] = $subgrupo; }
        }

        return [
            'seriesku'         => SeriesSkus::where('activo',1)->pluck('nombre_serie','id_serie_sku')->sortBy('nombre_serie')->prepend('...',''),
            'subgrupo'         => collect($subgrupos ?? [])->prepend('...','')->toArray(),
            'formafarmaceutica'=> FormaFarmaceutica::where('activo',1)->pluck('forma_farmaceutica','id_forma_farmaceutica')->sortBy('forma_farmaceutica')->prepend('...',''),
            'cbn'              => Cbn::where('activo',1)->selectRaw("Concat(clave_cbn,'-',descripcion) as text,id_cbn as id")->pluck('text','id')->prepend('...',''),
            'impuesto'         => Impuestos::where('activo',1)->pluck('impuesto','id_impuesto')->sortBy('impuesto')->prepend('...',''),
            'familia'          => FamiliasProductos::where('activo',1)->pluck('descripcion','id_familia')->sortBy('descripcion')->prepend('...',''),
            'metodovaloracion' => MetodosValoracion::where('activo',1)->pluck('metodo_valoracion','id_metodo_valoracion')->sortBy('metodo_valoracion')->prepend('...',''),
            'periodos'         => Periodos::where('activo',1)->pluck('periodo','id_periodo')->sortBy('periodo')->prepend('...',''),
            'sociosnegocio'    => SociosNegocio::where('activo',1)->whereNotNull('fk_id_tipo_socio_compra')
                                                ->pluck('nombre_comercial','id_socio_negocio')->sortBy('nombre_comercial')->prepend('...',''),
            'presentaciones'   => Presentaciones::join('gen_cat_unidades_medidas', 'gen_cat_unidades_medidas.id_unidad_medida', '=', 'adm_cat_presentaciones.fk_id_unidad_medida')
                                                ->whereNotNull('clave')->selectRaw("Concat(cantidad,' ',clave) as text, id_presentacion as id")->pluck('text','id'),
            'sales'            => Sales::where('activo',1)->pluck('nombre','id_sal')->sortBy('nombre'),
            'api_js'           => Crypt::encryptString('"select": ["nombre_comercial", "descripcion","fk_id_laboratorio"], "conditions": [{"where": ["id_upc","$id_upc"]}], "with": ["laboratorio"]'),
            // 'upcs_js'          => Crypt::encryptString('
            // "select":["id_upc","upc","descripcion","marca","nombre_comercial"],
            // "conditions":
            //     [{"where":["fk_id_forma_farmaceutica",$fk_id_forma_farmaceutica],["fk_id_presentaciones", $fk_id_presentaciones]}],
            // "whereHas": [{"presentaciones":{"where":["fk_id_presentaciones", "$fk_id_presentaciones"],["fk_id_sal", "$fk_id_sal"]}}]
            // ')
        ];
    }

    public function obtenerSkus($company,Request $request)
    {
        $term = $request->term;     
        $id_proyecto = $request->fk_id_proyecto;
        $id_socio = $request->fk_id_socio_negocio;
        $skus = null;
        if($id_proyecto) {
            $skus = Productos::where('activo',1)->whereRaw("(sku ILIKE '%$term%' OR descripcion_corta ILIKE '%$term%' OR descripcion ILIKE '%$term%')")->
            whereHas('clave_cliente_productos', function ($q) use ($id_proyecto) {
                $q->whereHas('proyectos', function ($q2) use ($id_proyecto) {
                    $q2->where('id_proyecto', $id_proyecto);
                });
            })
                ->whereIn('id_sku',SociosNegocio::find($id_socio)->productos->pluck('fk_id_sku'))
                ->get();
        }else{
            $skus = Productos::where('activo',1)->whereRaw("(sku ILIKE '%$term%' OR descripcion_corta ILIKE '%$term%' OR descripcion ILIKE '%$term%')")->whereIn('id_sku',SociosNegocio::find($id_socio)->productos->pluck('fk_id_sku'))->get();
        }
        $skus_set = [];
        foreach ($skus as $sku)
        {
            $sku_data['id'] = (int)$sku->id_sku;
            $sku_data['text'] = $sku->sku;
            $sku_data['descripcion_corta'] = $sku->descripcion_corta;
            $sku_data['descripcion'] = $sku->descripcion;
            $skus_set[] = $sku_data;
        }
        return Response::json($skus_set);
    }

    public function getUpcs()
    {
        $id_forma_farmaceutica = request()->id_forma;
        $id_presentaciones = request()->id_presentaciones;
        $sales = json_decode(request()->arr_sales);
        $upcs = Upcs::where('fk_id_forma_farmaceutica',$id_forma_farmaceutica)->where('fk_id_presentaciones',$id_presentaciones)->where('activo',1)->with('laboratorio')->get();

        $upcFiltered = $upcs->filter(function($upc) use ($sales){
            foreach ($upc->presentaciones as $upc_detalle) {
                foreach ($sales as $sal) {
                    if($upc_detalle->fk_id_presentaciones == $sal->id_concentracion && $upc_detalle->fk_id_sal == $sal->id_sal)
                    {
                        return $upc;
                    }
                }
            }
        });
        return json_encode($upcFiltered);
    }

    public function getThisSkus($company, $id, Request $request)
    {
        $sku_data = $this->entity->find($id)->presentaciones()->get();

        $upcs = Upcs::where('activo',1)->with('presentaciones')->get();
        $upcFiltered = $upcs->filter(function($upc) use ($sku_data){
            foreach ($upc->presentaciones as $detalle) {
                foreach ($sku_data as $sku_detalle) {
                    if($detalle->fk_id_presentaciones == $sku_detalle->fk_id_presentaciones && $detalle->fk_id_sal == $sku_detalle->fk_id_sal)
                    {
                        return $upc;
                    }
                }
            }
        });
        return json_encode($upcFiltered);
    }

    public function getRelatedSkus()
    {
        $id_forma_farmaceutica = request()->id_forma;
        $id_presentaciones = request()->id_presentaciones;
        $sales = json_decode(request()->sales);
        // $sales = json_decode(request()->arr_sales);
        // $presentaciones = json_decode(request()->arr_presentaciones);
        $skus = $id_presentaciones > 0 ? Productos::select('id_sku','sku')->where('fk_id_forma_farmaceutica',$id_forma_farmaceutica)->where('fk_id_presentaciones',$id_presentaciones)->get() : Productos::select('id_sku','sku')->where('fk_id_forma_farmaceutica',$id_forma_farmaceutica)->get();
        $upcs = $id_presentaciones > 0 ? Upcs::select('id_upc','upc','nombre_comercial','marca','descripcion','fk_id_laboratorio')->where('fk_id_forma_farmaceutica',$id_forma_farmaceutica)->where('fk_id_presentaciones',$id_presentaciones)->with('laboratorio:id_laboratorio,laboratorio')->get() : Upcs::select('id_upc','upc','nombre_comercial','marca','descripcion','fk_id_laboratorio')->where('fk_id_forma_farmaceutica',$id_forma_farmaceutica)->with('laboratorio:id_laboratorio,laboratorio')->get();

        $skus = $skus->filter(function ($sku) use ($sales){//Para obtener los SKUS que coinciden
            $presentacion = $sku->presentaciones->filter(function ($presentacion) use ($sales){
                $bool = false;
                foreach ($sales as $sal){
                    if($sal->id_sal == $presentacion->fk_id_sal && $sal->id_concentraciones == $presentacion->fk_id_presentaciones)
                        $bool = true;
                }
                return $bool;
            });
            return $presentacion->count() == count($sales) ? true : false;
        })->filter(function ($sku) use ($sales,$upcs){//Agrega los UPCS que coinciden con el SKU
            $newcollection = [];
            foreach ($upcs as $upc)
            {
                $presentacion = $upc->presentaciones->filter(function ($presentacion) use ($sales){
                    $bool = false;
                    foreach ($sales as $sal){
                        if($sal->id_sal == $presentacion->fk_id_sal && $sal->id_concentraciones == $presentacion->fk_id_presentaciones)
                            $bool = true;
                    }
                    return $bool;
                });
                $presentacion->count() == count($sales) ? $newcollection[] = $upc : false;
            }
            return $sku->upcs = $newcollection;
        });

        return json_encode($skus);
    }

}
