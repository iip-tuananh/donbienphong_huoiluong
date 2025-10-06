<table style="height:100%!important;width:100%!important;border-spacing:0;border-collapse:collapse">
    <tbody>
    <tr>
        <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
            <table class="m_4006744279472501712header"
                   style="width:100%;border-spacing:0;border-collapse:collapse;margin:40px 0 20px">
                <tbody>
                <tr>
                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                        <center>

                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">

                                        <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                            <tbody>
                                            <tr>
                                                <td class="m_4006744279472501712shop-name__cell"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                    <h1 style="font-weight:normal;font-size:30px;color:#333;margin:0">
                                                        <a href="{{route('front.home-page')}}"
                                                           style="font-size:30px;color:#333;text-decoration:none"
                                                           target="_blank"
                                                           data-saferedirecturl="
                                                           ">{{$config->web_title}}</a>
                                                    </h1>
                                                </td>

                                                <td class="m_4006744279472501712order-number__cell"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;text-transform:uppercase;font-size:14px;color:#999"
                                                    align="right">
                      <span style="font-size:16px">
                        Order {{$data->code}}
                      </span>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </center>
                    </td>
                </tr>
                </tbody>
            </table>

            <table style="width:100%;border-spacing:0;border-collapse:collapse">
                <tbody>
                <tr>
                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px">
                        <center>
                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">

                                        <h2 style="font-weight:normal;font-size:24px;margin:0 0 10px">{{$type == 'customer' ? 'Cảm ơn bạn đã mua
                                            hàng !' : 'Thông báo đơn hàng mới'}}</h2>
                                        <p style="color:#777;line-height:150%;font-size:16px;margin:0">
                                            {{$type == 'customer' ? " Hi $data->customer_name , Chúng tôi đang chuẩn bị chuyển đơn hàng của
                                            bạn.
                                            Chúng tôi sẽ thông báo cho bạn khi nó đã được gửi đi." : ""}}

                                        </p>

                                        <table
                                            style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:20px">
                                            <tbody>
                                            <tr>
                                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;line-height:0.5em">
                                                    &nbsp;
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
                </tbody>
            </table>

            <table
                style="width:100%;border-spacing:0;border-collapse:collapse;border-top-width:1px;border-top-color:#e5e5e5;border-top-style:solid">
                <tbody>
                <tr>
                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:40px 0">
                        <center>
                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                        <h3 style="font-weight:normal;font-size:20px;margin:0 0 25px">Thông tin đơn
                                            hàng</h3>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">


                                        <table style="width:100%;border-spacing:0;border-collapse:collapse">

                                            <tbody>
                                            <?php

                                            $items = $data->details;
                                            ?>
                                            @foreach($items as $item)
                                                <tr style="width:100%">
                                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:15px">
                                                        <table style="border-spacing:0;border-collapse:collapse">
                                                            <tbody>
                                                            <tr>
                                                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                                    <img
                                                                        src="{{$item->product->image ? $_SERVER['HTTP_HOST'].$item->product->image->path : asset('site/image/no-image.png')}}"
                                                                        align="left" width="60" height="60"
                                                                        style="margin-right:15px;border-radius:8px;border:1px solid #e5e5e5"
                                                                        class="CToWUd">

                                                                </td>
                                                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:100%">
                                                                <span
                                                                    style="font-size:16px;font-weight:600;line-height:1.4;color:#555">{{$item->product->name}} ×&nbsp;{{$item->qty}}</span><br>
                                                                    <span style="font-size:14px;color:#999"></span><br>
                                                                </td>
                                                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;white-space:nowrap">
                                                                    <p style="color:#555;line-height:150%;font-size:16px;font-weight:600;margin:0 0 0 15px"
                                                                       align="right">
                                                                        {{number_format($item->price * $item->qty)}} đ
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>

                                        <table
                                            style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:15px;border-top-width:1px;border-top-color:#e5e5e5;border-top-style:solid">
                                            <tbody>
                                            <tr>
                                                <td class="m_4006744279472501712subtotal-spacer"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;width:40%"></td>
                                                <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                                    <table
                                                        style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:20px">


                                                        <tbody>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:5px 0">
                                                                <p style="color:#777;line-height:1.2em;font-size:16px;margin:0">
                                                                    <span style="font-size:16px">Tổng</span>
                                                                </p>
                                                            </td>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:5px 0"
                                                                align="right">
                                                                <strong
                                                                    style="font-size:16px;color:#555">{{number_format($data->total_before_discount)}}
                                                                    đ</strong>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <table
                                                        style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:20px">


                                                        <tbody>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:5px 0">
                                                                <p style="color:#777;line-height:1.2em;font-size:16px;margin:0">
                                                                    <span style="font-size:16px">Giảm giá</span>
                                                                </p>
                                                            </td>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:5px 0"
                                                                align="right">
                                                                <strong
                                                                    style="font-size:16px;color:#555">{{number_format($data->discount_value)}}
                                                                    đ</strong>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <table
                                                        style="width:100%;border-spacing:0;border-collapse:collapse;margin-top:20px;border-top-width:2px;border-top-color:#e5e5e5;border-top-style:solid">

                                                        <tbody>
                                                        <tr>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:20px 0 0">
                                                                <p style="color:#777;line-height:1.2em;font-size:16px;margin:0">
                                                                    <span style="font-size:16px">Thành tiền</span>
                                                                </p>
                                                            </td>
                                                            <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:20px 0 0"
                                                                align="right">
                                                                <strong
                                                                    style="font-size:24px;color:#555">{{number_format($data->total_after_discount)}}
                                                                    đ</strong>
                                                            </td>
                                                        </tr>

                                                        </tbody>
                                                    </table>


                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>


                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
                </tbody>
            </table>

            <table
                style="width:100%;border-spacing:0;border-collapse:collapse;border-top-width:1px;border-top-color:#e5e5e5;border-top-style:solid">
                <tbody>
                <tr>
                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:40px 0">
                        <center>
                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">
                                        <h3 style="font-weight:normal;font-size:20px;margin:0 0 25px">Thông tin khách
                                            hàng</h3>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">

                                        <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                            <tbody>
                                            <tr>
                                                <td class="m_4006744279472501712customer-info__item"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%">
                                                    <h4 style="font-weight:500;font-size:16px;color:#555;margin:0 0 5px">
                                                        Khách hàng</h4>
                                                    <p style="color:#777;line-height:150%;font-size:16px;margin:0">{{$data->customer_name}}
                                                        <br/>
                                                    </p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                            <tbody>
                                            <tr>
                                                <td class="m_4006744279472501712customer-info__item"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%">
                                                    <h4 style="font-weight:500;font-size:16px;color:#555;margin:0 0 5px">
                                                        SĐT</h4>
                                                    <p style="color:#777;line-height:150%;font-size:16px;margin:0">{{$data->customer_phone}}
                                                        <br/>
                                                    </p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                            <tbody>
                                            <tr>
                                                <td class="m_4006744279472501712customer-info__item"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%">
                                                    <h4 style="font-weight:500;font-size:16px;color:#555;margin:0 0 5px">
                                                        Địa chỉ nhận hàng</h4>
                                                    <p style="color:#777;line-height:150%;font-size:16px;margin:0">{{$data->customer_address}}
                                                        <br/>
                                                    </p>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <table style="width:100%;border-spacing:0;border-collapse:collapse">
                                            <tbody>
                                            <tr>
                                                <td class="m_4006744279472501712customer-info__item"
                                                    style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding-bottom:40px;width:50%">
                                                    <h4 style="font-weight:500;font-size:16px;color:#555;margin:0 0 5px">
                                                        Phương thức thanh toán</h4>

                                                    <p style="color:#777;line-height:150%;font-size:16px;margin:0">
                                                        Thanh toán khi nhận hàng - COD
                                                    </p>
                                                </td>

                                            </tr>
                                            </tbody>
                                        </table>

                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
                </tbody>
            </table>

            <table
                style="width:100%;border-spacing:0;border-collapse:collapse;border-top-width:1px;border-top-color:#e5e5e5;border-top-style:solid">
                <tbody>
                <tr>
                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif;padding:35px 0">
                        <center>
                            <table class="m_4006744279472501712container"
                                   style="width:560px;text-align:left;border-spacing:0;border-collapse:collapse;margin:0 auto">
                                <tbody>
                                <tr>
                                    <td style="font-family:-apple-system,BlinkMacSystemFont,&quot;Segoe UI&quot;,&quot;Roboto&quot;,&quot;Oxygen&quot;,&quot;Ubuntu&quot;,&quot;Cantarell&quot;,&quot;Fira Sans&quot;,&quot;Droid Sans&quot;,&quot;Helvetica Neue&quot;,sans-serif">

                                        <p style="color:#999;line-height:150%;font-size:14px;margin:0">Nếu bạn có bất kỳ
                                            câu hỏi nào,
                                            hãy trả lời email này hoặc liên hệ với chúng tôi tại <a
                                                href="mailto:{{ $config->email }}"
                                                style="font-size:14px;text-decoration:none;color:#1990c6"
                                                target="_blank">{{ $config->email }}</a></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </center>
                    </td>
                </tr>
                </tbody>
            </table>

            <img
                src="https://ci3.googleusercontent.com/proxy/uy_YJEmylUvjof7YRGh4UuCW_WGXqiXe9bytoPd4Du6njxZsKBvqjDPCwpvLrf2DlakVXE-EWg6klYBQ-ghUY3IeP5ICxP__dkoCzkhYqlOBZuoU9i0d5bQRiAPOY1XXlTly4A1Fjpl7XbNaCLO5FDSAFCPkfTRrLYE-EP4kYE3M-JqMHVMsLIeci1dGszEM0BKVPdxwvQt1IglaxA=s0-d-e1-ft#https://cdn.shopify.com/s/assets/themes_support/notifications/spacer-1a26dfd5c56b21ac888f9f1610ef81191b571603cb207c6c0f564148473cab3c.png"
                class="m_4006744279472501712spacer CToWUd" height="1" style="min-width:600px;height:0">

        </td>
    </tr>
    </tbody>
</table>
