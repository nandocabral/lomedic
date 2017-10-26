@extends('layouts.dashboard')

@section('title', 'Modulos')

@section('header-top')
	<!--dataTable.css-->
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">
@endsection

@section('header-bottom')
	<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
	<script src="{{ asset('js/modulos.js') }}"></script>
	<!-- <script src="https://cdn.datatables.net/v/dt/jszip-2.5.0/pdfmake-0.1.18/dt-1.10.12/b-1.2.2/b-colvis-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/r-2.1.0/datatables.min.js"></script> -->
@endsection

@section('content')
<div class="col-12">
	<p class="text-right">
		<a href="{{ companyRoute('create') }}" class="btn btn-primary">Nuevo</a>
		<a href="{{ companyRoute('index') }}" class="btn btn-info"><i class="material-icons align-middle">cached</i> Actualizar</a>
	</p>
</div>
@if (session('success'))
<div class="col-12">
	<div class="alert alert-success">
		{{ session('success') }}
	</div>
</div>
@endif
<div class="col-12">
	<table class="table table-striped table-responsive-sm table-hover">
		<thead>
			<tr>
				<th>Modulos</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		@foreach ($data as $modulo)
		<tr>
			<td>{{ $modulo->nombre }}</td>
			<td class="width-auto">
				<a href="{{ companyRoute('show', ['id' => $modulo->id_modulo]) }}" class="btn is-icon" data-toggle="tooltip" data-placement="top" title="Ver"><i class="material-icons">visibility</i></a>
				<a href="{{ companyRoute('edit', ['id' => $modulo->id_modulo]) }}" class="btn is-icon" data-toggle="tooltip" data-placement="top" title="Editar"><i class="material-icons">mode_edit</i></a>
				<a href="#" class="btn is-icon" onclick="event.preventDefault(); document.getElementById('delete-form-{{$modulo->id_modulo}}').submit();" data-toggle="tooltip" data-placement="top" title="Borrar"><i class="material-icons">delete</i></a>
				<form id="delete-form-{{$modulo->id_modulo}}" action="{{ companyRoute('destroy', ['id' => $modulo->id_modulo]) }}" method="POST" style="display: none;">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}
				</form>
			</td>
		</tr>
		@endforeach
		</tbody>
	</table>
</div>
@endsection
