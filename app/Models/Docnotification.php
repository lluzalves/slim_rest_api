<?php

namespace App\Models;


use PHPMailer\PHPMailer\PHPMailer;

class Docnotification extends BaseModel
{
    public $timestamps = true;

    static function notification()
    {
        return new Docnotification();
    }

    public function output()
    {
        $output = [];
        $output['id'] = $this->id;
        $output['body'] = $this->body;
        $output['creator_id'] = $this->creator_id;
        $output['receiver_id'] = $this->receiver_id;
        $output['read_status'] = $this->read_status;
        $output['type'] = $this->type;
        $output['document_id'] = $this->document_id;
        $output['created_at'] = $this->created_at->toDateTimeString();;
        $output['updated_at'] = $this->updated_at->toDateTimeString();;

        return $output;
    }


    public function create($notification)
    {
        if (empty($notification->body) or empty($notification->creator_id) or empty($notification->receiver)) {
            return null;
        }
        $notification->save();

        return $notification;
    }


    public function getNotifications($userId)
    {
        $notifications = Docnotification::where(
            array('receiver_id' => $userId,
                'read_status' => false))
            ->take(1)
            ->get();

        if (count($notifications) > 0) {
            return null;
        }

        return $notifications;
    }

    public function getNotification($notificationId)
    {
        $notifications = Docnotification::where('id', '=', $notificationId)->take(1)->get();

        if (count($notifications) > 0) {
            return null;
        }

        return $notifications;
    }

    public function notify($email, $body)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ifspdocs@gmail.com';
            $mail->Password = 'Ifsp889!!';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('ifspdocs@gmail.com', 'ifdocs admin');
            $mail->addAddress($email);

            $mail->Subject = 'ifdocs - Oi, novidades do ifdoc';
            $mail->Body = "Notificações - " . $body;
            $mail->send();
            return true;

        } catch (Exception $e) {
            $error = $mail->ErrorInfo;
            return false;
        }
    }

}