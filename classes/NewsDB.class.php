<?php
require_once "INewsDB.class.php";
require_once "DB.php";

use \mysql_xdevapi\Exception;
use \classes\DB;

class NewsDB extends DB implements INewsDB{
    const RSS_NAME = "rss/rss.xml";
    const RSS_TITLE = "Новостная лента";
    const RSS_LINC = "http://news-list/index.php";

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
        parent::__construct();

        if (filesize(self::$DB_NAME) == 0)
        {
            try {
                $sql = "CREATE TABLE msgs(
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title TEXT,
                        category INTEGER,
                        description TEXT,
                        source TEXT,
                        datetime INTEGER
                    )";
                if (!$this->_db->exec($sql))
                {
                    throw new Exception($this->_db->lastErrorMsg());
                }

                $sql = "CREATE TABLE category(
                        id INTEGER,
                        name TEXT
                    )";
                if (!$this->_db->exec($sql))
                {
                    throw new Exception($this->_db->lastErrorMsg());
                }

                $sql = "INSERT INTO category(id, name)
                        SELECT 1 as id, 'Политика' as name
                        UNION SELECT 2 as id, 'Культура' as name
                        UNION SELECT 3 as id, 'Спорт' as name ";
                if (!$this->_db->exec($sql))
                {
                    throw new Exception($this->_db->lastErrorMsg());
                }

            }catch(Exception $e){
                echo "Ошибка";
            }

        }
    }

    function __destruct()
    {
        unset($this->_db) ;
    }

    function saveNews($title, $category, $description, $source)
    {
        $dt = time();
        $sql = "INSERT INTO msgs(title, category, description, source, datetime)
                    VALUES ('$title', '$category', '$description', '$source', '$dt')";
        $res = $this->_db->exec($sql);
        if (!$res)
        {
            return false;
        }
        $this->createRSS();
        return true;
    }

    function db2Arr($data)
    {
        $arr = [];
        while ($row = $data->fetchArray(SQLITE3_ASSOC))
        {
            $arr[] = $row;
        };
        return $arr;
    }

    function getNews()
    {
        $sql = "SELECT msgs.id as id, title, category.name as category, description, source, datetime 
                    FROM msgs, category
                    WHERE category.id = msgs.category 
                    ORDER BY msgs.id DESC";
        $res = $this->_db->query($sql);
        if (!$res)
        {
            return false;
        }
        return $this->db2Arr($res);
    }


    function deleteNews($id)
    {

        $sql = "DELETE FROM msgs WHERE id ='$id'";
        return $this->_db->exec($sql);
    }

    function clearStr($data)
    {
        $data = strip_tags($data);
        return $this->_db->escapeString($data);
    }

    function clearInt($data)
    {
        return abs((int)$data);
    }

    function createRSS()
    {
        $dom = new DOMDocument("1.0", "utf-8");

        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        $rss = $dom->createElement("rss");
        $dom->appendChild($rss);

        $version = $dom->createAttribute("version");
        $version->value = '2.0';
        $rss->appendChild($version);

        $channel = $dom->createElement("channel");

        $title = $dom->createElement("title", self::RSS_TITLE);
        $linc = $dom->createElement("linc", self::RSS_LINC);

        $channel->appendChild($title);
        $channel->appendChild($linc);

        $rss->appendChild($channel);

        $lenta = $this->getNews();
        if (!$lenta)
        {
            return false;
        }

        foreach ($lenta as $news)
        {
            $item = $dom->createElement("item");
            $title = $dom->createElement("title", $news["title"]);
            $category = $dom->createElement("category", $news["category"]);

            $desc = $dom->createElement("description");
            $cdata = $dom->createCDATASection($news["description"]);
            $desc->appendChild($cdata);

            $linc = $dom->createElement("linc", "#");

            $dt = date("r", $news["datetime"]);
            $pubDate = $dom->createElement("pubDate", $dt);

            $item->appendChild($title);
            $item->appendChild($linc);
            $item->appendChild($desc);
            $item->appendChild($pubDate);
            $item->appendChild($category);

            $channel->appendChild($item);
        }

        $dom->save(self::RSS_NAME);
    }
}
?>
