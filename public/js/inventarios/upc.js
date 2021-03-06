$(document).ready(function () {
    ($('#fk_id_subgrupo option:selected').val() > 0)
        ? showMeTheTables(optionSelected = $('#fk_id_subgrupo option:selected').data())
        : '';
    $('#addIndication').on('click', function () {
        addIndication();
    });
    $('#addPresentation').on('click', function () {
        addSalt();
    });
    $('#addMaterial').on('click',function(){
        addMaterial();
    });
	$('#fk_id_forma_farmaceutica, #fk_id_presentaciones').on('change',function(){
		return validateFieldsforUPC();
	})
    $(document).on('submit', function (e) {
        if($('#fk_id_subgrupo').val() == ''){
            e.preventDefault();
            mensajeAlerta("Es necesario que ingreses el Subgrupo para posteriormente indicar su composición.", "danger");
        }
        if($('#tbodyPresentation tr').length == 0 && $('#tbodyMaterials tr').length == 0){
            e.preventDefault();
            mensajeAlerta("Es necesario indicar la(s) sales y/o materiales del UPC", "danger");
        }
    });
    $('[data-toggle]').tooltip();
    $('.nav-link').on('click', function (e) {
        e.preventDefault();
        $('#clothing-nav li').each(function () {
            $(this).children().removeClass('active');
        });
        $('.tab-pane').removeClass('active').removeClass('show');
        $(this).addClass('active');
        let tab = $(this).prop('href');
        tab = tab.split('#');
        $('#' + tab[1]).addClass('active').addClass('show');
    });
    $('#fk_id_subgrupo').on('change',function(){
        $('#tbodyIndication').empty();
        $('#tbodyPresentation').empty();
        $('#tbodyMaterials').empty();
        const optionSelected = $('#fk_id_subgrupo option:selected').data()
        showMeTheTables(optionSelected);
    })
	if($('#fk_id_forma_farmaceutica').val() > 0 && $('#fk_id_presentaciones').val() > 0 && ($('#tbodyPresentation tr').length > 0 || $('#tbodyMaterials tr').length > 0)){
		validateFieldsforUPC();
	}
});

function showMeTheTables(optionSelected){
    let $tIndication = $('#tableIndicacion');
    let $tSal = $('#tableSal');
    let $tMaterial = $('#tableMaterial');
    if(optionSelected.sales == true){
        $tIndication.show('slow');
        $tSal.show('slow');
    } else{
        $tIndication.hide('slow');
        $tSal.hide('slow');
    }
    if(optionSelected.especificaciones == true){
        $tMaterial.show('slow');
    } else{
        $tMaterial.hide('slow');
    }
}

function addIndication(){
    let indicationId = $('#indicacion option:selected').val();
    let indicationText = $('#indicacion option:selected').text();
    let $tbody = $('#tbodyIndication');
    let i = $('#tbodyIndication > tr').length;
    let row_id = i > 0 ? +$('#tbodyIndication > tr:last').find('#index').val()+1 : 0;
    let existe = false;

    if(i > 0){
        $tbody.children().each(function(){
            let indiRow = $(this).find('.id_indi').val()
            if(indiRow == indicationId){
                existe = true;
            }
        });
    }
    if(!existe){
        $tbody.append(
            '<tr>'+
            '<td>' + '<input type="hidden" id="index" value="'+row_id+'"><input class="id_indi" type="hidden" name="relations[has][indicaciones]['+row_id+'][fk_id_indicacion_terapeutica]" value="'+indicationId+'">' + indicationText + '</td>' +
            '<td>' + '<button data-toggle="Eliminar" data-placement="top" title="Eliminar" data-original-title="Eliminar" type="button" class="text-primary btn btn_tables is-icon eliminar bg-white" data-delay="50" onclick="borrarFila(this)"><i class="material-icons">delete</i></button>' + '</td>' +
             +'</tr>'
        );
        mensajeAlerta("Elemento agregado con éxito","success");
        $('[data-toggle]').tooltip();
    } else {
		mensajeAlerta(`La indicación: <b>${indicationText}</b> ya existe, prueba con otra indicación.`,"warning");
	}
}

function addSalt(){
    let salId = $('#sal option:selected').val();
    let salText = $('#sal option:selected').text();
    let concentrationId = $('#concentracion option:selected').val();
    let concentrationText = $('#concentracion option:selected').text();
    let $tbody = $('#tbodyPresentation');
    let i = $('#tbodyPresentation > tr').length;
    let row_id = i > 0 ? +$('#tbodyPresentation > tr:last').find('#index').val()+1 : 0;
    let existe = false;

    if(i > 0){
        $tbody.children().each(function(){
            let salRow = $(this).find('.id_sal').val()
            let concRow = $(this).find('.id_concentracion').val();
            if(salRow == salId && concRow == concentrationId){
                existe = true;
            }
        });
    }

    if(!existe){
        $tbody.append(
            '<tr>'+
            '<td>' + '<input type="hidden" id="index" value="'+row_id+'"><input class="id_concentracion" type="hidden" name="relations[has][presentaciones]['+row_id+'][fk_id_presentaciones]" value="'+concentrationId+'"><input class="id_sal" type="hidden" name="relations[has][presentaciones]['+row_id+'][fk_id_sal]" value="'+salId+'">' + salText + '</td>' +
            '<td>' + concentrationText + '</td>' +
            '<td>' + '<button data-toggle="Eliminar" data-placement="top" title="Eliminar" data-original-title="Eliminar" type="button" class="text-primary btn btn_tables is-icon eliminar bg-white" data-delay="50" onclick="borrarFila(this)"><i class="material-icons">delete</i></button>' + '</td>' +
             +'</tr>'
        );
        mensajeAlerta("Elemento agregado con éxito","success");
        $('[data-toggle]').tooltip();
        validateFieldsforUPC()
    } else {
		mensajeAlerta(`La sal: <b>${salText}</b> y la cantidad: <b>${concentrationText}</b> ya existe, prueba con otra concentración.`,"warning");
	}
}

