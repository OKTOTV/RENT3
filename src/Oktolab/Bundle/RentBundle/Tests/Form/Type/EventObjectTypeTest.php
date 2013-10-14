<?php

namespace Oktolab\Bundle\RentBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Oktolab\Bundle\RentBundle\Form\EventObjectType;
use Oktolab\Bundle\RentBundle\Entity\EventObject;

/**
 * EventObjectType Tests
 *
 * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
 */
class EventObjectTypeTest extends TypeTestCase
{

    /**
     * @test
     */
    public function submitValidData()
    {
        $formData = array('object' => '3', 'type' => 'item');
        $eventObject = new EventObject();
        $eventObject->setObject('3')
                ->setType('item');

        $type = new EventObjectType();
        $form = $this->factory->create($type);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($eventObject, $form->getData());

        $view = $form->createView();
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $view->children);
        }
    }
}