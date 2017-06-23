<?php

namespace Xalix\WebServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Xalix\WebServiceBundle\Form\ParamType;

class WsFunctionType extends AbstractType {

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
                ->add('name', null, array(
                    'label' => $this->translator->trans('form.name'),))
                ->add('description', null, array('required' => false, 'label' => $this->translator->trans('table.description')))
                ->add('return', null, array(
                    'empty_value' => false,
                    'empty_data' => null,
                    'label' => $this->translator->trans('form.return'),
                ))
                ->add('method', null, array(
                    'attr' => array('class' => 'multiselect method', 'title' => 'Método'),
                    'required' => true,
                    'label' => $this->translator->trans('form.method')
                ))
                ->add('add', 'button', array(
                    'attr' => array('class' => 'addparam btn btn-success btn-small', 'title' => $this->translator->trans('form.addParam')),
                    'label' => 'Add'
                ))
                ->add('param', 'collection', array(
                    'type' => new ParamType($this->translator),
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
            'data_class' => 'Xalix\WebServiceBundle\Entity\WsFunction',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'wsfunction';
    }

}
