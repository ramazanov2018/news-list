<?php
namespace classes;
use SQLite3;

class DB

{
    protected static $DB_NAME = "";
    protected $_db = null;

    function __construct()
    {
        self::$DB_NAME = $_SERVER['DOCUMENT_ROOT']."/db/news.db";
        $this->_db = new SQLite3(self::$DB_NAME);
    }
}