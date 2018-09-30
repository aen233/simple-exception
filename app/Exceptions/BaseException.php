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

class BaseException extends Exception
{
    const HTTP_OK = 200;

    protected $data;

    protected $code;

    public function __construct($data, int $code = self::HTTP_OK, array $meta = [])
    {
        $this->data = $data;
        $this->code = $code;
        $this->meta = $meta;
//        parent::__construct($data, $code);
    }

    public function render()
    {
        $httpStatus = getHttpStatus();

        $status  = in_array($this->code, $httpStatus) ? $this->code : self::HTTP_OK;
        $content = [];
        if (is_array($this->data)) {
            $content = $this->data;
        }
        if (is_string($this->data)) {
            $content = in_array($this->code, $httpStatus)
                ? [
                    'message' => $this->data
                ]
                : [
                    'message' => $this->data,
                    'code'    => $this->code,
                    //                    'timestamp' => time()
                ];
        }

        if ($this->meta) {
            $content = array_add($content, 'meta', $this->meta);
        }

        return response($content, $status);
    }
}
