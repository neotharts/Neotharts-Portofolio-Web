@extends('admin.layout')

@section('pageTitle', 'Tambah Artwork')

@section('content')
    <div class="form-card glass-card">
        <div class="form-grid">
            <div class="form-preview">
                <p class="eyebrow">Preview Artwork</p>
                <div class="preview-box" id="previewBox">
                    <span class="preview-empty">Upload artwork dan lihat tampilan di sini</span>
                    <img id="previewImage" src="" alt="Preview" hidden>
                </div>
            </div>

            <form class="artwork-form">
                <label class="field">
                    <span>Upload Gambar Artwork</span>
                    <input type="file" id="imageUpload" accept="image/*">
                </label>

                <label class="field">
                    <span>Judul Artwork</span>
                    <input type="text" placeholder="Masukkan judul artwork">
                </label>

                <label class="field">
                    <span>Type Artwork</span>
                    <select>
                        <option>Komisi</option>
                        <option>Personal</option>
                        <option>Organisasi</option>
                        <option>Fanart</option>
                    </select>
                </label>

                <label class="field">
                    <span>Form Artwork</span>
                    <select>
                        <option>Chibi</option>
                        <option>Headshot</option>
                        <option>Halfbody</option>
                        <option>Fullbody</option>
                    </select>
                </label>

                <label class="field">
                    <span>Tanggal Pembuatan</span>
                    <input type="date">
                </label>

                <label class="field">
                    <span>Nama Orang/Karakter</span>
                    <input type="text" placeholder="Masukkan nama atau karakter">
                </label>

                <label class="field field-full">
                    <span>Deskripsi Artwork</span>
                    <textarea rows="5" placeholder="Tambahkan keterangan singkat tentang artwork..."></textarea>
                </label>

                <button type="submit" class="button button-primary">Simpan Artwork</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const preview = document.getElementById('previewImage');
            const previewBox = document.getElementById('previewBox');
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.hidden = false;
                previewBox.querySelector('.preview-empty').style.display = 'none';
            };
            reader.readAsDataURL(file);
        });
    </script>
@endsection
