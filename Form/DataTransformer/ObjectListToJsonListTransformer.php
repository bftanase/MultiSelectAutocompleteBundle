<?php
namespace Btanase\MultiSelectAutocompleteBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * User: bogdan
 */
class ObjectListToJsonListTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $om;

    private $labelProperty;

    /**
     * @var string
     */
    private $entityName;

    function __construct($om, $entityName, $labelProperty)
    {
        $this->labelProperty = $labelProperty;
        $this->entityName = $entityName;
        $this->om = $om;
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
        $labelMethod = 'get'.ucwords($this->labelProperty);

        if (!$value) {
            return '';
        } else {
            $data = array();

            foreach ($value as $item) {
                $data[] = array(
                    'value' => $item->getId(),
                    'label' => $item->{$labelMethod}()
                );

            }

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
        if (!$value) {
            return null;
        } else {

            $ids = array();

            foreach($value as $item) {
                $ids[] = $item->getId();
            }
            $qb = $this->om->createQueryBuilder();

            $qb->select('o')
                ->from($this->entityName, 'o')
                ->where($qb->expr()->in('o.id', $ids));

            return $qb->getQuery()->getResult();
        }
    }



}
