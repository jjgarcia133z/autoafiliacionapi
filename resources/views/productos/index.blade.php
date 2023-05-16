@extends('productos.layout')
 
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Planes</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('productos.create') }}"> Crear Un nuevo Plan </a>
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
            <th>Tipo</th>
   
            <th>Monto Titular</th>
            <th>Monto Beneficiario</th>
            <th>Monto Mascota</th>
            <th>Fecha Creacion</th>
            <th>Creado Por </th>
           
            <th>Fecha Modificación</th>
            <th>Modificado Por </th>

            <th>Estado</th>
            <th>Default</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($productos as $product)
        <tr>
        <td>{{ $product->id }}</td>
            <td>{{ $product->tipo }}</td>
 
            <td>{{ $product->montoTitular }}</td>
            <td>{{ $product->montoBeneficiario }}</td>
            <td>{{ $product->montoMascota }}</td>

            <td>{{ $product->fechacreado }}</td>
            <td>{{ $product->creado }}</td>
            <td>{{ $product->fechamodifica }}</td>
            <td>{{ $product->modificado }}</td>

            @if ($product->estado==1)
    <td style="color: green;">Activo</td>
@else
    <td style="color: red;">Inactivo</td>
@endif
@if ($product->default==1)
    <td style="color: green;">Activo</td>
@else
    <td style="color: red;">Inactivo</td>
@endif

          

            <td>
                <form action="{{ route('productos.destroy',$product->id) }}" method="POST">
   
                   
                    <a class="btn btn-primary" href="{{ route('productos.edit',$product) }}">Edit</a>
                   
                    
                    @csrf
                   
      
                
                
                </form>
            </td>
        </tr>
        @endforeach
    </table>



 <h5>Los precion deben de tener el IVA incluido</h5> 
@endsection
