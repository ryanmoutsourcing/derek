<?php

namespace App\Http\Controllers;

use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{

    public function index() {
        return view('contact');
    }

    public function store(Request $request) {
        dd(RecaptchaV3::verify($request->get('g-recaptcha-response'), 'contact'));
        
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'message' => 'required',
            'subject' => 'required',
            'g-recaptcha-response' => 'required|recaptchav3:contact,0.5' 
        ]);

        $requestData = $request->all();

        Contact::create($requestData);

        return redirect('contact')->with("status", "Your message has been sent");
    }

    public function listMessages() {
        $messages = Contact::all();
        return view('list-messages', compact('messages'));
    }

    public function message_details($id) {
        $message = Contact::findOrFail($id);
        return view('details-message', compact('message'));
    }

    public function delete_message($id) {
        $message = Contact::find($id);
        if ($message) {
            $message->delete();
        }
        return redirect('/admin/view-messages');
    }

    public function deleteMultipleMessages(Request $request) {
        $ids = $request->get('selected');
        Contact::whereIn('id', $ids)->delete();
        return response("Selected messages have been deleted successfully", 200);
    }
}
