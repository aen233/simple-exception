<?php

namespace App\Http\Controllers;

use App\Exceptions\BaseException;
use App\Exceptions\CodeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    public function index(Request $request)
    {
        Validator::make($request->all(), [
            'abc' => 'required',
//            'file' => 'bail|required|file'
        ], [
//            'file.required' => '请上传文件'
        ])->validate();

//        throw new CodeException(1001);
        throw new BaseException('abc', 1256);
    }
}
