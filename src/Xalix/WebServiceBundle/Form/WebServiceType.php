<?php

namespace Xalix\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Xalix\WebServiceBundle\Form\WsFunctionType;

class WebServiceType extends AbstractType {
    
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
                ->add('name', null, array('label' => $this->translator->trans('form.name')))
                ->add('isActive', null, array('required' => false, 'label' => $this->translator->trans('form.isActive')))
                ->add('isToken', null, array('required' => false, 'label' => $this->translator->trans('form.isToken')))
                ->add('isUser', null, array('required' => false, 'label' => $this->translator->trans('form.isUser')))
                ->add('uri', null, array('required' => false))
                ->add('contrate', 'collection', array(
                    'type' => new ContrateType(),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ))
                ->add('description', null, array('required' => false, 'label' => $this->translator->trans('table.description')))
                ->add('protocol', null, array('required' => true, 'label' => $this->translator->trans('table.protocol')))
                ->add('wsfunction', 'collection', array(
                    'type' => new WsFunctionType($this->translator),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
        ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'Xalix\WebServiceBundle\Entity\WebService',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName() {

        return 'webservice';
    }

}
