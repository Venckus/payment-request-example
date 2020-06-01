@extends('layouts.app')

@section('content')

  <div class="content">

    <h5>Reponse Page</h5>

    <div class="col-6">

      <p>Response:</p>

      @if (isset($response['error']))

        <p>{{ $response['error'] }}</p>
      
      @else

        @foreach ($response as $k => $v)

          <p>{{ $k }} - {{ $v }}</p>

        @endforeach

      @endif
      
    </div>
    
  </div>

@endsection