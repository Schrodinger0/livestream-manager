@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Live Stream Manager</h1>
    
    <div class="row mt-5">
        <div class="col-md-6">
            <h2>YouTube Integration</h2>
            <a href="{{ route('youtube.connect') }}" class="btn btn-primary">Connect YouTube Account</a>
            @auth
                <a href="{{ route('youtube.channels') }}" class="btn btn-secondary">View My Channels</a>
            @endauth
        </div>
        
        <div class="col-md-6">
            <h2>Facebook Integration</h2>
            <a href="{{ route('facebook.connect') }}" class="btn btn-primary">Connect Facebook Account</a>
            @auth
                <a href="{{ route('facebook.accounts') }}" class="btn btn-secondary">View My Accounts</a>
            @endauth
        </div>
    </div>
</div>
@endsection