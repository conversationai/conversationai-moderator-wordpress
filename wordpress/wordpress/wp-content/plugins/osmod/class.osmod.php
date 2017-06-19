<?php

require_once( OSMOD_PLUGIN_DIR . 'class.transport.php' );
require_once( OSMOD_PLUGIN_DIR . 'class.comments.php' );

class Osmod {

    private static $transport = null;
	public static function init() {
		self::$transport = new Transport();
	}

    /*
    *   Use publisher API for comment creation/update and new articles.
    */
	public static function newComment($payload) {
       self::$transport->deliver('publisher/comments',$payload,"POST");
	}

    public static function newArticle($payload) {
       self::$transport->deliver('publisher/articles',$payload,"POST");
	}

    public static function updateArticle($payload,$sourceId) {
       self::$transport->deliver('publisher/articles/'.$sourceId,$payload,"PATCH");
	}

    /*
    *   Use REST API for categories and comment status.
    */
    public static function newCategory($payload) {
       self::$transport->deliver('rest/categories',$payload,"POST");
	}


    public static function getDecisions() {
       return (new Transport())->deliver('publisher/decisions');
	}

    public static function resolveDecisions($ids) {
       return (new Transport())->deliver('publisher/decisions/confirm', array(
           "data" => $ids
       ), "POST");
	}


}
