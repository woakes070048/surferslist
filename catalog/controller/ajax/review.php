<?php
class ControllerAjaxReview extends Controller {
	// use Captcha;
    //
	// public function __construct($registry) {
	// 	parent::__construct($registry);
    //
	// 	$this->setCaptchaStatus($this->config->get('config_captcha_review'));
	// }

    public function write_member() {
        if (empty($this->request->get['member_id'])) {
            return false;
        }

        $member_id = (int)$this->request->get['member_id'];

        $json = array();

        $this->load->language('product/member');
        $this->load->model('catalog/review');
        $this->load->model('catalog/member');

        $member_info = $this->model_catalog_member->getMember($member_id);

        if (!$member_info) {
            return false;
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->config->get('config_review_status')) {
            if (!$this->customer->validateLogin()) {
               $json['error'] = $this->language->get('error_review_logged');
               unset($this->session->data['warning']);
            } else if (!$this->customer->validateProfile()) {
               $json['error'] = $this->language->get('error_review_membership');
               unset($this->session->data['warning']);
           } else if ($this->customer->getProfileId() == $member_id) {
               $json['error'] = $this->language->get('error_review_self');
               unset($this->session->data['warning']);
            } /*else if (!$this->customer->getTotalOrdersWithMember($member_info['customer_id'])) {
               $json['error'] = $this->language->get('error_review_order');
            } */

            $review_exists = $this->model_catalog_review->hasSubmittedReviewForMemberId($member_id);

            if ($review_exists) {
                $json['error'] = $this->language->get('error_review_exists');
            }

            if (!isset($json['error'])) {
                /*
                if (empty($this->request->post['name']) || (utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
                    $json['error'] = sprintf($this->language->get('error_name'), 3, 32);
                }*/

                if (empty($this->request->post['text']) || (utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
                    $json['error'] = sprintf($this->language->get('error_review_text'), 25, 1000);
                }

                if (empty($this->request->post['rating']) || (int)$this->request->post['rating'] < 1 || (int)$this->request->post['rating'] > 5) {
                    $json['error'] = sprintf($this->language->get('error_rating'), 1, 5);
                }
            }

            if (!isset($json['error'])) {
                $this->model_catalog_review->addReview($member_id, $this->request->post);
                $json['success'] = $this->language->get('text_success');
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function show_member() {
        if (empty($this->request->get['member_id'])) {
            return false;
        }

        $member_id = (int)$this->request->get['member_id'];

        $this->load->language('product/member');
        $this->load->model('catalog/review');
        $this->load->model('tool/image');

        $page = isset($this->request->get['page']) && (int)$this->request->get['page'] > 0 ? (int)$this->request->get['page'] : 1;
        $limit = isset($this->request->get['limit']) && (int)$this->request->get['limit'] > 0 ? (int)$this->request->get['limit'] : 10;

        $this->data['reviews'] = array();

        $review_total = $this->model_catalog_review->getTotalReviewsByMemberId($member_id);
        $results = $this->model_catalog_review->getReviewsByMemberId($member_id, ($page - 1) * $limit, $limit);

        foreach ($results as $result) {
            if ($result['author_member_name']) {
                $name = $result['author_member_name'];
            } else if ($result['author_customer_name']) {
                $name = $result['author_customer_name'];
            } else {
                $name = $this->language->get('text_anon');
            }

            $image = $this->model_tool_image->resize($result['author_member_image'], 40, 40, 'autocrop');

            $this->data['reviews'][] = array(
                'name'       => $name,
                'href'       => $result['author_member_id'] ? $this->url->link('product/member/info', 'member_id=' . $result['author_member_id'], 'SSL') : '',
                'image'      => $image,
                'text'       => $result['text'],
                'rating'     => (int)$result['rating'],
                'reviews'    => sprintf($this->language->get('text_reviews'), (int)$review_total),
                'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added']))
            );
        }

        $this->data['text_on'] = $this->language->get('text_on');
        $this->data['text_no_reviews'] = $this->language->get('text_no_reviews');

        $this->data['pagination'] = $this->getPagination($review_total, $page, $limit, 'product/member/review', 'member_id=' . $member_id);

        $this->template = '/template/product/review.tpl';

        $this->response->setOutput($this->render());
    }

}
