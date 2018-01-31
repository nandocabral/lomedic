@extends(smart())
@section('content-width', 's12')

@section('header-bottom')
    @parent
    @if (!Route::currentRouteNamed(currentRouteName('index')) && !Route::currentRouteNamed(currentRouteName('show')) )
        <script type="text/javascript" src="{{ asset('js/pacientes.js') }}"></script>
    @endif
@endsection

@section('form-content')
    {{ Form::setModel($data) }}

    <div class="card z-depth-1-half">

    @if (Route::currentRouteNamed(currentRouteName('create')))
    <div class="card-header">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="tab-content">
                    <div class="tab-pane active" role="tabpanel">
                        <div class="row">

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cSelect('Paciente', 'fk_id_afiliacion', $afiliados ?? [],['class'=>'select2','data-url'=>companyRoute('getDependientes')]) }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cText('Numero de paciente', 'id_afiliacion',['class'=>'form-control'])}}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{ Form::cSelect('Parentesco', 'fk_id_parentesco', $parentescos ?? [],['class'=>'select2']) }}
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    {{Form::cRadio('Genero','genero',['M'=>'Masculino','F'=>'Femenino'],['class'=>'form-control'])}}
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    {{Form::cDate('Fecha de solicitud','fecha_nacimiento')}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cText('Nombre', 'nombre',['class'=>'form-control'])}}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cText('Apellido paterno', 'paterno',['class'=>'form-control'])}}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {{ Form::cText('Apellido materno', 'materno',['class'=>'form-control']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!--/row-->
        </div>
    </div>
    @endif
    @if(!Route::currentRouteNamed(currentRouteName('index')))
    <div class="card-body row table-responsive">
        <table class="table highlight mt-3" id="tContactos">
            <thead>
            <tr>
                <th>Numero de paciente</th>
                <th>Nombre</th>
                <th>Genero</th>
                <th>Parentesco</th>
                <th>Fecha de nacimiento</th>
            </tr>
            </thead>
            <tbody class="dependientes">

                @if(!Route::currentRouteNamed(currentRouteName('create')))
                    @foreach($afiliados as $afiliado)
                        <tr>
                            <td>{{$afiliado['id_afiliacion']}}</td>
                            <td>{{$afiliado['nombre'].' '.$afiliado['paterno'].' '.$afiliado['materno']}}</td>
                            <td>{{$afiliado['genero']}}</td>
                            <td>{{$afiliado->parentesco->nombre}}</td>
                            <td>{{$afiliado->fecha_nacimiento}}</td>

                            {{--<td>--}}
                            {{--<input name="relations[has][detalles][{{$row}}][id_receta_detalle]" type="hidden" value="{{$detalle->id_receta_detalle}}">--}}
                            {{--<p><input id="clave_cliente" name="relations[has][detalles][{{$row}}][fk_id_clave_cliente_producto]" type="hidden" value="{{$detalle->producto['id_sku']}}">{{$detalle->producto['descripcion']}}</p>--}}
                            {{--<p><input id="tbdosis" name="relations[has][detalles][{{$row}}][dosis]" type="hidden" value="{{$detalle->dosis}}">{{$detalle->dosis}}</p>--}}
                            {{--<input id="tbveces_surtir" name="relations[has][detalles][{{$row}}][veces_surtir]" type="hidden" value="{{$detalle->veces_surtidas}}">--}}
                            {{--</td>--}}
                            {{--<td>--}}
                            {{--<a data-delete-type="single"  data-toggle="tooltip" data-placement="top" title="Borrar"  id="{{$row}}" aria-describedby="tooltip687783" onclick="eliminarFila(this)" ><i class="material-icons text-primary">delete</i></a>--}}
                            {{--</td>--}}
                        </tr>
                     @endforeach
                @endif
            </tbody>
        </table>

    </div>
    @endif
</div>



@endsection
