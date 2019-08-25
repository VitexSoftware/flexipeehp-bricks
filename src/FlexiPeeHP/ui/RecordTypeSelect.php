<?php
/**
 * FlexiPeeHP Bricks
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */

namespace FlexiPeeHP\ui;

/**
 * Description of TypSklPohSelect
 *
 * @author vitex
 */
class RecordTypeSelect extends \Ease\Html\SelectTag
{

    /**
     * Common FlexiBee evidence record Type Select
     *
     * @param \FlexiPeeHP\FlexiBeeRO $engine
     * @param string $valueType Column with return value eg. kod
     * @param array $conditons Additonal conditions
     */
    public function __construct($engine, $valueType = 'id', $conditions = [])
    {
        if (!isset($conditions['order'])) {
            $conditions['order'] = 'nazev';
        }
        $typesRaw = $engine->getColumnsFromFlexibee(['nazev', $valueType],
            $conditions);

        $types = ['' => _('Undefined')];
        if (!empty($typesRaw)) {
            foreach ($typesRaw as $type) {
                $types[($valueType == 'kod' ? 'code:' : '').$type[$valueType]] = $type['nazev'];
            }
        }
        parent::__construct($engine->getEvidence(), $types,
            ($valueType == 'kod' ? \FlexiPeeHP\FlexiBeeRO::code($engine->getDataValue($valueType))
                    : $engine->getDataValue($valueType))
        );
    }
}
