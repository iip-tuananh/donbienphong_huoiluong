@extends('layouts.main')
@section('page_title')
Trang chủ
@endsection

@section('title')
Admin Panel - {{ $config->web_title }}
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('plugins/morrisjs/morris.css') }}">

<style>
    /* Chung cho KPI cards */
    .text-orange { color: #F58220; }
    .small-box .inner h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
    }
    .small-box .inner p {
        font-size: 1rem;
        margin-bottom: 0;
    }
    @media (max-width: 768px) {
        .small-box .inner h3 {
            font-size: 2rem;
        }
    }


    .chart-container {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;               /* khoảng cách giữa 2 khối */
        justify-content: center; /* căn giữa ngang */
        align-items: stretch;    /* kéo các item cùng chiều cao */
    }

    /* 2. Wrapper mỗi chart */
    .chart-wrapper {
        flex: 1 1 45%;           /* mỗi khối chiếm gần 50% (còn gap) */
        max-width: 600px;        /* giới hạn chiều ngang */
        display: flex;
        flex-direction: column;  /* để canvas có thể flex đầy chiều cao */
        background: #fff;
        padding: 1rem;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* 3. Canvas chiếm hết wrapper, chiều cao đồng đều */
    .chart-wrapper canvas {
        flex: 1;                 /* fill toàn bộ chiều cao wrapper */
        width: 100% !important;  /* buộc full-width */
        height: 100% !important; /* buộc full-height */
    }

    /* 4. Tinh chỉnh mobile */
    @media (max-width: 576px) {
        .chart-wrapper {
            flex: 1 1 100%;
            padding: .75rem;
        }
    }
</style>
@endsection

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <!-- Card: Bài viết Blog -->
    <div class="col-lg-3 col-md-6 mb-4">
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="small-box bg-info">
            <div class="inner text-center">
                <h3 class="text-orange">{{ $data['blogs'] ?? 0 }}</h3>
                <p>Bài viết</p>
            </div>
            <div class="icon">
                <i class="fas fa-blog"></i>
            </div>
            <a href="{{ route('Post.index') }}" class="small-box-footer">
                Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>


    <!-- Card: Khách hàng liên hệ -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="small-box bg-warning">
            <div class="inner text-center">
                <h3 class="text-orange">{{ $data['contacts'] ?? 0 }}</h3>
                <p>Liên hệ</p>
            </div>
            <div class="icon">
                <i class="fas fa-envelope"></i>
            </div>
            <a href="{{ route('contacts.index') }}" class="small-box-footer">
                Xem chi tiết <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>


    <div class="col-lg-3 col-md-6 mb-4">
    </div>

</div>

<div class="chart-container">
    <div class="chart-wrapper">
        <canvas id="chartWeek"></canvas>
    </div>
    <div class="chart-wrapper">
        <canvas id="chartMonth"></canvas>
    </div>
</div>


<!-- /.row (main row) -->
@endsection
@section('script')
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ URL('plugins/countjs/count.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="{{ asset('plugins/morrisjs/morris.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const weekData  = @json($visitsByWeek);
    const monthData = @json($visitsByMonth);

    new Chart(
        document.getElementById('chartWeek').getContext('2d'),
        {
            type: 'line',
            data: {
                labels: weekData.map(i => i.label),
                datasets: [{
                    label: 'Lượt truy cập mỗi tuần',
                    data:  weekData.map(i => i.visits),
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,           // mỗi tick cách nhau 1 đơn vị
                            callback: function(v) {
                                // chỉ trả về label nếu v là số nguyên
                                return Number.isInteger(v) ? v : '';
                            }
                        }
                    }
                }
            }
        }
    );

    new Chart(
        document.getElementById('chartMonth').getContext('2d'),
        {
            type: 'bar',
            data: {
                labels: monthData.map(i => i.label),
                datasets: [{
                    label: 'Lượt truy cập mỗi tháng',
                    data:  monthData.map(i => i.visits),
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function(v) {
                                return Number.isInteger(v) ? v : '';
                            }
                        }
                    }
                }
            }
        }
    );
</script>




@endsection
