var primeracarga = true;
$(document).ready(function(){

    $('[data-toggle]').tooltip();
    $('#fk_id_upc').select2({
        disabled:true,
        placeholder:"Seleccione el proveedor..."
    })
    initSelects();
    totalOferta();

    //Para obtener los IVAS con sus porcentajes y IDs
    $.ajax({
        url: $('#fk_id_impuesto').data('url'),
        dataType:'json',
        success:function (data) {
            $('.idImpuestoRow').each(function (index,select) {
                var id_default = $(select).data('default');
                var data2 = [];
                $.each(data,function (index,option) {
                    var datadefault = false;
                    if(id_default == option.id)
                        datadefault = true;

                    if(option.id != 0){
                        if(datadefault)
                            option.selected = true;
                        data2.push(option);
                    }
                });
                $(select).select2({
                    minimumResultsForSearch:'Infinity',
                    data:data2
                })
            });

            $('#fk_id_impuesto').select2({
                minimumResultsForSearch:'Infinity',
                data:data
            });
        }
    });

    $('#fk_id_proveedor').on('change',function(){
        let _url = $(this).data('url');
        let thisVal = $(this).val();
        if(thisVal > 0){
            $.ajax({
                url: _url,
                data:{
                    'param_js':upcs_js,
                    $fk_id_socio_negocio:thisVal
                },
                dataType: "json",
                success: function (response) {
                    console.log(response);
                    const dataUPC = response.forEach(element => {
                        console.log(element);
                        return `<option value="${element.id_upc}" data-desc="${element.descripcion}">${element.upc}</option>`;    
                    });
                    $("#fk_id_upc").select2({
                        disabled:false,
                        data:dataUPC,
                    });
                }                  
            });
        } else {
            $('#fk_id_upc').select2({
                disabled:true,
                placeholder: "Seleccione primero el proveedor..."
            });
            $.toaster({priority : 'warning',title : '¡Lo sentimos!',message : 'Al parecer el proveedor no cuenta con UPC(s) dados de alta.',
            settings:{'timeout':3000,'toaster':{'css':{'top':'5em'}}}}); 
        }

        if(primeracarga && $('#productos tbody tr').length > 0) {
            var skus = [];
            var upcs = [];
            $('#productos tbody tr').each(function () {
                var sku = $(this).find('.fk_id_sku').val();
                if($.inArray(sku,skus) == -1)
                    skus.push(sku);
                var upc = $(this).find('.fk_id_upc').val();
                if($.inArray(upc,upcs) == -1)
                    upcs.push(upc);
            });
            $.ajax({
                url: $('#fk_id_sku').data('url-tiempo_entrega'),
                data: {
                    'param_js': tiempo_entrega_js,
                    $fk_id_sku: skus.toString(),
                    $fk_id_socio_negocio: $(this).val(),
                    $fk_id_upc: upcs.toString()
                },
                dataType: 'JSON',
                success: function (tiempo_entrega) {
                    $('.tiempo_entrega').val(tiempo_entrega[0].tiempo_entrega);
                    tiemposentrega();
                }
            });
            primeracarga = false;
        }else{
            primeracarga = false;
        }
    });

    $('#agregar').on('click',function () {
       agregarProducto();
    });

    $(document).on('submit',function (e) {
        if($('#productos tbody tr').length < 1){
            e.preventDefault();
            $.toaster({
                priority: 'danger', title: '¡Advertencia!', message: 'La oferta de compra debe tener al menos un producto',
                settings: {'timeout': 5000, 'toaster': {'css': {'top': '5em'}}}
            });
        }
    });

    $('.precioRow, .descuentoRow, .idImpuestoRow').on('change keyup',function (event) {//Para cuando se modifica una fila que le pertenece a una solicitud si se está en el create
        var row = $(event.target).closest('tr');
        var cantidad = +$(row).find('.cantidadRow').val();
        var precio = +$(row).find('.precioUnitarioRow').val();
        var descuento = +$(row).find('.descuentoRow').val();
        $.validator.addMethod('cRequerido',$.validator.methods.required,'Este campo es requerido');
        $.validator.addMethod('precio',function (value,element) {
            return this.optional(element) || /^\d{0,6}(\.\d{0,2})?$/g.test(value);
        },'Verifica la cantidad. Ej. 999999.00');
        $.validator.addMethod("greaterThan", function( value, element, param ) {
            return value > param;
        }, "El campo debe ser mayor a {0}" );
        $.validator.addMethod( "lessThan", function( value, element, param ) {
            return value < param;
        }, "Ingresa un valor menor a precio por cantidad ({0})" );

        $.validator.addClassRules('descuentoRow',{
            precio:true,
            greaterThan:-1,
            lessThan: precio * cantidad
        });
        $.validator.addClassRules('precioUnitarioRow',{
            cRequerido:true,
            precio:true,
            greaterThan:0
        });
        $.validator.addClassRules('idImpuestoRow',{
            cRequerido:true
        });
        if($('#form-model').valid()){
            var subtotal = cantidad*precio;
            subtotal = subtotal - descuento;
            var impuesto = ($(row).find('.idImpuestoRow').select2('data')[0].porcentaje/100) * subtotal;
            $(event.target).closest('tr').find('.totalRow').val((subtotal).toFixed(2));
            $(event.target).closest('tr').find('.porcentajeRow').val($(row).find('.idImpuestoRow').select2('data')[0].porcentaje);
            $(event.target).closest('tr').find('.impuestoRow').val(impuesto);
        }
        totalOferta();
    });

});//docReady

