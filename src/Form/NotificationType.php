<?php

namespace Bolt\Extensions\Ross\FormEditor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * A form collection that provides options for notification settings.
 */
class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject',   'text', [])
            ->add('to_name',   'text', [])
            ->add('to_email',   'text', []);
    }

    public function getName()
    {
        return 'notification';
    }
}
