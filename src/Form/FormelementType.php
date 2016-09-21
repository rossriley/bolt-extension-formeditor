<?php

namespace Bolt\Extensions\Ross\FormEditor\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Bolt\Translation\Translator as Trans;


/**
 * A Form Collection that sets up the options for the repeating field types.
 */
class FormelementType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',   'text', [
                'label' => Trans::__('Internal name for field'),
                'read_only' => true,
                'attr' => ['help' => Trans::__('Only letters, numbers and underscores allowed')],
            ])
            ->add('label',   'text', [
                'label' => Trans::__('Label for this form field'),
                'attr' => ['help' => Trans::__('This is the user-visible label')],
            ])
            ->add('type',   'choice', [
                'label' => Trans::__('Type of form element'),
                'choices' => [
                    'text' => Trans::__('Text'),
                    'textarea' => Trans::__('Text Area'),
                    'choice' => Trans::__('Select Dropdown'),
                    'submit' => Trans::__('Submit Button'),
                ],
            ])
            ->add('required', 'checkbox', [
                'label' => Trans::__('Required Field'),
                'required' => false,
            ]);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();

                $form->add('choices', 'choice', [
                    'label' => Trans::__('Options to show'),
                    'required' => false,
                    'multiple' => true,
                    'attr' => ['help' => Trans::__('Setup the available choices')],
                    'choices' => array_combine((array)$data['choices'], (array)$data['choices']),
                ]);
            }
        );
    }

    public function getName()
    {
        return 'fields';
    }
}
