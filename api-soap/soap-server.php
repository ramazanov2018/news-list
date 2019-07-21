<?php
require_once "NewsService.php";


// Отключение кеширования wsdl-документа
ini_set("soap.wsdl_cache_enabled", "0");
// Создание SOAP-сервера
$server = new SoapServer("http://news-list/api-soap/news.wsdl");
// Регистрация класса
$server->setClass("NewsService");
// Запуск сервера
$server->handle();