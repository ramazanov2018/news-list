<?php
require_once "INewsDB.class.php";
use \mysql_xdevapi\Exception;

class NewsDB implements INewsDB{
    const DB_NAME = "/db/news.db";
    private $_db = null;

    function __get($name)
    {
        if ($name == "db")
        {
            return $this->_db;
        }
        throw new Exception("Unknown property");
    }

    function __construct()
    {
        $this->_db = new SQLite3(self::DB_NAME);
        if (filesize(self::DB_NAME) == 0)
        {
            $sql = "CREATE TABLE msgs(
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title TEXT,
                        category INTEGER,
                        description TEXT,
                        source TEXT,
                        datetime INTEGER
                    )";
            $this->_db->exec($sql) or die($this->_db->lastErrorMsg());

            $sql = "CREATE TABLE category(
                        id INTEGER,
                        name TEXT
                    )";
            $this->_db->exec($sql) or die($this->_db->lastErrorMsg());
            $sql = "INSERT INTO category(id, name)
                        SELECT 1 as id, 'Политика' as name
                        UNION SELECT 2 as id, 'Культура' as name
                        UNION SELECT 3 as id, 'Спорт' as name ";
            $this->_db->exec($sql) or die($this->_db->lastErrorMsg());
        }
    }
    function __destruct()
    {
        unset($this->_db) ;
    }

    function saveNews($title, $category, $description, $source)
    {

    }


    function getNews()
    {

    }


    function deleteNews($id)
    {

    }
}
?>
