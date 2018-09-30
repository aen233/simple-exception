<?php
/*
 * This file is part of the car/chedianai_bc.
 *
 * (c) chedianai <i@chedianai.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Exceptions;

use Exception;

class CodeException extends Exception
{
    const HTTP_OK = 200;

    protected $data;

    protected $code;

    public function __construct($code, $data = [])
    {
        $this->code = $code;
        $this->data = $data;
    }

    public function render()
    {
        $content = [
                'message' => getErrorMessage($this->code),
                'code'    => $this->code,
            ];

        if ($this->data) {
            $content = array_add($content, 'data', $this->data);
        }

        return response()->json($content, self::HTTP_OK);
    }
}