function addMaterial(){
    let materialId = $('#especificacion option:selected').val();
    let materialText = $('#especificacion option:selected').text();
    let $tbody = $('#tbodyMaterials');
    let i = $('#tbodyMaterials > tr').length;
    let row_id = i > 0 ? +$('#tbodyMaterials > tr:last').find('#index').val()+1 : 0;
    let existe = false;

    if(i > 0){
        $tbody.children().each(function(){
            let matRow = $(this).find('.id_mat').val()
            if(matRow == materialId){
                existe = true;
            }
        })
    }

    if(!existe){
        $tbody.append(
            '<tr>'+
            '<td>' + '<input type="hidden" id="index" value="'+row_id+'"><input class="id_mat" type="hidden" name="especificaciones['+row_id+'][fk_id_especificacion]" value="'+materialId+'">' + materialText + '</td>' +
            '<td>' + '<button data-toggle="Eliminar" data-placement="top" title="Eliminar" data-original-title="Eliminar" type="button" class="text-primary btn btn_tables is-icon eliminar bg-white" data-delay="50" onclick="borrarFila(this)"><i class="material-icons">delete</i></button>' + '</td>' +
             +'</tr>'
        );
        mensajeAlerta("Elemento agregado con éxito","success");
        $('[data-toggle]').tooltip();
        validateFieldsforUPC()
    } else {
		mensajeAlerta(`El material: <b>${materialText}</b> ya existe, prueba con otro material.`,"warning");
	}
}

function validateFieldsforUPC(){
	const formaFarma = $('#fk_id_forma_farmaceutica').val();
    const $matTable = $('#tbodyMaterials tr');
    const $preTable = $('#tbodyPresentation tr');
    if($matTable.length > 0 || $preTable.length > 0){
        return getUpcs($matTable,$preTable,formaFarma);
    }
}

function addUpcs(response){
	let $tbody = $('#tbodyClients');
	$tbody.empty();
	for (const key in response) {
		if (response.hasOwnProperty(key)) {
			$tbody.append(
				'<tr>'+
				'<td>' + response[key].clave_producto_cliente + '</td>' +
				'<td>' + response[key].subclave + '</td>' +
				'<td>' + response[key].descripcion + '</td>' +
				'<td>' + '$' +  response[key].precio + '</td>' +
				+'</tr>'
			);
		}
	}
    mensajeAlerta("Detectamos que este UPC cuenta con las mismas características que otros ya <b>relacionados a claves Cliente</b>, le recomendamos verificar en la pestaña <b>Clientes</b>.","success");
    $('[data-toggle]').tooltip();
}

function getUpcs($matTable,$preTable,formaFarma){
	let	idPresentaciones = $('#fk_id_presentaciones').val();
    let sales = [];
    let especificaciones = [];
    let upc = true;
	if($preTable.length > 0){
		for (const row of $preTable) {
			let sal =  +$(row).find('.id_sal').val();
			let concentracion =  +$(row).find('.id_concentracion').val();
			let obj = {};
			obj.fk_id_sal = sal;
			obj.fk_id_presentaciones = concentracion;
			sales.push(obj);
		}
    }
    if($matTable.length > 0){        
        for (const row of $matTable) {
            let mat =  +$(row).find('.id_mat').val();
            especificaciones.push(mat);
        }
    }
    $.ajax({
        url: $('#upc').data('url'),
        data: {
            'id_forma':formaFarma,
            'id_presentaciones':idPresentaciones,
            'arr_especificaciones':JSON.stringify(especificaciones),
            'arr_sales':JSON.stringify(sales),
            'upc':upc,
        },
        dataType: "json",
        success: function (response) {
            let type = typeof(response);
            if(response.length > 0 || (type == "object" && Object.keys(response).length > 0)){
                (type == "object") ? $('#current_clients').text(Object.keys(response).length) : $('#current_clients').text(response.length);
                addUpcs(response);
            }else{
                mensajeAlerta("Al parecer este UPC <b>no se encuentra relacionado a una clave cliente</b>, en caso requerido verifica los datos","warning");
            }
        }
    });
}

function borrarFila(el) {
    var tr = $(el).closest('tr');
    tr.fadeOut(400, function(){
      tr.remove().stop();
      $('#tbodyClients').empty();
      $('#current_clients').text(0);
      mensajeAlerta("Fila eliminada con éxito","success");
      validateFieldsforUPC();
    })
  };

function mensajeAlerta(mensaje,tipo){
    var titulo = '';
    if(tipo == 'danger'){ titulo = '¡Error!'}
    else if(tipo == 'success'){titulo = '¡Correcto!' }
    else if(tipo == 'warning'){titulo = '¡Advertencia!' }
    $.toaster({priority:tipo,
            title: titulo,
            message:mensaje,
            settings:{'timeout':8000,
                'toaster':{'css':{'top':'5em'}}}
        }
    );
}