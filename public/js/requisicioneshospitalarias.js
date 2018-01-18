
$(document).ready(function () {


    $('.programa').select2();
    $('.area').select2();

    medicamento();

    $('#agregar').click(function () {

        var medicamento = $('#medicamento').select2('data');
        var campos = '';
        if($('#medicamento').select2('data').length ==0){
            campos += '<br><br>Medicamento: ¿Seleccionaste un medicamento?';
        }
        if(campos!=''){
            $.toaster({
                priority : 'danger',
                title : 'Verifica los siguientes campos',
                message : campos,
                settings:{
                    'timeout':10000,
                    'toaster':{
                        'css':{
                            'top':'5em'
                        }
                    }
                }
            });
            return
        }


        $('#fk_id_sucursal').select2();
        // $('#id_receta').select2();
        let token = document.querySelector("meta[name='csrf-token']").getAttribute("content");
        $('#fk_id_sucursal').on('change', function() {
            $.ajax({
                type: "POST",
                url: $(this).data('url'),
                data: {'fk_id_sucursal':$(this).val(),'_token':token},
                dataType: "json",
                success:function(data) {
                    $('#fk_id_requisiciones_hospitalarias').empty();
                    $.each(data, function(key, value) {
                        $('#fk_id_requisiciones_hospitalarias').append('<option value="'+ key +'">'+ value +'</option>');
                    });
                    $('#fk_id_requisiciones_hospitalarias').val('');
                }
            });
        });


        var filas = $('.medicine_detail tr').length;
        var agregar = true;

        if(agregar) {

            var area = $('#fk_id_area').select2('data');
            var medicamento = $('#medicamento').select2('data');
            var cantidad = $('#cantidad');
            $('.medicine_detail').append('' +
                '<tr id="'+ filas +'">' +
                    '<td>' + area[0]['text'] + '</td>' +
                    '<td>' + medicamento[0].clave_cliente_producto + '</td>' +
                    '<td>' + medicamento[0]['text'] + '</td>' +
                    '<td>' + cantidad.val() + '</td>' +
                    '<td>' +
                        '<a onclick="eliminarFila(this)" data-delete-type="single"  data-toggle="tooltip" data-placement="top" title="Borrar"  id="' + filas + '" aria-describedby="tooltip687783"><i class="material-icons text-primary">delete</i></a> ' +
                    '</td> ' +
                    '<input type="hidden" name="relations[has][detalles][' + filas + '][id_detalle_requisicion]"  value=""/> ' +
                    '<input type="hidden" name="relations[has][detalles]['+filas+'][fk_id_area]" value="'+area[0]['id']+'"> ' +
                    '<input type="hidden" name="relations[has][detalles]['+filas+'][fk_id_clave_cliente_producto]" value="'+medicamento[0].fk_id_clave_cliente_producto+'"> ' +
                    '<input type="hidden" name="relations[has][detalles]['+filas+'][cantidad_solicitada]" value="'+cantidad.val()+'"> ' +
                '</tr>');
            $('#guardar').prop('disabled', filas = 0);
            limpiarCampos();
            $.toaster({
                priority: 'success',
                title: 'Éxito!',
                message: '<br>Medicamento agregado exitosamente',
                settings: {
                    'toaster': {
                        'css': {
                            'top': '5em'
                        }
                    }
                }
            });
        }
    });


});


function formatMedicine(medicine) {
    if(!medicine.id){return medicine.text;}
    return $('<span>'+medicine.text+'</span><br>Presentación: <b>'+medicine.familia+'</b> Cantidad en la presentación: <b>'+medicine.cantidad_presentacion+'</b>' +
        '<br>Disponibilidad: <b>'+medicine.disponible+'</b> Máximo para recetar: <b>'+medicine.tope_receta+'</b>');
}

function eliminarFila(el)
{

    $(el).parent().parent('tr').remove();
    $.toaster({priority:'success',title:'¡Correcto!',message:'Se ha eliminado correctamente el '+$(el).data('tooltip'),settings:{'timeout':10000,'toaster':{'css':{'top':'5em'}}}});

}

