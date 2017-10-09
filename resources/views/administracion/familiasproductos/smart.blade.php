
@section('content-width', 's12')

@section('form-content')
{{ Form::setModel($data) }}
<div class="row">
    <div class="form-group col-md-6 col-xs-12">
        {{ Form::label('descripcion', 'Familia') }}
        {{ Form::text('descripcion', null, ['id'=>'descripcion','class'=>'form-control']) }}
        {{ $errors->has('descripcion') ? HTML::tag('span', $errors->first('descripcion'), ['class'=>'help-block deep-orange-text']) : '' }}
    </div>
    <div class="form-group col-md-6 col-xs-12">
        {{ Form::label('nomenclatura', 'Nomenclatura') }}
        {{ Form::text('nomenclatura', null, ['id'=>'nomenclatura','class'=>'form-control']) }}
        {{ $errors->has('nomenclatura') ? HTML::tag('span', $errors->first('nomenclatura'), ['class'=>'help-block deep-orange-text']) : '' }}
    </div>
    <div class="form-group col-md-6 col-xs-12">
        {{ Form::label('fk_id_tipo_producto', 'Tipo') }}
        {{Form::select('fk_id_tipo_producto',isset($product_types)?$product_types:[],null,['id'=>'fk_id_tipo_producto','class'=>'form-control'])}}
        {{ $errors->has('tipo') ? HTML::tag('span', $errors->first('tipo'), ['class'=>'help-block deep-orange-text']) : '' }}
    </div>
    <div class="form-group col-md-6 col-xs-12">
        {{ Form::label('tipo_presentacion', 'Presentacion') }}
        {{ Form::select('tipo_presentacion',
        ['1'=>'Cantidad',
        '2'=>'Cantidad y Unidad',
        '3'=>'Ampolletas (Ã�mpulas)',
        '4'=>'Dosis'],
        null, ['id'=>'tipo_presentacion','class'=>'form-control']) }}
        {{ $errors->has('tipo_presentacion') ? HTML::tag('span', $errors->first('tipo_presentacion'), ['class'=>'help-block deep-orange-text']) : '' }}
    </div>
    <div  class="col-md-12 text-center mt-2">
        <div class="alert alert-warning" role="alert">
            Recuerda que al no estar <b>activo</b>, este <b>dato</b> no se mostrara en los modulos correspondientes que se requieran.
        </div>
        {{ Form::cCheckboxBtn('Activo','activo', $data['activo'] ?? null) }}
    </div>
</div>
@endsection

{{-- DONT DELETE --}}
@if (Route::currentRouteNamed(currentRouteName('index')))
    @include('layouts.smart.index')
@endif

@if (Route::currentRouteNamed(currentRouteName('create')))
    @include('layouts.smart.create')
@endif

@if (Route::currentRouteNamed(currentRouteName('edit')))
    @include('layouts.smart.edit')
@endif

@if (Route::currentRouteNamed(currentRouteName('show')))
    @include('layouts.smart.show')
@endif

@if (Route::currentRouteNamed(currentRouteName('export')))
    @include('layouts.smart.export')
@endif