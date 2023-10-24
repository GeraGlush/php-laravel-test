@extends('layouts.layout')

@section('title', 'Ошибка!')

@section('content')
    <div class="text-center">
        <h1 style="margin-left: 450px" class="text-center error">{{ $error_message }}</h1>
    </div>
@endsection
