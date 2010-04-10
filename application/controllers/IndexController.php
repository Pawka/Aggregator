<?php

class IndexController extends Site_Controller {

    public function init() {

    }

    public function indexAction() {
        $fetcher = new Site_Feeds_Fetcher();
        $fetcher->fetch();

    }

}

