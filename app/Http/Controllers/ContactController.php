<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Message;
use App\Services\AttachmentService;

class ContactController extends Controller
{
    protected $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Handle contact form submission.
     */
    public function store(ContactRequest $request)
    {
        $validated = $request->validated();

        try {
            $attachments = [];

            // Process attachments if any
            if ($request->hasFile('attachments')) {
                $files = $request->file('attachments');
                $results = $this->attachmentService->processAttachments($files);

                foreach ($results as $result) {
                    if ($result['success']) {
                        $attachments[] = [
                            'name' => $result['name'],
                            'path' => $result['path'],
                            'type' => $result['type'],
                            'size' => $result['original_size'],
                            'compressed_size' => $result['compressed_size'] ?? null,
                        ];
                    }
                }
            }

            Message::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'] ?? null,
                'message' => $validated['message'],
                'attachments' => $attachments,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pesan kamu berhasil dikirim!' . (count($attachments) > 0 ? ' (' . count($attachments) . ' lampiran terlampir)' : ''),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan. Silakan coba lagi.',
            ], 500);
        }
    }
}
