<?php

namespace App\User\Service;

class Test
{

    /** @var string */
    private $message;

    /**
     * Test constructor
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function test(): string
    {
        return $this->message;
    }

}
