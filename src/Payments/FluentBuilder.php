<?php
namespace Paynow\Payments;


/**
 * @property int|null count
 * @property float|null total
 * @property int|null ref
 * @property int|null description
 */
class FluentBuilder
{
    /**
     * Array containing the items in the cart
     *
     * @var array
     */
    protected $_items = [];

    /**
     * Boolean value indicating whether the total should be recalculated
     *
     * @var boolean
     */
    protected $_recalc = true;

    /**
     * Boolean value indicating whether the description of the cart should be regenerated
     *
     * @var boolean
     */
    protected $_recache = true;

    /**
     * The reference of the transaction
     *
     * @var mixed
     */
    protected $_ref;

    /**
     * The total of the items in the list
     *
     * @var float
     */
    protected $_total;

    /**
     * The description of the items in the list
     *
     * @var string
     */
    protected $_description = '';
    
    /**
     * Boolean value indicating whether description should be generated 
     * from the list of provided items
     *
     * @var boolean
     */
    protected $_override_description = true;

    /**
     * Default constructor
     *
     * @param mixed $item
     * @param mixed $ref
     * @param float|int $amount
     */
    public function __construct($item = null, $ref = null, $amount = null)
    {
        $this->add($item, $amount);

        if(!is_null($ref) && !empty($ref)) {
            $this->_ref = $ref;
        }
    }

    /**
     * Add a new item to the list
     *
     * @param string|array $item
     * @param float|int $amount
     * @return void
     */
    public function add($item, $amount = null)
    {
        if (is_array($item) && count($item) > 1) {
            $this->parseItems($item);

            return $this;
        }

        if (!empty($item) && !empty($item)) {
            $this->pushItem($item, $amount);
        }

        return $this;
    }

    /**
     * Parse an array of items
     * 
     * @param array $items
     */
    protected function parseItems(array $items)
    {
        foreach ($items as $item) {
            if (!is_array($item) || count($item) <> 2) {
                return;
            }

            if (!arr_has($item, 'title') || !arr_has($item, 'amount')) {
                return;
            }

            $this->pushItem($item);
        }
    }

    /**
     * Push an item to the list
     * 
     * @param string $item The name of the item
     * @param float|int $amount The cost of the item
     */
    private function pushItem($item, $amount = null)
    {
        $this->_recalc = true;
        $this->_recache = true;

        if (is_array($item)) {
            $this->_items[] = $item;

            return;
        }

        $this->_items[] = [
            'title' => $item,
            'amount' => floatval($amount)
        ];
    }

    public function __get($name)
    {
        switch ($name)
        {
            case 'total':
                return ($this->_recalc) ? $this->computeTotal() : $this->_total;

            case 'count':
                return count($this->_items);
            case 'description':
                return ($this->_recache) ? $this->itemsDescription() : $this->_description;
            case 'ref':
                return $this->_ref;

            default:
                return null;
        }
    }

    public function computeTotal()
    {
        $total = 0;

        foreach ($this->_items as $item) {
            $total += $item['amount'];
        }

        $this->_total = $total;
        $this->_recalc = false;

        return $total;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        $this->_override_description = false;
    }

    /**
     * Get the description 
     *
     * @return void
     */
    public function itemsDescription()
    {
        if(!$this->_recache) {
            return $this->_description;
        }

        if($this->_override_description) {
            return $this->_description;
        }

        $this->_description = '';
        for($i = 0; $i < count($this->_items); $i++) {
            $this->_description .= "{$this->_items[$i]['title']}, ";
        }

        return $this->_description;
    }

    

    /**
     * Convert the builder to an array
     *
     * @return void
     */
    public function toArray()
    {
        return [
            'resulturl' => '',
            'returnurl' => '',
            'reference' => $this->_ref,
            'amount' => $this->total,
            'id' => '',
            'additionalinfo' => $this->itemsDescription(),
            'authemail' => '',
            'status' => 'Message'
        ];
    }
}