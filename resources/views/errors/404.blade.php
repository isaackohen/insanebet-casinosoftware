@if(config('app.debug')) {{ url()->current() }} @endif
@include('errors.error', ['code' => 404])
