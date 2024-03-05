<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Model\CMS;
use App\Model\FAQQuestion;
use Illuminate\Http\Request;

class PagesController extends Controller
{

    public function index(Request $request, $slug)
    {
        if (auth()->user()) {
            if ($slug == '') {
                return $this->InvalidUrl();
            }
            $cms = CMS::where('status', 'AC')->where('slug', $slug)->first();
            $faqs = [];

            if ($request->segment(2) == 'faq') {
                $faqs = FAQQuestion::where('status', 'AC')->select('question', 'answer', 'id')->where('type', 'faq')->get();
                $pageTitle = "FAQ's";
                $title = "FAQ's";

            } elseif ($request->segment(2) == 'delivery-faq') {
                $faqs = FAQQuestion::where('status', 'AC')->select('question', 'answer', 'id')->where('type', 'delivery-faq')->get();
                $pageTitle = "Delivery FAQ's";
                $title = "Delivery FAQ's";

            } else {
                if (empty($cms)) {
                    return redirect()->route('front.unauthorized.notfound');
                }
                $pageTitle = $cms->name;
                $title = $cms->name;
            }

            return view('front.pages.index', compact('cms', 'pageTitle', 'title', 'faqs'));
        } else {
            return redirect()->route('home');
        }

    }
}
