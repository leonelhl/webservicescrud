<?php

namespace Xalix\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SecurityType extends AbstractType {

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
                ->add('isToken', null, array('required' => false, 'label' => $this->translator->trans('form.isToken')))
                ->add('isUser', null, array('required' => false, 'label' => $this->translator->trans('form.isUser')))
                ->add('token', null, array('label' => $this->translator->trans('form.tokens')))
                ->add('user', null, array('label' => $this->translator->trans('form.users')))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Xalix\WebServiceBundle\Entity\WebService'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'webservice_security';
    }

}
