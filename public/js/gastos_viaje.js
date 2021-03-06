$(document).ready(function () {

  //Iniciamos los selects, fechas y tooltips
  initSelect2();

  $("#periodo_inicio").pickadate('picker').on({
    set: function(){
      $('#periodo_fin').pickadate('picker').set('min', $('#periodo_inicio').pickadate('picker').get('select'));
      ingresarDias();
    }
  });

  $("#periodo_fin").pickadate('picker').on({
    set: function(){
      ingresarDias();
    }
  });

  $('[data-toggle]').tooltip();

  //Funciones para ingresar valores de acuerdeo al empleado seleccionado (puesto, departamento, sucursal)
  $('#fk_id_empleado').on('change', function() {
    var idempleado = $('#fk_id_empleado option:selected').val();
    var _url = $(this).data('url');


    $.ajax({
        async: true,
        url: _url,
        data: {'param_js':js_departamento,$id_empleado:idempleado},
        dataType: 'json',
            success: function (data) {
              if(data[0].departamento == null)
                $('#departamento').val('');
              else
                $('#departamento').val(data[0].departamento.descripcion);
        }
    });

    $.ajax({
        async: true,
        url: _url,
        data: {'param_js':js_puesto,$id_empleado:idempleado},
        dataType: 'json',
            success: function (data) {
              if(data[0].puesto == null)
                $('#puesto').val('');
              else
                $('#puesto').val(data[0].puesto.descripcion);
        }
    });

    $.ajax({
        async: true,
        url: _url,
        data: {'param_js':js_sucursal,$id_empleado:idempleado},
        dataType: 'json',
            success: function (data) {
              if(data[0].sucursales == null)
                $('#sucursal').val('');
              else
                $('#sucursal').val(data[0].sucursales.sucursal);
        }
    });
    
  });

  //Funciones para ingresar valores de acuerdo al impuesto seleccionado
  $('#fk_id_impuesto').on('change', function() {
    var idimpuesto = $('#fk_id_impuesto option:selected').val();
    var _url = $(this).data('url');


    $.ajax({
        async: true,
        url: _url,
        data: {'param_js':js_impuesto,$id_impuesto:idimpuesto},
        dataType: 'json',
            success: function (data) {
              if(data[0].tasa_o_cuota == null)
                $('#impuesto').val('');
              else
                $('#impuesto').val(data[0].tasa_o_cuota);

              calcular();
        }
    });
  });

  //Realizamos función de cálculo en caso de que el usuario cambie el subtotal
  $('#subtotal_fac').on('keyup',function(){
    calcular();
  });

  $(document).on('submit', function(e){
    if($('#detalle-form-body > tr').length == 0){
      e.preventDefault();
      $.toaster({priority : 'danger',title : '¡Error!',message : 'Para guardar es necesario que mínimo agregues una factura/nota',
        settings:{'timeout':3000,'toaster':{'css':{'top':'5em'}}}});
    }
  })

/*
  --- FORMULARIO DE DETALLE PARA FACTURAS ---
*/
  $('#saveTable').click(function() {
    //Prevenimos que genere la acción default
    validateDetail()

    //Confición para validar
    if($('#form-model').valid())
    {
      var formulario = $('#detalle-form');
      var oveData = formulario.serializeArray();
      //hacemos un for each para obtener cada valor por individual
      $.each(oveData, function(i, val){
      // Convertimos el array en objeto JSON
        returnArray = {};
        for (var i = 0; i < oveData.length; i++){
            returnArray[oveData[i]['name']] = oveData[i]['value'];
          }
        return returnArray;
      });

      agregarFilaDetalle();
      limpiarCampos();
    } else {
        $.toaster({priority : 'danger',title : '¡Error!',message : 'Hay campos que requieren de tu atención',
          settings:{'timeout':3000,'toaster':{'css':{'top':'5em'}}}});
    }
  }); 
});

function agregarFilaDetalle(){
var tableData = $('table > tbody');
var i = $('#detalle-form-body > tr').length;
var row_id = i > 0 ? +$('#detalle-form-body > tr:last').find('#index').val()+1 : 0;
var tipo = $('#fk_id_tipo option:selected').text();
var impuesto = $('#fk_id_impuesto option:selected').text();
tableData.append(
  '<tr><td>'+ returnArray.folio_fac + '<input type="hidden" id="index" value="'+row_id+'"></input><input type="hidden" name="relations[has][detalle]['+row_id+'][folio]" value="'+returnArray.folio_fac+'"/>'+'</td>'
  + '<td>'+ tipo + '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_tipo]" value="'+returnArray.fk_id_tipo+'"/>'+'</td>'
  + '<td>'+ '$' +returnArray.subtotal_fac + '<input class="subtotal" type="hidden" name="relations[has][detalle]['+row_id+'][subtotal]" value="'+returnArray.subtotal_fac+'"/>'+'</td>'
  + '<td>'+ impuesto + '<input type="hidden" name="relations[has][detalle]['+row_id+'][fk_id_impuesto]" value="'+returnArray.fk_id_impuesto+'"/>'+'</td>'
  + '<td>'+ '$' +returnArray.total_fac + '<input class="total" type="hidden" name="relations[has][detalle]['+row_id+'][total]" value="'+returnArray.total_fac+'"/>'+'</td>'
  + '<td>'+ '<button data-toggle="Eliminar" data-placement="top" title="Eliminar" data-original-title="Eliminar" type="button" class="text-primary btn btn_tables is-icon eliminar bg-white" data-delay="50" onclick="borrarFila(this)"><i class="material-icons">delete</i></button>'+'</td></tr>'
  );
  $.toaster({priority : 'success',title : '¡Éxito!',message : 'Factura/Nota agregada con éxito',
    settings:{'timeout':1000,'toaster':{'css':{'top':'5em'}}}
  });
};

