@extends('admin.layouts.app')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Shipping Charge</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{route('shipping.index')}}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="shippingForm" name="shippingForm">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <select name="country" id="country" class="form-control">
                                    <option value="">Select a country</option>
                                    @if ($countries->isNotEmpty())
                                    @foreach ($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <input type="text" name="amount" id="amount" placeholder="Amount" class="form-control">
                                <p></p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /.card -->
</section>
<!-- /.content -->
@endsection

@section('customJS')
<script>
    $('#shippingForm').submit(function(event) {
        event.preventDefault()
        $('button[type="submit"]').prop('disabled', true)
        $.ajax({
            url: '{{route("shipping.store")}}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                $('button[type="submit"]').prop('disabled', false)
                if (response['status']) {
                    window.location.href = "{{route('shipping.index')}}"
                    $('#country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    $('#amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                } else {
                    var errors = response['errors']
                    if (errors['country']) {
                        $('#country').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['country'])
                    } else {
                        $('#country').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }

                    if (errors['amount']) {
                        $('#amount').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['amount'])
                    } else {
                        $('#amount').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                    }
                }
            }
        })
    })
</script>
@endsection