@extends('productos.layout')
  
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Agregar Plan</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('productos.index') }}"> Back</a>
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
   
<form action="{{ route('productos.store') }}" method="POST">
    @csrf
  
     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Tipo:</strong>
                <input type="text" name="tipo" class="form-control" placeholder="Tipo">
            </div>
           
            <div class="form-group">
                <strong>Monto Titular:</strong>
                <input type="text"  id="montoTitular" name="montoTitular" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales"  class="form-control" placeholder="Monto Titular">
            </div>
            <div class="form-group">
                <strong>Monto Beneficiario:</strong>
                <input type="text"  id="montoBeneficiario" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales"  name="montoBeneficiario" class="form-control" placeholder="Monto Beneficiario">
            </div>
            <div class="form-group">
                <strong>Monto mascota:</strong>
                <input type="text"  id="montoMascota" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales"  name="montoMascota" class="form-control" placeholder="Monto Mascota">
            </div>
            <div class="form-group">
                <strong>Estado:</strong>
                <input type="checkbox" value="1" id="estado" name="estado"  >

            </div>
            <div class="form-group">
                <strong>Ahorro:</strong>
                <input type="checkbox" value="1" id="ahorro" name="ahorro"  >
            </div>
           
            <div class="form-group">
                <strong>Default:</strong>
               
                <input type="checkbox" value="1"id="default"  name="default" >

            </div>
        </div>
     
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
   
</form>


@endsection
