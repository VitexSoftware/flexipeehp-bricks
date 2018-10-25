<?php
/**
 * FlexiPeeHP Bricks - Orders/Invoices Listing
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Description of OrdersListing
 *
 * @author vitex
 */
class OrdersListing extends \Ease\TWB\Panel
{

    /**
     * Orders Listing
     *
     * @param \FlexiPeeHP\FlexiBeeRO $fetcher     Proforma/Invoice/Order
     * @param array                  $conditions  Conditions
     * @param string                 $caption     Panel caption
     */
    public function __construct($fetcher, $conditions = [], $caption)
    {
        $documents = $fetcher->getColumnsFromFlexiBee('full', $conditions);
        $celkem    = 0;
        $evidence  = $fetcher->getEvidence();
        parent::__construct($caption.' - '.\FlexiPeeHP\EvidenceList::$evidences[$evidence]['evidenceName'],
            'info');
        if (!empty($documents)) {
            foreach ($documents as $orderData) {
                $this->addOrderListingItem($orderData, $evidence);
                $celkem += $orderData['sumCelkem'];
            }
            $this->addToFooter = new \Ease\TWB\Row(null,
                ['style' => 'background-color: lightgray;']);
            $this->addToFooter->addColumn('2', count($documents));
            $this->addToFooter->addColumn('2', $celkem.' Kč');
        }
    }

    /**
     * Add Row with order listing item
     * 
     * @param array  $orderData
     * @param string $evidence
     * 
     * @return \Ease\TWB\Row
     */
    public function addOrderListingItem($orderData, $evidence)
    {
        return $this->addItem(new OrderListingItem($orderData, $evidence));
    }
}
