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
                <strong>Codigo Saleforce:</strong>
               
                <select name="codigoSalesforce" id="codigoSalesforce" class="form-control">
                    @foreach ($PlanesSalesforces as $PlanesSalesforce)
                    
                        <option value="{{ $PlanesSalesforce->codigo }}">{{ $PlanesSalesforce->nombre }}</option>
                       
                        @endforeach
                    
                     
                </select>
           
           
            </div>
            <div class="form-group">
                <strong>Monto Titular:</strong>
                <input type="text" readonly id="montoTitular" name="montoTitular" class="form-control" placeholder="Monto Titular">
            </div>
            <div class="form-group">
                <strong>Monto Beneficiario:</strong>
                <input type="text" readonly id="montoBeneficiario" name="montoBeneficiario" class="form-control" placeholder="Monto Beneficiario">
            </div>
            <div class="form-group">
                <strong>Estado:</strong>
             
                <input type="checkbox" value="1" id="estado" name="estado"  >

            </div>
           
            <div class="form-group">
                <strong>Default:</strong>
               
                <input type="checkbox" value="1"id="default"  name="default" {{$plan >= 1 ? 'disabled' : ''}} >

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