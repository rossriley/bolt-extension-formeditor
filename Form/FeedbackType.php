<?php

namespace Bolt\Extensions\Ross\FormEditor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * A Form Collection that sets up the options for the feedback settings.
 */
class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('success',   'textarea', [])
            ->add('error',   'textarea', []);
    }

    public function getName()
    {
        return 'feedback';
    }
}
