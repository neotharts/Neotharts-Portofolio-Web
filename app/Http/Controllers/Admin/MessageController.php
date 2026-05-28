<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    /**
     * Only admin can access
     */
    public function __construct()
    {
        $this->middleware(\App\Http\Middleware\AdminMiddleware::class);
    }

    /**
     * Display a listing of all messages.
     */
    public function index(Request $request)
    {
        $query = Message::query();

        // Filter by read status
        if ($request->filled('filter')) {
            if ($request->filter === 'unread') {
                $query->unread();
            } elseif ($request->filter === 'read') {
                $query->where('is_read', true);
            }
        }

        // Search by name, email, or subject
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Order by newest first
        $messages = $query->orderByDesc('created_at')->paginate(15);

        // Get unread count
        $unreadCount = Message::unread()->count();

        return view('admin.messages.index', compact('messages', 'unreadCount'));
    }

    /**
     * Display the specified message.
     */
    public function show(Message $message)
    {
        // Mark as read when viewing
        if (!$message->is_read) {
            $message->markAsRead();
        }

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead(Message $message)
    {
        $message->markAsRead();

        return redirect()->back()->with('success', 'Message marked as read');
    }

    /**
     * Mark all messages as read.
     */
    public function markAllAsRead()
    {
        Message::unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'All messages marked as read');
    }

    /**
     * Remove the specified message.
     */
    public function destroy(Message $message)
    {
        // Delete attachment files
        $attachmentService = app(AttachmentService::class);
        if ($message->attachments) {
            foreach ($message->attachments as $attachment) {
                $attachmentService->deleteAttachment($attachment['path']);
            }
        }

        $message->delete();

        return redirect()->route('admin.messages.index')
                        ->with('success', 'Message deleted successfully');
    }

    /**
     * Download attachment.
     */
    public function download(Message $message, int $index)
    {
        $attachments = $message->attachments ?? [];

        if (!isset($attachments[$index])) {
            abort(404, 'Attachment not found');
        }

        $attachment = $attachments[$index];
        $path = $attachment['path'];

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download(
            $path,
            $attachment['name']
        );
    }
}
