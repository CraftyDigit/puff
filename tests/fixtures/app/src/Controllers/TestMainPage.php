<?php

namespace App\src\Controllers;

use CraftyDigit\Puff\Common\Attributes\Controller;
use CraftyDigit\Puff\Common\Attributes\Route;
use CraftyDigit\Puff\Controller\AbstractPageController;
use Psr\Http\Message\ResponseInterface;

#[Controller('TestMainPage')]
class TestMainPage extends AbstractPageController
{
    #[Route('/', 'testMainPage')]
    public function main(): ResponseInterface
    {
        $html = $this->templateEngine->render('test_main_page');

        return $this->defaultRespond($html);
    }
}