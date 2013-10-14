<?php

namespace Oktolab\Bundle\RentBundle\Tests\Form\Type;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Validator\Type\FormTypeValidatorExtension;

use Oktolab\Bundle\RentBundle\Form\EventObjectType;
use Oktolab\Bundle\RentBundle\Form\EventType;
use Oktolab\Bundle\RentBundle\Entity\EventObject;
use Oktolab\Bundle\RentBundle\Entity\Event;

/**
 * EventType Tests
 *
 * @see http://symfony.com/doc/current/cookbook/form/unit_testing.html
 */
class EventTypeTest extends TypeTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtension(
                new FormTypeValidatorExtension($this->getMock('Symfony\Component\Validator\ValidatorInterface'))
            )
            ->addTypeGuesser(
                $this->getMockBuilder('Symfony\Component\Form\Extension\Validator\ValidatorTypeGuesser')
                    ->disableOriginalConstructor()
                    ->getMock()
            )
            ->getFormFactory();

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->builder = new FormBuilder(null, null, $this->dispatcher, $this->factory);
    }

    /**
     * @test
     */
    public function submitValidData()
    {
        $this->markTestIncomplete(
            '"validation_groups" can not be tested by Symfony 2.3. @see https://github.com/symfony/symfony/issues/2387'
        );

        $formData = array(
            'name'        => 'my event',
            'description' => 'a test for event type',
            'begin'       => '2013-10-14 12:00:00',
            'end'         => '2013-10-17 18:00:00',
            'objects'     => array('object' => '3', 'type' => 'item'),
        );

        $eventObject = new EventObject();
        $eventObject->setObject('3')
                ->setType('item');

        $event = new Event();
        $event->setName($formData['name'])
                ->setDescription($formData['description'])
                ->setBegin(new \DateTime($formData['begin']))
                ->setEnd(new \DateTime($formData['end']))
                ->addObject($eventObject);

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $type = new EventType();
        $form = $this->factory->create($type, null, array('em' => $em));

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($eventObject, $form->getData());

        $view = $form->createView();
        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $view->children);
        }
    }
}
