<?php

namespace CraftyDigit\Puff\Enums;

enum DataSourceType: string
{
    case JSON = 'json';
    case DOCTRINE = 'doctrine';
}