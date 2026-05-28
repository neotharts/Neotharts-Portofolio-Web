<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ], [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'message.required' => 'Pesan wajib diisi',
            'attachments.*.max' => 'File terlalu besar. Maksimal 10MB per file.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

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
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
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
