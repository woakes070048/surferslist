<?php
class ControllerAjaxQuestion extends Controller {
	use Captcha, ValidateField, Contact, Admin;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_question') && !$this->customer->isLogged() && !$this->isAdmin());
	}

    public function discuss_listing() {
        if (!isset($this->request->get['listing_id']) || $this->request->server['REQUEST_METHOD'] != 'POST') {
            return false;
        }

        $json = array();

		$listing_id = (int)$this->request->get['listing_id'];

		$this->load->language('product/product');
		$this->load->model('catalog/question');
		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($listing_id);

        if (!$product_info) {
            return false;
        }

		if ($product_info['member_id'] && !$product_info['customer_id']) {
			$this->setError('warning', $this->language->get('error_question_invalid'));
		}

        if (!$this->validateDiscuss()) {
            $json['error'] = implode('<br />', $this->getErrors());
        } else {
			$data = strip_tags_decode($this->request->post);

			$member_info = $this->model_catalog_product->getMemberByProductId($listing_id);

			$data['product_id'] = $listing_id;
			$data['parent_id'] = 0;
			$data['member_id'] = $member_info ? $member_info['member_id'] : '';
			$data['member_name'] = $member_info ? $member_info['member_name'] : '';
			$data['member_email'] = $member_info ? $member_info['member_email'] : '';

			if ($this->model_catalog_question->addQuestion($data)) {
				$mail_sent = $this->sendQuestion($data);

				$json['success'] = $this->language->get('text_success_discussion');

				if (!$this->getContactMailStatus()) {
					$json['error'] = $this->language->get('error_disabled_mail');
				} else if (!$mail_sent) {
					$json['error'] = $this->language->get('error_send_mail');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

    public function discuss_member() {
		if (empty($this->request->get['member_id']) || $this->request->server['REQUEST_METHOD'] != 'POST') {
			return false;
		}

        $json = array();

		$member_id = (int)$this->request->get['member_id'];

		$this->load->language('product/member');
		$this->load->model('catalog/question');
		$this->load->model('catalog/member');

		$member_info = $this->model_catalog_member->getMember($member_id);

        if (!$member_info) {
            return false;
        }

		if (!$this->validateDiscuss()) {
            $json['error'] = implode('<br />', $this->getErrors());
        } else {
			$data = strip_tags_decode($this->request->post);

			$data['product_id'] = 0;
			$data['parent_id'] = 0;
			$data['member_id'] = $member_id;
			$data['member_name'] = $member_info['member_account_name'];
			$data['member_email'] = $member_info['email'] ?: '';

			if ($this->model_catalog_question->addQuestion($data)) {
				$mail_sent = $this->sendQuestion($data);

				$json['success'] = $this->language->get('text_success_discussion');

				if (!$this->getContactMailStatus()) {
					$json['error'] = $this->language->get('error_disabled_mail');
				} else if (!$mail_sent) {
					$json['error'] = $this->language->get('error_send_mail');
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}

    public function show_listing() {
		if (empty($this->request->get['listing_id'])) {
			return false;
		}

		$listing_id = (int)$this->request->get['listing_id'];

		$this->load->language('product/product');
		$this->load->model('catalog/question');
		$this->load->model('catalog/member');
		$this->load->model('tool/image');

		$this->data['questions'] = array();

		$page = isset($this->request->get['page']) && (int)$this->request->get['page'] > 0 ? (int)$this->request->get['page'] : 1;
		$limit = isset($this->request->get['limit']) && (int)$this->request->get['limit'] > 0 ? (int)$this->request->get['limit'] : 10;

		$question_total = $this->model_catalog_question->getTotalQuestionsByProductId($listing_id);
		$results = $this->model_catalog_question->getQuestionsByProductId($listing_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			if ($result['author_member_name']) {
				$name = $result['author_member_name'];
			} else if ($result['author_customer_name']) {
				$name = $result['author_customer_name'];
			} else {
				$name = $this->language->get('text_anon');
			}

			$image = $this->model_tool_image->resize($result['author_member_image'], 40, 40, 'autocrop');

			$this->data['questions'][] = array(
				'name'       => $name,
				'href'       => $result['author_member_id'] ? $this->url->link('product/member/info', 'member_id=' . $result['author_member_id']) : '',
				'image'      => $image,
				'text'       => nl2br($result['text']),
				'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added']))
			);
		}

		$this->data['text_questions'] = sprintf($this->language->get('text_questions'), (int)$question_total);
		$this->data['text_on'] = $this->language->get('text_on');
		$this->data['text_no_questions'] = $this->language->get('text_no_questions');

		$this->data['pagination'] = $this->getPagination($question_total, $page, $limit, 'ajax/question/show_listing', 'listing_id=' . $listing_id);

		$this->template = '/template/product/question.tpl';

		$this->response->setOutput($this->render());
	}

    public function show_member() {
		if (empty($this->request->get['member_id'])) {
			return false;
		}

		$member_id = (int)$this->request->get['member_id'];

		$this->load->language('product/member');
		$this->load->model('catalog/question');
		$this->load->model('tool/image');

		$this->data['questions'] = array();

		$page = isset($this->request->get['page']) && (int)$this->request->get['page'] > 0 ? (int)$this->request->get['page'] : 1;
		$limit = isset($this->request->get['limit']) && (int)$this->request->get['limit'] > 0 ? (int)$this->request->get['limit'] : 10;

		$discussion_total = $this->model_catalog_question->getTotalQuestionsByMemberId($member_id);
		$results = $this->model_catalog_question->getQuestionsByMemberId($member_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			if ($result['author_member_name']) {
				$name = $result['author_member_name'];
			} else if ($result['author_customer_name']) {
				$name = $result['author_customer_name'];
			} else {
				$name = $this->language->get('text_anon');
			}

			$image = $this->model_tool_image->resize($result['author_member_image'], 40, 40, 'autocrop');

			$this->data['questions'][] = array(
				'name'       => $name,
				'href'       => $result['author_member_id'] ? $this->url->link('product/member/info', 'member_id=' . $result['author_member_id']) : '',
				'image'      => $image,
				'text'       => nl2br($result['text']),
				'questions'  => sprintf($this->language->get('text_discussions'), (int)$discussion_total),
				'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added']))
			);
		}

		$this->data['text_on'] = $this->language->get('text_on');
		$this->data['text_no_questions'] = $this->language->get('text_no_discussions');

		$this->data['pagination'] = $this->getPagination($discussion_total, $page, $limit, 'ajax/question/show_member', 'member_id=' . $member_id);

		$this->template = '/template/product/question.tpl';

		$this->response->setOutput($this->render());
	}

    protected function validateDiscuss() {
        if (!$this->validateCaptcha()) {
            $this->setError('captcha', $this->getCaptchaError());
            return false;
        }

		if (empty($this->request->post['text']) || !$this->validateStringLength($this->request->post['text'], 3, 500)) {
			$this->setError('text', sprintf($this->language->get('error_text'), 3, 500));
		}

        if (!$this->customer->isLogged() && !$this->isAdmin()) {
            if (!isset($this->request->post['name']) || !$this->validateStringLength($this->request->post['name'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['name'])) {
                $this->setError('name', sprintf($this->language->get('error_name'), 3, 32));
            }

			if (!isset($this->request->post['email']) || !$this->validateEmail($this->request->post['email'])) {
                $$this->setError('email', $this->language->get('error_email'));
            }

            $this->load->model('account/customer');

            if (isset($this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
                $this->setError('account', sprintf($this->language->get('error_exists'), $this->url->link('account/login', '', 'SSL')));
            }
        }

		if ($this->customer->isLogged() && !$this->hasError()) {
			$question_count = $this->model_catalog_question->getTotalQuestionsByUser(date('Y-m-d H:i:s', strtotime('-30 minutes')));

			if ($question_count && ($question_count['total'] >= 10) && strtotime('-30 minutes') < strtotime($question_count['date_added'])) {
				$this->setError('warning', $this->language->get('error_max_limit'));
			}
		}

        // removed to allow anyone to discuss
        /*
		if (!$this->customer->validateLogin()) {
            $this->setError('login', $this->language->get('error_question_logged'));
        } else if (!$this->customer->validateProfile()) {
            $this->setError('profile', $this->language->get('error_question_membership'));
        }
		*/

        return !$this->hasError();
    }

	protected function sendQuestion($data) {
		$this->load->language('mail/question');

		$subject = '';
		$message = '';
		$bcc = array();

		// author
		if ($this->customer->isLogged()) {
			$asker_email = $this->customer->getEmail();
			$asker_name = $this->customer->hasProfile()
				? $this->customer->getProfileName() . ' (' . $this->customer->getProfileUrl() . ')'
				: $this->customer->getLastName() . ', ' . $this->customer->getFirstName();
		} else if ($this->isAdmin()) {
			$asker_email = $this->config->get('config_email');
			$asker_name = $this->config->get('config_name');
		} else {
			$asker_email = $data['email'];
			$asker_name = $data['name'];
		}

		// discussion/question
		$message_text = strip_tags(html_entity_decode($data['text'], ENT_QUOTES, 'UTF-8'));

		// listing
		if ($data['product_id']) {
			$this->load->model('catalog/product');

			$product_info = $this->model_catalog_product->getProduct($data['product_id']);
			$product_name = $product_info ? strip_tags_decode($product_info['name']) : '';

			$subject = sprintf(
				$this->language->get('text_question_mail_subject'),
				$this->config->get('config_name'),
				$product_name
			);

			$message = sprintf(
				$this->language->get('text_question_mail'),
				'"' . $product_name . '" (' . $this->url->link('product/product', 'product_id=' . $data['product_id']) . ')',
				$asker_name,
				$message_text
			);
		}

		// profile
		if ($data['member_id'] && !$data['product_id']) {
			$member_name = strip_tags_decode($data['member_name']);
			$member_email = $data['member_email'];

			$subject = sprintf(
				$this->language->get('text_disucssion_mail_subject'),
				$this->config->get('config_name'),
				$member_name
			);

			$message = sprintf(
				$this->language->get('text_disucssion_mail'),
				'"' . $member_name . '" (' . $this->url->link('product/member/info', 'member_id=' . $data['member_id']) . ')',
				$asker_name,
				$message_text
			);
		}

		if ($this->config->get('member_email_customers')) {
			// copy profile owner
			if (!empty($member_email)) {
				$bcc[] = $member_email;
			}

			// copy user submitting the discussion message, unless profile activated and email notification disabled
			if (!$this->isAdmin() && (!$this->customer->hasProfile() || $this->customer->getEmailNotifySetting('email_discuss'))) {
				$bcc[] = $asker_email;
			}
		}

		return $this->sendEmail(array(
			'to' 		=> $this->config->get('config_email'),
			'from' 		=> $this->config->get('config_email_noreply'),
			'sender' 	=> $this->config->get('config_name'),
			'subject' 	=> $subject,
			'message' 	=> $message,
			'bcc' 		=> $bcc,
			'reply' 	=> $this->config->get('config_email_noreply')
		));
	}

}
