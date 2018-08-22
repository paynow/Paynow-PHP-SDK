<?php
namespace Paynow\Payments;


/**
 * @property int|null count
 * @property float|null total
 * @property int|null ref
 * @property int|null description
 * @property string authEmail
 */
class Payment
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
     * The description of the transaction to use if the generated on has been overriden
     *
     * @var string
     */
    protected $_ov_description = null;

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
    protected $_override_description = false;

    /**
     * The email address of the authenticated user 
     *
     * @var string
     */
    protected $_auth_email = '';

    /**
     * Boolean value indicating whether transaction is mobile or not
     *
     * @var boolean
     */
    public $_is_mobile = false;

    /**
     * Phone number to use for transaction (for mobile transactions)
     *
     * @var string
     */
    protected $_phone = null;


    /**
     * Mobile money method to use for transaction
     *
     * @var string
     */
    protected $_method = null;
	
	/**
     * Default constructor
     *
     * @param mixed $item
     * @param mixed $ref
     * @param float|int $amount
     */
    private function __construct($mobile, $ref, $authEmail = '', $phone, $method)
    {   
        $this->_is_mobile = $mobile;
        $this->_ref = $ref;
        $this->_auth_email = $authEmail;
        $this->_phone = $phone;
        $this->_method = $method;
    }

    /**
     * Create an instance of the payment class (for mobile payments)
     *
     * @param string $ref
     * @param string $authEmail
     * @param string $phone The mobile phone making the payment
     * @param string $method The mobile money method     
     * 
     * @return void
     */
    public static function createMobile($ref, $authEmail, $phone, $method) 
    {
        if (!isset($method)) {
            throw new InvalidArgumentException("The mobile money method should be specified");
        }

        return new static(true, $ref, $authEmail, $phone, $method);
    }

    /**
     * Create an instance of the payment class (for normal payments)
     *
     * @param string $ref
     * @param string $authEmail
     * 
     * @return void
     */
    public static function create($ref, $authEmail = '') 
    {
        return new static(false, $ref, $authEmail);
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

            case 'auth_email':
                return $this->_auth_email;

            case 'method':
                return $this->_method;

            case 'phone':
                return $this->_phone;

            case 'is_mobile':
                return $this->_is_mobile;

                
            case 'authEmail':
                return $this->_auth_email;



            default:
                return null;
        }
    }

    /**
     * Calculate the total of the items in the 'cart'
     *
     * @return void
     */
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

    /**
     * Sets the description for the transaction
     *
     * @param string $description
     * @return void
     */
    public function setDescription($description)
    {
        $this->_ov_description = $description;
        $this->_override_description = true;
    }
		
    /**
     * Get the description for the items in the cart
     *
     * @return void
     */
    public function itemsDescription()
    {
        if($this->_override_description) {
            return $this->_ov_description;
        }

        if(!$this->_recache) {
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