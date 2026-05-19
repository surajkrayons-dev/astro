<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Mail\SendBulkMail;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BulkMailController extends AdminController
{
    public function getIndex(Request $request)
    {
        return view('admin.bulk_mails.index');
    }

    public function getList(Request $request)
    {
        $list = MailLog::query()
            ->select([
                'id',
                'email',
                'created_at'
            ])
            ->orderBy('id', 'desc');

        return \DataTables::of($list)

        ->editColumn('created_at', function ($row) {
    
            return $row->created_at
                ? $row->created_at->format('d M Y h:i A')
                : '-';
    
        })
    
        ->make();
    }

    public function getCreate()
    {
        return view('admin.bulk_mails.create');
    }

    public function postCreate(Request $request)
    {
        $request->validate([
            'emails' => 'required'
        ]);

        $emails = preg_split('/\r\n|\r|\n|,/', $request->emails);

        $emails = array_filter(array_map('trim', $emails));

        foreach ($emails as $email) {

            $user = User::where('email', $email)->first();

            if (!$user) {
                continue;
            }

            Mail::to($email)
                ->send(new SendBulkMail($user));

            MailLog::create([
                'email' => $email,
            ]);
        }

        return response()->json([
            'message' => 'Bulk mail sent successfully'
        ]);
    }
}