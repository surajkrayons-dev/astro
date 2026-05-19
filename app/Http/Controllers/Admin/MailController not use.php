<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\BulkAstroMail;
use Illuminate\Support\Str;

class MailController extends AdminController
{
    public function getIndex()
    {
        return view('admin.send_mail.index');
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        Mail::to($request->email)->send(new BulkAstroMail($request->name));

        return back()->with('success', 'Mail Sent Successfully!');
    }
}
