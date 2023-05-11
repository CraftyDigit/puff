<?php

namespace CraftyDigit\Puff\Enums;

enum ResponseType
{
    case JSON;
    case HTML;
    case TEXT;
    case REDIRECT;
}
