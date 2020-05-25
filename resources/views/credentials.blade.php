@extends('layouts.app')

@section('content')

  <div class="content">

    <h5>Product Page</h5>

    <div class="col-6">
      <p>Enter card details</p>
      <form method="post" action={{ route( 'process' ) }}>
        @method('POST')
        @csrf
        <input type="text" name="amount" value={{$amount}} /><br>
        <input type="text" name="name" value="Name Surname" /><br>
        <input type="text" name="pan" value="PAN Number" /><br>
        <input type="text" name="expdate" value="Exp date" /><br>
        <input type="text" name="cvv" value="CVV" /><br>
        <br/>
        <button type="submit">Process</button>
      </form>
    </div>
  </div>

@endsection