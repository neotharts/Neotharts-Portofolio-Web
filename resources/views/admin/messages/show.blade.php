@extends('admin.layout')

@section('pageTitle', 'View Message')

@section('content')
    <!-- Back Button -->
    <div class="detail-header">
        <a href="{{ route('admin.messages.index') }}" class="button button-outline">
            <span class="material-icons-outlined">arrow_back</span>
            Back to Messages
        </a>
        <div class="detail-actions">
            @if(!$message->is_read)
                <a href="{{ route('admin.messages.markAsRead', $message) }}" class="button button-soft">
                    <span class="material-icons-outlined">done</span>
                    Mark as Read
                </a>
            @endif
            <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this message? This cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="button button-danger">
                    <span class="material-icons-outlined">delete</span>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Message Detail Card -->
    <div class="detail-card glass-card">
        <!-- Message Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 24px 28px; border-bottom: 1px solid rgba(31, 27, 24, 0.08); flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, var(--accent), var(--accent-strong)); display: grid; place-items: center; color: white; font-weight: 700; font-size: 1.25rem;">
                    {{ strtoupper(substr($message->name, 0, 1)) }}
                </div>
                <div>
                    <h2 style="margin: 0 0 4px; font-size: 1.25rem; color: var(--text);">{{ $message->name }}</h2>
                    <a href="mailto:{{ $message->email }}" style="color: var(--accent-strong); font-size: 0.9rem; text-decoration: none;">{{ $message->email }}</a>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <span style="color: var(--muted); font-size: 0.85rem;">{{ $message->created_at->format('d M Y, H:i') }}</span>
                @if($message->is_read)
                    <span class="badge badge-success">
                        <span class="material-icons-outlined" style="font-size: 16px;">done_all</span>
                        Read
                    </span>
                @else
                    <span class="badge badge-warning">
                        <span class="material-icons-outlined" style="font-size: 16px;">mark_email_unread</span>
                        Unread
                    </span>
                @endif
            </div>
        </div>

        <!-- Subject -->
        @if($message->subject)
            <div style="padding: 16px 28px; background: rgba(255, 149, 67, 0.08); border-bottom: 1px solid rgba(31, 27, 24, 0.08);">
                <span style="color: var(--muted); font-size: 0.9rem;">Subject:</span>
                <span style="font-weight: 600; color: var(--accent-strong); margin-left: 8px;">{{ $message->subject }}</span>
            </div>
        @endif

        <!-- Message Body -->
        <div style="padding: 28px; font-size: 1rem; line-height: 1.8; color: var(--text);">
            {!! nl2br(e($message->message)) !!}
        </div>

        <!-- Attachments -->
        @if($message->hasAttachments())
            <div style="padding: 24px 28px; border-top: 1px solid rgba(31, 27, 24, 0.08);">
                <h4 style="margin: 0 0 16px; color: var(--text); display: flex; align-items: center; gap: 8px;">
                    <span class="material-icons-outlined">attach_file</span>
                    Attachments ({{ count($message->attachments) }})
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    @foreach($message->attachments as $attachment)
                        <a href="{{ route('admin.messages.download', ['message' => $message->id, 'index' => $loop->index]) }}"
                           class="attachment-card" target="_blank" style="text-decoration: none;">
                            @if($attachment['type'] === 'image')
                                <img src="{{ asset('storage/' . $attachment['path']) }}"
                                     alt="{{ $attachment['name'] }}"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; border: 1px solid rgba(31, 27, 24, 0.1);">
                            @else
                                <div style="width: 80px; height: 80px; background: rgba(255, 149, 67, 0.1); border-radius: 12px; display: grid; place-items: center;">
                                    <span class="material-icons-outlined" style="font-size: 32px; color: var(--accent-strong);">
                                        @if(str_contains($attachment['name'], '.pdf'))
                                            picture_as_pdf
                                        @elseif(str_contains($attachment['name'], '.doc') || str_contains($attachment['name'], '.docx'))
                                            description
                                        @elseif(str_contains($attachment['name'], '.xls') || str_contains($attachment['name'], '.xlsx'))
                                            table_chart
                                        @else
                                            insert_drive_file
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <span style="font-size: 0.8rem; color: var(--text); max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $attachment['name'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Footer Actions -->
        <div style="padding: 24px 28px; border-top: 1px solid rgba(31, 27, 24, 0.08);">
            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject ?? 'Your Message' }}" class="button button-primary">
                <span class="material-icons-outlined">reply</span>
                Reply via Email
            </a>
        </div>
    </div>
@endsection