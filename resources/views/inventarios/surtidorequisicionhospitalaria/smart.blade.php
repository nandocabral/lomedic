@extends(smart())

@section('header-bottom')
    @parent
    @if (!Route::currentRouteNamed(currentRouteName('index')) && !Route::currentRouteNamed(currentRouteName('show')) )
        <script type="text/javascript" src="{{ asset('js/surtidorequisicioneshospitalarias.js') }}"></script>
    @endif
@endsection

@section('form-content')
    {{ Form::setModel($data) }}
    @if(Route::currentRouteNamed(currentRouteName('create')))
    <div class="card z-depth-1-half">
        <div class="card-body row table-responsive">
            <div class="col-12 mb-3">
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cSelect('Sucursal', 'fk_id_sucursal', $sucursales ?? [],['class'=>'select2','data-url'=>companyRoute('getRequisiciones')]) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cSelect('Numero de requisicion', 'fk_id_requisicion_hospitalaria', $requisicones ?? [],['class'=>'select2','data-url'=>companyRoute('getRequisicionDetalle')]) }}
                                </div>
                            </div>
                            <input type="hidden" name="fk_id_usuario_captura" value="{{$fk_id_usuario_captura}}">
                        </div><!--/row forms-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="card z-depth-1-half">
         <div class="card-body row table-responsive">
            <table class="table highlight mt-3" id="tContactos">
                <thead>
                    <tr>
                        <th>Clave producto</th>
                        <th>Descripcion</th>
                        <th>Cantidad solicitada</th>
                        <th>Cantidad surtida</th>
                        @if(!Route::currentRouteNamed(currentRouteName('show')))
                        <th>Cantidad disponible</th>
                        <th>Cantidad a surtir</th>
                        <th>Precio unitario</th>
                        <th>Importe</th>
                        <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody id="detalle">
                    @if(!Route::currentRouteNamed(currentRouteName('create')))
                         @if(isset($data->detalles))
                            @foreach($data->detalles->where('eliminar',0) as $row => $detalle)
                                <tr>
                                    <td>{{$detalle->claveClienteProducto['clave_producto_cliente'] }}</td>
                                    <td>{{$detalle->claveClienteProducto->sku['descripcion']}}</td>
                                    <td>{{$detalle->cantidad_solicitada}}</td>
                                    @if(Route::currentRouteNamed(currentRouteName('edit')))
                                        @if($detalle->cantidad_surtida < $detalle->cantidad_solicitada )
                                            <td class="cantidad_surtida">{{$detalle->cantidad_surtida}}</td>
                                        @else
                                            <td>Producto entregado en su totalidad.</td>
                                        @endif
                                            <td>{{$detalle->claveClienteProducto->stock($detalle->claveClienteProducto->fk_id_sku,$detalle->claveClienteProducto->fk_id_upc)->first()}}</td>
                                        @if($detalle->cantidad_surtida < $detalle->cantidad_solicitada )
                                                <td><input type="number" onchange="calculatotal(this)" name="relations[has][detalles][{{$row}}][cantidad_surtida]" min="0" max="{{$detalle->cantidad_solicitada-$detalle->cantidad_surtida}}" class="form-control cantidad" value="0"></td>
                                        @else
                                                <td><input type="number" onchange="calculatotal(this)" name="relations[has][detalles][{{$row}}][cantidad_surtida]" min="0" max="{{$detalle->cantidad_solicitada-$detalle->cantidad_surtida}}" class="form-control cantidad" value="0" disabled></td>
                                        @endif
                                        <td>$ {{ number_format($detalle->claveClienteProducto->precio, 2, '.', '')}}</td>
                                        <td class="text-right total">$ {{ number_format(($detalle->claveClienteProducto->precio*$detalle->cantidad_surtida), 2, '.', '')}}</td>
                                        <input type="hidden" name="relations[has][detalles][{{$row}}][id_detalle_requisicion_hospitalaria]"  value="{{$detalle->id_detalle_requisicion_hospitalaria}}"/>
                                        <input type="hidden" name="relations[has][detalles][{{$row}}][fk_id_surtido_requisicion]"  value="{{$detalle->fk_id_surtido_requisicion}}"/>
                                        <input type="hidden" name="relations[has][detalles][{{$row}}][fk_id_clave_cliente_producto]"  value="{{$detalle->fk_id_clave_cliente_producto}}"/>
                                        <input type="hidden" name="relations[has][detalles][{{$row}}][cantidad_solicitada]"  value="{{$detalle->cantidad_solicitada}}"/>
                                        <input type="hidden" name="relations[has][detalles][{{$row}}][precio_unitario]" class="precio" value="{{$detalle->claveClienteProducto->precio}}">
                                        <input type="hidden" name="relations[has][detalles][{{$row}}][importe]" class="importe" value="{{$detalle->claveClienteProducto->precio*$detalle->cantidad_surtida}}">
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    @endif
                </tbody>
            </table>
             <div class="row" >
                 <div class="col-sm-12">
                     <div class="form-group">
                         {{ Form::cTextArea('Observaciones', 'observaciones',['rows'=>'3'] ) }}
                     </div>
                 </div>
             </div>
         </div>
    </div>

@endsection