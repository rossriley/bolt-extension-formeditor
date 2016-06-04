<?php

namespace Bolt\Extensions\Ross\FormEditor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * A Form Collection that sets up the options for the repeating field types.
 */
class FormelementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',   'text', [
                'label' => 'Internal name for field',
                'read_only' => true,
                'attr' => ['help' => 'Only letters, numbers and underscores allowed'],
            ])
            ->add('label',   'text', [
                'label' => 'Label for this form field',
                'attr' => ['help' => 'This is the user-visible label'],
            ])
            ->add('type',   'choice', [
                'label' => 'Type of form element',
                'choices' => [
                    'text' => 'Text',
                    'textarea' => 'Text Area',
                    'choice' => 'Select Dropdown',
                    'submit' => 'Submit Button',
                ],
            ])
            ->add('required', 'checkbox', [
                'label' => 'Required Field',
                'required' => false,
            ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $form->add('choices', 'choice', [
                    'label' => 'Options to show',
                    'required' => false,
                    'multiple' => true,
                    'attr' => ['help' => 'Setup the available choices'],
                    'choices' => array_combine($data['choices'], $data['choices']),
                ]);
            }
        );
    }

    public function getName()
    {
        return 'fields';
    }
}
