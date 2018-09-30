# simple-exception
> Laravel 5.5 之后支持在异常类中定义 render() 方法，该异常被触发时系统会调用 render() 方法来输出，我们在 render() 里判断如果是 AJAX 请求则返回 JSON 格式的数据，否则就返回一个错误页面。
##### 6.1 BaseException
BaseException是一个很灵活的异常类，可以自定义多种参数。
1. 在app/Exceptions下新建BaseException。
2. 在helpers中增加函数：
该函数是获取Symfony定义的所有Http状态码。比如200=HTTP_OK。
```
function getHttpStatus()
{
    $objClass = new \ReflectionClass(\Symfony\Component\HttpFoundation\Response::class);
    // 此处获取类中定义的全部常量 返回的是 [key=>value,...] 的数组,key是常量名,value是常量值
    return array_values($objClass->getConstants());
}
```
3.基础使用
> baseException($data, int $code=200, array $meta=[]);   
> 第1个参数可以为string 或 array.  
> 第2个参数默认为200，如果传的code是任意一个httpStatus，表示返回的http状态码（如404、500等），
如果是自定义错误码（非任意一个httpStatus，如1001、1002），则http状态码返回200，code码在json内容中返回  
> 第3个参数默认为空数组。如果传第3个参数，将一起返回。

3.1 参数传string
````
throw new BaseException('都是好孩子');

Status: 200 OK 
{
    "message": "都是好孩子"
}
````
3.2 参数传string,code(自定义错误码，非httpStatus)
````
throw new BaseException('都是好孩子',1001);

Status: 200 OK 
{
    "message": "都是好孩子",
    "code": 1001
}
````
3.3 参数传string,code（httpStatus）
````
throw new BaseException('都是好孩子', 404);

Status: 404 Not Found
{
    "message": "都是好孩子"
}
````
3.4 参数传array
````
throw new BaseException(['msg' => '都是好孩子', 'code' => '123']);

Status: 200 OK
{
    "msg": "都是好孩子",
    "code": "123"
}
````
3.5 参数传array，code（httpStatus）
````
throw new BaseException(['msg' => '都是好孩子', 'code' => '123'], 403);

Status: 403 Forbidden
{
    "msg": "都是好孩子",
    "code": "123"
}
````
3.6 参数传string，code（httpStatus），array
````
throw new BaseException('都是好孩子', 422, ['msg' => '天是蓝的', 'code' => '24678']);

Status: 422 Unprocessable Entity
{
    "message": "都是好孩子",
    "meta": {
        "msg": "天是蓝的",
        "code": "24678"
    }
}
````
3.7 参数传string，code（自定义错误码，非httpStatus），array
````
throw new BaseException('都是好孩子', 4567, ['msg' => '天是蓝的', 'code' => '24678']);

Status: 200 OK
{
    "message": "都是好孩子",
    "code": 4567,
    "meta": {
        "msg": "天是蓝的",
        "code": “24678"  
    }
}
````
3.8 参数传array，code（自定义错误码，非httpStatus），array
````
throw new BaseException(['msg' => '都是好孩子', 'code' => '123'], 1234, ['msg' => '天是蓝的', 'code' => '24678']);

Status: 200 OK
{
    "msg": "都是好孩子",
    "code": "123",
    "meta": {
        "msg": "天是蓝的",
        "code": "24678"
    }
}
````
3.9 参数传array，code（自定义错误码，非httpStatus），array
````
throw new BaseException(['msg' => '都是好孩子', 'code' => '123'], 500, ['msg' => '天是蓝的', 'code' => '24678']);

Status: 500 Internal Server Error
{
    "msg": "都是好孩子",
    "code": "123",
    "meta": {
        "msg": "天是蓝的",
        "code": "24678"
    }
}
````
##### 6.1.2 参数校验异常
使用laravel内置的ValidationException  
1.在app/Exceptions/Handler中添加
```php
 public function render($request, Exception $exception)
    {
        //-------新加这4行---------
        if ($exception instanceof ValidationException) {
            $message = current(current(array_values($exception->errors())));
            throw new BaseException($message, 4022); // 不加4022，会返回httpStatus=422;加4022是因为返回前端统一httpStatus为200，就在422中加了0
        }
        //------------------------

        return parent::render($request, $exception);
    }
```
2.基础使用
````
    //控制器中，不需要额外抛出异常
    public function index(Request $request)
    {
        Validator::make($request->all(), [
            'file' => 'bail|required|file'
        ], [
            'file.required' => '请上传文件'
        ])->validate();
    }
    
    //handler中不加4022
    Status: 422 Unprocessable Entity
    {
        "message": "请上传文件"
    }
    
    //handler中不加4022
    Status: 200 OK
    {
        "message": "请上传文件",
        "code": 4022,
    }
````

##### 6.1.3 处理其他异常
同参数校验异常，如处理FatalThrowableError(定义错误码为5678)，然后故意写个语法错误。
同样不需要自己抛错，也不会出现报错大黑框
````
Status: 200 OK
{
    "message": "Parse error: syntax error, unexpected 'dd' (T_STRING)",
    "code": 5678
}
````

##### 6.2 CodeException
为了项目统一规范，需统一管理code错误码，所以建立CodeException。

1. 在app/Exceptions下新建error.php。返回错误信息数组。
```
<?php

return [
    1001 =>'门前大桥下',
    1002 =>'游过一群鸭'
];
```

2 . 在helpers中增加函数：
该函数是获取该errorCode相对应的errorMessage。
```
function getErrorMessage($code)
{
    $err = require_once __DIR__.'/../app/Exceptions/error.php';
    return $err[$code];
}
```
3 . 在app/Exceptions下新建CodeException

4 . 基础使用
```
throw new CodeException(1001);

// 返回
{
    "message": "门前大桥下",
    "code": 1001
}
```
有附加错误信息数组
```
throw new CodeException(1001,['info'=>'门前大桥下','text'=>'游过一群鸭']);

//返回
{
    "message": "门前大桥下",
    "code": 1001,
    "data": {
        "info": "门前大桥下",
        "text": "游过一群鸭"
    }
}
```