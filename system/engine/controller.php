<?php
abstract class Controller {
	use Breadcrumb, Param, Sort, Limit, Location, Paginate;

	protected $registry;
	protected $id;
	protected $layout;
	protected $template;
	protected $children = array();
	protected $data = array();
	protected $output;

	public function __construct($registry) {
		$this->registry = $registry;
	}

	public function __get($key) {
		return $this->registry->get($key);
	}

	public function __set($key, $value) {
		$this->registry->set($key, $value);
	}

	protected function forward($route, $args = array()) {
		return new Action($route, $args);
	}

	protected function redirect($url, $status = 302) {
		header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&', '', ''), $url), true, $status);
		exit();
	}

	protected function getChild($child, $args = array()) {
		$action = new Action($child, $args);

		if (file_exists($action->getFile())) {
			require_once($action->getFile());

			$class = $action->getClass();

			$controller = new $class($this->registry);

			$controller->{$action->getMethod()}($action->getArgs());

			return $controller->output;
		} else {
			trigger_error('Error: Could not load controller ' . $child . '!');
			exit();
		}
	}

	protected function setOutput($output) {
		$this->output = $output;
	}

	protected function render() {
		foreach ($this->children as $child) {
			$this->data[basename($child)] = $this->getChild($child);
		}

		if (file_exists(DIR_TEMPLATE . $this->template)) {
			extract($this->data);

			ob_start();

			require(DIR_TEMPLATE . $this->template);

			$this->output = ob_get_contents();

			ob_end_clean();

			return $this->output;
		} else {
			trigger_error('Error: Could not load template ' . DIR_TEMPLATE . $this->template . '!');
			exit();
		}
	}

	// fallback/legacy
	protected function generateQueryString($params_encode = array(), $params = array()) {
		$url = '';

		foreach ($params_encode as $param) {
			if (isset($this->request->get[$param])) {
				$url .= '&' . $param . '=' . urlencode(html_entity_decode($this->request->get[$param], ENT_QUOTES, 'UTF-8'));
			}
		}

		foreach ($params as $param) {
			if (isset($this->request->get[$param]) && !is_array($this->request->get[$param])) {
				$url .= '&' . $param . '=' . $this->request->get[$param];
			}
		}

		return $url;
	}
}
?>
