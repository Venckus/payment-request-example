@extends('layouts.app')

@section('content')

  <div class="content">

    <h5>Shopping Cart Page</h5>

    <div class="col-6">
      <p>Total amount:</p>
      <p>Price {{$amount}}</p>
      <form method="post" action={{ route( 'credentials' ) }}>
        @method('POST')
        @csrf
        <input type="text" name="amount" value={{$amount}} />
        <br/>
        <button type="submit">Pay</button>
      </form>
    </div>
  </div>

@endsection