@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My YouTube Channels</h1>
    
    @foreach($channels as $channel)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <img src="{{ $channel->thumbnail_url }}" alt="Channel thumbnail" class="img-fluid">
                    </div>
                    <div class="col-md-8">
                        <h3>{{ $channel->title }}</h3>
                        <p>Channel ID: {{ $channel->channel_id }}</p>
                        <p>Streaming: {{ $channel->can_stream ? 'Allowed' : 'Not allowed' }}</p>
                    </div>
                    <div class="col-md-2">
                        @if($channel->can_stream)
                            <a href="{{ route('youtube.stream', $channel->id) }}" class="btn btn-success">Generate Stream</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection