<?php

namespace App\Notifications;

use App\Currency\Currency;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use \Carbon\Carbon;

class WithdrawAccepted extends Notification
{
    use Queueable;

    private $message;

    private $data;

    public function __construct($withdraw)
    {
        $this->message = 'general.notifications.withdraw_accepted.message';
        $this->data = [
            'diff' => Carbon::createFromFormat('Y-m-d H:m', $withdraw->created_at)->toDateTimeString(),
            'sum' => $withdraw->sum,
            'currency' => Currency::find($withdraw->currency)->name(),
        ];
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'general.notifications.withdraw_accepted.title',
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}
