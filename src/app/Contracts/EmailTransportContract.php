<?php

namespace App\Contracts;


interface EmailTransportContract
{
    public function getTransport();
}