<?php

namespace SymfonyContrib\Bundle\CronBundle\Form;

use SymfonyContrib\Bundle\CronBundle\Entity\Cron;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Cron admin add/edit form.
 */
class CronForm extends AbstractType
{
    /** @var array */
    public $crons;

    /** @var array */
    public $dataPoints;

    public function __construct(array $crons = [], array $dataPoints = [])
    {
        $this->crons   = $crons;
        $this->dataPoints = $dataPoints;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group', 'text')
            ->add('name', 'text')
            ->add('desc', 'text', [
                'required' => false,
            ])
            ->add('job', 'text')
            ->add('runInterval', 'text')
            ->add('weight', 'integer')
            ->add('enabled', 'checkbox', [
                'required' => false,
            ])
            ->add('save', 'submit', [
                'attr' => [
                    'class' => 'btn-success',
                ],
            ])
            ->add('cancel', 'button', [
                'url' => $options['cancel_url'],
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'SymfonyContrib\Bundle\CronBundle\Entity\Cron',
            'cancel_url' => '/',
        ]);
    }

    public function getName()
    {
        return 'cron_form';
    }
}
