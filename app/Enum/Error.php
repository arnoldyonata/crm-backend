<?php

namespace App\Enum;

enum Error: string
{
    case IMSI_EXISTS = 'Imsi already exists';
    case NUMBER_EXISTS = 'Number already exists';
    case PRODUCT_NOT_EXISTS = 'Product does not exist';
    case PACK_EXISTS = 'Pack with imsi or number already exists';
}
