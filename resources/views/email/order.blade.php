<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Example 1</title>
    <link rel="stylesheet" href="{{asset('email/style.css')}}" media="all" />
</head>

<body>
    <header class="clearfix">
        <div id="logo">
            <img src="{{asset('email/logo.png')}}">
        </div>
        <h1>INVOICE 3-2-1</h1>
        <div id="company" class="clearfix">
            <div>Company Name</div>
            <div>455 Foggy Heights,<br /> AZ 85004, US</div>
            <div>(602) 519-0450</div>
            <div><a href="mailto:company@example.com">company@example.com</a></div>
        </div>
        <div id="project">
            <div><span>PROJECT</span> Website development</div>
            <div><span>CLIENT</span> {{$data['order']->first_name.' '.$data['order']->last_name}}</div>
            <div><span>ADDRESS</span> {{ $data['order']->address }}, {{ $data['order']->city }}, {{ getCountry($data['order']->country_id)->name }}</div>
            <div><span>EMAIL</span> <a href="mailto:john@example.com">john@example.com</a></div>
            <!-- <div><span>DATE</span> August 17, 2015</div>
            <div><span>DUE DATE</span> September 17, 2015</div> -->
        </div>
    </header>
    <main>
        <table>
            <thead>
                <tr>
                    <th>PRODUCT</th>
                    <th>PRICE</th>
                    <th>QTY</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['order']->items as $item)
                <tr>
                    <td>{{$item->name}}</td>
                    <td>${{number_format($item->price, 2)}}</td>
                    <td>{{$item->qty}}</td>
                    <td>${{number_format($item->total, 2)}}</td>
                </tr>
                @endforeach

                <tr>
                    <td colspan="3">SUBTOTAL</td>
                    <td class="total">${{number_format($data['order']->subtotal, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="3">Discount</td>
                    <td class="total">${{number_format($data['order']->discount, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="3">Shipping</td>
                    <td class="total">${{number_format($data['order']->shipping, 2)}}</td>
                </tr>
                <tr>
                    <td colspan="3" class="grand total">GRAND TOTAL</td>
                    <td class="grand total">${{ $data['order']->grand_total }}</td>
                </tr>
            </tbody>
        </table>
        <div id="notices">
            <div>NOTICE:</div>
            <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>
        </div>
    </main>
    <footer>
        Invoice was created on a computer and is valid without the signature and seal.
    </footer>
</body>

</html>