<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\Bricks;

/**
 * Description of ConvertorRule
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ConvertorRule extends \Ease\Sand
{
    /**
     *
     * @var array 
     */
    public $rules      = [];
    private $convertor = null;
    private $keepId    = null;
    private $addExtId  = null;
    private $keepCode  = null;

    /**
     * Conversion Rule
     * 
     * @param Convertor $convertor Convertor Engine
     * @param boolean $keepId
     * @param boolean $addExtId
     * @param boolean $keepCode
     * @param boolean $handleAccounting set columns "ucetni" like target or ignore it
     */
    public function __construct(Convertor &$convertor, $keepId = false,
                                $addExtId = false, $keepCode = false,
                                $handleAccounting = true)
    {
        parent::__construct();

        $this->keepId   = $keepId;
        $this->addExtId = $addExtId;
        $this->keepCode = $keepCode;

        $this->convertor = &$convertor;

        if ($keepId === false) {
            unset($this->rules['id']);
        }
        if ($addExtId) {
            $this->rules['id'] = 'addExtId()';
        }
        if ($keepCode === false) {
            unset($this->rules['kod']);
        }
        if ($handleAccounting) {

//            unset($this->rules['ucetni']);
//            unset($this->rules['clenDph']);
        }
    }

    public function addExtId()
    {
        $this->convertor->getOutput()->setDataValue('id',
            'ext:src:'.$this->convertor->getInput()->getEvidence().':'.$this->convertor->getInput()->getMyKey());
    }

    function getRules()
    {
        return $this->rules;
    }

    function getRuleForColumn($columnName)
    {
        return $this->rules[$columnName];
    }

    public static function convertorClassTemplateGenerator($convertor,
                                                           $className)
    {
        $inputColumns  = $convertor->getInput()->getColumnsInfo();
        $outputColumns = $convertor->getOutput()->getColumnsInfo();

        $oposites = self::getOposites($inputColumns, $outputColumns);

        $inputRelations = $convertor->getInput()->getRelationsInfo();
        if (!empty($inputRelations)) {
            if (array_key_exists('polozkyDokladu',
                    self::reindexArrayBy($inputRelations, 'url'))) {
                $outSubitemsInfo            = $convertor->getOutput()->getColumnsInfo($convertor->getOutput()->getEvidence().'-polozka');
                $inSubitemsInfo             = $convertor->getInput()->getColumnsInfo($convertor->getInput()->getEvidence().'-polozka');
                $oposites['polozkyDokladu'] = self::getOposites($inSubitemsInfo,
                        $outSubitemsInfo);
            }
        }

        $classFile = '<?php
namespace FlexiPeehp\Bricks\ConvertRules;
/**
 * Description of '.$className.'
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class '.$className.' extends \FlexiPeeHP\Bricks\ConvertorRule
{
    public $rules = ';

        $classFile .= var_export($oposites, true);

        $classFile .= ';

}
';
        if (file_put_contents($className.'.php', $classFile)) {
            $convertor->addStatusMessage($classFile, 'success');
        } else {
            throw new \Ease\Exception(sprintf(_('Cannot save ClassFile: %s'),
                    $className.'.php'));
        }
    }

    public static function getOposites($inProps, $outProps)
    {
        foreach ($outProps as $colName => $colProps) {
            if ($colProps['isWritable'] == 'true') {
                if (array_key_exists($colName, $inProps)) {
                    $outProps[$colName] = $colName;
                } else {
                    $outProps[$colName] = null;
                }
            } else {
                unset($outProps[$colName]);
            }
        }

        return $outProps;
    }
}
