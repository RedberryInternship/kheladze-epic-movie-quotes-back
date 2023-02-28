<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreate;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function addComment(Request $request)
    {
        Comment::create([
            'quote_id' => $request['quoteId'],
            'comment' => $request['comment'],
            'writer_id' => $request['writerId']
        ]);

        if ($request['userId'] !== $request['writerId']) {
            $notification =  Notification::create([
                'quote_id' => $request['quoteId'],
                'user_id' => $request['userId'],
                'type' => 'comment',
                'is_new' => true,
                'writer_id' => $request['writerId'],
            ]);

            NotificationCreate::dispatch($notification);
        }

        return response()->json(['message' => 'Comment added Successfully']);
    }
    public function like(Request $request)
    {
        Like::create([
            'quote_id' => $request['quoteId'],
            'user_id' => $request['userId']
        ]);

        if ($request['userId'] !== $request['recieverId']) {
            $notification =  Notification::create([
                'quote_id' => $request['quoteId'],
                'user_id' => $request['recieverId'],
                'type' => 'like',
                'is_new' => true,
                'writer_id' => $request['userId'],
            ]);

            NotificationCreate::dispatch($notification);
        }

        return response()->json([
            'message' => 'Quote Liked Successfully',
        ]);
    }

    public function unLike(Request $request)
    {
        Like::where('user_id', $request['userId'])
            ->where('quote_id', $request['quoteId'])
            ->first()
            ->delete();
        return response()->json(['message' => 'Quote unliked Successfully']);
    }
    public function markAsRead(Request $request)
    {
        $notifications = Notification::where("user_id", $request[0])->get();

        foreach ($notifications as $notification) {
            $notification->update(['is_new' => false]);
        }

        return 'Notifications Status updated successfully.';
    }

    public function readNotification(Request $request)
    {
        Notification::find($request['id'])->update(['is_new' => false]);

        return 'Notification Status updated successfully.';
    }
}
