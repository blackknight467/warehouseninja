<?php

namespace WarehouseNinja\ShipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderProduct
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="WarehouseNinja\ShipBundle\Repository\OrderProductRepository")
 */
class OrderProduct extends CreatedUpdatedEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ShipOrder
     *
     * @ORM\ManyToOne(targetEntity="WarehouseNinja\ShipBundle\Entity\ShipOrder")
     * @ORM\JoinColumn(name="ship_order_id", referencedColumnName="id")
     */
    private $shipOrder;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="WarehouseNinja\ShipBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var Warehouse
     *
     * @ORM\ManyToOne(targetEntity="WarehouseNinja\ShipBundle\Entity\Warehouse")
     * @ORM\JoinColumn(name="warehouse_id", referencedColumnName="id")
     */
    private $warehouse;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shipOrder
     *
     * @param ShipOrder $shipOrder
     * @return OrderProduct
     */
    public function setShipOrder($shipOrder)
    {
        $this->shipOrder = $shipOrder;

        return $this;
    }

    /**
     * Get shipOrder
     *
     * @return ShipOrder
     */
    public function getShipOrder()
    {
        return $this->shipOrder;
    }

    /**
     * Set product
     *
     * @param Product $product
     * @return OrderProduct
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set warehouse
     *
     * @param Warehouse $warehouse
     * @return OrderProduct
     */
    public function setWarehouse(Warehouse $warehouse)
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * Get warehouse
     *
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }
}
