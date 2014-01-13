<?php

namespace Oktolab\Bundle\RentBundle\Util\Configuration;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of DateTimeParamConverter.
 *
 * @author rs
 */
class DateTimeParamConverter implements ParamConverterInterface
{
    /**
     * {@inheritDoc}
     */
    public function apply(Request $request, ConfigurationInterface $configuration)
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

        $date = ($value === 'default') ? new \DateTime($options['default']) : new \DateTime($value);
        if (!$date) {
            throw new NotFoundHttpException('Invalid date given.');
        }

        $request->attributes->set($param, $date);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ConfigurationInterface $configuration)
    {
        return true;
    }
}
