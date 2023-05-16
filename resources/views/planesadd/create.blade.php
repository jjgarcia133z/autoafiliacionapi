@extends('productos.layout')
  
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Agregar Plan</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('planadd.index') }}"> Back</a>
        </div>
    </div>
</div>
@if (isset($message ))
        <div class="alert alert-error">
            <p>Solo se permite un default</p>
        </div>
    @endif
   
@if ($errors->any())
    <div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
   
<form action="{{ route('planadd.store') }}" method="POST">
    @csrf
  
     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nombre:</strong>
                <input type="text" name="nombre" class="form-control" placeholder="Nombre">
            </div>
        
            <div class="form-group">
                <strong>Descripción:</strong>
                <input type="text"  id="descripcion" name="descripcion" class="form-control" placeholder="Descripción">
            </div>
            <div class="form-group">
                <strong>Precio:</strong>

                <input type="text"  id="precio" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales"  name="precio" class="form-control" placeholder="Precio">


            </div>
            <div class="form-group">
                <strong>Estado:</strong>
             
                <input type="checkbox" value="1" id="estado" name="Estado"  >

            </div>
          
        </div>
     
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
   
</form>


@endsection
