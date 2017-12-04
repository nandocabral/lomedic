var a=[];
// Inicializar los datepicker para las fechas necesarias
$('.datepicker').pickadate({
    selectMonths: true, // Creates a dropdown to control month
    selectYears: 3, // Creates a dropdown of 3 years to control year
    min: true,
    format: 'yyyy-mm-dd'
});
$(document).ready(function(){
    //Inicializar tabla
    window.dataTable = new DataTable('#productos', {
        fixedHeight: true,
        fixedColumns: true,
        searchable: false,
        perPageSelect: false,
        labels:{
            info: "Mostrando del registro {start} al {end} de {rows}"
        },
        footer: true
    });
    totalOrden();
    if(window.location.href.toString().indexOf('editar') > -1 || window.location.href.toString().indexOf('crear') > -1)
    {
        initSelects();
        //Por si se selecciona una empresa diferente
        $('#otra_empresa').on('change',function () {
            $( this ).parent().nextAll( "select" ).prop( "disabled", !this.checked );
            if( !this.checked ){
                if(window.location.href.toString().indexOf('crear') > -1)
                    $( this ).parent().nextAll( "select" ).val(0).trigger('change');
            }
        });

        $('#agregar').on('click',function () {
            agregarProducto();
        });

        $('#fk_id_proveedor').on('change',function () {
            $('#tiempo_entrega').val($('#fk_id_proveedor').select2('data')[0].tiempo_entrega);
            var fecha = new Date();
            fecha.setDate(fecha.getDate()+$('#fk_id_proveedor').select2('data')[0].tiempo_entrega);
            $('#fecha_estimada_entrega').val(fecha.getFullYear()+'-'+fecha.getMonth()+'-'+fecha.getDate());
        });
        $(document).on('submit',function (e) {
            //Validaciones detalles
            $.validator.addMethod('minStrict', function (value, element, param) {
                return value > param;
            },'El número debe ser mayor a {0}');
            $.validator.addMethod('cRequerido',$.validator.methods.required,'Este campo es requerido');
            $.validator.addMethod('cDigits',$.validator.methods.digits,'El campo debe ser entero');

            $.validator.addClassRules('cantidad',{
                cRequerido: true,
                cDigits: true,
                minStrict: 0
            });
            $.validator.addMethod('precio',function (value,element) {
                return this.optional(element) || /^\d{0,6}(\.\d{0,2})?$/g.test(value);
            },'Verifica la cantidad. Ej. 999999.00');
            $.validator.addClassRules('precio',{
                cRequerido: true,
                precio: true,
                minStrict: 0
            });
            $.validator.addMethod('descuento',function (value,element) {
                return this.optional(element) || /^\d{0,2}(\.\d{0,4})?$/g.test(value);
            },'Verifica el porcentaje. Ej. 99.0000');
            $.validator.addClassRules('descuento',{
                minStrict: -1,
                descuento:true
            });

            if($('#form-model').valid()){
                if(dataTable.activeRows.length>0){
                    if(a.length>0) {
                        let url = $('#productos').data('delete');
                        $.delete(url, {ids: a});
                    }
                }else{
                    e.preventDefault();
                    $.toaster({priority : 'danger',title : '¡Error!',message : 'La tabla se encuentra vacía.',
                        settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
                }
            }else{
                e.preventDefault();
                $('.descuento').rules('remove');
                $('.cantidad').rules('remove');
                $('.precio').rules('remove');
                $.toaster({
                    priority: 'danger', title: '¡Error!', message: 'Hay campos que requieren de tu atención',
                    settings: {'timeout': 10000, 'toaster': {'css': {'top': '5em'}}}
                });
            }
        });
    }

    $('#descuento_oferta').on('keyup',function () {
       totalOrden();
    });
});
function select2Placeholder(id_select,text,searchable = 1,selected = true, disabled = true,value = 0,select2=true) {
    let option = $('<option/>');
    option.val(value);
    option.attr('disabled',disabled);
    option.attr('selected',selected);
    option.text(text);
    if(select2)
        $('#'+id_select).prepend(option).select2({
            minimumResultsForSearch:searchable,
            theme: "bootstrap",
        });
    else
        $('#'+id_select).prepend(option);
}
function initSelects() {
    $.ajax({
        url: $('#fk_id_proveedor').data('url'),
        dataType:'json',
        success:function (data) {
            $('#fk_id_proveedor').select2({
                minimumResultsForSearch: 15,
                data:data
            });
        }
    });
    $("#fk_id_sku").select2({
        minimumInputLength:3,
        ajax:{
            url: $("#fk_id_sku").data('url'),
            dataType: 'json',
            data: function (params) {
                return {term: params.term};
            },
            processResults: function (data) {
                return {results: data}
            }
        }
    });

    $('#fk_id_sucursal').select2({minimumResultsForSearch:25});
    select2Placeholder('fk_id_cliente','Sin cliente',10,true,false);
    select2Placeholder('fk_id_proyecto','Sin proyecto',10,true,false);
    $('#fk_id_upc').select2({minimumResultsForSearch:20});
    $.ajax({
        url: $('#fk_id_impuesto').data('url'),
        dataType:'json',
        success:function (data) {
            $('#fk_id_impuesto').select2({
                minimumResultsForSearch:'Infinity',
                data:data,
            });
        }
    });

}

function agregarProducto() {
    validateDetail();

    if($('#form-model').valid()){
        let row_id = dataTable.activeRows.length;
        let total = totalProducto();

        let text_upc = 'UPC no seleccionado';
        let id_upc = 0;
        if($('#fk_id_upc').val()){
            text_upc = $('#fk_id_upc').select2('data')[0].text;
            id_upc = $('#fk_id_upc').val();
        }

        var data = [];
        data.push([
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_solicitud]"/>')[0].outerHTML + 'N/A',
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_sku]" value="' + $('#fk_id_sku').val() + '" />')[0].outerHTML + $('#fk_id_sku').select2('data')[0].text,
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_upc]" value="' + id_upc + '" />')[0].outerHTML + text_upc,
            $('#fk_id_sku').select2('data')[0].descripcion_corta,
            $('#fk_id_sku').select2('data')[0].descripcion,
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_cliente]" value="' + $('#fk_id_cliente').val() + '" />')[0].outerHTML + $('#fk_id_cliente').select2('data')[0].text,
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_proyecto]" value="' + $('#fk_id_proyecto').val() + '" />')[0].outerHTML + $('#fk_id_proyecto').select2('data')[0].text,
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_unidad_medida]" value="' + $('#fk_id_unidad_medida').val() + '" />')[0].outerHTML + $('#fk_id_unidad_medida option:selected').text(),
            $('<input type="hidden" name="_detalles['+row_id+'][cantidad]" value="' + $('#cantidad').val() + '" />')[0].outerHTML + $('#cantidad').val(),
            $('<input type="hidden" name="_detalles['+row_id+'][fk_id_impuesto]" value="' + $('#fk_id_impuesto').val() + '" />')[0].outerHTML + $('#fk_id_impuesto').select2('data')[0].text,
            $('<input type="hidden" name="_detalles['+row_id+'][precio_unitario]" value="' + $('#precio_unitario').val() + '" />')[0].outerHTML + $('#precio_unitario').val(),
            $('<input type="hidden" name="_detalles['+row_id+'][descuento_detalle]" value="' + $('#descuento_detalle').val() + '" />')[0].outerHTML + $('#descuento_detalle').val(),
            $('<input type="text" value="'+ total +'" style="min-width: 100px" name="_detalles['+row_id+'][total]" class="form-control total" readonly>')[0].outerHTML,
            '<button class="btn is-icon text-primary bg-white" type="button" data-delay="50" onclick="borrarFila(this)"> <i class="material-icons">delete</i></button>'
        ]);

        dataTable.insert( {
            data:data
        });
        $.toaster({priority : 'success',title : '¡Éxito!',message : 'Producto agregado con éxito',
            settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}
        });
        limpiarCampos();
        totalOrden();
    }else{
        $.toaster({priority : 'danger',title : '¡Error!',message : 'Hay campos que requieren de tu atención',
            settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
    }
}

function totalProducto() {
    let cantidad = $('#cantidad').val();
    let precio = $('#precio_unitario').val();
    let descuento_porcentaje = $('#descuento_detalle').val()/100;

    let descuento = precio*descuento_porcentaje;

    precio = precio-descuento;

    let subtotal = cantidad * precio;
    let impuesto = ($('#fk_id_impuesto').select2('data')[0].porcentaje * subtotal)/100;

    return (subtotal + impuesto).toFixed(2);
}

function totalOrden() {

    let total = 0;
    // $(".total").each(function () {
    //     total +=  parseFloat($(this).val());
    // });
    $.each(window.dataTable.data,function () {
        total += parseFloat($(this).find('td .total').val());
    });
    let descuento_porcentaje = $('#descuento_oferta').val()/100;
    let descuento = descuento_porcentaje * total;
    total = total-descuento;
    $('#total_orden').val(total.toFixed(2));
}

function borrarFila(el) {
    dataTable.rows().remove([$(el).closest('tr').index()]);
        $.toaster({priority : 'success',title : '¡Advertencia!',message : 'Se ha eliminado la fila correctamente',
            settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});
    if(dataTable.rows.length<1)
        validateDetail();

    totalOrden();
}

function limpiarCampos() {
    $('#fk_id_sku').val(0).select2();
    $('#fk_id_upc').val(0).select2();
    $('#fk_id_proyecto').val(0).trigger('change');
    $('#fk_id_cliente').val(0).trigger('change');
    $('#fk_id_impuesto').val('0').trigger('change');
    $('#fk_id_unidad_medida').val('0').trigger('change');
    $('#cantidad').val('');
    $('#precio_unitario').val('');
    //Eliminar reglas de validación detalle
    $('#fk_id_sku').rules('remove');
    $('#fk_id_upc').rules('remove');
    $('#cantidad').rules('remove');
    $('#fk_id_impuesto').rules('remove');
    $('#precio_unitario').rules('remove');
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
    $.validator.addMethod('descuento',function (value,element) {
        return this.optional(element) || /^\d{0,2}(\.\d{0,4})?$/g.test(value);
    },'El descuento no tiene un formato válido');
    $('#descuento_detalle').rules('add',{
        number: true,
        descuento:true,
        greaterThan:0,
        messages:{
            required: 'Ingresa un precio unitario',
            number: 'El campo debe ser un número',
            greaterThan: 'El número debe ser mayor a 0',
        }
    });

    $('#fk_id_unidad_medida').rules('add',{
       required:true,
        messages:{
           required: 'Selecciona una unidad de medida'
        }
    });
}

function borrarFila_edit(el) {
    a.push(el.id);
    dataTable.rows().remove([$(el).parents('tr').index()]);
    if(dataTable.activeRows.length<1)
        validateDetail();

    totalOrden();
}

function total_row(id) {
    let cantidad = $('#cantidad'+id).val();
    let precio = $('#precio_unitario'+id).val();
    let descuento_porcentaje = $('#descuento_detalle'+id).val()/100;

    let descuento = precio*descuento_porcentaje;

    precio = precio-descuento;

    let subtotal = cantidad * precio;
    let impuesto = ($('#fk_id_impuesto'+id).data('porcentaje') * subtotal)/100;

    $('#total'+id).val((subtotal + impuesto).toFixed(2));
    totalOrden();
}