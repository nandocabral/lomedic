@extends('layouts.dashboard')

@section('title', 'Crear')

@section('header-top')
@endsection

@section('header-bottom')
	<script src="{{ asset('js/modulos.js') }}"></script>
@endsection

@section('content')

<form action="{{ companyRoute('index') }}" method="post" class="col s12">
	{{ csrf_field() }}
	{{ method_field('POST') }}
	<div class="col s12 xl8 offset-xl2">
		<div class="row">
			<div class="right">
				<button type="submit" class="waves-effect btn orange">Guardar y salir</button>
				<a href="{{ url()->previous() }}" class="waves-effect waves-teal btn-flat teal-text">Cancelar y salir</a>
			</div>
		</div>
	</div>
	<div class="col s12 xl8 offset-xl2">
		<h5>Datos del Diagnostico</h5>
			<div class="row">
				<div class="input-field col s12 m5">
					<input type="text" name="clave_diagnostico" id="clave_diagnostico" class="validate">
					<label for="Calve">Calve:</label>
					@if ($errors->has('clave_diagnostico'))
						<span class="help-block">
							<strong>{{ $errors->first('clave_diagnostico') }}</strong>
						</span>
					@endif
				</div>
				<div class="input-field col s12 m7">
					<input type="text" name="diagnostico" id="diagnostico" class="validate">
					<label for="Diagnostico">Diagnostico:</label>
					@if ($errors->has('diagnostico'))
						<span class="help-block">
							<strong>{{ $errors->first('diagnostico') }}</strong>
						</span>
					@endif
				</div>
			</div>
			<div class="row">
				<div class="input-field col s12 m5">
					<input type="text" name="medicamento_sugerido" id="medicamento_sugerido" class="validate">
					<label for="Medicamento Sugerido">Medicamento Sugerido:</label>
					@if ($errors->has('medicamento_sugerido'))
						<span class="help-block">
							<strong>{{ $errors->first('medicamento_sugerido') }}</strong>
						</span>
					@endif
				</div>
				<div class="input-field col s12 l6 xl3">
					<p>
						<input type="checkbox" id="estatus" name="estatus" />
						<label for="Estatus">Estatus</label>
						@if ($errors->has('estatus'))
							<span class="help-block">
								<strong>{{ $errors->first('estatus') }}</strong>
							</span>
						@endif
					</p>
				</div>
			</div>
	</div>
</form>
@endsection