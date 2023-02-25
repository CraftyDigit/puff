<?php

namespace CraftyDigit\Puff\Tests\Model;

use CraftyDigit\Puff\Model\Model;
use PHPUnit\Framework\TestCase;

final class ModelTest extends TestCase
{
    /**
     * @var Model
     */
    public Model $model;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $data = ['id' => 0, 'foo' => 'bar'];
        $this->model = new Model($data);
    }

    /**
     * @return void
     */
    public function testGetDataReturnNotEmptyArray(): void
    {
        $this->assertNotEmpty($this->model->getData(), 'Model data is empty array.');
    }

    /**
     * @return void
     */
    public function testMagicGet(): void
    {
        $this->assertNotNull($this->model->id, 'Model data is not accessible thru __get method.');
    }

    /**
     * @return void
     */
    public function testMagicSet(): void
    {
        $this->model->foo = 'baz';
        $this->assertEquals('baz', $this->model->foo, 'Model data can\'t be changed using __set method.');
    }
}
