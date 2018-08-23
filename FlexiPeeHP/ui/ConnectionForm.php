<?php
/**
 * FlexiPeeHP Bricks - Connection Config Form
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Form to configure used FlexiBee instance
 *
 * @author vitex
 */
class ConnectionForm extends \Ease\TWB\Form
{
    /**
     * FlexiBee URL Input name
     * @var string eg. https://demo.flexibee.eu:5434
     */
    public $urlField = 'url';

    /**
     * FlexiBee User Input name
     * @var string eg. winstrom
     */
    public $usernameField = 'login';

    /**
     * FlexiBee Password Input name
     * @var string eg. winstrom
     */
    public $passwordField = 'password';

    /**
     * FlexiBee Company Input name
     * @var string eg. demo_s_r_o_
     */
    public $companyField = 'company';

    /**
     * FlexiBee Server connection form
     * 
     * @param string $formAction    where to put response
     * @param string $formMethod    GET | POST
     * @param array  $tagProperties additional form tag propeties
     */
    public function __construct($formAction = null, $formMethod = 'post',
                                $tagProperties = null)
    {
        parent::__construct('SignIn', $formAction, $formMethod, null,
            $tagProperties);

        $this->addInput(new \Ease\Html\InputTextTag($this->urlField),
            _('RestAPI endpoint url'));

        $this->addInput(new \Ease\Html\InputTextTag($this->usernameField),
            _('REST API Username'));

        $this->addInput(new \Ease\Html\InputTextTag($this->passwordField),
            _('Rest API Password'));

        $this->addInput(new \Ease\Html\InputTextTag($this->companyField),
            _('Company Code'));
    }

    /**
     * Finally add subnut button
     */
    public function finalize()
    {
        $this->addItem(new \Ease\TWB\SubmitButton(_('Use Connection'), 'success',
            ['width' => '100%']));
        parent::finalize();
    }
}
