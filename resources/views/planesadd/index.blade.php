@extends('productos.layout')
 
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Planes Adicionales</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('planadd.create') }}"> Crear Un nuevo Plan </a>
            </div>
        </div>
    </div>
   
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif
   
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Fecha Creacion</th>
            <th>Creado Por </th>
           
            <th>Fecha Modificación</th>
            <th>Modificado Por </th>
            <th>Precio</th>
            <th>Estado</th>

            <th width="280px">Action</th>
        </tr>
        @foreach ($planesadd as $planesad)
        <tr>
            <td>{{ $planesad->id}}</td>
            <td>{{ $planesad->nombre }}</td>
            <td>{{ $planesad->descripcion }}</td>

            <td>{{ $planesad->fecha_creacion }}</td>
            <td>{{ $planesad->creado }}</td>
            <td>{{ $planesad->fecha_modificacion }}</td>
            <td>{{ $planesad->modificado }}</td>
           
            <td>{{ $planesad->precio }}</td>
            @if ($planesad->estado==1)
    <td style="color: green;">Activo</td>
@else
    <td style="color: red;">Inactivo</td>
@endif

          

            <td>
                <form action="{{ route('planadd.destroy',$planesad->id) }}" method="POST">
   
                   
                    <a class="btn btn-primary" href="{{ route('planadd.edit',$planesad->id) }}">Editar</a>
                   
                    
                    @csrf
              
                 
               
               
                </form>
            </td>
        </tr>
        @endforeach
    </table>
 <h5>Los precion deben de tener el IVA incluido</h5>  
   
@endsection
