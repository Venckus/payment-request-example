@extends('layouts.app')

@section('content')

  <div class="content">

    <h5>Product Page</h5>

    <div class="col-6">
      <p>Price 135.99</p>
      <form method="post" action={{ route( 'cart' ) }}>
        @method('POST')
        @csrf
        <input type="text" name="amount" value="135.99" />
        <br/>
        <button type="submit">Buy</button>
      </form>
    </div>
  </div>

@endsection