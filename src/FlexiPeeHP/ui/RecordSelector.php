<?php
/**
 * Common selectize.js based FlexiBee records chooser
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright (c) 2019, Vitex Software
 */

namespace FlexiPeeHP\ui;

/**
 * Select One Value 
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class RecordSelector extends \Ease\Html\SelectTag
{
    use \Ease\ui\Selectizer;

    /**
     * Selectize.js based input
     * 
     * @param string                 $name
     * @param array                  $values
     * @param \FlexiPeeHP\FlexiBeeRO $optionsEngine
     * @param array                  $properties
     */
    public function __construct($name, $values, $optionsEngine,
                                $properties = array())
    {
        if (empty($optionsEngine->getColumnInfo('nazev'))) {
            $nameColumn = 'kod';
        } else {
            $nameColumn = 'nazev';
        }

        if (empty($optionsEngine->getColumnInfo('kod'))) {
            $keyColumn = 'id';
        } else {
            $keyColumn = 'kod';
        }

        parent::__construct($name, $values, $properties);
        $values = $optionsEngine->getColumnsFromFlexibee([$keyColumn, $nameColumn]);
        if ($keyColumn == 'kod') {
            foreach ($values as $id => $valueRow) {
                $values[$id][$nameColumn] = $values[$id][$keyColumn].': '.$values[$id][$nameColumn];
            }
        }
        
        $this->selectize( ['valueField' => $keyColumn, 'labelField' => $nameColumn, 'searchField' => ['kod', 'nazev'], 'create' => false  ],$values );
    }
}
