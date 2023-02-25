<?php

namespace CraftyDigit\Puff\ErrorReporter;

enum ErrorCode: int 
{
    case Error404 = 404;
    case Error500 = 500;
}
