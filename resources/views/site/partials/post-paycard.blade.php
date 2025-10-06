@php
    $basePrice  = (int)($blog->price ?? 0);
    $isPaid     = $blog->type == 1 ? false : true ;
    $finalPrice = $basePrice;
@endphp

<div class="post-paycard">
    <div class="post-paycard-head">
        <h5 class="ppc-title">{{ $blog->name ?? 'Bài viết' }}</h5>
    </div>

    <div class="post-paycard-price">

    </div>


    {{-- Thông tin phụ (tuỳ chọn) --}}
    <ul class="post-paycard-meta">
        @if(!empty($blog->category->name))
            <li><span>Danh mục:</span> <strong>{{ $blog->category->name }}</strong></li>
        @endif
        <li><span>Tác giả:</span> <strong>Admin</strong></li>

        @if(!empty($blog->reading_time))
            <li><span>Thời gian đọc:</span> <strong>15 phút</strong></li>
        @endif
    </ul>
</div>
