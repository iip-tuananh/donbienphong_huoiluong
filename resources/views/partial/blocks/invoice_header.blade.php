<div class="col-md-12 g7-info mb-5">
    @php
        $g7 = App\Model\Uptek\G7Info::where('id',Auth::user()->g7_id)->first();
    @endphp

    <img class="g7-logo" src="{{ asset('img/logo/g7_logo.png') }}" alt=""><h5 class="g7-name"><strong>{{ $g7->name }}</strong></h5>
    <p>Địa chỉ: <strong>{{ $g7->adress }}</strong></p>
    <p>Số điện thoại: <strong>{{ $g7->mobile }}</strong></p>
</div>