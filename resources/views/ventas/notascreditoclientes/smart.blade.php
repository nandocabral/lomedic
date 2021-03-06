@extends(smart())

@section('header-bottom')
	@php
		list($this_path,$this_controller,$this_action) = explode('.',\Route::currentRouteName());
	@endphp
	@parent
	<script type="text/javascript">
		var empresa_js    = '{{ $js_empresa ?? '' }}';
		var cliente_js    = '{{ $js_cliente ?? '' }}';
		var clientes_js   = '{{ $js_clientes ?? '' }}';
		var series_js     = '{{ $js_series ?? '' }}';
        var serie_js      = '{{ $js_serie ?? '' }}';
        var proyectos_js  = '{{ $js_proyectos ?? '' }}';
    	var sucursales_js = '{{ $js_sucursales ?? '' }}';
    	var impuestos_js  = '{{ $js_impuestos ?? '' }}';
    	var action = '{{ $this_action }}';
    	var certificados_js = '{{$js_certificados ?? ''}}';
	</script>
	@notroute(['index'])
		{{ HTML::script(asset('js/ventas/notascreditoclientes.js')) }}
	@endif
@endsection

@section('form-content')
	{{ Form::setModel($data) }}
	<div class="row mx-0 my-3">
		<div class="card col-lg-7">
    		<div class="card-header row">
    			<h5 class="col-md-12 text-center">Emisor</h5>
        	</div>
        	<div class="card-body row">
        		<div class="form-group col-md-4">
        			{{Form::cSelect('* Empresa','fk_id_empresa', $empresas ?? [],['class'=>'select2','disabled'=>!Route::currentRouteNamed(currentRouteName('create')),'data-url'=>ApiAction('administracion.empresas')])}}
        		</div>
				<div class="form-group col-md-4">
					{{Form::cSelect('* Certificado','fk_id_certificado',$certificados ?? [],['class'=>'select2','disabled','data-url'=>ApiAction('administracion.empresas')])}}
				</div>
        		<div class="form-group col-md-4">
        			{{Form::cText('Rfc','rfc',['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cSelect('* Regimen Fiscal','fk_id_regimen_fiscal', $regimens ?? [],['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cSelect('* Pais','fk_id_pais', $paises ?? [],['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cSelect('* Estado','fk_id_estado', $estados ?? [],['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cSelect('* Municipio','fk_id_municipio', $municipios ?? [],['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cText('Colonia','colonia',['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cText('Calle','calle',['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-4">
        			{{Form::cText('No. Exterior','no_exterior',['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-4">
        			{{Form::cText('No. Interior','no_interior',['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-4">
        			{{Form::cText('Codigo Postal','codigo_postal',['disabled'=>true])}}
        		</div>
        	</div>
    	</div>
    	
    	<div class="card col-lg-5">
    		<div class="card-header row">
    			<h5 class="col-md-12 text-center">Informacion del CFDI</h5>
        	</div>
        	<div class="card-body row">
				@inroute(['edit','show'])
				<div class="form-group col-md-12">
						<h4 class="text-center {{$data->fk_id_estatus_cfdi == 1 ? 'text-danger' :'text-success'}}">{{$data->estatuscfdi->estatus}}</h4>
				</div>
				@endif
				<div class="form-group col-md-4">
        			{{Form::cSelect('* Serie','fk_id_serie', $series ?? [],['class'=>'select2','disabled'=>!Route::currentRouteNamed(currentRouteName('create')),'data-url'=>ApiAction('administracion.seriesdocumentos')])}}
        		</div>
        		<div class="form-group col-md-3">
					<i class="material-icons text-danger float-left" data-toggle="tooltip" data-placement="top" title="" data-original-title="El folio puede cambiar si otro usuario genero una nota de crédito antes de que se guardara esta. Verificalo despues de guardarlo.">warning</i>
        			{{Form::hidden('serie',null,['id'=>'serie'])}}
        			{{Form::cText('* Folio','folio',['readonly'=>true])}}
        		</div>
        		<div class="form-group col-md-5">
        			{{Form::cText('* Fecha','fecha_creacion',['readonly'=>true])}}
        		</div>
        	
        		<div class="form-group col-md-7">
        			{{Form::cSelect('* Uso CFDI','fk_id_uso_cfdi', $usoscfdi ?? [],['class'=>'select2'])}}
        		</div>
        		<div class="form-group col-md-5">
        			{{Form::cSelect('* Metodo Pago','fk_id_metodo_pago', $metodospago ?? [])}}
        		</div>
        
        		<div class="form-group col-md-7">	
        			{{Form::cSelect('* Forma Pago','fk_id_forma_pago', $formaspago ?? [], ['class'=>'select2'])}}
        		</div>
        		
        		<div class="form-group col-md-5">
        			{{Form::cSelect('* Condicion Pago','fk_id_condicion_pago', $condicionespago ?? [], ['class'=>'select2'])}}
        		</div>
        		<div class="form-group col-md-7">
        			{{Form::cSelect('* Moneda','fk_id_moneda', $monedas ?? [], ['class'=>'select2'])}}
        		</div>
        		<div class="form-group col-md-5">
        			{{Form::cText('* Tipo Cambio','tipo_cambio',[isset($data->fk_id_moneda) && $data->fk_id_moneda == 100 ? 'readonly' : ''])}}
        		</div>
        	</div>
    	</div>
    </div>
		
	<div class="row mx-0 my-2">
		<div class="card col-lg-7">
    		<div class="card-header row">
    			<h5 class="col-md-12 text-center">Receptor</h5>
        	</div>
        	<div class="card-body row">
        		<div class="form-group col-md-8">
        			{{Form::cSelect('* Cliente','fk_id_socio_negocio', $clientes ?? [], ['class'=>'select2','disabled'=>!Route::currentRouteNamed(currentRouteName('create')),'data-url'=>ApiAction('sociosnegocio.sociosnegocio')])}}
        		</div>
        		<div class="form-group col-md-4">
        			{{Form::cText('Rfc','rfc_cliente',['disabled'=>true])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cSelect('* Proyecto','fk_id_proyecto', $proyectos ?? [], ['class'=>'select2','disabled'=>!Route::currentRouteNamed(currentRouteName('create')),'data-url'=>ApiAction('proyectos.proyectos')])}}
        		</div>
        		<div class="form-group col-md-6">
        			{{Form::cSelect('* Sucursal','fk_id_sucursal', $sucursales ?? [], ['class'=>'select2','disabled'=>!Route::currentRouteNamed(currentRouteName('create')),'data-url'=>ApiAction('administracion.sucursales')])}}
        		</div>
        	</div>
    	</div>
	
		<div class="card col-lg-5">
			<div class="card-header row">
				<h5 class="col-md-12 text-center">CFDI Relacionados</h5>
    			<div class="form-group col-md-12">
        			{{Form::cSelect('* Tipo Relacion','fk_id_tipo_relacion', $tiposrelacion ?? [],['data-url'=>companyAction('Ventas\NotasCreditoClientesController@getProductosRelacionados')])}}
        		</div>
        		<div class="form-group col-md-5">
        			{!!Form::cSelect('* Factura','fk_id_factura_relacion', $facturasrelacionadas ?? [],['class'=>'select2'])!!}
        		</div>
				<div class="form-group col-md-2 d-flex align-items-center justify-content-center">
					<span>O</span>
				</div>
				<div class="form-group col-md-5">
					{{Form::cSelect('* Nota Cargo','fk_id_nota_cargo_relacion', $notascargorelacionadas ?? [],['class'=>'select2'])}}
				</div>
        		@if(!Route::currentRouteNamed(currentRouteName('view')))
        		<div class="form-group col-md-12 my-2">
					<div class="sep sepBtn">
						<button id="agregarRelacion" class="btn btn-primary btn-large btn-circle" data-placement="bottom" data-delay="100" data-tooltip="Agregar" data-toggle="tooltip" data-action="add" title="Agregar" type="button"><i class="material-icons">add</i></button>
					</div>
				</div>
				@endif
        	</div>
        	<div class="card-body row table-responsive">
        		<table class="table highlight mt-3" id="detalleRelaciones">
					<thead>
						<tr>
							<th>Tipo Relacion</th>
							<th>Documento</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					@if(isset($data->relaciones)) 
						@foreach($data->relaciones->where('eliminar',0) as $row=>$detalle)
						<tr>
							<td>
								{!! Form::hidden('',$row,['class'=>'index']) !!}
								{!! Form::hidden('relations[has][relaciones]['.$row.'][id_relacion_cfdi_cliente]',$detalle->id_relacion_cfdi_cliente) !!}
								{!! Form::hidden('relations[has][relaciones]['.$row.'][fk_id_tipo_relacion]',$detalle->fk_id_tipo_relacion,['class'=>'fk_id_tipo_relacion']) !!}
                                {!! Form::hidden('relations[has][relaciones]['.$row.'][fk_id_tipo_documento]',$detalle->fk_id_tipo_documento,['class'=>'fk_id_tipo_documento']) !!}
								{{'('.$detalle->tiporelacion->tipo_relacion.')'.$detalle->tiporelacion->descripcion}}
							</td>
							<td>
								{!! Form::hidden('relations[has][relaciones]['.$row.'][fk_id_tipo_documento_relacionado]',$detalle->fk_id_tipo_documento_relacionado,['class'=>'tipo_documento']) !!}
								{!! Form::hidden('relations[has][relaciones]['.$row.'][fk_id_documento_relacionado]',$detalle->fk_id_documento_relacionado) !!}
								{{$detalle->documento->uuid}}
							</td>
							<td>
    							@if(!Route::currentRouteNamed(currentRouteName('view')))
    							<button class="btn is-icon text-primary bg-white" type="button" data-delay="50" onclick="borrarFila(this,'cfdi')"> <i class="material-icons">delete</i></button>
    							@endif
							</td>
						</tr>
						@endforeach
					@endif
					</tbody>
				</table>
        	</div>
		</div>
	</div>
		
	<div class="card z-depth-1-half my-3">
		<div class="card-header">
			<div class="row py-2">
				<div class="form-group col-md-12">
					<div id="loadingfk_id_producto" class="w-100 h-100 text-center text-white align-middle loadingData" style="display: none">
						Cargando datos... <i class="material-icons align-middle loading">cached</i>
					</div>
					{{Form::cSelect('* Producto','fk_id_producto', $productos ?? [], ['class'=>'select2'])}}
				</div>
				<div class="form-group col-md-3">
					{{Form::cNumber('* Cantidad','cantidad')}}
				</div>
				<div class="form-group col-md-3">
					{{Form::cNumber('* Precio Unitario','precio_unitario')}}
				</div>
				<div class="form-group col-md-3">
					{{Form::cNumber('* Descuento','descuento_producto')}}
				</div>
				<div class="form-group col-md-3">
					{{Form::cSelect('* Impuesto','fk_id_impuesto', $impuestos ?? [], ['class'=>'select2','data-url'=>ApiAction('administracion.impuestos')])}}
				</div>
				@if(!Route::currentRouteNamed(currentRouteName('view')))
					<div class="form-group col-md-12 my-2">
						<div class="sep sepBtn">
							<button id="agregar-concepto" class="btn btn-primary btn-large btn-circle" data-placement="bottom" data-delay="100" data-tooltip="Agregar" data-toggle="tooltip" data-action="add" title="Agregar" type="button"><i class="material-icons">add</i></button>
						</div>
					</div>
				@endif
			</div>
    	</div>
    	<div class="card-body row table-responsive">
    		<table class="table highlight mt-3" id="tConceptos">
        		<thead>
    				<tr>
						<th>Factura Referencia</th>
    					<th>Clave Producto</th>
    					<th>Codigo</th>
    					<th>Concepto</th>
    					<th>Unidad Medida</th>
    					<th>Cantidad</th>
    					<th>Precio Unitario</th>
    					<th>Descuento</th>
    					<th>Impuesto</th>
    					<th>Pedimento</th>
    					<th>Cuenta Predial</th>
    					<th>Importe</th>
    					<th></th>
    				</tr>
    			</thead>
    			<tbody>
    			@if(isset($data->detalle)) 
    				@foreach($data->detalle->where('eliminar',0) as $key=>$detalle)
    				<tr>
						<td>
							{{$detalle->documentobase->serie.' - '.$detalle->documentobase->folio}}
							{{Form::hidden('',$key,['class'=>'index'])}}
							{{Form::hidden('relations[has][detalle]['.$key.'][fk_id_documento_base]',$detalle->fk_id_documento_base,['class'=>'factura'])}}
							{{Form::hidden('relations[has][detalle]['.$key.'][fk_id_tipo_documento_base]',$detalle->fk_id_tipo_documento_base,['class'=>'tipo_documento'])}}
						</td>
    					<td>
    						{!! Form::hidden('relations[has][detalle]['.$key.'][id_documento_detalle]',$detalle->id_documento_detalle,['class'=>'id_documento_detalle']) !!}
    						{{$detalle->claveproducto->clave_producto_servicio}}
    						{!! Form::hidden('relations[has][detalle]['.$key.'][fk_id_clave_producto_servicio]',$detalle->fk_id_clave_producto_servicio,['class'=>'fk_id_clave_producto_servicio']) !!}
    					</td>
    					<td>
    						{{$detalle->sku->sku}}
    						{!! Form::hidden('relations[has][detalle]['.$key.'][fk_id_sku]',$detalle->fk_id_sku) !!}
    					</td>
    					<td>
    						{{$detalle->upc->descripcion}}<br>
    						{{$detalle->sku->descripcion}}
    					</td>
    					<td>
    						{{$detalle->unidadmedida->clave_unidad.' - '.$detalle->unidadmedida->descripcion}}
    					</td>
    					<td>
    						{{number_format($detalle->cantidad,0)}}
							{{Form::hidden('',number_format($detalle->cantidad,0),['class'=>'cantidad'])}}
    					</td>
    					<td>
    						${{number_format($detalle->precio_unitario,2,'.','')}}
							{{Form::hidden('',number_format($detalle->precio_unitario,2,'.',''),['class'=>'precio_unitario'])}}
    					</td>
    					<td>
    						${{number_format($detalle->descuento,2,'.','')}}
							{{Form::hidden('',number_format($detalle->descuento,2,'.',''),['class'=>'descuento'])}}
    					</td>
    					<td>
							<input type="hidden" value="{{$detalle->impuestos->descripcion}}" class="tipo_impuesto">
							<input type="hidden" class="porcentaje" value="{{$detalle->impuestos->porcentaje}}">
							<input type="hidden" class="impuesto" value="{{number_format($detalle->impuesto,2,'.','')}}">
							<span>{{$detalle->impuestos->impuesto}}</span><br><span style="font-size: 11px"><b>${{number_format($detalle->impuesto,2,'.','')}}</b></span>
    					</td>
    					<td>
    						{{Form::cText('','relations[has][detalle]['.$row.'][pedimento]',['class'=>'pedimento'],$detalle->pedimento)}}
    					</td>
    					<td>
    						{{Form::cText('','relations[has][detalle]['.$row.'][cuenta_predial]',['class'=>'cuenta_predial'],$detalle->cuenta_predial)}}
    					</td>
    					<td>
    						${{number_format($detalle->importe,2,'.','')}}
							{{Form::hidden('',number_format($detalle->importe,2,'.',''),['class'=>'total'])}}
						</td>
    					<td>
        					@if(!Route::currentRouteNamed(currentRouteName('view')))
        					<button class="btn is-icon text-primary bg-white" type="button" data-delay="50" onclick="borrarFila(this,'total')"> <i class="material-icons">delete</i></button>
        					@endif
        				</td>
    				</tr>
    				@endforeach
    			@endif
    			</tbody>
    		</table>
    	</div>
    	<div class="card-footer">
    		<table class="table highlight mt-3 float-right w-25 text-right" id="tContactos">
    			<tbody>
				@notroute(['index'])
					<tr>
						<th>TOTAL DESCUENTOS</th>
						<td>
							{{Form::hidden('descuento',number_format($data->descuento ?? null,2,'.',''),['id'=>'descuento'])}}
							<span id="descuento_span">{!! number_format($data->descuento ?? null,2,'.','')!!}</span>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
    					<th>SUBTOTAL</th>
						<td>{{Form::hidden('subtotal',number_format($data->subtotal ?? null,2,'.',''),['id'=>'subtotal'])}}<span id="subtotal_span">{{number_format($data->subtotal ?? null,2,'.','')}}</span></td>
    					<td>&nbsp;</td>
    				</tr>
    				<tr id="impuestos_factura" data-toggle="collapse" data-target="#impuestos_accordion" class="clickable">
    					<th><button type="button" data-tooltip="Ver descripción de impuestos" data-toggle="tooltip" title="Ver descripción de impuestos" class="btn btn-secondary is-icon"><i class="material-icons add">add</i></button> IMPUESTOS</th>
    					<td>{{Form::hidden('impuestos',null,['id'=>'impuestos'])}}<span id="impuesto_label">{{number_format($data->impuestos ?? null,2,'.','')}}</span></td>
    					<td>&nbsp;</td>
    				</tr>
					<tr>
						<td colspan="3">
							<div id="impuestos_accordion" class="collapse">
								<table id="impuestos_descripcion" class="w-100 text-right">
									<tbody>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
    				<tr>
    					<th>TOTAL</th>
    					<td>{{Form::hidden('total',null,['id'=>'total'])}}<span id="total_span">{{number_format($data->total ?? null,2,'.','')}}</span></td>
    					<td>&nbsp;</td>
    				</tr>
    			</tbody>
				@endif
			</table>
    	</div>
	</div>
@endsection

@inroute(['edit','create'])
    @section('left-actions')
		{{ Form::button(cTrans('forms.save_stamp','Guardar y Timbrar'), ['id'=>'timbrar','type' =>'submit', 'class'=>'btn btn-info progress-button']) }}
    @endsection
@endif