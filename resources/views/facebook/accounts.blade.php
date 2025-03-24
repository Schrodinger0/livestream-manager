@extends('layouts.app')

@section('content')
<div class="container">
    <h1>My Facebook Accounts</h1>
    
    @foreach($accounts as $account)
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <img src="{{ $account->avatar_url }}" alt="Account avatar" class="img-fluid">
                    </div>
                    <div class="col-md-8">
                        <h3>{{ $account->name }}</h3>
                        <p>Type: {{ ucfirst($account->type) }}</p>
                        <p>ID: {{ $account->account_id }}</p>
                        <p>Streaming: {{ $account->can_stream ? 'Allowed' : 'Not allowed' }}</p>
                    </div>
                    <div class="col-md-2">
                        @if($account->can_stream)
                            <a href="{{ route('facebook.stream', $account->id) }}" class="btn btn-success">Generate Stream</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection