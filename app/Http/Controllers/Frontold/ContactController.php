<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Contact;
// use App\Helpers\BasicFunction;
use App\Model\EmailTemplate;
use App\Library\Helper;
use Validator;
use Config;

class ContactController extends Controller {

    public function index() {

        $pageTitle = 'Contact Us';
        $title = 'Contact Us';
        // $pages['Home'] = '/';
        // $breadcrumb = array('pages' => $pages, 'active' => 'Contact Us');

        return view('front.pages.contact', compact('pageTitle', 'title'));

    }

    public function store(Request $request) {
        // $contactObj = new Contact();
        $validator = validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone_no' => 'required|numeric',
            'message' => 'required',
            
        ],['phone_no.required'=>'The mobile no. is required','phone_no.numeric'=>'The mobile no. must be numeric.']
        );

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input = $request->all();
        //$input['slug'] = BasicFunction::getUniqueSlug($buildingsObj, $request->name);

        // $contact = $contactObj->create($input);
        
        /*********contact mail send*********/
        $email_template = EmailTemplate::where('slug', 
            '=', 'contact-us-admin')->first();
        $email_type = $email_template->email_type;
        $subject = $email_template->subject;
        $body = $email_template->body;

        // $email_to = Config::get('settings.CONFIG_ADMIN_EMAIL');
        $email_to = 'sakshij@officebox.vervelogic.com';

        // $login_link = WEBSITE_URL;
        $body = str_replace(array(
            '{NAME}',
            '{EMAIL}',
            '{MESSAGE}'
                ), array(
            ucfirst($request->name),
            $request->email,
            $request->message,
                ), $body);


        $subject = str_replace(array(
            '{NAME}',
            '{EMAIL}',
            '{MESSAGE}'
                ), array(
            ucfirst($request->name),
            $request->email,
            $request->message,
                ), $subject);

        Helper::sendMail($email_to, '', '', $subject, 'default', $body, $email_type);
        /*********contact mail send end*********/

        return redirect()->back()->with('alert-sucess', 'Thank You for contacting us!');
    }
}
