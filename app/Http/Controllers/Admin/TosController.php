<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class TosController extends Controller
{
    /**
     * Display TOS edit form
     */
    public function edit()
    {
        $tosContent = SiteSetting::getValue('tos', '');
        return view('admin.tos.edit', compact('tosContent'));
    }

    /**
     * Update TOS
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'tos' => 'nullable|string',
        ]);

        SiteSetting::setValue('tos', $validated['tos'] ?? '');

        return redirect()->route('admin.tos.edit')
                        ->with('success', 'Terms of Service berhasil diperbarui!');
    }
}