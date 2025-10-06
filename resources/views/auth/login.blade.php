@extends('layouts.app')
@section('content')
    <form class="login" method="POST" action="{{ route('login.submit') }}">
        @csrf
        <input id="account_name" type="text" class="form-control @error('account_name') is-invalid @enderror" name="account_name" value="{{ old('account_name') }}" required autocomplete="" autofocus placeholder="Tên tài khoản">
        @error('account_name')
        <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
        @enderror
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Mật khẩu">
        @error('password')
        <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
        @enderror
        <button type="submit "><i class="fas fa-sign-in-alt" style="margin-right: 5px"></i>Đăng nhập</button>
    </form>
@endsection
