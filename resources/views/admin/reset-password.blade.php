<!DOCTYPE html>
<html lang="en">
<head>
    @php
		$setting = \App\Models\Admin\SiteSetting::first();
	@endphp
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Amazing Life App - Reset Password</title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon"
		href="{{ $setting && $setting->favicon ? asset($setting->favicon) : asset('assets/img/favicon.png') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .loginbox {
            max-width: 420px;
            margin: 60px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 3px 12px rgba(0,0,0,0.1);
            padding: 30px 25px;
        }
        .login-right-wrap h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .login-right-wrap p.account-subtitle {
            font-size: 15px;
            margin-bottom: 25px;
        }
        .position-relative { position: relative; }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 12px;
            top: 38px;
            color: #6c757d;
        }
        .text-error {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #0069d9;
        }
    </style>
</head>
<body>

<div class="main-wrapper login-body">
    <div class="login-wrapper">
        <div class="container">
            <div class="loginbox">
                <div class="login-right">
                    <div class="login-right-wrap">

                        <h1>Reset Password</h1>
                        <p class="account-subtitle">Enter your new password below</p>

                        <form method="POST" id="resetForm">
                            @csrf
                            <input type="hidden" name="email" value="{{ $email ?? '' }}">

                            <div class="mb-3 position-relative">
                                <label for="password" class="form-label">New Password</label>
                                <input id="password" class="form-control" type="password" name="password" placeholder="Enter new password">
                                <i class="fa fa-eye password-toggle" id="togglePassword"></i>
                            </div>

                            <div class="mb-3 position-relative">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" placeholder="Confirm new password">
                                <i class="fa fa-eye password-toggle" id="togglePasswordConfirm"></i>
                            </div>

                            <button type="submit" id="submitBtn" class="btn btn-primary w-100">Reset Password</button>
                        </form>

                        <div class="text-center mt-3">
                            <small>Remember your password? <a href="{{ route('admin.login') }}">Login</a></small>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  jQuery (must be first) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function(){

        //  Toggle Password Visibility
        $('#togglePassword').on('click', function(){
            const input = $('#password');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });
        $('#togglePasswordConfirm').on('click', function(){
            const input = $('#password_confirmation');
            const type = input.attr('type') === 'password' ? 'text' : 'password';
            input.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // jQuery Validation
        $('#resetForm').validate({
            rules: {
                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    minlength: 6,
                    equalTo: '#password'
                }
            },
            messages: {
                password: {
                    required: "Please enter your new password",
                    minlength: "Password must be at least 6 characters long"
                },
                password_confirmation: {
                    required: "Please confirm your password",
                    minlength: "Password must be at least 6 characters long",
                    equalTo: "Passwords do not match"
                }
            },
            errorPlacement: function(error, element) {
                error.addClass('text-error');
                error.insertAfter(element);
            },
            submitHandler: function(form) {
                $.ajax({
                    url: "{{ route('admin.resetPasswordSubmit') }}",
                    type: "POST",
                    data: $(form).serialize(),
                    beforeSend: function(){
                        $('#submitBtn').prop('disabled', true).text('Processing...');
                    },
                    success: function(res){
                        if(res.success){
                            localStorage.setItem('resetSuccessMessage', res.message);
                            window.location.href = "{{ route('admin.login') }}";
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: res.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(){
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error'
                        });
                    },
                    complete: function(){
                        $('#submitBtn').prop('disabled', false).text('Reset Password');
                    }
                });
            }

        });
    });
</script>

<style>
.text-error { color: #e74c3c; font-size: 14px; margin-top: 4px; }
.password-toggle { position: absolute; right: 10px; top: 43px; cursor: pointer; color: #6c757d; }

</style>



</body>
</html>
