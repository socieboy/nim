@extends('template.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card card-default">
                <div class="card-header">Streaming</div>
                <div class="card-body">
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card card-default">
                <div class="card-header">Netowork Configuration</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <network-manager></network-manager>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
