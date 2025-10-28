@section('title', 'Register')

@section('css')
    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
@endsection

@section('js')
    <!-- General JS Scripts -->
    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/popper.js') }}"></script>
    <script src="{{ asset('assets/modules/tooltip.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>

    <!-- Template JS File -->
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
@endsection

<div id="app">
    <section class="section">
        <div class="container mt-5">
            <div class="row">
                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">

                    <!-- Logo -->
                    <div class="login-brand">
                        <img src="{{ asset('assets/img/stisla-fill.svg') }}" alt="logo" width="100"
                            class="shadow-light rounded-circle">
                    </div>

                    <!-- Register Card -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h4>Register</h4>
                        </div>

                        <div class="card-body">
                            <form wire:submit.prevent="register" novalidate>

                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name">Full Name</label>
                                    <input id="name" type="text" wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror" tabindex="1" required
                                        autofocus autocomplete="name">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" wire:model="email"
                                        class="form-control @error('email') is-invalid @enderror" tabindex="2" required
                                        autocomplete="username">
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="form-group">
                                    <label for="password" class="control-label">Password</label>
                                    <input id="password" type="password" wire:model="password"
                                        class="form-control @error('password') is-invalid @enderror" tabindex="3"
                                        required autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="form-group">
                                    <label for="password_confirmation" class="control-label">Confirm Password</label>
                                    <input id="password_confirmation" type="password" wire:model="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        tabindex="4" required autocomplete="new-password">
                                    @error('password_confirmation')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="5"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>Register</span>
                                        <span wire:loading>Processing...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-5 text-center text-muted">
                        Already have an account? <a href="{{ route('login') }}">Login</a>
                    </div>
                    <div class="simple-footer">
                        Copyright &copy; {{ date('Y') }} Stisla
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

