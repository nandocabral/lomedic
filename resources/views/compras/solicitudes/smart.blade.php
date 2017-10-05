@section('header-top')
	<link rel="stylesheet" href="{{ asset('vendor/vanilla-datatables/vanilla-dataTables.css') }}">
	<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/pickadate/default.css') }}">
	<link rel="stylesheet" href="{{ asset('css/pickadate/default.date.css') }}">
@endsection
@section('header-bottom')
	@parent
	{{--<script type="text/javascript" src="{{ asset('js/jquery.ui.autocomplete2.js') }}"></script>--}}
	<script type="text/javascript" src="{{ asset('js/pickadate/picker.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/pickadate/picker.date.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/pickadate/translations/es_Es.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/toaster.js') }}"></script>
	<script src="{{ asset('vendor/vanilla-datatables/vanilla-dataTables.js') }}"></script>
	@if(!Route::currentRouteNamed(currentRouteName('index')))
		<script type="text/javascript" src="{{ asset('js/solicitudes_compras.js') }}"></script>
	@endif
@endsection

@section('form-actions')
	<div class="text-right col-md-12 mb-3">
		{{ Form::button('Guardar', ['type' =>'submit', 'class'=>'btn btn-primary']) }}
		@if (Route::currentRouteNamed(currentRouteName('show')))
			{!! HTML::decode(link_to(companyAction('impress',['id'=>$data->id_solicitud]), '<i class="material-icons">print</i> Imprimir', ['class'=>'btn btn-info imprimir'])) !!}
			@can('edit', currentEntity())
				@if($data->fk_id_estatus_solicitud == 1 && !Route::currentRouteNamed(currentRouteName('edit')))
					{!! HTML::decode(link_to(companyRoute('edit'), 'Editar', ['class'=>'btn btn-info'])) !!}
				@endif
			@endcan
		@endif
		{!! HTML::decode(link_to(companyRoute('index'), 'Cerrar', ['class'=>'btn btn-default '])) !!}
	</div>
@endsection

@section('content-width','mt-3')

