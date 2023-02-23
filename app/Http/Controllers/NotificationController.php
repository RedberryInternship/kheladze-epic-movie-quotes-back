<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Notification;
use Illuminate\Http\Request;
use Termwind\Components\Li;

class NotificationController extends Controller
{
    public function addComment(Request $request)
    {
        Comment::create([
            'quote_id' => $request['quoteId'],
            'comment' => $request['comment'],
            'writer_id' => $request['writerId']
        ]);

        Notification::create([
            'quote_id' => $request['quoteId'],
            'user_id' => $request['userId'],
            'type' => 'comment',
            'is_new' => true,
            'writer_id' => $request['writerId'],
        ]);

        return response()->json(['message' => 'Comment added Successfully']);
    }
    public function like(Request $request)
    {
        Like::create([
            'quote_id' => $request['quoteId'],
            'user_id' => $request['userId']
        ]);

        Notification::create([
            'quote_id' => $request['quoteId'],
            'user_id' => $request['recieverId'],
            'type' => 'like',
            'is_new' => true,
            'writer_id' => $request['userId'],
        ]);
        return response()->json(['message' => 'Quote Liked Successfully']);
    }

    public function unLike(Request $request)
    {
        Like::where('user_id', $request['userId'])
            ->where('quote_id', $request['quoteId'])
            ->first()
            ->delete();
        return response()->json(['message' => 'Quote unliked Successfully']);
    }
    public function markAsRead()
    {
        $notifications = Notification::all();

        foreach ($notifications as $notification) {
            $notification->update(['is_new' => false]);
        }

        return 'Notifications Status updated successfully.';
    }

    public function readNotification(Request $request)
    {

        $notification = Notification::where('id', $request['id'])->first();
        $notification->is_new = false;
        $notification->save();

        return 'Notification Status updated successfully.';
    }
}
