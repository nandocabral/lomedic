@extends('layouts.dashboard')

@section('title', 'Editar')

@section('header-top')
@endsection

@section('header-bottom')
	<script src="{{ asset('js/dataTableGeneralConfig.js') }}"></script>
@endsection

@section('content')
<div class="col s12 xl8 offset-xl2">
	<p class="left-align">
		<a href="{{ url()->previous() }}" class="waves-effect waves-light btn">Regresar</a> <br>
	</p>
	<div class="divider"></div>
</div>
<div class="col s12 xl8 offset-xl2">
	<h4>Editar {{ trans_choice('messages.'.$entity, 0) }}</h4>
</div>

<div class="col s12 xl8 offset-xl2">
	<div class="row">
		<form action="{{ companyRoute("update", ['company'=> $company, 'id' => $data->id_motivo]) }}" method="post" class="col s12">
			{{ csrf_field() }}
			{{ method_field('PUT') }}
			<div class="row">
				<div class="input-field col s4">
					<input type="text" name="motivo" id="motivo" class="validate" value="{{ $data->motivo }}">
					<label for="motivo">Motivo</label>
					@if ($errors->has('motivo'))
						<span class="help-block">
							<strong>{{ $errors->first('motivo') }}</strong>
						</span>
					@endif
				</div>
				<div class="input-field col s2">
					<select name="tipo">
						<option disabled selected>Tipo</option>
						@if ($data->activo == 1)
							<option value="1" selected>Cuentas por Pagar</option>
							<option value="2">Cuentas por Cobrar</option>
						@else
							<option value="1">Cuentas por Pagar</option>
							<option value="2" selected>Cuentas por Cobrar</option>
						@endif
					</select>
					<span class="help-block">
						<strong>{{ $errors->first('tipo') }}</strong>
					</span>
				</div>
				<div class="input-field col s4">
					<p>
						@if ($data->activo == 1)
						<input type="hidden" name="activo" value="0">
						<input type="checkbox" id="activo"  name="activo" checked="true"/>
						@else
						<input type="hidden" name="activo" value="0">
						<input type="checkbox" id="activo"  name="activo" />
						@endif
						<label for="activo">¿Activo?</label>
					</p>

					@if ($errors->has('activo'))
					<span class="help-block">
						<strong>{{ $errors->first('activo') }}</strong>
					</span>
					@endif
				</div>
			</div>
			<div class="row">
			</div>
			<div class="row">
				<div class="col s12">
					<button class="waves-effect waves-light btn right">Guardar {{ trans_choice('messages.'.$entity, 0) }}</button>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
