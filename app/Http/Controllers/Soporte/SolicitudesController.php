<?php

namespace App\Http\Controllers\Soporte;

//Used models
use App\Http\Controllers\Controller;
use App\Http\Models\Soporte\SeguimientoSolicitudes;
use App\Http\Models\Soporte\Solicitudes;
use App\Http\Models\Administracion\Usuarios;
use App\Http\Models\RecursosHumanos\Empleados;
use App\Http\Models\Administracion\Empresas;
use App\Http\Models\Administracion\Sucursales;
use App\Http\Models\Soporte\ArchivosAdjuntos;
use App\Http\Models\Soporte\EstatusTickets;
use App\Http\Models\Soporte\Categorias;
use App\Http\Models\Soporte\Subcategorias;
use App\Http\Models\Soporte\Acciones;
use App\Http\Models\Soporte\Prioridades;
use App\Http\Models\Soporte\ModosContacto;
use App\Http\Models\Soporte\Impactos;
use App\Http\Models\Soporte\Urgencias;
use App\Http\Models\Logs;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class SolicitudesController extends Controller
{
    public function __construct(Solicitudes $entity)
    {
        $this->entity = $entity;
        $this->entity_name = strtolower(class_basename($entity));
    }

    public function index($company)
    {
        Logs::createLog($this->entity->getTable(),$company,null,'index', null);

        return view(Route::currentRouteName(),[
            'entity' => $this->entity_name,
            'company' => $company,
            'data' => $this->entity->all()->where('eliminar','0')
                ->where('fk_id_empleado_solicitud',Auth::id()),
        ]);
    }

    public function index_tecnicos($company)
    {
        return view(Route::currentRouteName(),[
            'entity' => $this->entity_name,
            'company' => $company,
            'data' => $this->entity->all()->where('eliminar','0')
                ->where('fk_id_empleado_tecnico',null),
        ]);
    }

    public function index_tecnico($company)
    {
        $id_empleado = Empleados::findOrFail(Usuarios::where('id_usuario', Auth::id())
            ->first()->
            fk_id_empleado)->id_empleado;

        return view(Route::currentRouteName(),[
            'entity' => $this->entity_name,
            'company' => $company,
            'data' => $this->entity->all()->where('eliminar','0')
                ->where('fk_id_empleado_tecnico',$id_empleado),
        ]);
    }

    public function store(Request $request, $company)//Para crear un nuevo ticket
    {
//        dd($request->request);

        if($request->nombre_solicitante == '' || $request->nombre_solicitante == null){//Comprobar si es para otro usuario o no
            //Obtener nombre de empleado
            $fk_id_empleado = Usuarios::where('id_usuario',Auth::id())->first()->fk_id_empleado;
            $nombre_empleado = Empleados::where('id_empleado',$fk_id_empleado)->first()->nombre;
            $apellido_paterno = Empleados::where('id_empleado',$fk_id_empleado)->first()->apellido_paterno;
            $apellido_materno = Empleados::where('id_empleado',$fk_id_empleado)->first()->apellido_materno;
            $request->request->set('nombre_solicitante',$nombre_empleado." ".$apellido_paterno." ".$apellido_materno);
        }

        $request->request->set('fk_id_estatus_ticket',2);//Estatus "Abierto"
        $request->request->set('fk_id_modo_contacto',1);//Se contacó por medio del sistema de tickets
        $request->request->set('fk_id_empleado_solicitud',Auth::id());
        $request->request->set('fk_id_empresa_empleado_solicitud',
            Empresas::where('conexion',$company)
                ->first()->id_empresa);//Empresa del empleado que solicitó el ticket
//        dd($request->request);
        $this->validate($request,$this->entity->rules);
        $created = $this->entity->create($request->all());
        if($created)
            {
                $files = Input::file('archivo');
                if(Input::hasFile('archivo'))
                {
                    foreach ($files as $file){
                        $path = public_path().'\storage\\'.$company.'\ticket'.$created->id_solicitud;
                        $filename = uniqid().$file->getClientOriginalName();
                        $file->move($path,$filename);
                        $archivo_adjunto = new ArchivosAdjuntos();
                        $archivo_adjunto->fk_id_solicitud = $created->id_solicitud;
                        $archivo_adjunto->ruta_archivo = $path;
                        $archivo_adjunto->nombre_archivo = $filename;
                        $archivo_adjunto->save();
                    }
                }

                Logs::createLog($this->entity->getTable(),$company,$created->id_solicitud,'crear','Ticket creado');
            }
        else
            {Logs::createLog($this->entity->getTable(),$company,null,'crear','Error al crear ticket');}

        return redirect(URL::previous());
    }

    public function show($company, $id)
    {
        Logs::createLog($this->entity->getTable(),$company,$id,'ver',null);
        return view (Route::currentRouteName(), [
            'entity' => $this->entity_name,
            'company' => $company,
            'data' => $this->entity->findOrFail($id),
            'employees' => Empleados::all(),
            'status' => EstatusTickets::all(),
            'impacts' => Impactos::all(),
            'urgencies' => Urgencias::all(),
            'employee_department' => Empleados::findOrFail(Usuarios::where('id_usuario', Auth::id())
                ->first()->
                fk_id_empleado)->fk_id_departamento,
        ]);
    }

    public function update(Request $request, $company, $id)
    {
//        dd($request->request);

        $entity = $this->entity->findOrFail($id);

        $entity->setAttribute('fecha_hora_resolucion','now()');
        $entity->fill($request->all());
        if($entity->save())
        {Logs::createLog($this->entity->getTable(),$company,$id,'editar','Registro actualizado');}
        else
        {Logs::createLog($this->entity->getTable(),$company,$id,'editar','Error al editar');}

        # Redirigimos a index
        return redirect(companyAction('index'));
    }

    public function obtenerSubcategorias($company, $id)
    {
        $subcategorias = Categorias::all()->find($id)->subcategorias->where('activo','1');
        foreach ($subcategorias as $subcategoria) {
            $subcategoria->url = companyAction('obtenerAcciones', ['id' =>$subcategoria->id_subcategoria]);
        }

        return Response::json($subcategorias->toArray());
    }

    public function obtenerAcciones($company,$id)
    {
        $acciones = Subcategorias::all()->find($id)->acciones->where('activo','1')->toArray();
        return Response::json($acciones);
    }

    public function descargarArchivosAdjuntos($company,$id)
    {
        $archivo = ArchivosAdjuntos::where('id_archivo_adjunto',$id)->first();
        Logs::createLog($archivo->getTable(),$company,$archivo->id_archivo_adjunto,'descargar','Archivo adjunto de ticket');
        return Response::download($archivo->ruta_archivo.'/'.$archivo->nombre_archivo);
    }

}
