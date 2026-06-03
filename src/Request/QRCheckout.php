<?php

namespace RevenueMonster\SDK\Request;

use JsonSerializable;
use Rakit\Validation\Validator;
use RevenueMonster\SDK\Exceptions\ValidationException;

class QRCheckout implements JsonSerializable
{
    private $type = 'QRCODE';
    public $method = '';
    public $checkoutId = '';

    public function __construct(array $arguments = [])
    {
        $this->checkoutId = $arguments['checkoutId'] ?? '';
        $this->method = $arguments['method'] ?? '';
    }

    // Add the #[\ReturnTypeWillChange] attribute to suppress warnings supporting PHP versions before PHP 8.1
    // or the proper return type in PHP 8.1+ would be eg. jsonSerialize(): mixed
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = [
            'checkoutId' => $this->checkoutId,
            'type' => $this->type,
            'method' => $this->method
        ];

        $validator = new Validator;
        $validation = $validator->make($data, [
            'checkoutId' => 'required',
            'method' => 'required|in:WECHATPAY_CN,BOOST_MY,MAYBANK_MY,SHOPEEPAY_MY',
            'type' => 'required',
        ]);


        $validation->validate();

        if ($validation->fails()) {
            throw new ValidationException($validation->errors());
        }

        return $data;
    }
}
