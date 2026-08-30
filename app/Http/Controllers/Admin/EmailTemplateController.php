<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();

        return view('admin.email_template.index', compact('templates'));
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.email_template.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:190',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'name' => $data['name'],
            'subject' => $data['subject'],
            'body' => $data['body'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.email-templates.index')->with('success', 'Template updated.');
    }
}
