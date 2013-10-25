<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 4/9/13
 * Time: 12:17 PM
 * To change this template use File | Settings | File Templates.
 */

namespace Btanase\MultiSelectAutocompleteBundle\Form\Type;

use Btanase\MultiSelectAutocompleteBundle\Form\DataTransformer\ObjectListToJsonListTransformer;
use Btanase\MultiSelectAutocompleteBundle\Form\DataTransformer\ObjectToJsonTransformer;
use Btanase\MultiSelectAutocompleteBundle\Persistence\Manager\ItemManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;


class BTAutocompleteType extends AbstractType {


    function __construct()
    {

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "bt_autocomplete";
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /* @var ItemManagerInterface $itemManager */
        $itemManager = $options['item_manager'];
        $propertyLabel = $options['property_label'];

        if ($options['multiple']) {
            $builder->addModelTransformer(new ObjectListToJsonListTransformer($itemManager, $propertyLabel));
        } else {
            $builder->addModelTransformer(new ObjectToJsonTransformer($itemManager, $propertyLabel));
        }

        parent::buildForm($builder, $options);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'item_manager' => null,
            'property_label' => null,
            'ajax_autocomplete_route_name' => null,
            'multiple' => true,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['ajax_autocomplete_route_name'] = $options['ajax_autocomplete_route_name'];
        $view->vars['multiple'] = $options['multiple'];

    }


}