<?php

namespace Bolt\Extensions\Ross\FormEditor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add('choices', 'text', [
                'label' => 'Options to show',
                'required' => false,
                'attr' => ['help' => 'Separate choices with a comma.'],
            ])
            ->add('required', 'checkbox', [
                'label' => 'Required Field',
                'required' => false,
            ]);
    }

    public function getName()
    {
        return 'fields';
    }
}
