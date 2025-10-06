<?php

namespace App\Mail;


use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderActive extends Mailable
{

    protected $data;
    protected $config;

    /**
     * @param $data
     * @param $config
     */
    public function __construct($data, $config)
    {
        $this->data = $data;
        $this->config = $config;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $title = 'Thông báo đơn hàng đã được duyệt - thongnx #';

        return $this->subject($title)->view('site.mails.order-noti', ['data' => $this->data, 'config' => $this->config]);
    }
}
