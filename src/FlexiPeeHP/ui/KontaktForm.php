<?php
/**
 * FlexiPeeHP Bricks - AddressForm
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

class KontaktForm extends \Ease\TWB\Form
{
    /**
     * Contact object.
     *
     * @var \FlexiPeeHP\Kontakt
     */
    public $contact = null;

    /**
     * 
     * @param \FlexiPeeHP\Kontakt $contact
     */
    public function __construct($contact)
    {
        $contactID     = $contact->getMyKey();
        $this->contact = $contact;
        parent::__construct('contact'.$contactID);

        $this->addInput(new \Ease\Html\InputTag('jmeno',
            $contact->getDataValue('jmeno')), _('Name'));
        $this->addInput(new \Ease\Html\InputTag('prijmeni',
            $contact->getDataValue('prijmeni')), _('Surname'));
        $this->addInput(new \Ease\Html\InputTag('email',
            $contact->getDataValue('email')), _('Email'));
        $this->addInput(new \Ease\Html\InputTag('tel',
            $contact->getDataValue('tel')), _('Phone'));
        $this->addInput(new \Ease\Html\InputTag('mobil',
            $contact->getDataValue('mobil')), _('Cell'));
        $this->addInput(new \Ease\Html\InputTextTag('username',
            $contact->getDataValue('username')), _('Login'));
        $this->addInput(new \Ease\Html\InputPasswordTag('password',
            $contact->getDataValue('password')), _('Password'));

        $this->addItem(new \Ease\Html\InputHiddenTag('firma',
            $contact->getDataValue('firma')));

        $this->addItem(new \Ease\Html\InputHiddenTag('class',
            get_class($contact)));

        if (!is_null($contactID)) {
            $this->addItem(new \Ease\Html\InputHiddenTag($contact->keyColumn,
                $contactID));
        }
    }
}
