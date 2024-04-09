<?php

namespace CraftyDigit\Puff\Common\Enums;

enum DataSourceType: string
{
    case JSON = 'json';
    case DOCTRINE = 'doctrine';
}