@section('form-content')
{{ Form::setModel($data) }}
{{--{{dd($validator->view('detalle-form'))}}--}}
{{--{{dd($reglasdetalles->view('detalle-form'))}}--}}
	<div class="row">
		<div class="form-group col-md-4 col-sm-6">
	{{--		{!! Form::text(array_has($data,'fk_id_solicitante')?'solicitante_formated':'solicitante',null, ['id'=>'solicitante','autocomplete'=>'off','data-url'=>companyAction('RecursosHumanos\EmpleadosController@obtenerEmpleados'),'data-url2'=>companyAction('RecursosHumanos\EmpleadosController@obtenerEmpleado')]) !!}--}}
			{{ Form::label('fk_id_solicitante', '* Solicitante') }}
			{!! Form::select('fk_id_solicitante',isset($empleados)?$empleados:[],null,['id'=>'fk_id_solicitante','data-url'=>companyAction('RecursosHumanos\EmpleadosController@obtenerEmpleado'),'class'=>'form-control','style'=>'width:100%']) !!}
			{{ $errors->has('fk_id_solicitante') ? HTML::tag('span', $errors->first('fk_id_solicitante'), ['class'=>'help-block deep-orange-text']) : '' }}
			{{Form::hidden('id_solicitante',null,['id'=>'id_solicitante','data-url'=>companyAction('Administracion\SucursalesController@sucursalesEmpleado',['id'=>'?id'])])}}
		</div>
		<div class="form-group input-field col-md-4 col-sm-6">
			{{--Se utilizan estas comprobaciones debido a que este campo se carga dinámicamente con base en el solicitante seleccionado y no se muestra el que está por defecto sin esto--}}
			@if(Route::currentRouteNamed(currentRouteName('edit')))
				{{ Form::label('fk_id_sucursal', '* Sucursal') }}
				{!! Form::select('fk_id_sucursal', isset($sucursalesempleado)?$sucursalesempleado:[],null, ['id'=>'fk_id_sucursal_','class'=>'form-control','style'=>'width:100%']) !!}
				{!! Form::hidden('sucursal_defecto',$data->fk_id_sucursal,['id'=>'sucursal_defecto']) !!}
			@elseif(Route::currentRouteNamed(currentRouteName('show')))
				{{ Form::label('fk_id_sucursal', '* Sucursal') }}
				{!! Form::text('sucursal',$data->sucursales->where('id_sucursal',$data->fk_id_sucursal)->first()->nombre_sucursal,['class'=>'form-control','style'=>'width:100%']) !!}
			@elseif(Route::currentRouteNamed(currentRouteName('create')))
				{{ Form::label('fk_id_sucursal', '* Sucursal') }}
				{!! Form::select('fk_id_sucursal', isset($sucursalesempleado)?$sucursalesempleado:[],null, ['id'=>'fk_id_sucursal_','class'=>'form-control','style'=>'width:100%']) !!}
			@endif
			{{ $errors->has('fk_id_sucursal') ? HTML::tag('span', $errors->first('fk_id_sucursal'), ['class'=>'help-block deep-orange-text']) : '' }}
		</div>
		<div class="form-group input-field col-md-2 col-sm-6">
			{{ Form::label('fecha_necesidad', '* ¿Para cuándo se necesita?') }}
			{!! Form::text('fecha_necesidad',null,['id'=>'fecha_necesidad','class'=>'datepicker form-control','value'=>old('fecha_necesidad'),'placeholder'=>'Selecciona una fecha']) !!}
		</div>
		<div class="form-group input-field col-md-2 col-sm-6">
			{{--{!! Form::select('fk_id_estatus_solicitud', \App\Http\Models\Compras\EstatusSolicitudes::all()->pluck('estatus','id_estatus'),null, ['id'=>'fk_id_sucursal']) !!}--}}
			{{ Form::label('estatus_solicitud', '* Estatus de la solicitud') }}
			@if(Route::currentRouteNamed(currentRouteName('edit')) || Route::currentRouteNamed(currentRouteName('show')))
				{!! Form::text('estatus_solicitud',$data->estatus->estatus,['disabled','class'=>'form-control']) !!}
			@elseif(Route::currentRouteNamed(currentRouteName('create')))
				{!! Form::text('estatus_solicitud','Abierto',['disabled','class'=>'form-control']) !!}
			@endif
		</div>
		{{--Si la solicitud está cancelada--}}
			@if(isset($data->fk_id_estatus_solicitud) && $data->fk_id_estatus_solicitud ==3)
				<div class="form-group input-field col-md-3 col-sm-12">
					{{ Form::label('fecha_cancelacion','Fecha de cancelación') }}
					{!! Form::text('fecha_cancelacion',$data->fecha_cancelacion,['disabled','class'=>'form-control']) !!}
				</div>
				<div class="form-group input-field col-md-9 col-sm-12">
					{{ Form::label('motivo_cancelacion','Motivo de la cancelación') }}
					{!! Form::text('motivo_cancelacion',$data->motivo_cancelacion,['disabled','class'=>'form-control']) !!}
				</div>
			@endif
	</div>
	<div class="row">
		<div class="col-sm-12">
			<h3>Detalle de la solicitud</h3>
			<div class="card">
				<div class="card-header">
					<fieldset name="detalle-form" id="detalle-form">
						<div class="row">
							<div class="form-group input-field col-md-3 col-sm-6">
								{{Form::label('fk_id_sku','SKU')}}
								{!!Form::select('fk_id_sku',isset($skus)?$skus:[],null,['id'=>'fk_id_sku','class'=>'form-control','style'=>'width:100%'])!!}
							</div>
							<div class="form-group input-field col-md-3 col-sm-6">
								{{Form::label('fk_id_upc','Código de barras')}}
								{!! Form::select('fk_id_upc',[],null,['id'=>'fk_id_upc','disabled',
								'data-url'=>companyAction('Inventarios\UpcsController@obtenerUpcs',['id'=>'?id']),
								'class'=>'form-control','style'=>'width:100%']) !!}
							</div>
							<div class="form-group input-field col-md-3 col-sm-6">
								{{Form::label('fk_id_proveedor','Proveedor')}}
								{!!Form::select('fk_id_proveedor',[],null,['id'=>'fk_id_proveedor','autocomplete'=>'off','class'=>'validate form-control','style'=>'width:100%'])!!}
							</div>
							<div class="form-group input-field col-md-3 col-sm-6">
								{{Form::label('fk_id_proyecto','Proyecto')}}
								{!!Form::select('fk_id_proyecto',isset($proyectos)?$proyectos:[],null,['id'=>'fk_id_proyecto','autocomplete'=>'off','class'=>'validate form-control','style'=>'width:100%',])!!}
							</div>
							<div class="form-group input-field col-md-2 col-sm-4">
								{{ Form::label('fecha_necesario', '* ¿Para cuándo se necesita?') }}
								{!! Form::text('fecha_necesario',null,['id'=>'fecha_necesario','class'=>'datepicker form-control','value'=>old('fecha_necesario'),'placeholder'=>'Selecciona una fecha']) !!}
							</div>
							<div class="form-group input-field col-md-2 col-sm-4">
								{{Form::label('cantidad','Cantidad')}}
								{!! Form::text('cantidad','1',['id'=>'cantidad','min'=>'1','class'=>'validate form-control cantidad','autocomplete'=>'off']) !!}
							</div>
							<div class="form-group input-field col-md-2 col-sm-4">
								{{Form::label('fk_id_unidad_medida','Unidad de medida')}}
								{!! Form::select('fk_id_unidad_medida',
								isset($unidadesmedidas) ? $unidadesmedidas : [],
								null,['id'=>'fk_id_unidad_medida','class'=>'form-control','style'=>'width:100%']) !!}
							</div>
							<div class="form-group input-field col-md-2 col-sm-6">
								{{Form::label('fk_id_impuesto','Tipo de impuesto')}}
								{{--{{dd($impuestos)}}--}}
								{!! Form::select('fk_id_impuesto',[]
									,null,['id'=>'fk_id_impuesto',
									'data-url'=>companyAction('Administracion\ImpuestosController@obtenerImpuestos'),
									'class'=>'form-control','style'=>'width:100%']) !!}
								{{Form::hidden('impuesto',null,['id'=>'impuesto'])}}
							</div>
							<div class="form-group input-field col-md-2 col-sm-6">
								{{Form::label('precio_unitario','Precio unitario',['class'=>'validate'])}}

								{!! Form::text('precio_unitario',old('precio_unitario'),['id'=>'precio_unitario','placeholder'=>'0.00','class'=>'validate form-control precio_unitario','autocomplete'=>'off']) !!}
							</div>
							<div class="col-sm-12 text-center">
								<div class="sep">
									<div class="sepBtn">
								<button style="width: 4em; height:4em; border-radius:50%;" class="btn btn-primary btn-large tooltipped "
										data-position="bottom" data-delay="50" data-tooltip="Agregar" type="button" onclick="agregarProducto()" id="agregar"><i
											class="material-icons">add</i></button>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
				</div>
			    <div class="card-body">
					<table id="productos" class="table-responsive highlight" data-url="{{companyAction('Compras\SolicitudesController@store')}}"
					data-delete="{{companyAction('Compras\DetalleSolicitudesController@destroyMultiple')}}"
					data-impuestos="{{companyAction('Administracion\ImpuestosController@obtenerImpuestos')}}"
							data-porcentaje="{{companyAction('Administracion\ImpuestosController@obtenerPorcentaje',['id'=>'?id'])}}">
						<thead>
							<tr>
								<th id="idsku">SKU</th>
								<th id="idupc">Código de Barras</th>
								<th id="idproveedor">Proveedor</th>
								<th>Fecha necesidad</th>
								<th id="idproyecto" >Proyecto</th>
								<th>Cantidad</th>
								<th id="idunidadmedida" >Unidad de medida</th>
								<th id="idimpuesto" >Tipo de impuesto</th>
								<th>Precio unitario</th>
								<th>Total</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
						@if( isset( $detalles ) )
							@foreach( $detalles as $detalle)
								<tr>
									<td>
										{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][id_solicitud_detalle]',$detalle->id_solicitud_detalle) !!}
										{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fk_id_sku]',$detalle->fk_id_sku) !!}
										{{$detalle->sku->sku}}
									</td>
									<td>
										{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fk_id_upc]',$detalle->fk_id_upc) !!}
										{{$detalle->upc->descripcion}}
									</td>
									<td>
										{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fk_id_proveedor]',$detalle->fk_id_proveedor) !!}
									</td>
									<td>
										{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fecha_necesario]',$detalle->fecha_necesario) !!}
										{{$detalle->fecha_necesario}}</td>
									<td>
										@if(!Route::currentRouteNamed(currentRouteName('edit')))
											{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fk_id_proyecto]',$detalle->fk_id_proyecto) !!}
											{{$detalle->proyecto->proyecto}}
										@else
											{!! Form::select('detalles['.$detalle->id_solicitud_detalle.'][fk_id_proyecto]',
													isset($proyectos) ? $proyectos : null,
													$detalle->id_proyecto,['id'=>'detalles['.$detalle->id_solicitud_detalle.'][fk_id_proyecto]',
													'class'=>'detalle_select','style'=>'width:100%'])
											!!}
										@endif
									</td>
									<td>
										@if (!Route::currentRouteNamed(currentRouteName('edit')))
											{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][cantidad]',$detalle->cantidad) !!}
											{{$detalle->cantidad}}
										@else
											{!! Form::text('detalles['.$detalle->id_solicitud_detalle.'][cantidad]',$detalle->cantidad,
											['class'=>'form-control cantidad',
											'id'=>'cantidad'.$detalle->id_solicitud_detalle,
											'onkeypress'=>'total_producto_row('.$detalle->id_solicitud_detalle.',"old")']) !!}
										@endif
									</td>
									<td>
										{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fk_unidad_medida]',$detalle->fk_unidad_medida) !!}
										{{$detalle->unidad_medida->nombre}}
									</td>
									<td>
										@if (!Route::currentRouteNamed(currentRouteName('edit')))
											{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][fk_id_impuesto]',$detalle->fk_id_impuesto) !!}
											{{$detalle->impuesto->impuesto}}
										@else
											{!! Form::select('detalles['.$detalle->id_solicitud_detalle.'][fk_id_impuesto]',$impuestos,
													$detalle->fk_id_impuesto,['class'=>'detalle_select','style'=>'width:100%','id'=>'fk_id_impuesto'.$detalle->id_solicitud_detalle,
													'onchange'=>'total_producto_row('.$detalle->id_solicitud_detalle.',"old")'])
											!!}
										@endif
									</td>
									<td>
										@if(!Route::currentRouteNamed(currentRouteName('edit')))
											{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][precio_unitario]',$detalle->precio_unitario) !!}
											{{number_format($detalle->precio_unitario,2,'.','')}}
										@else
											{!! Form::text('detalles['.$detalle->id_solicitud_detalle.'][precio_unitario]',number_format($detalle->precio_unitario,2,'.','')
											,['class'=>'form-control precio_unitario','onkeypress'=>'total_producto_row('.$detalle->id_solicitud_detalle.',"old")',
											'id'=>'precio_unitario'.$detalle->id_solicitud_detalle]) !!}
										@endif
									</td>
									<td>
										@if (!Route::currentRouteNamed(currentRouteName('edit')))
											{!! Form::hidden('detalles['.$detalle->id_solicitud_detalle.'][total]',$detalle->total) !!}
											{{number_format($detalle->total,2,'.','')}}
										@else
											{!! Form::text('detalles['.$detalle->id_solicitud_detalle.'][total]',number_format($detalle->total,2,'.','')
											,['class'=>'form-control','id'=>'total'.$detalle->id_solicitud_detalle,'readonly'])!!}
										@endif
									<td>
										{{--Si se va a editar, agrega el botón para "eliminar" la fila--}}
										@if(Route::currentRouteNamed(currentRouteName('edit')) && $data->fk_id_estatus_solicitud == 1)
											<a href="#" class="btn-flat teal lighten-5 halfway-fab waves-effect waves-light"
											   type="button" data-item-id="{{$detalle->id_solicitud_detalle}}"
											   id="{{$detalle->id_solicitud_detalle}}" data-delay="50"
											   onclick="borrarFila_edit(this)" data-delete-type="single">
											<i class="material-icons">delete</i></a>
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
	</div>
