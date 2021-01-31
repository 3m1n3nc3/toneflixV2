<?php

declare(strict_types=1);

namespace NiNaCoder\Updater\Notifications\Notifications;

use NiNaCoder\Updater\Events\UpdateFailed as UpdateFailedEvent;
use NiNaCoder\Updater\Notifications\BaseNotification;
use Illuminate\Notifications\Messages\MailMessage;

final class UpdateFailed extends BaseNotification
{
    /**
     * @var UpdateFailedEvent
     */
    protected $event;

    public function toMail(): MailMessage
    {
        return (new MailMessage())
            ->from(config('self-update.notifications.mail.from.address', config('mail.from.address')), config('self-update.notifications.mail.from.name', config('mail.from.name')))
            ->subject(config('app.name').': Update failed');
    }

    public function setEvent(UpdateFailedEvent $event)
    {
        $this->event = $event;

        return $this;
    }
}
