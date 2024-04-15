<?php

namespace CraftyDigit\Puff\Common\Enums;

enum ResponseType
{
    case JSON;
    case HTML;
    case TEXT;
    case REDIRECT;
}
