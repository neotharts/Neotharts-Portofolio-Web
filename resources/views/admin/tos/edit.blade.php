@extends('admin.layout')

@section('pageTitle', 'Edit Terms of Service')

@section('content')
    <div class="form-card glass-card">
        <div class="form-header">
            <div>
                <p class="eyebrow">Pengaturan Website</p>
                <h2>Terms of Service (TOS)</h2>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <h4>Validasi Gagal</h4>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.tos.update') }}" method="POST" class="artwork-form">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h3>Konten TOS</h3>
                <p class="muted-text" style="margin-bottom: 16px;">Konten ini akan ditampilkan di halaman Commission saat user klik tombol "TOS".</p>

                <div class="form-group">
                    <div class="tos-editor">
                        <div class="tos-toolbar">
                            <button type="button" onclick="insertTag('tos', '<h3>', '</h3>')" title="Heading 3"><b>H3</b></button>
                            <button type="button" onclick="insertTag('tos', '<h4>', '</h4>')" title="Heading 4"><b>H4</b></button>
                            <span class="toolbar-sep">|</span>
                            <button type="button" onclick="insertTag('tos', '<p>', '</p>')" title="Paragraph"><b>P</b></button>
                            <button type="button" onclick="insertTag('tos', '<strong>', '</strong>')" title="Bold"><b>B</b></button>
                            <button type="button" onclick="insertTag('tos', '<em>', '</em>')" title="Italic"><i>I</i></button>
                            <span class="toolbar-sep">|</span>
                            <button type="button" onclick="insertTag('tos', '<ul>\n<li>', '</li>\n</ul>')" title="Bullet List">
                                <span class="material-icons-outlined" style="font-size: 16px;">format_list_bulleted</span>
                            </button>
                            <button type="button" onclick="insertTag('tos', '<ol>\n<li>', '</li>\n</ol>')" title="Numbered List">
                                <span class="material-icons-outlined" style="font-size: 16px;">format_list_numbered</span>
                            </button>
                            <span class="toolbar-sep">|</span>
                            <button type="button" onclick="insertTag('tos', '<hr>', '')" title="Horizontal Line">
                                <span class="material-icons-outlined" style="font-size: 16px;">horizontal_rule</span>
                            </button>
                        </div>
                        <textarea name="tos" id="tos" rows="15" placeholder="Masukkan konten TOS di sini...">{{ $tosContent }}</textarea>
                        <small class="muted-text" style="display: block; margin-top: 8px;">Preview:</small>
                        <div class="tos-preview" id="tosPreview">{{ $tosContent }}</div>
                    </div>
                    @error('tos')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.dashboard') }}" class="button button-outline">Batal</a>
                <button type="submit" class="button button-primary">Simpan TOS</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .tos-editor {
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        background: var(--surface);
    }

    .tos-toolbar {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 10px 12px;
        background: var(--surface-soft);
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .tos-toolbar button {
        padding: 6px 12px;
        border: 1px solid var(--border);
        background: white;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }

    .tos-toolbar button:hover {
        background: var(--accent);
        color: white;
        border-color: var(--accent);
    }

    .toolbar-sep {
        color: var(--muted);
        margin: 0 8px;
    }

    .tos-editor textarea {
        width: 100%;
        padding: 12px;
        border: none;
        resize: vertical;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        min-height: 250px;
        background: white;
    }

    .tos-editor textarea:focus {
        outline: none;
    }

    .tos-preview {
        padding: 16px;
        background: var(--surface-soft);
        border-top: 1px solid var(--border);
        min-height: 150px;
        font-size: 14px;
        line-height: 1.6;
        max-height: 300px;
        overflow-y: auto;
    }

    .tos-preview h3 {
        color: var(--text);
        margin: 20px 0 10px;
        font-size: 18px;
    }

    .tos-preview h3:first-child {
        margin-top: 0;
    }

    .tos-preview h4 {
        color: var(--text);
        margin: 16px 0 8px;
        font-size: 16px;
    }

    .tos-preview p {
        margin: 10px 0;
    }

    .tos-preview ul, .tos-preview ol {
        margin: 10px 0;
        padding-left: 24px;
    }

    .tos-preview li {
        margin: 6px 0;
    }

    .tos-preview hr {
        border: none;
        border-top: 2px solid var(--border);
        margin: 20px 0;
    }

    .tos-preview strong {
        font-weight: 700;
    }

    .tos-preview em {
        font-style: italic;
    }
</style>
@endpush

@push('scripts')
<script>
    function insertTag(textareaId, openTag, closeTag) {
        const textarea = document.getElementById(textareaId);
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        const selectedText = text.substring(start, end);

        // Insert tag around selection or at cursor
        const newText = text.substring(0, start) + openTag + selectedText + closeTag + text.substring(end);
        textarea.value = newText;

        // Set cursor position
        if (selectedText) {
            textarea.selectionStart = start + openTag.length;
            textarea.selectionEnd = start + openTag.length + selectedText.length;
        } else {
            textarea.selectionStart = textarea.selectionEnd = start + openTag.length;
        }

        textarea.focus();
        updateTosPreview();
    }

    function updateTosPreview() {
        const textarea = document.getElementById('tos');
        const preview = document.getElementById('tosPreview');
        if (textarea && preview) {
            preview.innerHTML = textarea.value || '<em style="color: var(--muted);">Preview akan muncul di sini...</em>';
        }
    }

    // Update preview on input
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('tos');
        if (textarea) {
            textarea.addEventListener('input', updateTosPreview);
            // Initial render
            updateTosPreview();
        }
    });
</script>
@endpush