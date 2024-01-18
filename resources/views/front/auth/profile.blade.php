@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{route('front.home')}}"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item">Profile</li>
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
        <div class="row">
            <div class="col-md-3">
                @include('front.auth.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Personal Information</h2>
                    </div>
                    <form name="profileForm" id="profileForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input type="text" value="{{$user->name}}" name="name" id="name" placeholder="Enter Your Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="email">Email</label>
                                    <input type="text" value="{{$user->email}}" name="email" id="email" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="phone">Phone</label>
                                    <input type="text" value="{{$user->phone}}" name="phone" id="phone" placeholder="Enter Your Phone" class="form-control">
                                    <p></p>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" id="profile" class="btn btn-dark">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card mt-5">
                    <div class="card-header">
                        <h2 class="h5 mb-0 pt-2 pb-2">Address</h2>
                    </div>
                    <form name="addressForm" id="addressForm">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="first_name">First Name</label>
                                    <input type="text" value="{{$customerAddress?->first_name}}" name="first_name" id="first_name" placeholder="Enter Your First Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" value="{{$customerAddress?->last_name}}" name="last_name" id="last_name" placeholder="Enter Your Last Name" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="email">Email</label>
                                    <input type="text" value="{{$customerAddress?->email}}" name="email" id="email_address" placeholder="Enter Your Email" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="mobile">Mobile</label>
                                    <input type="text" value="{{$customerAddress?->mobile}}" name="mobile" id="mobile" placeholder="Enter Your Mobile" class="form-control">
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="country_id">Country</label>
                                    <select name="country_id" id="country_id" class="form-control">
                                        <option value="">Select Country</option>
                                        @if ($countries->isNotEmpty())
                                        @foreach ($countries as $country)
                                        <option {{$customerAddress?->country_id == $country->id ? 'selected' : ''}} value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <p></p>
                                </div>
                                <div class="mb-3">
                                    <label for="address">Address</label>
                                    <textarea name="address" placeholder="Address" id="address" cols="30" rows="5" class="form-control">{{$customerAddress?->address}}</textarea>
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="apartment">Apartment</label>
                                    <input type="text" value="{{$customerAddress?->apartment}}" name="apartment" id="apartment" class="form-control" placeholder="Apartment, suite, unit, etc. (optional)">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="city">City</label>
                                    <input type="text" value="{{$customerAddress?->city}}" name="city" id="city" class="form-control" placeholder="City">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="state">State</label>
                                    <input type="text" value="{{$customerAddress?->state}}" name="state" id="state" class="form-control" placeholder="State">
                                    <p></p>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="zip">Zip</label>
                                    <input type="text" value="{{$customerAddress?->zip}}" name="zip" id="zip" class="form-control" placeholder="Zip">
                                    <p></p>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" id="confirm" class="btn btn-dark">Confirm</button>
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
    $('#profileForm').submit(function(event) {
        event.preventDefault()
        $('#profile').prop('disabled', true)
        $.ajax({
            url: '{{route("account.updateProfile")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('#profile').prop('disabled', false)
                if (response['status']) {
                    window.location.reload()
                    $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#phone').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                } else {
                    var errors = response['errors']
                    if (errors['name']) {
                        $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name'])
                    } else {
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors['email']) {
                        $('#email').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['email'])
                    } else {
                        $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors['phone']) {
                        $('#phone').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['phone'])
                    } else {
                        $('#phone').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }
                }
            }
        })
    })

    $('#addressForm').submit(function(event) {
        event.preventDefault()
        $('#confirm').prop('disabled', true)
        $.ajax({
            url: '{{route("account.updateAddress")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('#confirm').prop('disabled', false)
                if (response['status']) {
                    window.location.reload()
                    $('#first_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#last_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#country_id').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#address').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#city').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#state').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#zip').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#mobile').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                } else {
                    var errors = response['errors']
                    if (errors.first_name) {
                        $('#first_name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.first_name)
                    } else {
                        $('#first_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.last_name) {
                        $('#last_name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.last_name)
                    } else {
                        $('#last_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.email) {
                        $('#email_address').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email)
                    } else {
                        $('#email_address').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.country_id) {
                        $('#country_id').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.country_id)
                    } else {
                        $('#country_id').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.address) {
                        $('#address').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.address)
                    } else {
                        $('#address').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.city) {
                        $('#city').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.city)
                    } else {
                        $('#city').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.state) {
                        $('#state').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.state)
                    } else {
                        $('#state').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.zip) {
                        $('#zip').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.zip)
                    } else {
                        $('#zip').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors.mobile) {
                        $('#mobile').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.mobile)
                    } else {
                        $('#mobile').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }
                }
            }
        })
    })
</script>
@endsection