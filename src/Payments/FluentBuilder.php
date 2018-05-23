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
    protected $_items = [];

    protected $_recalc = true;

    protected $_recache = true;

    protected $_ref;

    protected $_total;

    protected $_description;

    public function __construct($item = null, $ref = null, $amount = null)
    {
        $this->add($item, $amount);

        if(!is_null($ref) && !empty($ref)) {
            $this->_ref = $ref;
        }
    }

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
     * Push an item to the
     * @param $item
     * @param $amount
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

    public function itemsDescription()
    {
        if(!$this->_recache) {
            return $this->description;
        }

        $this->description = '';
        for($i = 0; $i < count($this->_items); $i++) {
            $this->description .= "{$this->_items[$i]['title']}, ";
        }

        return $this->description;
    }

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