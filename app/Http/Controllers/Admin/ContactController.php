<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::where('user_id', '=', auth()->user()->id)->get();

        return view('admin.contacts.index', ['contacts' => $contacts]);
    }
}
