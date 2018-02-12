@extends('handheld.layout')

@section('title', 'Handheld - Ordenes')
{{ session('message') ? HTML::tag('p', session('message'), ['class'=>'success-message']) : '' }}
@section('content')
	<p style="text-align: center;margin-bottom:0;">Selecciona el <b>Orden de compra:</b></p>
	<div id="navigation">
		@foreach ($ordenes as $orden)
		{{-- {{dump($orden)}} --}}
			{{ link_to(companyRoute('handheld.orden-compra', ['id' => $orden->id_orden]), $orden->id_orden, ['class'=>'list-item']) }}
		@endforeach
	</div><br>
	{{ link_to(route('home'), 'Regresar', ['class'=>'square actionBtn red','style'=>'width:100%;']) }}
@endsection