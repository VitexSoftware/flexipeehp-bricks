<?php
/**
 * FlexiPeeHP Bricks - WebHook handler
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */
namespace FlexiPeeHP\Bricks;

/**
 * Description of WebHookHandler
 *
 * @author vitex
 */
class WebHookHandler extends \FlexiPeeHP\FlexiBeeRW
{

    use \Ease\SQL\Orm;
    /**
     * Current processed Change id
     * @var int
     */
    public $changeid = null;

    /**
     * WebHook API operation
     * @var string create|update|delete
     */
    public $operation = null;

    /**
     * External IDs for current record
     * @var array 
     */
    public $extids = [];

    /**
     * Database Helper
     * @var \Ease\Brick
     */
    public $dbHelper = null;

    /**
     * Handle Incoming change
     *
     * @param int $id changed record id
     * @param array $options 
     */
    public function __construct($id, $options)
    {
        parent::__construct($id, $options);
        $this->takemyTable('flexihistory');
        $this->myCreateColumn = 'when';
    }

    /**
     * SetUp Object to be ready for connect
     *
     * @param array $options Object Options (company,url,user,password,evidence,
     *                                       prefix,defaultUrlParams,debug)
     */
    public function setUp($options = [])
    {
        parent::setUp($options);
        if (isset($options['changeid'])) {
            $this->changeid = $options['changeid'];
        }
        if (isset($options['operation'])) {
            $this->setOperation($options['operation']);
            if ($options['operation'] == 'delete') {
                $this->ignore404(true);
            }
        }
        if (isset($options['external-ids'])) {
            $this->extids = $options['external-ids'];
        }
    }

    /**
     *
     * @return type
     */
    public function saveHistory()
    {
        $change = [
            'operation' => $this->operation,
            'evidence' => $this->getEvidence(),
            'recordid' => $this->getMyKey(),
            'json' => $this->dblink->addslashes(json_encode($this->getData()))];

        if ($this->changeid) {
            $change['changeid'] = $this->changeid;
        }

        return $this->insertToSQL($change);
    }

    /**
     *
     * @param type $operation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;
    }

    /**
     *
     */
    public function process($operation)
    {
        $result = false;
        switch ($operation) {
            case 'create':
                $result = $this->create();
                break;
            case 'update':
                $result = $this->update();
                break;
            case 'delete':
                $result = $this->delete();
                break;
            default:
                $this->addToLog(\FlexiPeeHP\FlexiBeeRO::uncode($this->getRecordIdent()).': '.sprintf('Unknown operation %s',
                        $operation), 'error');
                break;
        }
        return $result;
    }

    /**
     *
     */
    public function create()
    {
        if ($this->debug === true) {
            $this->addStatusMessage(\FlexiPeeHP\FlexiBeeRO::uncode($this->getRecordIdent()).': '._('No Create Action Defined'),
                'debug');
        }
        return null;
    }

    /**
     *
     */
    public function update()
    {
        if ($this->debug === true) {
            $this->addStatusMessage(\FlexiPeeHP\FlexiBeeRO::uncode($this->getRecordIdent()).': '._('No Update Action Defined').' '.json_encode($this->getChanges()),
                'debug');
        }
        return null;
    }

    /**
     *
     */
    public function delete()
    {
        if ($this->debug === true) {
            $this->addStatusMessage(\FlexiPeeHP\FlexiBeeRO::uncode($this->getRecordIdent()).': '._('No Delete Action Defined'),
                'debug');
        }
        return null;
    }

    /**
     * 
     * @return array|null
     */
    public function getCurrentData()
    {

        $dataRaw = $this->getColumnsFromFlexibee('*',
            ['id' => $this->getMyKey()]);
        return count($dataRaw) ? $dataRaw[0] : null;
    }

    /**
     * 
     * @return array
     */
    public function getPreviousData()
    {
        $prevData   = null;
        $lastChange = $this->dblink->queryToArray('SELECT json FROM '.$this->getMyTable().' WHERE evidence='.$this->getEvidence().' AND recordid='.$this->getMyKey().'  ORDER BY id DESC LIMIT 1,1');
        if (!empty($lastChange) && count($lastChange)) {
            $lastChange = json_decode($lastChange['json']);
        }
        return $lastChange;
    }

    /**
     * 
     * @return array
     */
    public function getChanges()
    {
        $previous = $this->getPreviousData();
        if (empty($previous)) {
            $previous = $this->getData();
        } else {
            $previous = array_diff($this->getData(), $previous);
        }
        return $previous;
    }

    /**
     * 
     */
    public function getChangedData()
    {
        
    }
}
