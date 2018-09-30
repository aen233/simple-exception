<?php

namespace App\Http\Controllers;

use App\Exceptions\BaseException;
use App\Exceptions\CodeException;

/**
 * Class IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    public function index()
    {
        throw new CodeException(1001);
    }
}
