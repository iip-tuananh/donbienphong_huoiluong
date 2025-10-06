@extends('layouts.main')

@section('page_title')
{{ $object->name }} | G7 Autocare
@endsection
@section('title')
{{ $object->name }}
@endsection
@section('content')
<div class="col-md-12">
    {!! $object->body !!}
</div>
@endsection