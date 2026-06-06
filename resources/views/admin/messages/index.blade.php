@extends('admin.layout')

@section('pageTitle', 'Messages')

@section('content')
    <!-- Page Header -->
    <div class="table-header">
        <div>
            <p class="eyebrow">Admin Dashboard</p>
            <h2>Messages</h2>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('admin.messages.markAllAsRead') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="button button-soft">
                    <span class="material-icons-outlined">done_all</span>
                    Mark All Read ({{ $unreadCount }})
                </button>
            </form>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('admin.messages.index') }}" method="GET" class="filter-form" autocomplete="off">
            <input type="text" name="search" class="filter-input" placeholder="Search name, email, or subject..." value="{{ request('search') }}" autocomplete="off">
            <select name="filter" class="filter-select">
                <option value="">All Messages</option>
                <option value="unread" {{ request('filter') === 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ request('filter') === 'read' ? 'selected' : '' }}>Read</option>
            </select>
            <button type="submit" class="button button-primary">Filter</button>
            @if(request()->has('search') || request()->filled('filter'))
                <a href="{{ route('admin.messages.index') }}" class="button button-outline">Clear</a>
            @endif
        </form>
    </div>

    <!-- Messages Card -->
    <div class="table-card glass-card">
        @if($messages->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th style="width: 80px;">Files</th>
                        <th>Date</th>
                        <th style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr style="{{ !$message->is_read ? 'background: rgba(255, 149, 67, 0.05);' : '' }}">
                            <td>
                                @if(!$message->is_read)
                                    <span style="display: inline-block; width: 10px; height: 10px; background: var(--accent-strong); border-radius: 50%;"></span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-weight: 600; color: var(--text);">{{ $message->name }}</span>
                                    <span style="font-size: 0.85rem; color: var(--muted);">{{ $message->email }}</span>
                                </div>
                            </td>
                            <td>
                                @if($message->subject)
                                    <span style="font-weight: 500; color: var(--accent-strong);">{{ $message->subject }}</span>
                                @else
                                    <span style="color: var(--muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--text-soft); font-size: 0.9rem;">{{ Str::limit($message->message, 60) }}</span>
                            </td>
                            <td>
                                @if($message->hasAttachments())
                                    <span class="badge badge-success" title="{{ count($message->attachments) }} attachments">
                                        <span class="material-icons-outlined" style="font-size: 16px;">attach_file</span>
                                        {{ count($message->attachments) }}
                                    </span>
                                @else
                                    <span style="color: var(--muted);">-</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: var(--muted); font-size: 0.85rem;">{{ $message->created_at->format('d M Y') }}</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.messages.show', $message) }}" class="button button-sm button-soft" title="View">
                                        <span class="material-icons-outlined">visibility</span>
                                    </a>
                                    <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this message?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button button-sm button-danger" title="Delete">
                                            <span class="material-icons-outlined">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $messages->withQueryString()->links() }}
            </div>
        @else
            <div class="empty-state">
                <span class="material-icons-outlined">inbox</span>
                <h3>No messages found</h3>
                <p>Messages from visitors will appear here.</p>
            </div>
        @endif
    </div>
@endsection