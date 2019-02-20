<?php
trait Paginate {
	protected function getPagination($total, $page, $limit, $route, $query = '', $params = '', $anchor = '') {
		if (!$total || !$page || !$limit || !$route) {
			return false;
		}

		$max = ($limit > 0 && $total) ? ceil($total / $limit) : 1;

		if ($page == 1) {
		    $this->document->addLink($this->url->link($route, $query), 'canonical');
		} else if ($page == 2) {
		    $this->document->addLink($this->url->link($route, $query), 'prev');
		} else if ($page <= $max) {
		    $this->document->addLink($this->url->link($route, $query . '&page='. ($page - 1)), 'prev');
		}

		if ($limit && $max > $page) {
		    $this->document->addLink($this->url->link($route, $query . '&page='. ($page + 1)), 'next');
		}

		$pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link($route, $query . $params . '&page={page}') . $anchor;

		return $pagination->render();
	}
}
?>
