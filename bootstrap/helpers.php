<?php

function getHttpStatus()
{
    $objClass = new \ReflectionClass(\Symfony\Component\HttpFoundation\Response::class);
    // 此处获取类中定义的全部常量 返回的是 [key=>value,...] 的数组
    // key是常量名 value是常量值
//        dd($objClass->getConstants());
    return array_values($objClass->getConstants());
}


function getErrorMessage($code)
{
    $err = require_once __DIR__.'/../app/Exceptions/error.php';
    return $err[$code];
}
