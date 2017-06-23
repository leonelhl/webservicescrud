<?php

namespace Xalix\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Xalix\WebServiceBundle\Form\AtributeType;

class TypeType extends AbstractType{
    
    private $translator;

    public function __construct($translator) {
        $this->translator = $translator;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('type', null, array('label' => $this->translator->trans('form.type')))
                ->add('orderIndicator', 'choice', array('label' => $this->translator->trans('table.orderType'), 'choices' => array('all' => 'all', 'sequence' => 'sequence', 'choice' => 'choice')))
                ->add('atribute', 'collection', array(
                    'type' => new AtributeType($this->translator),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Xalix\WebServiceBundle\Entity\Type',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'type';
    }

}
