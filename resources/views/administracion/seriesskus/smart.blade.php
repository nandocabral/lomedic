@extends(smart())
@section('content-width', 's12')
@section('form-title', 'Series para Skus')

@section('header-bottom')
    @parent
    <script type="text/javascript">
		$(document).ready(function() {
        	$("#primer_numero").on('change',function(){
            	var primero = parseInt($("#primer_numero").val());
        		$("#numero_siguiente").val(primero);
        		$("#ultimo_numero").attr('min',primero+1);
        	});

        });
    </script>
@endsection

@section('form-content')
    {{ Form::setModel($data) }}
    <div class="row">
        <div class="form-group col-md-12 col-lg-6">
            {{ Form::cText('* Nombre Serie','nombre_serie') }}
        </div>
        <div class="form-group col-md-4 col-lg-2">
            {{ Form::cText('* Prefijo','prefijo', null) }}
        </div>
        <div class="form-group col-md-4 col-lg-2">
            {{ Form::cNumber('* Primer Numero','primer_numero') }}
        </div>
        <div class="form-group col-md-4 col-lg-2">
            {{ Form::cNumber('Ultimo Numero','ultimo_numero') }}
        </div>
        <div class="form-group col-md-4 col-lg-2">
            {{ Form::cNumber('Siguiente Numero','numero_siguiente',['readonly'=>true]) }}
        </div>
        <div class="form-group col-md-4 col-lg-2">
            {{ Form::cText('Sufijo','sufijo', null) }}
        </div>
        <div class="form-group col-md-12 col-xs-12">
        	{{ Form::cTextArea('Descripcion','descripcion', null) }}
        </div>
    
        <div  class="col-md-12 text-center mt-2">
            <div class="alert alert-warning" role="alert">
                Recuerda que al no estar <b>activo</b>, este <b>dato</b> no se mostrara en los modulos correspondientes que se requieran.
            </div>
            {{ Form::cCheckboxBtn('Estatus','Activo','activo', $data['activo'] ?? null, 'Inactivo') }}
        </div>
    </div>
@endsection