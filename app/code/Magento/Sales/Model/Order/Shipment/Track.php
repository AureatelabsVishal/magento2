<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Order\Shipment;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Sales\Api\Data\ShipmentTrackInterface;
use Magento\Sales\Model\AbstractModel;

/**
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Track _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Shipment\Track getResource()
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Track extends AbstractModel implements ShipmentTrackInterface
{
    /**
     * Code of custom carrier
     */
    const CUSTOM_CARRIER_CODE = 'custom';

    /**
     * @var \Magento\Sales\Model\Order\Shipment|null
     */
    protected $_shipment = null;

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_order_shipment_track';

    /**
     * @var string
     */
    protected $_eventObject = 'track';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
        $this->_shipmentFactory = $shipmentFactory;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Shipment\Track');
    }

    /**
     * Tracking number getter
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->getData('track_number');
    }

    /**
     * Tracking number setter
     *
     * @param string $number
     * @return \Magento\Framework\Object
     */
    public function setNumber($number)
    {
        return $this->setData('track_number', $number);
    }

    /**
     * Declare Shipment instance
     *
     * @param \Magento\Sales\Model\Order\Shipment $shipment
     * @return $this
     */
    public function setShipment(\Magento\Sales\Model\Order\Shipment $shipment)
    {
        $this->_shipment = $shipment;
        return $this;
    }

    /**
     * Retrieve Shipment instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        if (!$this->_shipment instanceof \Magento\Sales\Model\Order\Shipment) {
            $this->_shipment = $this->_shipmentFactory->create()->load($this->getParentId());
        }

        return $this->_shipment;
    }

    /**
     * Check whether custom carrier was used for this track
     *
     * @return bool
     */
    public function isCustom()
    {
        return $this->getCarrierCode() == self::CUSTOM_CARRIER_CODE;
    }

    /**
     * Retrieve hash code of current order
     *
     * @return string
     */
    public function getProtectCode()
    {
        return (string)$this->getShipment()->getProtectCode();
    }

    /**
     * Get store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->getShipment()) {
            return $this->getShipment()->getStore();
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getStore()->getId();
    }

    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $data
     * @return $this
     */
    public function addData(array $data)
    {
        if (array_key_exists('number', $data)) {
            $this->setNumber($data['number']);
            unset($data['number']);
        }
        return parent::addData($data);
    }

    /**
     * Returns track_number
     *
     * @return string
     */
    public function getTrackNumber()
    {
        return $this->getData(ShipmentTrackInterface::TRACK_NUMBER);
    }

    /**
     * Returns carrier_code
     *
     * @return string
     */
    public function getCarrierCode()
    {
        return $this->getData(ShipmentTrackInterface::CARRIER_CODE);
    }

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(ShipmentTrackInterface::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(ShipmentTrackInterface::CREATED_AT, $createdAt);
    }

    /**
     * Returns description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getData(ShipmentTrackInterface::DESCRIPTION);
    }

    /**
     * Returns order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(ShipmentTrackInterface::ORDER_ID);
    }

    /**
     * Returns parent_id
     *
     * @return int
     */
    public function getParentId()
    {
        return $this->getData(ShipmentTrackInterface::PARENT_ID);
    }

    /**
     * Returns qty
     *
     * @return float
     */
    public function getQty()
    {
        return $this->getData(ShipmentTrackInterface::QTY);
    }

    /**
     * Returns title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(ShipmentTrackInterface::TITLE);
    }

    /**
     * Returns updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(ShipmentTrackInterface::UPDATED_AT);
    }

    /**
     * Returns weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->getData(ShipmentTrackInterface::WEIGHT);
    }

    //@codeCoverageIgnoreStart
    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($timestamp)
    {
        return $this->setData(ShipmentTrackInterface::UPDATED_AT, $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($id)
    {
        return $this->setData(ShipmentTrackInterface::PARENT_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        return $this->setData(ShipmentTrackInterface::WEIGHT, $weight);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($qty)
    {
        return $this->setData(ShipmentTrackInterface::QTY, $qty);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($id)
    {
        return $this->setData(ShipmentTrackInterface::ORDER_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackNumber($trackNumber)
    {
        return $this->setData(ShipmentTrackInterface::TRACK_NUMBER, $trackNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(ShipmentTrackInterface::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        return $this->setData(ShipmentTrackInterface::TITLE, $title);
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrierCode($code)
    {
        return $this->setData(ShipmentTrackInterface::CARRIER_CODE, $code);
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magento\Sales\Api\Data\ShipmentTrackExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     *
     * @param \Magento\Sales\Api\Data\ShipmentTrackExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Sales\Api\Data\ShipmentTrackExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
    //@codeCoverageIgnoreEnd
}
