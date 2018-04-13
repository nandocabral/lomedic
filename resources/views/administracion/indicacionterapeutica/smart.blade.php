@extends(smart())
@section('content-width', 's12')

@if (Route::currentRouteNamed(currentRouteName('index')))
    @section('form-title', 'Indicaciones terapéuticas')
@elseif(Route::currentRouteNamed(currentRouteName('create')))
    @section('form-title', 'Nueva indicación terapéutica')
@elseif(Route::currentRouteNamed(currentRouteName('edit')))
    @section('form-title', 'Editar indicación terapéutica')
@elseif(Route::currentRouteNamed(currentRouteName('show')))
    @section('form-title', 'Indicación terapéutica')
@endif

@section('form-content')
    {{ Form::setModel($data) }}
    <div class="row">
        <div class="form-group col-md-6 col-xs-12">
            {{ Form::cText('* Indicacion Terapeutica','indicacion_terapeutica') }}
        </div>
        <div class="form-group col-md-6 col-xs-12">
            {{ Form::cText('Descripcion','descripcion') }}
        </div>
        <div  class="col-md-12 text-center mt-2">
            <div class="alert alert-warning" role="alert">
                Recuerda que al no estar <b>activo</b>, este <b>dato</b> no se mostrara en los modulos correspondientes que se requieran.
            </div>
            {{ Form::cCheckboxBtn('Estatus','Activo','activo', $data['activo'] ?? null, 'Inactivo') }}
        </div>
    </div>
@endsection