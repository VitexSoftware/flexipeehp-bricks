<?php
/**
 * FlexiPeeHP Bricks
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Description of SearchBox
 *
 * @author vitex
 */
class SearchBox extends \Ease\Html\InputSearchTag
{

    public function afterAdd($parent)
    {
        $parent->addItem(new \Ease\Html\DatalistTag(new \Ease\Html\OptionTag('zatim nic')
            ,
            
            ['id' => 'datalist-'.$this->getTagID()]));
        $this->addJavaScript('
var dataList = $(\'#datalist-'.$this->getTagID().'\');
var input = $(\'#'.$this->getTagID().'\');

input.change(function() {
    $.getJSON( "pricelistsearcher.php?column=nazev&q=" + input.val() , function( data ) {
//      dataList.empty();
      $.each( data, function( key, val ) {
        alert(val[\'nazev\']);
        var option = document.createElement(\'option\');
        option.value = val[\'nazev\'];
        dataList.appendChild(option);
      });

    });

});


');
    }

    public function finalize()
    {
        $this->setTagProperties(['list' => 'datalist-'.$this->getTagID()]);
        parent::finalize();
    }
}
