<?php
/**
 * Created by @exife
 * Website: exife.com
 * Email: support@exife.com
 *
 */

class SecurityBase {

    protected $config;
    protected $db;
    protected $request;
    protected $user;
    protected $language;
    protected $session;
    protected $cache;

    public function __construct($registry) {
        $this->config = $registry->get('config');
        $this->db = $registry->get('db');
        $this->request = $registry->get('request');
        $this->user = $registry->get('user');
        $this->language = $registry->get('language');
        $this->session = $registry->get('session');
        $this->cache = $registry->get('cache');
    }

}
