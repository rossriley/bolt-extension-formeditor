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
            ->add('to_email',   'text', [])
            ->add('from_email',  'text', [
                'attr'=> [
                        'help'=> 'This email appears in the From address when sending a notification (as well as an email you can also use a field name)'
                    ]
                ]
            );
    }

    public function getName()
    {
        return 'notification';
    }
}