function agregarProducto() {
    validateDetail();
    if($('#form-model').valid()){
        var sku = "NULL";
        if($('#fk_id_sku').val() > 0){
            sku = $('#fk_id_sku').val();
        }

        var proveedor = "NULL";
        if($('#fk_id_socio_negocio').val() > 0){
            proveedor = $('#fk_id_socio_negocio').val();
        }
        var upc = "NULL";
        if($('#fk_id_upc').val() > 0){
            upc = $('#fk_id_upc').val();
        }
        $.ajax({
            url: $('#fk_id_sku').data('url-tiempo_entrega'),
            data: {
                'param_js':tiempo_entrega_js,
                $fk_id_sku:sku,
                $fk_id_socio_negocio:proveedor,
                $fk_id_upc:upc
            },
            dataType:'JSON',
            success: function (tiempo_entrega) {
            var tableData = $('#productos > tbody');
            var total = totalProducto();
            var impuesto = impuestoProducto();
            var i = $('#productos > tbody > tr').length;
            var row_id = i > 0 ? +$('#productos > tbody > tr:last').find('.index').val()+1 : 0;
            var id_proyecto = '';
            if($('#fk_id_proyecto').val()){
                id_proyecto = $('#fk_id_proyecto').val();
            }
            var id_upc = '';
            if($('#fk_id_upc').val()){
                id_upc = $('#fk_id_upc').val();
            }
            tableData.append(
                '<tr>' +
                    '<th>' + 'N/A' +
                        '<input type="hidden" class="index" value="'+row_id+'">'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_documento_base]" value=""/>'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_sku]" value="'+ $('#fk_id_sku').val() +'"/>'+
                        '<input type="hidden" class="cantidadRow" name="relations[has][detalle]['+row_id+'][cantidad]" value="'+ $('#cantidad').val() +'"/>'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_upc]" value="'+ id_upc +'"/>'+
                        '<input type="hidden" class="precioUnitarioRow" name="relations[has][detalle]['+row_id+'][precio_unitario]" value="'+ $('#precio_unitario').val() +'"/>'+
                        '<input type="hidden" class="descuentoRow" name="relations[has][detalle]['+row_id+'][descuento_detalle]" value="'+ $('#descuento_detalle').val() +'"/>'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_impuesto]" value="'+ $('#fk_id_impuesto').val() +'"/>'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_proyecto]" value="' + id_proyecto + '" />'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_unidad_medida]" value="' + $('#fk_id_unidad_medida').val() + '" />'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_proveedor]" value="'+ $('#fk_id_proveedor').val() +'"/>'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][total_producto]" class="totalRow" value="'+ total +'"/>'+
                        '<input type="hidden" name="relations[has][detalle]['+row_id+'][total_impuesto]" class="impuestoRow" value="'+ impuesto +'"/>' +
                        '<input type="hidden" class="porcentajeRow" value="'+$('#fk_id_impuesto').select2('data')[0].porcentaje+'">'+
                        '<input type="hidden" value="'+ tiempo_entrega[0].tiempo_entrega +'" class="tiempo_entrega">' +
                    '</th>' +
                    '<td>' + '<img style="max-height:40px" src="img/sku.png" alt="sku"/> ' + $('#fk_id_sku option:selected').text() + '</td>' +
                    '<td>' + '<img style="max-height:40px" src="img/upc.png" alt="upc"/> ' + $('#fk_id_upc option:selected').text() + '</td>' +
                    '<td>'+$('#fk_id_sku').select2('data')[0].descripcion_corta + '</td>'+
                    '<td>'+$('#fk_id_sku').select2('data')[0].descripcion + '</td>'+
                    '<td>' + $('#fk_id_proyecto option:selected').text() + '</td>' +
                    '<td>' + $('#fk_id_unidad_medida option:selected').html() + '</td>' +
                    '<td>' + $('#cantidad').val() + '</td>' +
                    '<td>' + $('#fk_id_impuesto option:selected').html() + '</td>' +
                    '<td>' + $('#precio_unitario').val() + '</td>' +
                    '<td>' + $('#descuento_detalle').val() + '</td>' +
                    '<td class="position-relative">'+ '<div class="w-100 h-100 text-center text-white align-middle loadingData" style="display: none">Calculando el total... <i class="material-icons align-middle loading">cached</i></div>'+
                        total + '</td>' +
                    '<td>'+ '<button data-toggle="Eliminar" data-placement="top" title="Eliminar" data-original-title="Eliminar" type="button" class="text-primary btn btn_tables is-icon eliminar" style="background:none;" data-delay="50" onclick="borrarFila(this)"><i class="material-icons">delete</i></button>'+'</td>' +
                '</tr>'
                );
                $.toaster({priority : 'success',title : '¡Éxito!',message : 'Producto agregado con éxito',
                    settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}
                });
                limpiarCampos();
                totalOferta();
                tiemposentrega();
            },
            error: function () {
            }
        });
        $('[data-toggle]').tooltip();
    }else{
        $.toaster({priority : 'danger',title : 'Â¡Error!',message : 'Hay campos que requieren de tu atención',
            settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
        }
}

function initSelects() {
    $('#fk_id_proyecto').select2({
        disabled: true,
        placeholder: "Seleccione primero el proveedor y el SKU..."
    });
    $('#fk_id_sku').select2({
        disabled:true,
        placeholder: "Seleccione primero el proveedor..."
    })
}

function totalProducto() {
    var cantidad = +$('#cantidad').val();
    var precio = +$('#precio_unitario').val();
    var subtotal =cantidad*precio;
    subtotal = subtotal - +$('#descuento_detalle').val();
    return (subtotal).toFixed(2);
}

function impuestoProducto() {
    var cantidad = +$('#cantidad').val();
    var precio = +$('#precio_unitario').val();
    var subtotal =cantidad*precio;
    subtotal = subtotal - +$('#descuento_detalle').val();
    var impuesto = ($('#fk_id_impuesto').select2('data')[0].porcentaje)/100 * subtotal;
    return (impuesto).toFixed(2);
}

function totalOferta() {

    var subtotal = 0;
    var impuesto = 0;
    var descuento_total = 0;

    if($('#productos tbody tr').length){
        $('#productos tbody tr').each(function (tr) {
            //Del producto
            var cantidad_row = +$(this).find('.cantidadRow').val();//Decimal
            var precio_row = +$(this).find('.precioUnitarioRow').val();//Decimal
            var porcentaje_row = +$(this).find('.porcentajeRow').val()/100;//Decimal
            var descuento_row = +$(this).find('.descuentoRow').val();
            descuento_total += descuento_row;//Decimal
            var subtotal_row = (cantidad_row * precio_row) - descuento_row;
            //Del total
            subtotal += cantidad_row*precio_row;
            impuesto += subtotal_row * porcentaje_row;
        });

        var total = subtotal + impuesto - descuento_total;
        $('#subtotal').val(subtotal.toFixed(2));
        $('#descuento_oferta').val(descuento_total.toFixed(2));
        $('#impuesto_oferta').val(impuesto.toFixed(2));
        $('#total_oferta').val(total.toFixed(2));
    }else{
        $('#subtotal').val(0);
        $('#impuesto_oferta').val(0);
        $('#total_oferta').val(0);
        $('#descuento_oferta').val(0);
    }
}

function limpiarCampos() {
    // $('#fk_id_cliente').val('').select2();
    $('#fk_id_proyecto').empty().select2();
    $('#fk_id_sku').val('').trigger('change');
    $('#fk_id_upc').empty().select2({
        placeholder: "Seleccione el UPC...",
        disabled:true
    });
    $('#activo_upc').prop('checked',false);
    $('#fk_id_unidad_medida').val(0).trigger('change');
    $('#cantidad').val('');
    $('#fk_id_impuesto').val(0).trigger('change');
    $('#precio_unitario').val(0);
    $('#descuento_detalle').val(0);
    //Eliminar reglas de validación detalle
    $('#fk_id_sku').rules('remove');
    $('#fk_id_upc').rules('remove');
    $('#cantidad').rules('remove');
    $('#fk_id_impuesto').rules('remove');
    $('#precio_unitario').rules('remove');
    $('#descuento_detalle').rules('remove');
    $('#fk_id_unidad_medida').rules('remove');
}

function validateDetail() {
    $('#fk_id_sku').rules('add',{
        required: true,
        messages:{
            required: 'Selecciona un SKU'
        }
    });
    $('#cantidad').rules('add',{
        required: true,
        number: true,
        range: [1,9999],
        messages:{
            required: 'Ingresa una cantidad',
            number: 'El campo debe ser un número',
            range: 'El número debe ser entre 1 y 9999'
        }
    });
    $('#fk_id_impuesto').rules('add',{
        required: true,
        messages:{
            required: 'Selecciona un tipo de impuesto'
        }
    });
    $.validator.addMethod('precio',function (value,element) {
        return this.optional(element) || /^\d{0,6}(\.\d{0,2})?$/g.test(value);
    },'El precio no tiene un formato válido');
    $.validator.addMethod( "greaterThan", function( value, element, param ) {
        if ( this.settings.onfocusout ) {
            $(element).addClass( "validate-greaterThan-blur" ).on( "blur.validate-greaterThan", function() {
                $( element ).valid();
            } );
        }
        return value > param;
    }, "Please enter a greater value." );
    $('#precio_unitario').rules('add',{
        required: true,
        number: true,
        precio:true,
        greaterThan:0,
        messages:{
            required: 'Ingresa un precio unitario',
            number: 'El campo debe ser un número',
            greaterThan: 'El número debe ser mayor a 0',
        }
    });
    $.validator.addMethod( "lessThan", function( value, element, param ) {
        return value < param;
    }, "Please enter a smaller value." );
    $('#descuento_detalle').rules('add',{
        number: true,
        precio:true,
        greaterThan:-1,
        lessThan: +$('#precio_unitario').val() * +$('#cantidad').val(),
        messages:{
            number: 'El campo debe ser un número',
            greaterThan: 'El número debe ser positivo',
            lessThan: 'El descuento debe ser menor al precio por la cantidad'
        }
    });
    $('#fk_id_unidad_medida').rules('add',{
        required:true,
        messages:{
            required: 'Selecciona una unidad de medida'
        }
    });
}

function tiemposentrega() {
    var mayor_tiempo = 0;
    $('#productos tbody tr').each(function (index,row) {
        if($(row).find('.tiempo_entrega').val() != "null")
            mayor_tiempo = $(row).find('.tiempo_entrega').val() > mayor_tiempo ? $(row).find('.tiempo_entrega').val() : mayor_tiempo;
    });
    var fecha = new Date();
    fecha.setDate(fecha.getDate() + +mayor_tiempo);
    $('#fecha_estimada_entrega').val(fecha.getFullYear()+'-'+(fecha.getMonth()+1)+'-'+fecha.getDate());
    $('#tiempo_entrega').val(mayor_tiempo);
}

function borrarFila(el) {
    var tr = $(el).closest('tr');
    tr.remove().stop();
    $.toaster({priority : 'success',title : '¡Advertencia!',message : 'Se ha eliminado la fila correctamente',
        settings:{'timeout':2000,'toaster':{'css':{'top':'5em'}}}});
    totalOferta();
}