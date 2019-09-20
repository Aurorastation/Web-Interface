@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="display-2 text-danger">STOP!!!</h1>
        <div class="row">
            <div class="alert alert-danger">Potential cross user authentication attack has been detected. Authentication process been halted. Please alert administrator of this case and how you got here. <br>
            If you are fully sure that that this link is directly from your game window, you may click <a href="{{ route('server.login.end') }}">here</a> to continue. By clicking the link you understand the risk that you may allow someone to login using your credentials.
            </div>
        </div>
    </div>
@endsection