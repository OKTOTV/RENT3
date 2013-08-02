<?php

namespace Oktolab\Bundle\RentBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\ORM\EntityManager;

use Oktolab\Bundle\RentBundle\Entity\Inventory\Item;

/**
 * Description of ItemToNumberTransformer
 *
 * @author meh
 */
class ItemToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var EnhtityManager
     */
    private $em = null;

    /**
     * Constructor.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $om
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Transforms an object (item) to a string (id).
     *
     * @param  Item|null $item
     * @return string
     */
    public function transform($item)
    {
        if (!$item) {
            return '';
        }

        var_dump($item); die();

        return sprintf('%d', $item->getId());
    }

    /**
     * Transforms a string (id) to an object (Item).
     *
     * @param  string $id
     *
     * @return Item|null
     *
     * @throws TransformationFailedException if object (Item) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $item = $this->em
            ->getRepository('OktolabRentBundle:Inventory\Item')
            ->findOneBy(array('id' => $id));

        if (null === $item) {
            throw new TransformerFailedException(sprintf(
                'An Item with id "%d" does not exist!',
                $id
            ));
        }

        return $item;
    }
}
