<?php
namespace Mageplaza\GiftCard\Model;
class GiftCardHistory extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'mageplaza_giftcard_history';

    protected $_cacheTag = 'mageplaza_giftcard_history';

    protected $_eventPrefix = 'mageplaza_giftcard_history';

    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