function validateDetail() {
  $('#folio_fac').rules('add',{
    required: true,
    maxlength: 15,
    messages:{
      required: 'Ingresa el número de folio o número de la nota'
    }
  });
  $('#fk_id_tipo').rules('add',{
    required: true,
    messages:{
      required: 'Es necesario seleccionar el concepto'
    }
  });
  $('#fk_id_impuesto').rules('add',{
    required: true,
    messages:{
      required: 'Es necesario seleccionar el tipo de impuesto'
    }
  });
  $.validator.addMethod('subtotal',function (value,element) {
      return this.optional(element) || /^\d{0,6}(\.\d{0,2})?$/g.test(value);
  },'El subtotal no tiene un formato´válido');
  $.validator.addMethod( "greaterThan", function( value, element, param ) {

    if ( this.settings.onfocusout ) {
        $(element).addClass( "validate-greaterThan-blur" ).on( "blur.validate-greaterThan", function() {
            $( element ).valid();
        } );
    }

      return value > param;
  }, "Ingresa un valor mayor a 0" );
  $('#subtotal_fac').rules('add',{
      required: true,
      subtotal:true,
      greaterThan:0,
      messages:{
          required: 'Para continuar es necesario un subtotal',
          greaterThan: 'El número debe ser mayor a 0',
      }
  });
};

function limpiarCampos() {
  initSelect2();
  $('#fk_id_impuesto').val(0)
  $('#folio_fac').val('');
  $('#subtotal_fac').val('');
  $('#total_fac').val('');
  $('#impuesto').val(0);
  //Eliminar reglas de validación detalle
  $('#fk_id_tipo').rules('remove');
  $('#fk_id_impuesto').rules('remove');
  $('#folio_fac').rules('remove');
  $('#fk_id_impuesto').rules('remove');
  $('#subtotal_fac').rules('remove');
};

function borrarFila(el) {
  var tr = $(el).closest('tr');
  var trTotal = $(el).parent().parent().find('input.total').val();
  var trSubtotal = $(el).parent().parent().find('input.subtotal').val();
  var hiddenTotal = $('#total_detalles').val();
  var hiddenSubtotal = $('#subtotal_detalles').val();
  var overallTotal = hiddenTotal - trTotal;
  var overallSubtotal = hiddenSubtotal - trSubtotal;
  $('#total_detalles').val(overallTotal.toFixed(2));
  $('#subtotal_detalles').val(overallSubtotal.toFixed(2));

  tr.fadeOut(400, function(){
    tr.remove().stop();
  })
  $.toaster({priority : 'success',title : '¡Advertencia!',message : 'Se ha eliminado la fila correctamente',
      settings:{'timeout':1000,'toaster':{'css':{'top':'5em'}}}});
};

function calcular(){
  var subtotal = +$('#subtotal_fac').val();
  var impuesto = +$('#impuesto').val();
  var totalImpuesto = subtotal * impuesto;
  var total = subtotal + totalImpuesto
  $('#total_fac').val(total.toFixed(2));
}

function initSelect2(){
  $('#fk_id_tipo').select2({
    placeholder: "Seleccione el concepto de la nota",
    allowClear: true
  });
  $('#fk_id_empleado.select2').select2({
    placeholder: "Seleccione el empleado",
    allowClear: true
  });
};

function ingresarDias(){
  var fecha1 = $('#periodo_inicio').pickadate('picker');
  var fecha2 = $('#periodo_fin').pickadate('picker');
  var datoFinal = $('#total_dias');
  var fTotal = "";

  var start= fecha1.get('select', 'yyyy/mm/dd');
  var end= fecha2.get('select', 'yyyy/mm/dd');

  start = new Date(start).getTime()
  end = new Date(end).getTime()

  var fTotal = end - start;
  var days = Math.floor(fTotal / (1000 * 60 * 60 * 24));

  //Función para indicar que la segunda fecha tome el valor de la primera
  fecha1.on('set', function(event) {
    if ( 'select' in event ) {
      fecha2.start().clear().set('min', fecha1.get('select'));
    }
    if ( 'clear' in event ) {
      fecha2.clear().set('min', false).stop();
      $('#periodo_fin').prop('readonly', true);
    }
  });
  //Condición para ingresar el total
  if(days >= 0)
    datoFinal.val(days);
  else
    datoFinal.val("N/A");
};