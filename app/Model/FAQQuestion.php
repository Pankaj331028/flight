<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FAQQuestion extends Model {
	protected $table = 'c_faq_questions';

	public static function fetchquestions($type) {
		return FAQQuestion::where('status', '!=', 'DL')->where('type',$type)->get();
	}

	public static function saveQuestions($count, $req) {

		for ($i = 1; $i <= $count; $i++) {
			$exists = $req["faqid_" . $i];
			$faq = FAQQuestion::find($exists);
			if (isset($faq->id)) {
				$ques = "question_" . $i;
				$ans = "answer_" . $i;
				$type = "faqtype_" . $i;
				$faq->type = $req[$type];
				$faq->question = $req[$ques];
				$faq->answer = $req[$ans];
				$faq->status = trim($req["faqstatus_" . $i]); 
			} else {
				$faq = new FAQQuestion;
				$ques = "question_" . $i;
				$ans = "answer_" . $i;
				$type = "faqtype_" . $i;
				$faq->type = $req[$type];
				$faq->question = $req[$ques];
				$faq->answer = $req[$ans];
				$faq->status = trim($req["faqstatus_" . $i]);
			}
			$faq->save();
		}
	}
}
