<!-- PREHEADER (ẩn) -->
@php
    $order = $data['order'];
    $customer = $data['customer'];
@endphp
<div style="display:none;max-height:0;overflow:hidden;opacity:0;">
    Đơn hàng #{{ $order->code ?? 'XXXXXX' }} đã được kích hoạt.
</div>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#f6f7f9;">
    <tr>
        <td align="center" style="padding:24px;">
            <!-- CONTAINER -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="width:600px;max-width:100%;background:#ffffff;border:1px solid #ececec;border-radius:10px;overflow:hidden;">
                <!-- HEADER -->
                <tr>
                    <td style="background:#8B5E34;color:#ffffff;padding:22px 24px;text-align:left;">
                        <div style="font-family:Segoe UI,Arial,sans-serif;font-size:18px;font-weight:600;line-height:1.3;">
                            Đơn hàng đã được kích hoạt
                        </div>
                        <div style="font-family:Segoe UI,Arial,sans-serif;font-size:13px;opacity:.9;">
                            Cảm ơn bạn đã tin tưởng chúng tôi
                        </div>
                    </td>
                </tr>

                <!-- TITLE + INTRO -->
                <tr>
                    <td style="padding:22px 24px 8px 24px;">
                        <h2 style="margin:0 0 8px 0;font-family:Segoe UI,Arial,sans-serif;font-weight:600;font-size:20px;color:#333;">
                            Xin chào {{ $customer->fullname ?? 'Quý khách' }},
                        </h2>
                        <p style="margin:0;font-family:Segoe UI,Arial,sans-serif;font-size:15px;line-height:1.6;color:#555;">
                            Đơn hàng của bạn đã được chúng tôi <strong>duyệt</strong> và <strong>kích hoạt</strong>. Thông tin chi tiết như sau:
                        </p>
                    </td>
                </tr>

                <!-- ORDER SUMMARY (2 CỘT) -->
                <tr>
                    <td style="padding:16px 24px 0 24px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;border-collapse:collapse;">
                            <tr>
                                <!-- Cột trái -->
                                <td valign="top" style="width:50%;padding:12px;border:1px solid #eee;border-right:none;border-radius:8px 0 0 8px;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                                        <tr>
                                            <td style="font:600 13px Segoe UI,Arial,sans-serif;color:#777;">Mã đơn hàng</td>
                                        </tr>
                                        <tr>
                                            <td style="font:600 16px Segoe UI,Arial,sans-serif;color:#222;padding:4px 0;">
                                                #{{ $order->code ?? 'XXXXXX' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font:600 13px Segoe UI,Arial,sans-serif;color:#777;padding-top:10px;">Ngày tạo đơn</td>
                                        </tr>
                                        <tr>
                                            <td style="font:500 15px Segoe UI,Arial,sans-serif;color:#222;padding:4px 0;">
                                                {{ optional($order->created_at)->format('d/m/Y H:i') ?? 'dd/mm/yyyy HH:MM' }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                <!-- Cột phải -->
                                <td valign="top" style="width:50%;padding:12px;border:1px solid #eee;border-radius:0 8px 8px 0;">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                                        <tr>
                                            <td style="font:600 13px Segoe UI,Arial,sans-serif;color:#777;">Khách hàng</td>
                                        </tr>
                                        <tr>
                                            <td style="font:500 15px Segoe UI,Arial,sans-serif;color:#222;padding:4px 0;">
                                                {{ $customer->fullname ?? 'Quý khách' }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="font:600 13px Segoe UI,Arial,sans-serif;color:#777;padding-top:10px;">Email</td>
                                        </tr>
                                        <tr>
                                            <td style="font:500 15px Segoe UI,Arial,sans-serif;color:#222;padding:4px 0;">
                                                {{ $customer->email ?? 'example@mail.com' }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- SPACER -->
                <tr><td style="height:12px;line-height:12px;">&nbsp;</td></tr>

                <!-- ORDER ITEMS TITLE + TOTAL -->
                <tr>
                    <td style="padding:0 24px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                            <tr>
                                <td style="font:600 16px Segoe UI,Arial,sans-serif;color:#333;padding:8px 0;">Chi tiết đơn hàng</td>
                                <td align="right" style="font:600 16px Segoe UI,Arial,sans-serif;color:#8B5E34;padding:8px 0;">
                                    Tổng: {{ number_format($order->total_after_discount ?? 0, 0, ',', '.') }}₫
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- ORDER ITEMS TABLE -->
                <tr>
                    <td style="padding:8px 24px 20px 24px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;border:1px solid #eee;border-radius:8px;overflow:hidden;border-collapse:separate;">
                            <thead>
                            <tr>
                                <th align="left" style="background:#faf6f3;color:#8B5E34;padding:12px;font:700 13px Segoe UI,Arial,sans-serif;border-bottom:1px solid #eee;">Bài viết</th>
                                <th align="right" style="background:#faf6f3;color:#8B5E34;padding:12px;font:700 13px Segoe UI,Arial,sans-serif;border-bottom:1px solid #eee;">Giá</th>
                                <th align="center" style="background:#faf6f3;color:#8B5E34;padding:12px;font:700 13px Segoe UI,Arial,sans-serif;border-bottom:1px solid #eee;">Trạng thái</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($order->details ?? [] as $item)
                                <tr>
                                    <td style="padding:12px;font:500 14px Segoe UI,Arial,sans-serif;color:#222;border-bottom:1px solid #f0f0f0;">
                                        {{ $item->post->name ?? 'Bài viết' }}
                                    </td>
                                    <td align="right" style="padding:12px;font:600 14px Segoe UI,Arial,sans-serif;color:#222;border-bottom:1px solid #f0f0f0;">
                                        {{ number_format($item->price ?? 0, 0, ',', '.') }}₫
                                    </td>
                                    <td align="center" style="padding:12px;border-bottom:1px solid #f0f0f0;">
                      <span style="display:inline-block;padding:4px 10px;font:700 12px Segoe UI,Arial,sans-serif;color:#1e7e34;background:#e9f7ef;border:1px solid #c7eed8;border-radius:999px;letter-spacing:.3px;">
                        ĐÃ KÍCH HOẠT
                      </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" align="center" style="padding:16px;font:500 14px Segoe UI,Arial,sans-serif;color:#777;">
                                        Không có sản phẩm nào.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>

                <!-- CTA -->
                <tr>
{{--                    <td align="center" style="padding:4px 24px 22px 24px;">--}}
{{--                        <a href="{{ $orderUrl ?? url('/account/orders/'.$order->code) }}"--}}
{{--                           style="display:inline-block;padding:12px 20px;background:#8B5E34;color:#ffffff;text-decoration:none;font:600 14px Segoe UI,Arial,sans-serif;border-radius:8px;">--}}
{{--                            Xem đơn hàng--}}
{{--                        </a>--}}
{{--                    </td>--}}
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#fafafa;border-top:1px solid #eee;padding:16px 24px;">
                        <p style="margin:0 0 6px 0;font:500 13px Segoe UI,Arial,sans-serif;color:#666;">
                            Nếu bạn cần hỗ trợ, vui lòng phản hồi email này hoặc liên hệ:
                            <a href="mailto:{{ $config->email }}" style="color:#8B5E34;text-decoration:none;">{{ $config->email }}</a>
                        </p>
                        <p style="margin:0;font:500 12px Segoe UI,Arial,sans-serif;color:#999;">
                            © {{ $config->web_title }}. Mọi quyền được bảo lưu.
                        </p>
                    </td>
                </tr>
            </table>
            <!-- /CONTAINER -->
        </td>
    </tr>
</table>
