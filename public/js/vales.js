$(document).ready(function () {


    $('#fk_id_sucursal').select2();
    let token = document.querySelector("meta[name='csrf-token']").getAttribute("content");
    $('#fk_id_sucursal').on('change', function() {
        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            data: {'fk_id_sucursal':$(this).val(),'_token':token},
            dataType: "json",
            success:function(data) {
                $('#fk_id_receta').empty();
                $.each(data, function(key, value) {
                    $('#fk_id_receta').append('<option value="'+ key +'">'+ value +'</option>');
                });
                $('#fk_id_receta').val('');
            }
        });
    });
    $('#fk_id_receta').on('change', function() {
        if (!$(this).is(":empty")) {
            $('#detalle').empty();
            $.ajax({
                type: "POST",
                url: $('#fk_id_receta').data('url'),
                data: {'fk_id_receta':$(this).val(),'_token':token},
                dataType: "json",
                success:function(data) {

                    $('#paciente').val(data.receta.paciente);
                    $('#titular').val(data.receta.titular);
                    $('#medico').val(data.receta.medico);
                    $('#diagnostico').val(data.receta.diagnostico);
                    $('#edad').val(data.receta.edad);
                    $('#patente').val(data.receta.patente);
                    $('#genero').val(data.receta.genero);
                    $('#parentesco').val(data.receta.parentesco);

                    $.each(data.detalle, function(key,values) {
                        if(values.cantidad_disponible == 0 ){
                            $('#detalle').append(
                                '<tr>' +
                                '<td>'+values.sku+'</td>'+
                                '<td>'+values.descripcion+'</td>'+
                                '<td>'+values.cantidad_solicitada+'</td>'+
                                '<td class="cantidad_surtida">'+values.cantidad_surtida+'</td>'+
                                '<td class="cantidad_disponible">'+values.cantidad_disponible+'</td>'+
                                '<td><input type="number" onchange="calculatotal(this)" name="relations[has][detalles][' + key + '][cantidad_surtida]" min="0" max="'+(values.cantidad_solicitada - values.cantidad_surtida)+'" class="form-control cantidad" value="0"></td>'+
                                '<td>$ '+parseFloat(values.precio_unitario, 10).toFixed(2)+'</td>'+
                                '<td class="text-right total">$ '+parseFloat(0, 10).toFixed(2)+'</td>' +
                                '<input type="hidden" class="cantidad_inicial_disponible" value="'+values.cantidad_disponible+'"/> ' +
                                '<input type="hidden" name="relations[has][detalles][' + key + '][id_surtido_vale]"  value=""/> ' +
                                '<input type="hidden" name="relations[has][detalles][' + key + '][fk_id_surtido_vale]"  value=""/> ' +
                                '<input type="hidden" name="relations[has][detalles][' + key + '][fk_id_clave_cliente_producto]"  value="'+ values.fk_id_clave_cliente_producto +'"/> ' +
                                '<input type="hidden" name="relations[has][detalles][' + key + '][cantidad_solicitada]"  value="'+ values.cantidad_solicitada +'"/> ' +
                                '<input type="hidden" name="relations[has][detalles]['+ key +'][precio_unitario]" class="precio" value="'+ values.precio_unitario +'">'+
                                '<input type="hidden" name="relations[has][detalles]['+ key +'][importe]" class="importe" value="'+ values.precio_unitario +'">'+
                                '</tr>'
                            );
                        }

                    });
                }
            });
        }
    });

    $("#form-model").submit(function(){
        var cont = 0;
        $.each($('.cantidad'),function (index,value) {
            cont = cont + $(value).val();
        });

        if( cont == 0 )
        {
            mensajeAlerta('Favor de ingresar por lo menos un producto.','danger');
            return false;
        }
        else if(validarSurtido() != 0 )
        {
            mensajeAlerta('Se esta excediendo la cantidad solicita.','danger');
            return false;
        }
        else
        {
            return true;
        }
    });

    // $("#form-model").submit(function(){
    //     var cont = 0;
    //     $.each($('.cantidad'),function (index,value) {
    //         cont = cont + $(value).val();
    //     });
    //
    //     if( cont == 0 )
    //     {
    //         mensajeAlerta('Favor de ingresar por lo menos un producto.','danger');
    //         return false;
    //     }
    //     else
    //     {
    //         return true;
    //     }
    // });

});
function calculatotal(el) {
    var cantidad = $(el).val();
    var precio = $(el).parent().parent().find('.precio').val();
    var cantidad_surtida = $(el).parent().parent().find('.cantidad_surtida').html();
    var cantidad_total = parseInt(cantidad_surtida)+parseInt(cantidad);
    var cantidad_disponible = parseInt($(el).parent().parent().find('.cantidad_inicial_disponible').val());

    console.info(cantidad_disponible+' '+cantidad);
    if( (cantidad_disponible - cantidad) >= 0 )
    {
        var nueva_cantidad_diponible = cantidad_disponible - cantidad;
    }

    $(el).parent().parent().find('.cantidad_disponible').html(nueva_cantidad_diponible);
    $(el).parent().parent().find('.importe').val(cantidad_total*precio);
    $(el).parent().parent().find('.total').html('$ '+parseFloat((cantidad_total*precio), 10).toFixed(2));

    var total =  0;
    $('.importe').each(function (i) {
        total += cantidad_total*precio;
    });

    $('#total').html('$ '+parseFloat(total, 10).toFixed(2));
};

function validarSurtido()
{
    var correcto = 0;

    $.each($('#detalle tr'),function(index,value) {

        var cant_solicitada = parseInt($(value).find('td').eq(2).html());
        var cant_surtida = parseInt($(value).find('td').eq(3).html());
        var cant_a_surtir = parseInt($(value).find('.cantidad').val());

        if(cant_solicitada < cant_surtida + cant_a_surtir)
        {
            $(value).css("background-color", "#F8D7DA");
            correcto++;
        }
        else
        {
            $(value).css("background-color", "#FFFFFF");
        }


    });

    return correcto;
}

function mensajeAlerta(mensaje,tipo){

    var titulo = '';

    if(tipo == 'danger'){ titulo = '¡Error!'}
    else if(tipo == 'success'){titulo = '¡Correcto!' }
    $.toaster({priority:tipo,
            title: titulo,
            message:mensaje,
            settings:{'timeout':10000,
                'toaster':{'css':{'top':'5em'}}}
        }
    );
}
