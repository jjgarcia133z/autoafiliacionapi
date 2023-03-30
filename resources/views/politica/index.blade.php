@extends('politica.layout')
 
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Planes</h2>
            </div>
            <div class="pull-right">
            <a class="btn btn-success" href="{{ route('politica.create') }}"> Crear Un nuevo Plan </a>
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
            <th>Consecutivo</th>
            <th>URL</th>
            <th>Version</th>
            <th>Activo</th>
   
            <th width="280px">Action</th>
        </tr>
        @foreach ($politicas as $politica)
        <tr>
        <td>{{ $politica->id }}</td>
            <td>{{ $politica->url }}</td>
            <td>{{ $politica->version }}</td>
  
            <td><input readonly type="checkbox" {{$politica->activo == 1 ? 'checked' : ''}} ></td>
       

            <td>
                <form action="{{ route('politica.destroy',$politica->id) }}" method="POST">
   
                   
                    <a class="btn btn-primary" href="{{ route('politica.edit',$politica->id) }}">Edit</a>
                   
                    
                    @csrf
                    @method('DELETE')
      
                    <button type="submit" href="{{ route('politica.destroy',$politica->id) }}" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
  
   
@endsection