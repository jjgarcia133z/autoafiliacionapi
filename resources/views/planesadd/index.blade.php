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
            <th>Codigo Salesforce</th>
            <th>Monto Titular</th>
            <th>Monto Beneficiario</th>
            <th>Estado</th>
            <th>Default</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($productos as $product)
        <tr>
            <td>.</td>
            <td>{{ $product->tipo }}</td>
            <td>{{ $product->codigoSalesforce }}</td>
            <td>{{ $product->montoTitular }}</td>
            <td>{{ $product->montoBeneficiario }}</td>
            <td><input readonly type="checkbox" {{$product->estado == 1 ? 'checked' : ''}} ></td>
            <td><input readonly type="checkbox" {{$product->default == 1 ? 'checked' : ''}} ></td>


          

            <td>
                <form action="{{ route('productos.destroy',$product->id) }}" method="POST">
   
                   
                    <a class="btn btn-primary" href="{{ route('productos.edit',$product->id) }}">Edit</a>
                   
                    
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" href="{{ route('productos.destroy',$product->id) }}" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
   
@endsection