<?php

namespace RevenueMonster\SDK\Request;

use JsonSerializable;
use Rakit\Validation\Validator;
use RevenueMonster\SDK\Exceptions\ValidationException;

class URLCheckout implements JsonSerializable
{
    private $type = 'URL';
    public $method = '';
    public $checkoutId = '';
    public $bankCode = '';

    public function __construct(array $arguments = [])
    {
        $this->checkoutId = $arguments['checkoutId'] ?? '';
        $this->method = $arguments['method'] ?? '';
        $this->bankCode = $arguments['bankcode'] ?? null;
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

        if ($this->method === 'FPX_MY') {
            $data['fpx'] = [
                'bankCode' => $this->bankCode,
            ];
        }

        $validator = new Validator;
        $validation = $validator->make($data, [
            'checkoutId' => 'required',
            'method' => 'required|in:ALIPAYPLUS_MY,TNG_MY,PRESTO_MY,BOOST_MY,GRABPAY_MY,SHOPEEPAY_MY,FPX_MY,MASTERCARD_MY,PAYDEE_MY,GOBIZ_MY',
            'type' => 'required',
            'fpx.bankCode' => 'required_if:method,FPX_MY',
        ], [
            'fpx.bankCode' => 'The bankCode is required when method is FPX_MY.',
        ]);


        $validation->validate();

        if ($validation->fails()) {
            throw new ValidationException($validation->errors());
        }

        return $data;
    }
}
