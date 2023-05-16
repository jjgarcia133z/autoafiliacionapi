@extends('productos.layout')
   
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Product</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('productos.index') }}"> Back</a>
            </div>
        </div>
    </div>
   
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
  
    <form action="{{ route('productos.update',$products->id) }}" method="POST">
        @csrf
        @method('PUT')
   
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="tipo" value="{{ $products->tipo }}" class="form-control" placeholder="Name">
                </div>
            </div>
        

            <div class="form-group">
                <strong>Monto Titular:</strong>
                <input type="text"  id="montoTitular" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales" value="{{ $products->montoTitular }}" name="montoTitular" class="form-control" placeholder="Monto Titular">
            </div>
            <div class="form-group">
                <strong>Monto Beneficiario:</strong>
                <input type="text"  id="montoBeneficiario" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales" value="{{ $products->montoBeneficiario }}" name="montoBeneficiario" class="form-control" placeholder="Monto Beneficiario">
            </div>
            <div class="form-group">
                <strong>Monto mascota:</strong>
                <input type="text"  id="montoMascota" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales"  value="{{ $products->montoMascota }}" name="montoMascota" class="form-control" placeholder="Monto Mascota">
            </div>
            <div class="form-group">
                <strong>Estado:</strong>
             
                <input type="checkbox" value="1" id="estado"  name="estado" {{$products->estado == 1 ? 'checked' : ''}}   >

            </div>
            <div class="form-group">
                <strong>Ahorro:</strong>
             
                <input type="checkbox" value="1" id="ahorro"  name="ahorro" {{$products->ahorro == 1 ? 'checked' : ''}}   >

            </div>
           
            <div class="form-group">
                <strong>Default:</strong>
               
                <input type="checkbox" value="1"id="default"  name="default" {{$products->default == 1 ? 'checked' : ''}} >

            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
   
    </form>
@endsection
