@extends(smart())
@section('content-width', 's12')

@section('form-content')
    {{ Form::setModel($data) }}
    <div class="row">
    	<div class="form-group col-12">
    		{{ Form::label('grupo', '* Grupo') }}
    		{{ Form::text('grupo', null, ['id'=>'grupo','class'=>'form-control']) }}
    		{{ $errors->has('grupo') ? HTML::tag('span', $errors->first('grupo'), ['class'=>'help-block deep-orange-text']) : '' }}
    	</div>
    		
    	<div  class="col-12 text-center mt-2">
    		<div class="alert alert-warning" role="alert">
                Recuerda que al no estar <b>activo</b>, este <b>dato</b> no se mostrara en los modulos correspondientes que se requieran.
            </div>
            {{ Form::cCheckboxBtn('Estatus','Activo','activo', $data['activo'] ?? null, 'Inactivo') }}
    	</div>
    </div>
@endsection