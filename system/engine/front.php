<?php
final class Front {
	protected $registry;
	protected $pre_action = array();
	protected $security = null;
	protected $error;

	public function __construct($registry) {
		$this->registry = $registry;

		if ($registry->get('config')->get('security_enable_404_detection')) {
			$this->security = new SecurityCheck404($registry);
		}
	}

	public function addPreAction($pre_action) {
		$this->pre_action[] = $pre_action;
	}

	public function dispatch($action, $error) {
		$this->error = $error;

		foreach ($this->pre_action as $pre_action) {
			// $this->securityCheck($pre_action);
			$result = $this->execute($pre_action);

			if ($result) {
				$action = $result;
				break;
			}
		}

		while ($action) {
			$this->securityCheck($action);
			$action = $this->execute($action);
		}
	}

	private function execute($action) {
		if (file_exists($action->getFile())) {
			require_once($action->getFile());

			$class = $action->getClass();

			$controller = new $class($this->registry);

			if (is_callable(array($controller, $action->getMethod()))) {
				$action = call_user_func_array(array($controller, $action->getMethod()), $action->getArgs());
			} else {
				$action = $this->error;
				$this->error = '';
			}
		} else {
			$action = $this->error;
			$this->error = '';
		}

		return $action;
	}

	private function securityCheck($action) {
		if ($this->security) {
			$this->security->log_event($action->getClass() == 'Controllererrornotfound');
		}
	}
}
?>
