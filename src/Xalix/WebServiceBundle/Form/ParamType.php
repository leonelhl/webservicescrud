<?php

namespace Xalix\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ParamType extends AbstractType {

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
                ->add('name', null, array('label' => $this->translator->trans('form.name'), 'attr' => array('class' => 'param'),))
                ->add('type', null, array('label' => $this->translator->trans('form.type')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Xalix\WebServiceBundle\Entity\Param'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'param';
    }

}
