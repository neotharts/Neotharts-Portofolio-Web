<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Contact - Neotharts</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    @vite('resources/css/home.css')
    <style>
        :root {
            --orange: #FF9543;
            --black: #5A3F48;
        }

        body {
            min-height: 100vh;
        }

        /* Contact Page Specific Styles */
        .contact-page {
            padding: 100px 20px 60px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .contact-wrapper {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(90, 63, 72, 0.08);
            border-radius: 28px;
            box-shadow: 0 20px 60px rgba(90, 63, 72, 0.06);
            padding: 32px;
        }

        .contact-hero {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 40px;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--muted, #8c7f74);
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .contact-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--black);
        }

        .hero-desc {
            color: var(--text-soft, #8c7f74);
            font-size: 1rem;
            max-width: 400px;
        }

        .hero-icon {
            width: 90px;
            height: 90px;
            border-radius: 24px;
            background: linear-gradient(135deg, var(--orange), #ffb37a);
            display: grid;
            place-items: center;
            color: white;
            box-shadow: 0 20px 40px rgba(255, 149, 67, 0.2);
        }

        .hero-icon .material-icons {
            font-size: 40px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 28px;
        }

        .form-section h2,
        .social-section h2 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--black);
        }

        .contact-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--black);
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            border-radius: 18px;
            border: 1px solid rgba(90, 63, 72, 0.08);
            background: rgba(255, 255, 255, 0.9);
            padding: 16px 18px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            color: var(--black);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: rgba(255, 149, 67, 0.4);
            box-shadow: 0 0 0 4px rgba(255, 149, 67, 0.12);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--muted, #8c7f74);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 140px;
        }

        .submit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 32px;
            background: linear-gradient(135deg, var(--orange), #ffb37a);
            color: white;
            border: none;
            border-radius: 50px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(255, 149, 67, 0.25);
            align-self: flex-start;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(255, 149, 67, 0.35);
        }

        .submit-btn .material-icons {
            font-size: 20px;
        }

        .form-status {
            padding: 12px 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .form-status.success {
            background: rgba(46, 204, 113, 0.15);
            color: #27ae60;
            border: 1px solid rgba(46, 204, 113, 0.3);
        }

        .form-status.error {
            background: rgba(231, 76, 60, 0.15);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.3);
        }

        .form-status .material-icons {
            font-size: 20px;
        }

        /* Attachment styles */
        .attachment-area {
            border: 2px dashed rgba(90, 63, 72, 0.15);
            border-radius: 18px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.5);
        }

        .attachment-area:hover {
            border-color: rgba(255, 149, 67, 0.5);
            background: rgba(255, 149, 67, 0.05);
        }

        .attachment-area.dragover {
            border-color: var(--orange);
            background: rgba(255, 149, 67, 0.1);
            transform: scale(1.02);
        }

        .attachment-area input[type="file"] {
            display: none;
        }

        .attachment-area .material-icons {
            font-size: 32px;
            color: var(--orange);
            margin-bottom: 8px;
        }

        .attachment-area p {
            margin: 0;
            color: var(--text-soft, #8c7f74);
            font-size: 0.9rem;
        }

        .attachment-area .hint {
            font-size: 0.8rem;
            color: #aaa;
            margin-top: 4px;
        }

        .attachment-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(90, 63, 72, 0.1);
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .attachment-item .material-icons {
            font-size: 18px;
            color: var(--orange);
        }

        .attachment-item .remove-attachment {
            cursor: pointer;
            color: #999;
            margin-left: 4px;
        }

        .attachment-item .remove-attachment:hover {
            color: #e74c3c;
        }

        .social-desc {
            color: var(--text-soft, #8c7f74);
            margin-bottom: 24px;
        }

        .social-cards {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .social-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px 24px;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(90, 63, 72, 0.08);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .social-card:hover {
            transform: translateX(6px);
            box-shadow: 0 10px 30px rgba(90, 63, 72, 0.1);
        }

        .social-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .social-card.instagram .social-icon {
            background: rgba(225, 48, 108, 0.12);
            color: #E1306C;
        }

        .social-card.email .social-icon {
            background: rgba(255, 149, 67, 0.12);
            color: var(--orange);
        }

        .social-card.vgen .social-icon {
            background: rgba(26, 26, 26, 0.08);
            color: #1A1A1A;
        }

        .social-info {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .social-name {
            font-weight: 600;
            font-size: 1rem;
            color: var(--black);
        }

        .social-handle {
            font-size: 0.85rem;
            color: var(--muted, #8c7f74);
        }

        .social-card .arrow {
            color: var(--muted, #8c7f74);
            transition: transform 0.3s ease;
        }

        .social-card:hover .arrow {
            transform: translateX(4px);
            color: var(--orange);
        }

        @media (max-width: 900px) {
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .contact-hero {
                flex-direction: column;
                text-align: center;
                gap: 24px;
            }

            .contact-hero h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 600px) {
            .glass-card {
                padding: 24px;
            }

            .contact-hero {
                padding: 28px;
            }

            .submit-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="mainav">
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('artworks') }}">Artworks</a>
            <a href="{{ route('commission') }}">Commissions</a>
            <a href="{{ route('three_d') }}">3D</a>
            <a href="{{ route('contact') }}">Contact</a>
        </div>
        <div class="mainavmobile">
            <span class="material-icons">menu</span>
        </div>
    </nav>
    @include('partials.mobile-fullscreen-nav')

    <main class="contact-page">
        <div class="contact-wrapper">
            <!-- Content Grid -->
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="form-section glass-card">
                    <h2>Send a Message</h2>
                    <form id="contact-form" class="contact-form" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" placeholder="Your name" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="your@email.com" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="What's this about?" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" placeholder="Write your message here..." rows="6" autocomplete="off" required></textarea>
                        </div>

                        <!-- Attachment Upload -->
                        <div class="form-group">
                            <label>Attachments <span style="color: var(--muted, #8c7f74); font-weight: 400;">(optional)</span></label>
                            <div class="attachment-area" id="attachment-area">
                                <input type="file" id="attachments" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.rtf">
                                <span class="material-icons">attach_file</span>
                                <p>Click or drag files here</p>
                                <span class="hint">Images: max 5 (compressed) | Docs: max 3 | Max 10MB each</span>
                            </div>
                            <div class="attachment-preview" id="attachment-preview"></div>
                        </div>

                        <div id="form-status" class="form-status" style="display: none;"></div>
                        <button type="submit" id="submit-btn" class="submit-btn">
                            <span class="material-icons">send</span>
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Social Links -->
                <div class="social-section glass-card">
                    <h2>Connect with Me</h2>
                    <p class="social-desc">Find me on social media or reach out directly</p>

                    <div class="social-cards">
                        <a href="https://instagram.com/neotharts_" target="_blank" class="social-card instagram">
                            <div class="social-icon">
                                <svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                </svg>
                            </div>
                            <div class="social-info">
                                <span class="social-name">Instagram</span>
                                <span class="social-handle">@neotharts</span>
                            </div>
                            <span class="material-icons arrow">arrow_forward</span>
                        </a>

                        <a href="mailto:neotharts@gmail.com" class="social-card email">
                            <div class="social-icon">
                                <span class="material-icons">mail</span>
                            </div>
                            <div class="social-info">
                                <span class="social-name">Email</span>
                                <span class="social-handle">neotharts@gmail.com</span>
                            </div>
                            <span class="material-icons arrow">arrow_forward</span>
                        </a>

                        <a href="https://vgen.co/neotharts_" target="_blank" class="social-card vgen">
                            <div class="social-icon">
                                <img src="/img/VGen.svg" alt="VSCO Logo" width="32" height="32">
                            </div>
                            <div class="social-info">
                                <span class="social-name">VSCO</span>
                                <span class="social-handle">neotharts</span>
                            </div>
                            <span class="material-icons arrow">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const form = document.getElementById('contact-form');
        const submitBtn = document.getElementById('submit-btn');
        const formStatus = document.getElementById('form-status');
        const attachmentArea = document.getElementById('attachment-area');
        const fileInput = document.getElementById('attachments');
        const preview = document.getElementById('attachment-preview');

        let selectedFiles = [];

        // File input handling
        fileInput.addEventListener('change', handleFileSelect);

        // Drag and drop
        attachmentArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            attachmentArea.classList.add('dragover');
        });

        attachmentArea.addEventListener('dragleave', () => {
            attachmentArea.classList.remove('dragover');
        });

        attachmentArea.addEventListener('drop', (e) => {
            e.preventDefault();
            attachmentArea.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        // Click to upload
        attachmentArea.addEventListener('click', () => fileInput.click());

        function handleFileSelect(e) {
            handleFiles(e.target.files);
        }

        function handleFiles(files) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp',
                                  'application/pdf', 'application/msword',
                                  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                  'application/vnd.ms-excel',
                                  'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                  'application/vnd.ms-powerpoint',
                                  'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                                  'text/plain', 'application/rtf'];
            const maxSize = 10 * 1024 * 1024; // 10MB
            let imageCount = selectedFiles.filter(f => f.type.startsWith('image/')).length;
            let docCount = selectedFiles.filter(f => !f.type.startsWith('image/')).length;

            for (let file of files) {
                // Check type
                if (!allowedTypes.includes(file.type)) {
                    continue;
                }

                // Check size
                if (file.size > maxSize) {
                    continue;
                }

                // Check count limits
                if (file.type.startsWith('image/')) {
                    if (imageCount >= 5) continue;
                    imageCount++;
                } else {
                    if (docCount >= 3) continue;
                    docCount++;
                }

                selectedFiles.push(file);
            }

            updatePreview();
        }

        function updatePreview() {
            preview.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'attachment-item';

                let icon = 'insert_drive_file';
                if (file.type.startsWith('image/')) icon = 'image';
                else if (file.type.includes('pdf')) icon = 'picture_as_pdf';
                else if (file.type.includes('word')) icon = 'description';
                else if (file.type.includes('excel') || file.type.includes('spreadsheet')) icon = 'table_chart';
                else if (file.type.includes('powerpoint') || file.type.includes('presentation')) icon = 'slideshow';

                const size = file.size > 1024 * 1024
                    ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                    : (file.size / 1024).toFixed(0) + ' KB';

                div.innerHTML = `
                    <span class="material-icons">${icon}</span>
                    <span title="${file.name}">${file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name}</span>
                    <span style="color: #999;">(${size})</span>
                    <span class="material-icons remove-attachment" onclick="removeFile(${index})">close</span>
                `;
                preview.appendChild(div);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            updatePreview();
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="material-icons">hourglass_empty</span> Sending...';

            const formData = new FormData(form);

            // Add selected files
            selectedFiles.forEach(file => {
                formData.append('attachments[]', file);
            });

            try {
                const response = await fetch('{{ route('contact.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    formStatus.style.display = 'block';
                    formStatus.className = 'form-status success';
                    formStatus.innerHTML = '<span class="material-icons">check_circle</span> ' + result.message;
                    form.reset();
                    selectedFiles = [];
                    preview.innerHTML = '';
                    submitBtn.innerHTML = '<span class="material-icons">check</span> Sent!';

                    // Reset button after 3 seconds
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<span class="material-icons">send</span> Send Message';
                    }, 3000);

                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        formStatus.style.display = 'none';
                    }, 5000);
                } else {
                    formStatus.style.display = 'block';
                    formStatus.className = 'form-status error';
                    formStatus.innerHTML = '<span class="material-icons">error</span> ' + (result.message || 'Terjadi kesalahan');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<span class="material-icons">send</span> Send Message';
                }
            } catch (error) {
                formStatus.style.display = 'block';
                formStatus.className = 'form-status error';
                formStatus.innerHTML = '<span class="material-icons">error</span> Gagal mengirim pesan. Silakan coba lagi.';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span class="material-icons">send</span> Send Message';
            }
        });
    </script>
    <script src="{{ asset('js/mobile-fullscreen-nav.js') }}"></script>
</body>
</html>
