<?php
/**
 * FlexiBee-Bricks - přepínač daně položky
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2018 Vitex Software
 */

namespace SpojeNet\System\ui;

/**
 * Description of VATSwitch
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class VATSwitch extends TWBSwitch
{

    /**
     * Document Item VAT Type
     * 
     * @param string $name
     * @param string $checked typCeny.sDph|typCeny.bezDph
     * @param array  $properties
     */
    public function __construct($name = 'typCenyDphK', $checked = null,
                                $properties = null)
    {
        if (empty($checked)) {
            $checked = 'typCeny.sDph';
        }

        if (!isset($properties['onText'])) {
            $properties['onText'] = _('with VAT');
        }
        if (!isset($properties['offText'])) {
            $properties['offText'] = _('without VAT');
        }

        $properties['value'] = $checked;

        parent::__construct($name, $checked == 'typCeny.sDph', $checked,
            $properties);
    }

    public function finalize()
    {
        parent::finalize();
        $this->addJavaScript('
 $(\'input[name="'.$this->getTagName().'"]\').on(\'switchChange.bootstrapSwitch\', function(event, state) {
  console.log(this); // DOM element
  console.log(event); // jQuery event
  console.log(state); // true | false
  if(state){
    $(\'input[name="'.$this->getTagName().'"]\').val("typCeny.sDph");
  } else {
    $(\'input[name="'.$this->getTagName().'"]\').val("typCeny.bezDph");
  }
 
});                       
');
    }
}
