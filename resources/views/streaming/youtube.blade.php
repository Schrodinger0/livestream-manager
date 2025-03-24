@extends('layouts.app')

@section('content')
<div class="container">
    <h1>YouTube Stream Credentials</h1>
    
    <div class="card">
        <div class="card-body">
            <h3>RTMP Details</h3>
            <p><strong>RTMP URL:</strong> {{ $streamData['rtmp_url'] }}</p>
            <p><strong>Stream Key:</strong> {{ $streamData['stream_key'] }}</p>
            
            <h3 class="mt-4">Stream Info</h3>
            <p><strong>Broadcast ID:</strong> {{ $streamData['broadcast_id'] }}</p>
            <p><strong>Stream ID:</strong> {{ $streamData['stream_id'] }}</p>
            <p><strong>Watch URL:</strong> <a href="{{ $streamData['watch_url'] }}" target="_blank">{{ $streamData['watch_url'] }}</a></p>
        </div>
    </div>
</div>
@endsection