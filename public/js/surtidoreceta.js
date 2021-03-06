
$(document).ready(function () {

    $('#folio').prop('disabled',true);
    $('#fk_id_receta').prop('disabled',true);
    $('#sufijo').prop('disabled',true);
    $('#fk_id_sucursal').select2();

    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
          clearTimeout (timer);
          timer = setTimeout(callback, ms);
        };
    })();

    $('#folio').keyup(function() {
        delay(function(){
            if($('#fk_id_sucursal').val() != '' && $('#folio').val() != '' && $('#sufijo').val() != '')
            {
                showLoaders();
                validator = false;
                id_sucursal = $('#fk_id_sucursal').val();
                folio = $('#folio').val();
                sufijo = $('#sufijo').val();
                $.ajax({
                    type: "GET",
                    url: $('#fk_id_receta').data('consultafolio'),
                    data: {
                        'id_sucursal':id_sucursal,
                        'folio':folio,
                        'sufijo':sufijo,
                    },
                    dataType: "json",
                    success:function(data) {
                        if(!data.status){
                            $.each($('#fk_id_receta option'),function(i,opt){
                                if(data.id_receta == opt.value){
                                    validator = true;
                                }
                            })
                            if(validator == false){
                                $('#detalle').empty();
                                $('#fk_id_receta').append('<option value="'+ data.id_receta +'">'+ data.serie +'</option>');
                            }
                            $('#fk_id_receta').val(data.id_receta).trigger('change');
                            hideLoaders();
                        }else{
                            $.toaster({priority : 'danger',title : '¡Lo sentimos!',message : 'No hay comunicación con el servidor de IPEJAL en éste momento, te recomendamos intentar más tarde.',
                            settings:{'timeout':5000,'toaster':{'css':{'top':'5em'}}}});
                            hideLoaders();
                        }
                    },
                    error:function(){
                        $.toaster({priority : 'danger',title : '¡Lo sentimos!',message : 'No hay comunicación con el servidor de IPEJAL en éste momento, te recomendamos intentar más tarde.',
                        settings:{'timeout':5000,'toaster':{'css':{'top':'5em'}}}});
                        hideLoaders();
                    }
                });
            }
        }, 1500 );
    });

    $('#sufijo').keyup(function(){
        if($(this).val().length > 0)
        {
            $('#folio').prop('disabled',false);
        }
        else
        {
            $('#folio').prop('disabled',true);
        }
    });
    // $('#folio').on('change', function() {
    //     if($('#fk_id_sucursal').val() != '' && $('#folio').val() != '' && $('#sufijo').val() != '')
    //     {
    //         id_sucursal = $('#fk_id_sucursal').val();
    //         $.ajax({
    //             type: "POST",
    //             url: 'http://127.0.0.1:8000/abisa/inventarios/consultafolio',
    //             data: {'id_sucursal':id_sucursal,'_token':token},
    //             dataType: "json",
    //             success:function(data) {
    //                 console.log(data);
    //                 // if(data.id_receta)
    //                 //     $('#fk_id_receta').val(data.id_receta).trigger('change');
    //                 // else
    //                 // console.log(data.estatus);
    //             }
    //         });
    //     }
    // });

    $('#sufijo').on('change', function() {
        $('#folio').trigger('change');
    });
    // $('#id_receta').select2();
    let token = document.querySelector("meta[name='csrf-token']").getAttribute("content");
    $('#fk_id_sucursal').on('change', function() {
        $('#sufijo').prop('disabled',false);
        showLoaders();
        $.ajax({
            type: "POST",
            url: $(this).data('url'),
            data: {'fk_id_sucursal':$(this).val(),'_token':token},
            dataType: "json",
            success:function(data) {
                if(data)
                {
                    $('#fk_id_receta').empty();
                    $.each(data, function(key, value) {
                        $('#fk_id_receta').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                    $('#fk_id_receta').val('');
                    $('#fk_id_receta').select2({
                        disabled:false,
                    });
                    hideLoaders();
                }
                else
                {
                    $.toaster({priority : 'warning',title : '¡Lo sentimos!',message : 'No hay números de receta en la sucursal, pruebe buscando por sufijo y folio.',
                    settings:{'timeout':5000,'toaster':{'css':{'top':'5em'}}}});
                    hideLoaders();
                }
            }
        });
    });
    $('#fk_id_receta').on('change', function() {
        if (!$(this).is(":empty")) {
            $('#detalle tbody tr').remove();
            $.ajax({
                type: "POST",
                url: $('#fk_id_receta').data('url'),
                data: {'fk_id_receta':$(this).val(),'_token':token},
                dataType: "json",
                success:function(data) {
                    $('#detalle').empty();
                    $.each(data, function(key,values) {
                        $('#detalle').append(
                            '<tr>' +
                            '<td>'+values.sku+'</td>'+
                            // '<td>'+values.clave_cliente_producto+'</td>'+
                            '<td>'+values.descripcion+'</td>'+
                            '<td>'+values.cantidad_solicitada+'</td>'+
                            '<td class="cantidad_surtida">'+values.cantidad_surtida+'</td>'+
                            '<td class="cantidad_disponible">'+ values.cantidad_disponible +'</td>'+
                            '<td><input type="number" onchange="calculatotal(this)" name="relations[has][detalles][' + key + '][cantidad_surtida]" min="0" max="'+(values.cantidad_solicitada - values.cantidad_surtida)+'" class="form-control cantidad" value="0"></td>'+
                            '<td>$ '+parseFloat(values.precio_unitario, 10).toFixed(2)+'</td>'+
                            '<td class="text-right total">$ '+parseFloat(0, 10).toFixed(2)+'</td>' +
                            '<input type="hidden" class="cantidad_inicial_disponible" value="'+values.cantidad_disponible+'"/> ' +
                            '<input type="hidden" name="relations[has][detalles][' + key + '][id_surtido_receta]"  value=""/> ' +
                            '<input type="hidden" name="relations[has][detalles][' + key + '][fk_id_surtido_receta]"  value=""/> ' +
                            '<input type="hidden" name="relations[has][detalles][' + key + '][fk_id_clave_cliente_producto]"  value="'+ values.fk_id_clave_cliente_producto +'"/> ' +
                            '<input type="hidden" name="relations[has][detalles][' + key + '][cantidad_solicitada]"  value="'+ values.cantidad_solicitada +'"/> ' +
                            '<input type="hidden" name="relations[has][detalles]['+ key +'][precio_unitario]" class="precio" value="'+ values.precio_unitario +'">'+
                            '<input type="hidden" name="relations[has][detalles]['+ key +'][importe]" class="importe" value="'+ values.precio_unitario +'">'+
                            '</tr>');
                        })
                    $.toaster({priority : 'success',title : '¡Éxito!',message : 'Medicamentos mostrados con éxito',
                    settings:{'timeout':3000,'toaster':{'css':{'top':'5em'}}}});
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



});

function calculatotal(el) {

    var cantidad = $(el).val();
    var precio = $(el).parent().parent().find('.precio').val();
    var cantidad_surtida = $(el).parent().parent().find('.cantidad_surtida').html();
    var cantidad_total = parseInt(cantidad_surtida) + parseInt(cantidad);
    var cantidad_disponible = parseInt($(el).parent().parent().find('.cantidad_inicial_disponible').val());

    if ((cantidad_disponible - cantidad) >= 0) {
        var nueva_cantidad_diponible = cantidad_disponible - cantidad;

    }
    else if ((cantidad_disponible - cantidad) < 0) {
        var nueva_cantidad_diponible = 0;

        cantidad_total = cantidad_total - 1;
        // $(el).val(cantidad);
        // alert();
    }
    validarSurtido();
    $(el).parent().parent().find('.cantidad_disponible').html(nueva_cantidad_diponible);
    $(el).parent().parent().find('.importe').val(cantidad_total * precio);
    $(el).parent().parent().find('.total').html('$ ' + parseFloat((cantidad_total * precio), 10).toFixed(2));

    var total = 0;
    $('.importe').each(function (i) {
        total += cantidad_total * precio;
    });

    $('#total').html('$ ' + parseFloat(total, 10).toFixed(2));

    // validarSurtido();

}

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

function showLoaders(){
    $('#loadingsufijo').show();
    $('#loadinginvoices').show();
    $('#loadingsucursales').show();
    $('#loadingfolio').show();
}

function hideLoaders(){
    $('#loadingsufijo').hide();
    $('#loadinginvoices').hide();
    $('#loadingsucursales').hide();
    $('#loadingfolio').hide();
}