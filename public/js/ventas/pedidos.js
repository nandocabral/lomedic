$(document).ready(function () {
	$('#form-model').attr('enctype',"multipart/form-data");
    $.ajaxSetup({headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}});
    
	$('#fk_id_proyecto').on('change', function() {
		let contrato = $('#fk_id_contrato');
		
		if($(this).val() == '') {
			contrato.val('');
		}
		else {
    		$.ajax({
    		    async: true,
    		    url: contrato.data('url'),
    		    data: {'param_js':contratos_js,$id_proyecto:$(this).val()},
    		    dataType: 'json',
                success: function (data) {
                	$("#fk_id_contrato option").remove();
                	contrato.append('<option value="" disabled>Selecciona una Opción...</option>')
                    $.each(data[0].contratos, function(){
                    	contrato.append('<option value="'+this.id_contrato+'">'+this.num_contrato+'</option>')
                    });
                	contrato.val('');
                	contrato.prop('disabled', (data.length == 0)); 
    		    }
    		});
		}
	});
	
	$('#fk_id_localidad').on('change', function() {
		$('#fk_id_socio_negocio').trigger('change');
	});
	
	$('#fk_id_socio_negocio').on('change', function() {
		let val = $('#fk_id_socio_negocio option:selected').val();
		let localidad = $('#fk_id_localidad option:selected').val();
		let proyecto = $('#fk_id_proyecto');

		if($(this).val() == '') {
			proyecto.val('');
		}
		else {
    		$.ajax({
    		    async: true,
    		    url: proyecto.data('url'),
    		    data: {'param_js':proyectos_js,$fk_id_cliente:val},
    		    dataType: 'json',
                success: function (data) {
                	$("#fk_id_proyecto").empty();
                	proyecto.append('<option value="" disabled>Selecciona una Opción...</option>')
                    $.each(data, function(){
                    	proyecto.append('<option value="'+ this.id_proyecto +'">'+ this.proyecto +'</option>')
                    });
                	if(data.length !== 0) {
                		proyecto.val(modeldata.fk_id_proyecto);
                	}
                	proyecto.prop('disabled', (data.length == 0)); 
    		    }
    		});
    		
    		let sucursal = $('#fk_id_sucursal');
    		$.ajax({
    		    async: true,
    		    url: sucursal.data('url'),
    		    data: {'param_js':sucursales_js,$fk_id_cliente:val,$fk_id_localidad:localidad},
    		    dataType: 'json',
                success: function (data) {
                	$("#fk_id_sucursal").empty();
                	sucursal.append('<option value="" disabled>Selecciona una Opción...</option>')
                    $.each(data, function(){
                    	sucursal.append('<option value="'+this.id_sucursal+'">'+this.sucursal+'</option>')
                    });
                	sucursal.val('');
                	sucursal.prop('disabled', (data.length == 0)); 
    		    }
    		});
    		
    		//$('#detalleProductos tbody tr').remove();
    		$('#fk_id_clave_cliente_producto options').empty();
    		$('#fk_id_clave_cliente_producto').select2({
    			ajax: {
    			    url: $('#fk_id_clave_cliente_producto').data('url').replace('?id',val),
    			    processResults: function (data) {
    			    	return {
    			    		results: data
    			    	};
    			    }
				}
    		});
		}
	});

	$('#activo_upc').on('change',function () {
        if( !this.checked ){
            $( this ).parent().nextAll( "select" ).val(0).trigger('change').prop( "disabled", !this.checked ).empty();
        }else{
            if($('#fk_id_clave_cliente_producto').val()){
                $('#loadingfk_id_upc').show();
                let _url = $('#fk_id_upc').data('url').replace('?id',$('#fk_id_clave_cliente_producto').select2('data')[0].fk_id_sku);
                $('#fk_id_upc').empty();
                $.ajax({
                    url: _url,
                    dataType: 'json',
                    success: function (data) {
                        let option = $('<option/>');
                        option.val(0);
                        option.attr('disabled','disabled');
                        option.attr('selected','selected');
                        if (Object.keys(data).length == 0)
                            option.text('No se encontraron elementos');
                        else
                            option.text('...');
                        $('#fk_id_upc').attr('disabled',false).select2({
                            minimumResultsForSearch: 15,
                            data: data
                        }).prepend(option);
                        $('#loadingfk_id_upc').hide();
                    }
                });
            }else{
                $( this ).prop('checked',false);
                $( this ).parent().nextAll( "select" ).prop( "disabled", !this.checked );
                $.toaster({priority : 'danger',title : '¡Error!',message : 'Selecciona antes una Clave cliente producto',settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
            }
        }
    });
	
	$('#agregarProducto').on('click', function() {
		var i = $('#detalleProductos tbody tr').length;
		var row_id = i > 0 ? +$('#detalleProductos tbody tr:last').find('.index').val()+1 : 0;
		archivo = $('#file_xlsx').prop('files');
		if(archivo.length == 0) {
			validateDetail();
			if($('#form-model').valid()){
				cantidad = $('#cantidad').val();
				fk_id_clave = $('#fk_id_clave_cliente_producto option:selected').val();
				fk_id_sku = $('#fk_id_clave_cliente_producto').select2('data')[0].fk_id_sku;
				clave = $('#fk_id_clave_cliente_producto option:selected').text();
				descripcion = $('#fk_id_clave_cliente_producto').select2('data')[0].descripcionClave;
				fk_id_impuesto = $('#fk_id_clave_cliente_producto').select2('data')[0].fk_id_impuesto;
				precio_unitario = $('#fk_id_clave_cliente_producto').select2('data')[0].precio;
				descuento_producto = $('#descuento_detalle').val()/100;
				subtotal = 

				fk_id_upc = $('#fk_id_upc').val() == null ? '' : $('#fk_id_upc option:selected').val();
				
				upc = $('#fk_id_upc option:selected').text();
				
				importe = cantidad*precio_unitario+(cantidad*precio_unitario*descuento_producto);
				
				$('#detalleProductos').append('<tr>'+
						'<td><input class="index" name="relations[has][detalle]['+row_id+'][index]" type="hidden" value="'+row_id+'">'+
							'<input name="relations[has][detalle]['+row_id+'][cantidad]" type="hidden" value="'+cantidad+'">'+cantidad+'</td>'+
						'<td><input name="relations[has][detalle]['+row_id+'][fk_id_clave_cliente_producto]" type="hidden" value="'+fk_id_clave+'">'+clave+'</td>'+
						'<td>'+descripcion+'</td>'+
						'<td><input name="relations[has][detalle]['+row_id+'][fk_id_sku]" type="hidden" value="'+fk_id_sku+'"><input name="relations[has][detalle]['+row_id+'][fk_id_upc]" type="hidden" value="'+fk_id_upc+'">'+upc+'</td>'+
						'<td>'+upc+'</td>'+
						'<td><input name="relations[has][detalle]['+row_id+'][precio_unitario]" type="hidden" value="'+precio_unitario+'">'+precio_unitario+'</td>'+
						'<td><input name="relations[has][detalle]['+row_id+'][fk_id_impuesto]" type="hidden" value="'+fk_id_impuesto+'">'+'<input style="width:60px; text-align:center;" class="form-control" readonly name="relations[has][detalle]['+row_id+'][descuento]" value="'+$('#descuento_detalle').val()+'"></td>'+
						'<td><input name="relations[has][detalle]['+row_id+'][importe]" type="hidden" value="'+importe+'">'+importe+'</td>'+
						'<td><button class="btn is-icon text-primary bg-white" type="button" data-delay="50" onclick="borrarFila(this)" data-tooltip="Anexo"> <i class="material-icons">delete</i></button></td>'+
					'</tr>');
				$.toaster({priority:'success',title:'¡Correcto!',message:'El producto se agrego correctamente.',settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
			}
		}
		else {
			$('.loadingtabla').show();
			
			var xlsx = $('#file_xlsx').prop('files')[0];
			var formData = new FormData();
			formData.append('file', xlsx);
			formData.append('fk_id_cliente', $('#fk_id_socio_negocio').val());
			formData.append('fk_id_proyecto', $('#fk_id_proyecto').val());
			
			$.ajax({
				url: $('#file_xlsx').data('url'),
				type: 'POST',
				data: formData,
				contentType: false,
				cache: false,
				processData: false,
				dataType: 'json',
				success: function (data) {
					let messages = '';
					$.each(data.error, function (index, value) {
						messages += '<br>Error en fila '+(parseInt(index) + parseInt(1))+':';
						if(value['cantidad'])
							messages += ' cantidad no debe ser menor a 0,';
						if(value['clave_producto'])
							messages += ' clave de producto no valida,';
						if(value['upc'])
							messages += ' upc no valido.';
					});
					
					if(messages != '') {
						$('.loadingtabla').hide();
						$.toaster({priority: 'danger',title: 'Las siguientes filas contienen error.',message: '<br>'+messages,settings: {'toaster': {'css': {'top': '5em'}}, 'donotdismiss': ['danger']}});
					}
					else {
						$.each(data.data, function (index, value) {
							var i = $('#detalleProductos tr').length;
							var row_id = i > 0 ? +$('#detalleProductos tr:last').find('.index').val()+1 : 0;
							
							cantidad = value['cantidad'];
							descuento = value['descuento']/100;
							fk_id_clave = value['id_clave_cliente_producto'];
							fk_id_sku = value['fk_id_sku'];
							clave = value['clave_cliente_producto'];
							descripcion = value['descripcion_clave'];
							fk_id_upc = value['fk_id_upc'] ? value['fk_id_upc'] : '';
							upc = value['upc'] ? value['upc'] : '';
							fk_id_impuesto = value['fk_id_impuesto'] ? value['fk_id_impuesto'] : 0;
							descripcion_upc = value['descripcion_upc'] ? value['descripcion_upc'] : '';
							precio_unitario = value['precio'];
							importe = cantidad*precio_unitario+(cantidad*precio_unitario*descuento);
							
							$('#detalleProductos').append('<tr>'+
								'<td><input class="index" name="relations[has][detalle]['+row_id+'][index]" type="hidden" value="'+row_id+'">'+
									'<input name="relations[has][detalle]['+row_id+'][cantidad]" type="hidden" value="'+cantidad+'">'+cantidad+'</td>'+
								'<td><input name="relations[has][detalle]['+row_id+'][fk_id_clave_cliente_producto]" type="hidden" value="'+fk_id_clave+'">'+clave+'</td>'+
								'<td>'+descripcion+'</td>'+
								'<td><input name="relations[has][detalle]['+row_id+'][fk_id_sku]" type="hidden" value="'+fk_id_sku+'"><input name="relations[has][detalle]['+row_id+'][fk_id_upc]" type="hidden" value="'+fk_id_upc+'">'+upc+'</td>'+
								'<td>'+descripcion_upc+'</td>'+
								'<td><input name="relations[has][detalle]['+row_id+'][precio_unitario]" type="hidden" value="'+precio_unitario+'">'+precio_unitario+'</td>'+
								'<td><input name="relations[has][detalle]['+row_id+'][fk_id_impuesto]" type="hidden" value="'+fk_id_impuesto+'"><input style="width:60px; text-align:center;" class="form-control" readonly name="relations[has][detalle]['+row_id+'][descuento]" value="'+(descuento*100)+'"></td>'+
								'<td><input name="relations[has][detalle]['+row_id+'][importe]" type="hidden" value="'+importe+'">'+importe+'</td>'+
								'<td><button class="btn is-icon text-primary bg-white" type="button" data-delay="50" onclick="borrarFila(this)" data-tooltip="Anexo"> <i class="material-icons">delete</i></button></td>'+
							'</tr>');
						});
						$('.loadingtabla').hide();
						$.toaster({priority: 'success', title: '!Correcto!', message: 'Productos importados con Exito',settings: {'timeout': 10000, 'toaster': {'css': {'top': '5em'}}}});
					}
				},
				error: function () {
					$('.loadingtabla').hide();
					$.toaster({priority: 'danger', title: '¡Error!', message: 'Por favor verifica que el layout sea correcto',settings: {'timeout': 10000, 'toaster': {'css': {'top': '5em'}}}});
				}
			});
			$('#file_xlsx').val('');
		}
		
	});
	
	$('#agregarAnexo').on('click', function() {
    	var i = $('#detalleAnexos tr').length;
        var row_id = i > 0 ? +$('#detalleAnexos tr:last').find('#index').val()+1 : 0;
		
		nombre = $('#nombre_archivo').val();
		archivo = $("#archivo").prop('files');
		
		if(nombre == '') {
			$.toaster({priority:'danger',title:'¡Error!',message:'Debe introducir el nombre para el documento.',settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
		}
		else if($("#archivo").length == 0) {
			$.toaster({priority:'danger',title:'¡Error!',message:'Selecciona un archivo.',settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
		}
		else {
			$('#detalleAnexos').append('<tr>'+
				'<td><input class="index" name="relations[has][anexos]['+row_id+'][index]" type="hidden" value="'+row_id+'">'+
					'<input name="relations[has][anexos]['+row_id+'][nombre]" type="hidden" value="'+nombre+'">'+nombre+'</td>'+
				'<td><input name="relations[has][anexos]['+row_id+'][file]" type="file" id="fileAnexo-'+row_id+'" style="display:none">'+archivo[0].name+'</td>'+
				'<td><button class="btn is-icon text-primary bg-white" type="button" data-delay="50" onclick="borrarFila(this)" data-tooltip="Anexo"> <i class="material-icons">delete</i></button></td>'+
			'</tr>');
			$('#fileAnexo-'+row_id).prop('files',archivo);
			$.toaster({priority:'success',title:'¡Correcto!',message:'El archivo se agrego correctamente.',settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
		}
	});
	
	$('#fk_id_moneda').on('change', function() {
		$('#tipo_cambio').attr('readonly',$('#fk_id_moneda').val() == 100);
		
		if($('#fk_id_moneda').val() == 100)
			$('#tipo_cambio').val('1.00');
		else
			$('#tipo_cambio').val('');
	});
    $(document).on('submit',function (e) {
        if($('#detalleProductos tbody tr').length == 0){
            e.preventDefault();
            $.toaster({
                priority: 'danger', title: '¡Advertencia!', message: 'El pedido debe tener al menos un producto',
                settings: {'timeout': 5000, 'toaster': {'css': {'top': '5em'}}}
            });
        }
    });
});

function borrarFila(el) {
    $(el).parent().parent('tr').remove();
    $.toaster({priority:'success',title:'¡Correcto!',message:'Se ha eliminado correctamente el '+$(el).data('tooltip'),settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
}

function validateDetail() {
    $('#cantidad').rules('add',{
        required: true,
        digits: true,
        range: [1,9999],
        messages:{
            required: 'Ingresa una cantidad',
            digits: 'El valor debe ser un entero',
            range: 'El número debe ser entre 1 y 9999'
        }
	});
	$('#fk_id_clave_cliente_producto').rules('add',{
		required:true,
		messages:{
			required: 'Seleccione un producto'
		}
	})
    $.validator.addMethod( "greaterThan", function( value, element, param ) {
        return value > param;
    }, "Please enter a greater value." );
    $('#descuento_detalle').rules('add',{
        number: true,
        greaterThan:-1,
        messages:{
            number: 'El campo debe ser un número',
            greaterThan: 'El número debe ser positivo, es decir, mayor a cero',
        }
    });
}