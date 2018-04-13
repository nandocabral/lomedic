@extends(smart())
@section('content-width', 's12')

@if (Route::currentRouteNamed(currentRouteName('index')))
    @section('form-title', 'Formas de adjudicaciones')
@elseif(Route::currentRouteNamed(currentRouteName('create')))
    @section('form-title', 'Nueva forma de adjudicación')
@elseif(Route::currentRouteNamed(currentRouteName('edit')))
    @section('form-title', 'Editar forma de adjudicación')
@elseif(Route::currentRouteNamed(currentRouteName('show')))
    @section('form-title', 'Forma de adjudicación')
@endif

@section('form-content')
	{{ Form::setModel($data) }}
    <div class="row">
        <div class="form-group col-md-12 col-xs-12">
            {{ Form::cText('* Forma adjudicación','forma_adjudicacion') }}
        </div>
        <div  class="col-md-12 text-center mt-4">
            <div class="alert alert-warning" role="alert">
                Recuerda que al no estar <b>activo</b>, este <b>dato</b> no se mostrara en los modulos correspondientes que se requieran.
            </div>
            {{ Form::cCheckboxBtn('Estatus','Activo','activo', $data['activo'] ?? null, 'Inactivo') }}
        </div>
    </div>
@endsection
