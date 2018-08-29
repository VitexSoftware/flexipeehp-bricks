<?php
/**
 * FlexiPeeHP Bricks - AddressForm
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Description of AddressForm
 *
 * @author vitex
 */
class AdresarForm extends \Ease\TWB\Form
{
    /**
     * Address Object holder.
     *
     * @var \FlexiPeeHP\Adresar
     */
    public $address = null;

    /**
     * Address Book item form
     * 
     * @param \FlexiPeeHP\Adresar $address
     */
    public function __construct($address)
    {
        $addressID     = $address->getMyKey();
        $this->address = $address;
        parent::__construct('address'.$addressID);

        $this->addInput(new \Ease\Html\InputTag('kod',
            $address->getDataValue('kod')), _('Code'));
        $this->addInput(new \Ease\Html\InputTag('nazev',
            $address->getDataValue('nazev')), _('Name'));

        if (strlen($address->getDataValue('email')) == 0) {
            $address->addStatusMessage(_('Email address is empty'), 'warning');
        }

        $this->addInput(new \Ease\Html\InputTag('email',
            $address->getDataValue('email')), _('Email'));

        $this->addInput(new \Ease\Html\TextareaTag('poznam',
            $address->getDataValue('poznam')), _('Note'));

        $this->addItem(new \Ease\Html\InputHiddenTag('class',
            get_class($address)));
//        $this->addItem(new \Ease\Html\InputHiddenTag('enquiry_id', $address->getDataValue('enquiry_id')));

        $this->addItem(new \Ease\Html\DivTag(new \Ease\TWB\SubmitButton(_('Save'),
            'success'), ['style' => 'text-align: right']));

        if (!is_null($addressID)) {
            $this->addItem(new \Ease\Html\InputHiddenTag($address->keyColumn,
                $addressID));
        }
    }
}
