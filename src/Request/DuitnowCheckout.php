<?php

namespace RevenueMonster\SDK\Request;

use JsonSerializable;
use Rakit\Validation\Validator;
use RevenueMonster\SDK\Exceptions\ValidationException;

class DuitnowCheckout implements JsonSerializable
{
    private $type = 'DUITNOW_QRCODE';
    private $method = '';
    public $checkoutId = '';

    public function __construct(array $arguments = [])
    {
        $this->checkoutId = $arguments['checkoutId'] ?? '';
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
            'method' => 'nullable',
            'type' => 'required',
        ]);


        $validation->validate();

        if ($validation->fails()) {
            throw new ValidationException($validation->errors());
        }

        return $data;
    }
}
