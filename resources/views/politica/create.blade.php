@extends('productos.layout')
  
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Agregar Plan</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('politica.index') }}"> Back</a>
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
   
<form action="{{ route('politica.store') }}" method="POST">
    @csrf
  
     <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>URL:</strong>
                <input type="text" name="url" class="form-control" placeholder="url">
            </div>

            <div class="form-group">
                <strong>version:</strong>
                <input type="text"  id="version" name="version" class="form-control" placeholder="version">
            </div>
            <div class="form-group">
                <strong>Activo:</strong>
               
                <input type="checkbox" value="1" id="activo"  name="activo" {{$politi >= 1 ? 'disabled' : ''}} >


            </div>
  
           
         

        </div>
     
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
   
</form>

<script>
    document.getElementById("codigoSalesforce").onchange = function() {

        var codigoSalesforce = document.getElementById("codigoSalesforce").value;

       var datos = {!! json_encode($PlanesSalesforces) !!};
       for (let i = 0; i < datos.length; i++) {
          

                if(datos[i].codigo==codigoSalesforce){

                    document.getElementById("montoTitular").value = datos[i].montoTitular;
                 document.getElementById("montoBeneficiario").value =datos[i].montoAdicional;
              
                }



            }

   };



</script>
@endsection