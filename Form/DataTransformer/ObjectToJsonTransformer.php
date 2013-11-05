<?php
namespace Btanase\MultiSelectAutocompleteBundle\Form\DataTransformer;

use Btanase\MultiSelectAutocompleteBundle\Persistence\Manager\ItemManagerInterface;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\DataTransformerInterface;

class ObjectToJsonTransformer implements DataTransformerInterface
{

    private $labelProperty;

    private $manager;

    function __construct(ItemManagerInterface $manager, $labelProperty)
    {
        $this->labelProperty = $labelProperty;
        $this->manager = $manager;
    }

    /**
     * Transforms a value from the original representation to a transformed representation.
     *
     * This method is called on two occasions inside a form field:
     *
     * 1. When the form field is initialized with the data attached from the datasource (object or array).
     * 2. When data from a request is bound using {@link Form::bind()} to transform the new input data
     *    back into the renderable format. For example if you have a date field and bind '2009-10-10' onto
     *    it you might accept this value because its easily parsed, but the transformer still writes back
     *    "2009/10/10" onto the form field (for further displaying or other purposes).
     *
     * This method must be able to deal with empty values. Usually this will
     * be NULL, but depending on your implementation other empty values are
     * possible as well (such as empty strings). The reasoning behind this is
     * that value transformers must be chainable. If the transform() method
     * of the first value transformer outputs NULL, the second value transformer
     * must be able to process that value.
     *
     * By convention, transform() should return an empty string if NULL is
     * passed.
     *
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws UnexpectedTypeException   when the argument is not a string
     * @throws TransformationFailedException  when the transformation fails
     */
    public function transform($value)
    {
        $propertyParser = new PropertyParser($this->labelProperty);

        $data = array();
        if (!$value) {
            return '';
        } else {

            $data[] = array(
                'value' => $value->getId(),
                'label' => $propertyParser->getLabelValue($value),
            );

            return json_encode($data);
        }
    }

    /**
     * Transforms a value from the transformed representation to its original
     * representation.
     *
     * This method is called when {@link Form::bind()} is called to transform the requests tainted data
     * into an acceptable format for your data processing/model layer.
     *
     * This method must be able to deal with empty values. Usually this will
     * be an empty string, but depending on your implementation other empty
     * values are possible as well (such as empty strings). The reasoning behind
     * this is that value transformers must be chainable. If the
     * reverseTransform() method of the first value transformer outputs an
     * empty string, the second value transformer must be able to process that
     * value.
     *
     * By convention, reverseTransform() should return NULL if an empty string
     * is passed.
     *
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws UnexpectedTypeException   when the argument is not of the expected type
     * @throws TransformationFailedException  when the transformation fails
     */
    public function reverseTransform($value)
    {

        $value = json_decode($value);

        if (!$value) {
            return null;
        } else {

            $obj = $value[0];

            $id = $obj->value;

            return $this->manager->getEntityById($id);
        }
    }



}
