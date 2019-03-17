<?php

namespace App\Controllers;


use App\Models\Docnotification;
use App\Models\Usnotification;

class NotificationController extends BaseController
{
    public function createUserNotification($data)
    {
        $notification = new Usnotification();

        $notification->creator_id = $data['creator_id'];
        $notification->receiver_id = $data['receiver_id'];
        $notification->read_status = $data['read_status'];
        $notification->type = $data['type'];
        $notification->body = $data['body'];

        if (empty($notification->body) or empty($notification->receiver_id) or empty($notification->creator_id) or empty($notification->type)) {
            return null;
        }

        $notification->save();

        return $notification;
    }

    public function createNotification($request, $response)
    {

        $notification = new Usnotification();
        $notification->creator_id = 1;
        $notification->receiver_id = $request->getParsedBodyParam('receiver_id', '');
        $notification->type = 'atencao';
        $notification->body = $request->getParsedBodyParam('body', '');
        $notification->save();


        if (empty($notification->body) or empty($notification->receiver_id) or empty($notification->creator_id) or empty($notification->type)) {
            return null;
        }

        $notification->save();

        if ($notification->id) {
            $payload[] = $notification->output();
            return $response->withStatus(200)->withJson([
                'message' => 'Success',
                'code' => 200,
            ]);
        } else {
            return $response->withStatus(400);
        }

    }

    public function allUserNotifications($request, $response)
    {
        $currentUser = $this->currentUser($request);

        $notifications = Usnotification::where('receiver_id', '=', $currentUser[0]->id)->get();

        if (count($notifications) <= 0) {
            return $this->response($response, 'No notifications available for this user', 200);
        }

        foreach ($notifications as $_notification) {
            $payload[] = $_notification->output();
        }

        return $response->withStatus(200)->withJson([
            'message' => 'Success',
            'code' => 200,
            'notifications' => $payload
        ]);
    }


    public function createDocumentNotification($data)
    {
        $notification = new Docnotification();

        $notification->creator_id = $data['creator_id'];
        $notification->receiver_id = $data['receiver_id'];
        $notification->read_status = $data['read_status'];
        $notification->type = $data['type'];
        $notification->document_id = $data['document_id'];
        $notification->body = $data['body'];

        if (empty($notification->body) or empty($notification->receiver_id) or empty($notification->creator_id) or empty($notification->type)) {
            return null;
        }

        $notification->save();

        return $notification;
    }

    public function retrieveDocumentNotifications($receiver_id)
    {
        $notifications = Docnotification::getNotifications($receiver_id);
    }

    public function updateUserNotification($request, $response, $args)
    {
        $notification_id = $args['notification_id'];
        if (!empty($args['receiver_id'])) {
            $notification = Usnotification::getNotification($notification_id);
            if (!empty($notification)) {
                $notification->readstatus = true;
                $notification->save();
                return $this->response($response, 'Notification updated successfully', 200);
            } else {
                return $this->response($response, 'Unable to find requested notification', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [id], try again', 401);
        }
    }

    public function updateDocumentNotification($request, $response, $args)
    {
        $notification_id = $args['$notification_id'];
        if (!empty($args['$notification_id'])) {
            $notification = Usnotification::getNotification($notification_id);
            if (!empty($notification)) {
                $notification->readstatus = true;
                $notification->save();
                return $this->response($response, 'Notification updated successfully', 200);
            } else {
                return $this->response($response, 'Unable to find requested notification', 404);
            }
        } else {
            return $this->response($response, 'Missing parameter [id], try again', 401);
        }
    }
}