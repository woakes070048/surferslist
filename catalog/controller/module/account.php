<?php
class ControllerModuleAccount extends Controller {
	protected function index() {
		$language = $this->load->language('module/account');
        $this->data = array_merge($this->data, $language);

		$this->data['logged'] = $this->customer->isLogged();
		$this->data['enabled'] = $this->customer->isProfileEnabled();
		$this->data['activated'] = $this->customer->hasProfile();
		$this->data['profile'] = $this->customer->getProfileUrl();

		$this->data['register'] = $this->url->link('account/register', '', 'SSL');
		$this->data['login'] = $this->url->link('account/login', '', 'SSL');
		$this->data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['edit'] = $this->url->link('account/edit', '', 'SSL');
		$this->data['member'] = $this->url->link('account/member', '', 'SSL');
		$this->data['review'] = $this->url->link('account/review', '', 'SSL');
		$this->data['question'] = $this->url->link('account/question', '', 'SSL');
		$this->data['password'] = $this->url->link('account/password', '', 'SSL');
		$this->data['address'] = $this->url->link('account/address', '', 'SSL');
		$this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$this->data['order'] = $this->url->link('account/order', '', 'SSL');
		$this->data['sales'] = $this->url->link('account/sales', '', 'SSL');
		$this->data['product'] = $this->url->link('account/product', '', 'SSL');

		$this->template = 'template/module/account.tpl';

		$this->render();
	}
}

