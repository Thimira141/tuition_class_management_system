@extends('layouts.app')

@section('content')

@include('components.nav-bar.admin')
@include('auth.logout-form')

@yield('content-c1')

@endsection