@endsection

{{-- DONT DELETE --}}
@if (Route::currentRouteNamed(currentRouteName('index')))
	@section('smart-js')
		<script type="text/javascript">
            if ( sessionStorage.reloadAfterPageLoad ) {
                sessionStorage.clear();
                $.toaster({
                    priority: 'success', title: 'Exito', message: 'Solicitud cancelada',
                    settings:{'timeout': 5000, 'toaster':{'css':{'top':'5em'}}}
                });
            }
		</script>
	@parent
	<script type="text/javascript">
         rivets.binders['hide-delete'] = {
         	bind: function (el) {
         		if(el.dataset.fk_id_estatus_solicitud != 1)
         		{
         			$(el).hide();
         		}
         	}
         };
         rivets.binders['hide-update'] = {
             bind: function (el) {
                 if(el.dataset.fk_id_estatus_solicitud != 1)
                 {
                     $(el).hide();
                 }
             }
         };
		 @can('update', currentEntity())
             window['smart-model'].collections.itemsOptions.edit = {a: {
             'html': '<i class="material-icons">mode_edit</i>',
             'class': 'btn is-icon',
             'rv-get-edit-url': '',
             'rv-hide-update':''
         }};
		 @endcan
		@can('delete', currentEntity())
		window['smart-model'].collections.itemsOptions.delete = {a: {
			'html': '<i class="material-icons">not_interested</i>',
			'href' : '#',
			'class': 'btn is-icon',
			'rv-on-click': 'actions.showModalCancelar',
			'rv-get-delete-url': '',
			'data-delete-type': 'single',
			'rv-hide-delete':''
		}};
		@endcan
		window['smart-model'].actions.itemsCancel = function(e, rv, motivo){
		    if(!motivo.motivo_cancelacion){
                $.toaster({
                    priority : 'danger',
                    title : '¡Error!',
                    message : 'Por favor escribe un motivo por el que se está cancelando esta solicitud de compra',
                    settings:{
                        'timeout':10000,
                        'toaster':{
                            'css':{
                                'top':'5em'
                            }
                        }
                    }
                });
			}else{

		        let data = {motivo};
		        $.delete(this.dataset.deleteUrl,data,function (response) {
					if(response.success){
                        sessionStorage.reloadAfterPageLoad = true;
                        location.reload();
					}
                })
			}
        };
        window['smart-model'].actions.showModalCancelar = function(e, rv) {
            e.preventDefault();

            let modal = window['smart-modal'];
            modal.view = rivets.bind(modal, {
                title: '¿Estas seguro que deseas cancelar la solicitud?',
                content: '<form  id="cancel-form">' +
                '<div class="form-group">' +
                '<label for="recipient-name" class="form-control-label">Motivo de cancelación:</label>' +
                '<input type="text" class="form-control" id="motivo_cancelacion" name="motivo_cancelacion">' +
                '</div>' +
                '</form>',
                buttons: [
                    {button: {
                        'text': 'Cerrar',
                        'class': 'btn btn-secondary',
                        'data-dismiss': 'modal',
                    }},
                    {button: {
                        'html': 'Cancelar',
                        'class': 'btn btn-danger',
                        'rv-on-click': 'action',
                    }}
                ],
                action: function(e,rv) {
                    var formData = new FormData(document.querySelector('#cancel-form')), convertedJSON = {}, it = formData.entries(), n;

                    while(n = it.next()) {
                        if(!n || n.done) break;
                        convertedJSON[n.value[0]] = n.value[1];
                    }
                    console.log(convertedJSON);
                    window['smart-model'].actions.itemsCancel.call(this, e, rv,convertedJSON);
                }.bind(this),
                // Opcionales
                onModalShow: function() {

                    let btn = modal.querySelector('[rv-on-click="action"]');

                    // Copiamos data a boton de modal
                    for (var i in this.dataset) btn.dataset[i] = this.dataset[i];

                }.bind(this),
                // onModalHide: function() {}
            });
            // Abrimos modal
            $(modal).modal('show');
        };
	</script>
	@endsection

	@include('layouts.smart.index')
@endif

@if (Route::currentRouteNamed(currentRouteName('create')))
	@include('layouts.smart.create')
@endif

@if (Route::currentRouteNamed(currentRouteName('edit')))
	@include('layouts.smart.edit')
@endif

@if (Route::currentRouteNamed(currentRouteName('show')))
	@include('layouts.smart.show')
@endif