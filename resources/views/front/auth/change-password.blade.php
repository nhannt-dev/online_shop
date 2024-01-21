@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Change Password</li>
            </ol>
        </div>
    </div>
</section>

<section class="section-11">
    <div class="container mt-5">
        @if (Session::has('success'))
        <div class="alert alert-success">
            {{Session::get('success')}}
        </div>
        @endif
        @if (Session::has('error'))
        <div class="alert alert-danger">
            {{Session::get('error')}}
        </div>
        @endif
        <div class="row">
            <div class="col-md-3">
                @include('front.auth.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Change Password</h2>
                    </div>
                    <form method="post" name="changePasswordForm" id="changePasswordForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="old_password">Old Password</label>
                                    <input type="password" name="old_password" id="old_password" placeholder="Old Password" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password">New Password</label>
                                    <input type="password" name="new_password" id="new_password" placeholder="New Password" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Old Password" class="form-control">
                                    <p></p>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-dark">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('customJS')
<script>
    $('#changePasswordForm').submit(function(event) {
        event.preventDefault()
        $('button[type="submit"]').prop('disabled', true)
        $.ajax({
            url: '{{route("account.processChangePassword")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('button[type="submit"]').prop('disabled', false)
                if (response['status']) {
                    window.location.reload()
                    $('#old_password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#new_password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#confirm_password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                } else {
                    var errors = response['errors']
                    if (errors['old_password']) {
                        $('#old_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['old_password'])
                    } else {
                        $('#old_password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors['new_password']) {
                        $('#new_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['new_password'])
                    } else {
                        $('#new_password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors['confirm_password']) {
                        $('#confirm_password').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['confirm_password'])
                    } else {
                        $('#confirm_password').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }
                }
            }
        })
    })
</script>
@endsection