<?php

namespace Oktolab\Bundle\RentBundle\Tests\Form\Type;

use Oktolab\Bundle\RentBundle\Form\Inventory\ItemType;
use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 *  @see: http://symfony.com/doc/current/cookbook/form/unit_testing.html
 */
class ItemTypeTest extends TypeTestCase
{

    public function testSubmitValidData()
    {
        /**
         * I researched for one hour, nothing found.
         *
         * I found the class Symfony\Bridge\Doctrine\Tests\Form\Type\EntityTypeTest
         *   but I did not had time to work through. It seems to look for the right thing.
         *
         */
        $this->markTestIncomplete('How to mock doctrine entity type?');

        $formData = array(
            'title'         => 'My Item Title',
            'description'   => 'The description of my Item',
            'barcode'       => 'A5DF01',
        );

        $item = new Item();
        $item->setTitle($formData['title']);
        $item->setDescription($formData['description']);
        $item->setBarcode($formData['barcode']);

        $type = new ItemType();

        $resolver = new OptionsResolver();
        $type->setDefaultOptions($resolver);
        $options = $resolver->resolve();

        var_dump($options);
        die();

        //$form = $this->factory->create($type, $item);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized(), 'No data transformer exception expected');
        $this->assertEquals($item, $form->getData(), 'Form Data should contain same information as Item');

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
