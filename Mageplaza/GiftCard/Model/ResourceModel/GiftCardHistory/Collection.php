<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'history_id';
    protected $_eventPrefix = 'mageplaza_giftcard_history_collection';
    protected $_eventObject = 'giftcard_history_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\GiftCardHistory', 'Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory');
    }

}

