@extends('layouts.guest')

@section('content')
<div class="col-lg-4 col-md-6 ml-auto mr-auto">
    <form method="POST" action="{{ route('login') }}">
    @csrf
              <div class="card card-login" style="background: #fbfbfb; margin-top: 100px;">
                <div class="card-header ">
                  <div class="card-header ">
                    
                    <h3 class="header text-center">
                      <img style="width: 30px; margin-right: 8px;" src="{{ asset('img/ods-icone/ods.png') }}" class="img-fluid" alt="Responsive image">
                      Login
                    </h3>
                  </div>
                </div>
                <div class="card-body " >
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-single-02"></i>
                      </span>
                    </div>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                  </div>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text">
                        <i class="nc-icon nc-key-25"></i>
                      </span>
                    </div>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                  </div>
                  <br />
                  <div class="form-group">
                    <div class="form-check">
                      <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="form-check-sign"></span>
                        Lembrar-me
                      </label>
                    </div>
                  </div>
                </div>
                <div class="card-footer center">
                    <button type="submit" class="btn btn-primary btn-round btn-block mb-4 w-50" style="    margin: 0 auto !important;">
                        {{ __('Entrar') }}
                    </button>
                    <br/>
                    @if (Route::has('password.request'))
                        <a class="btn-link mb-3 mt-5" href="{{ route('password.request') }}">
                            <span class="forget-password">{{ __('esqueceu sua senha?') }}</span>
                        </a>
                    @endif
                </div>
              </div>
    </form>
</div>
@endsection