function medicamento() {
    let token = document.querySelector("meta[name='csrf-token']").getAttribute("content");
    $("#medicamento").select2({
        placeholder: 'Escriba el medicamento',
        ajax: {
            type: 'POST',
            url: $("#medicamento").data('url'),
            dataType: 'json',
            data: function(params) {
                return {
                    '_token':token,
                    medicamento: $.trim(params.term), // search term
                    // localidad: $('.unidad').val()
                };
            },
            processResults: function(data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        minimumInputLength: 3,
        language: {
            "noResults": function() {
                return "No se encontraron resultados";
            }
        },
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: formatMedicine,
    });
}

function limpiarCampos() {

    $("#fk_id_area").val('').trigger('change');
    $("#medicamento").val('').trigger('change');
    $('#cantidad').val('');

}

function escaparID(myid){
    return "#" + myid.replace( /(:|\.|\[|\]|,|=|@)/g, "\\$1" );
}




// /**
//  * Created by ihernandezt on 05/09/2017.
//  */
// $(document).ready(function () {
//
//     $('.integer').keypress(function (e) {
//         if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)){
//             e.preventDefault();
//             return false;
//         }else if(this.value.length>3 && e.which != 8 && e.which != 0){
//             e.preventDefault();
//             return false;
//         }
//     }).on("cut copy paste", function(e) {
//         e.preventDefault();
//     });
//
// });
//
// $cont_producto = 0;
// function agregarProducto() {
//
//     var length_max = $('#cantidad').attr('maxlength');
//     var length_cantidad = $('#cantidad').val().length;
//
//
//     if( parseInt($('#id_area').val()) > 0 && parseInt($('#producto').val()) > 0 && parseInt($('#cantidad').val()) > 0 )
//     {
//         if(length_cantidad > length_max)
//         {
//
//             mensajes_alert('Se esta excediendo en la cantidad maxima de producto permitida.');
//
//         }
//         else
//         {
//             // alert($('#id_area').val()+'_'+$('#producto').val());
//             if( $( '#'+$('#id_area').val()+'_'+$('#producto').val()).length )
//             {
//                 mensajes_alert('Esa area ya tiene asiganada ese producto.');
//             }
//             else
//             {
//                 var id_area =  $('#id_area').val();
//                 var area_nombre =  $('#id_area option:selected').text();
//                 var producto_clave = $('#producto').val();
//                 var producto_nombre = $('#producto option:selected').text();
//                 var cantidad = $('#cantidad').val();
//                 var id_renglon = $cont_producto+'_'+producto_clave;
//                 // var company_id = $('#company_email option:selected').val();
//
//                 $('#lista_productos').append('<tr id="' + id_renglon + '"> ' +
//                     '<td>' + area_nombre + '</td>' +
//                     '<td>' + producto_nombre + '</td>' +
//                     '<td>'+ cantidad +'</td> ' +
//                     '<td>' + '<a href="#" data-toggle="tooltip" data-placement="top" title="Borrar" class="text-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"  onclick="eliminarFila(\'' + id_renglon + '\')"></span> </a></td>  ' +
//                     '<input type="hidden" value="'+id_area+'" name="producto_requisicion['+$cont_producto+'][id_area]">' +
//                     '<input type="hidden" value="'+producto_clave+'" name="producto_requisicion['+$cont_producto+'][producto_clave]">' +
//                     '<input type="hidden" value="'+cantidad+'" name="producto_requisicion['+$cont_producto+'][cantidad]">' +
//                     '<input type="hidden" id="'+id_area+'_'+producto_clave+'" >'+
//                     '</tr>'
//
//                 );
//
//                 $cont_producto++;
//
//                 $('#id_area').val('0').trigger('change');
//                 $('#producto').val('0').trigger('change');
//                 $('#cantidad').val('0').trigger('change');
//             }
//
//
//         }
//     }
//     else
//     {
//         mensajes_alert('Uno o varios de los campos de Area, producto o cantidad estan vacios.');
//     }
//     var filas = $('#detalle tr').length;
//     $('#guardar').prop('disabled',(filas<=1));
//
// }
//
// function eliminarFila(fila)
// {
//     $('#'+fila).remove();
//
//     var filas = $('#detalle tr').length;
//     $('#guardar').prop('disabled',(filas<=1));
// }
//
// $('select[name="id_localidad"]').on('change', function() {
//     // alert($("#id_localidad").data('url'));
//     var id_localidad = $(this).val();
//     // alert(id_localidad);
//     if(id_localidad) {
//         $.ajax({
//             type: "POST",
//             url: $("#id_localidad").data('url'),
//             data: 'id_localidad='+id_localidad,
//             dataType: "json",
//             success:function(data) {
//
//                 console.info(data.producto);
//                 data_areas = $.parseJSON(data.areas);
//                 data_usuario = $.parseJSON(data.usuario);
//                 data_producto = $.parseJSON(data.producto);
//
//                 $('select[name="id_area"]').empty();
//                 $('select[name="id_usuario_surtido"]').empty();
//                 $('select[name="producto"]').empty();
//
//                 $('select[name="id_area"]').empty().append('<option value="0" selected disabled>Selecciona un Area...</option>');
//                 $('select[name="id_solicitante"]').empty().append('<option value="0" selected disabled>Selecciona un Solicitante...</option>');
//                 $('select[name="producto"]').empty().append('<option value="0" selected disabled>Selecciona un Producto...</option>');
//
//                 $.each(data_areas, function(key, value) {
//                     $('select[name="id_area"]').append('<option value="'+ key +'">'+ value +'</option>');
//                 });
//                 $.each(data_usuario, function(key, value) {
//                     $('select[name="id_solicitante"]').append('<option value="'+ key +'">'+ value +'</option>');
//                 });
//                 $.each(data_producto, function(key, value) {
//                     $('select[name="producto"]').append('<option value="'+ key +'">'+ value +'</option>');
//                 });
//             }
//         });
//
//     }else{
//         $('select[name="id_area"]').empty();
//         $('select[name="id_usuario_surtido"]').empty();
//         $('select[name="producto"]').empty();
//     }
// });
//
// $('input').change(function(event) {
//
//     var length_max = $('#'+event.target.id).attr('maxlength');
//     var length_cantidad = $('#'+event.target.id).val().length;
//
//     if( length_cantidad > length_max)
//     {
//
//         mensajes_alert('Se estÃ¡ excediendo en la cantidad de producto permitida a surtir.');
//         $('#'+event.target.id).val('0');
//     }
//
// });
//
// $('#cantidad').change(function(){
//
// });
//
// function guardarRequisicion()
// {
//     var fecha_requerido = $('#fecha_requerido').val();
//     var id_solicitante = $('#id_solicitante option:selected').val();
//
//     if(id_solicitante != 0)
//     {
//         if(fecha_requerido != '')
//         {
//             return true;
//         }
//         else
//         {
//             mensajes_alert('Favor de ingresar una fecha.');
//             return false;
//         }
//     }
//     else
//     {
//         mensajes_alert('Favor de ingresar un solicitante.');
//         return false;
//     }
//
// }
//
// function surtirRequisicion()
// {
//
//     var validado = 0;
//
//     for( var i=0 ; i < $('#lista_productos tr').length; i++)
//     {
//         if((parseInt($("#renglon_"+i).val())+ parseInt(detalle_requisicion[i].cantidad_surtida)) > detalle_requisicion[i].cantidad_pedida)
//         {
//             validado++;
//         }
//     }
//
//     if(validado == 0)
//     {
//         return true;
//     }
//     else
//     {
//         mensajes_alert('Se esta excediendo la cantdad de producto solicitada.');
//         return false;
//     }
//
// }
//
// function mensajes_alert(mensaje)
// {
//     $.toaster({
//         priority : 'danger',
//         css:{
//             'top': '3em'
//         },
//         title : 'Error!',
//         message : '<br>'+mensaje,
//         settings:{
//             'timeout':8000,
//             'toaster':{
//                 'css':{
//                     'top':'3em'
//                 }
//             }
//         }
//     });
// }
