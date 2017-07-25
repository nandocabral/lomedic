<?php
/**
 * Created by PhpStorm.
 * User: ihernandezt
 * Date: 20/07/2017
 * Time: 12:39
 */

/*
use App\Menu;

$Barra = New Menu();
$Acciones = $Barra->getBarra(47);

*/

?>
@extends('layouts.dashboard')

@section('title', 'Usuarios')

@section('header-top')
    <!--dataTable.css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">
@endsection

@section('header-bottom')
    <script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
    <script src="{{ asset('js/bancos.js') }}"></script>
    <!-- <script src="https://cdn.datatables.net/v/dt/jszip-2.5.0/pdfmake-0.1.18/dt-1.10.12/b-1.2.2/b-colvis-1.2.2/b-html5-1.2.2/b-print-1.2.2/cr-1.3.2/r-2.1.0/datatables.min.js"></script> -->
@endsection

@section('content')
    <div class="col s12 xl8 offset-xl2">
        <p class="right">
            <?php //echo $Acciones; ?>
            <a href="{{ route("$entity.create", ['company'=> $company]) }}" class="waves-effect waves-light btn"><i class="material-icons right">add</i>Nuevo Usuario</a> <br>
        </p>
    </div>
    @if (session('success'))
        <div class="col s12 xl8 offset-xl2">
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        </div>
    @endif
    <div class="col s12 xl8 offset-xl2">
        <table class="striped responsive-table highlight">
            <thead>
            <tr>
                <th>Usuarios</th>
                <th>Nombre Corto</th>
                <th>Activo</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($data as $usuario)
                <tr>
                    <td>{{ $usuario->usuario }}</td>
                    <td>{{ $usuario->nombre_corto }}</td>
                    <td>{{ $usuario->activo }}</td>
                    <td class="width-auto">
                        <a href="{{ route("$entity.show", ['company'=> $company, 'id' => $usuario->id_usuario]) }}" class="waves-effect waves-light btn btn-flat no-padding"><i class="material-icons">visibility</i></a>
                        <a href="{{ route("$entity.edit", ['company'=> $company, 'id' => $usuario->id_usuario]) }}" class="waves-effect waves-light btn btn-flat no-padding"><i class="material-icons">mode_edit</i></a>
                        <a href="#" class="waves-effect waves-light btn btn-flat no-padding" onclick="event.preventDefault(); document.getElementById('delete-form{{$usuario->id_usuario}}').submit();"><i class="material-icons">delete</i></a>
                        <form id="delete-form{{$usuario->id_usuario}}" action="{{ route("$entity.destroy", ['company'=> $company, 'id' => $usuario->id_usuario]) }}" method="POST" style="display: none;">
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
