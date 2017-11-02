<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\Bricks;

/**
 * Description of TypSklPohSelect
 *
 * @author vitex
 */
class RecordTypeSelect extends \Ease\Html\Select
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

        $types = [];
        foreach ($typesRaw as $type) {
            $types[$type[$valueType]] = $type['nazev'];
        }

        parent::__construct($engine->getEvidence(), $types);
    }
}