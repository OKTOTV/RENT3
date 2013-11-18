<?php

namespace Oktolab\Bundle\RentBundle\Util\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;

/**
 * Description of DateTimeParamConverter
 * @author rs
 */
class DateTimeParamConverter implements ParamConverterInterface
{
    public function apply(\Symfony\Component\HttpFoundation\Request $request, \Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration)
    {
        $param = $configuration->getName();

        if (!$request->attributes->has($param)) {
            return false;
        }

        $options = $configuration->getOptions();
        $value   = $request->attributes->get($param);


        if (!$value && $configuration->isOptional()) {
            return false;
        }

        $date = ($value === "default") ? new \DateTime($options['default']) : new \DateTime($value);

        if (!$date) {
            throw new NotFoundHttpException('Invalid date given.');
        }

        $request->attributes->set($param, $date);

        return true;
    }

    public function supports(\Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface $configuration)
    {
        return true;
    }
}
