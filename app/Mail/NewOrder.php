<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    protected $config;
    protected $type;

    /**
     * @param $data
     * @param $config
     */
    public function __construct($data, $config, $type)
    {
        $this->data = $data;
        $this->config = $config;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->type == 'customer') {
            $title = 'Đặt hàng thành công #';
        } else {
            $title = 'Thông báo đơn hàng mới #';
        }

        return $this->subject($title . $this->data->code)->view('site.mails.new-order', ['data' => $this->data, 'config' => $this->config, 'type' => $this->type]);
    }
}
