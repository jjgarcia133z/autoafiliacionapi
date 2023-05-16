@extends('productos.layout')
   
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit plan</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('planadd.index') }}"> Back</a>
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
  
    <form action="{{ route('planadd.update',$planadd->id) }}" method="POST">
        @csrf
        @method('PUT')
   
         <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{ $planadd->nombre }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>descripcion:</strong>
                    <input type="text" name="descripcion" value="{{ $planadd->descripcion }}" class="form-control" placeholder="descripcion">

                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>precio:</strong>


                <input type="text"  id="precio" pattern="[0-9\.]+" title="Solo se permiten números y puntos decimales"  name="precio" class="form-control" placeholder="Precio">

                </div>
            </div>
            <div class="form-group">
                <strong>Estado:</strong>
             
                <input type="checkbox" value="1" id="estado"  name="estado" {{$planadd->estado == 1 ? 'checked' : ''}}   >

            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
   
    </form>
@endsection
