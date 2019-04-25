@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h3 class="card-title">{{ Auth::user()->name }}</h3>
                    <p class="card-text">{{ Auth::user()->formattedPhone() }}</p>
                    <p class="card-text">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
