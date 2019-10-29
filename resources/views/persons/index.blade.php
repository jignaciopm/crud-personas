@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>Personas</div>
                </div>

                <div class="card-body">
                    <persons-component/>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
