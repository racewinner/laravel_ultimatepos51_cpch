// app/Notifications/UserEmailNotification.php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserEmailNotification extends Notification
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Optional: customize from per-notification
        $mail = (new MailMessage)
            ->subject('Notification Email')
            ->greeting('Hello ' . $notifiable->name)
            ->line('This is a test email sent via Notification.')
            ->line('Data: ' . json_encode($this->data))
            ->action('View', url('/'));

        // If you want to override the "from" address for this notification:
        // $mail->from('aaa@gmail.com', config('app.name'));

        return $mail;
    }
}