<?php

namespace FlexiPeeHP\Bricks;

use PrettyXml\Formatter;

/**
 * Description of Engine
 *
 * @author vitex
 */
class XSLT extends \FlexiPeeHP\FlexiBeeRW
{

    public $evidence = 'xslt';

    /**
     * Import all files in ./xslt directory
     */
    public function updateXSLTs($sourcedir = 'xslt')
    {
        $d     = dir($sourcedir);
        while (false !== ($entry = $d->read())) {
            if (($entry[0] != '.') && (substr($entry, -5) == '.xslt')) {
                $importdata                 = [];
                $importdata['nazev']        = str_replace('.xslt', '', $entry);
                $importdata['id']           = self::code(strtoupper($importdata['nazev']));
                $importdata['transformace'] = file_get_contents('xslt/'.$entry);
                $this->insertToFlexiBee($importdata);
                $this->addStatusMessage('XSLT: '.$importdata['nazev'],
                    $this->lastResponseCode == 201 ? 'success' : 'error' );
            }
        }
        $d->close();
    }

    /**
     * Import XML Data Using xiven XSLT
     *
     * @param string $xml
     * @param string $xslt
     *
     * @retur booelan operation restult status
     */
    public function importXMLwithXSLT($xml, $xslt, $label = '', $debug = false)
    {
        $this->errors = [];
        $this->setPostFields($xml);
        $this->setEvidence('faktura-vydana');
        $result       = $this->performRequest('?format='.self::code($xslt),
            'POST', 'xml');

        if ($this->lastResponseCode == 201) {
            if ($debug == true) {
                $this->saveXSLTizedXML($xml, $xslt, $label, 'ok');
            }
            $this->addStatusMessage('Data byla naimportována pomocí '.$xslt,
                'success');
        } else {
            $result = $this->lastResult;

            if ($debug) {
                $this->saveXSLTizedXML($xml, $xslt, $label, 'bad');
            }
            $this->addStatusMessage('Data nebyla naimportována  pomocí '.$xslt.' bez problémů',
                'warning');
        }
        return $result;
    }

    public function saveXSLTizedXML($xml, $xslt, $prefix, $suffix = '.')
    {
        $formatter     = new Formatter();
        $inputFile     = '../log/'.$prefix.'-input.xml';
        $outputFile    = '../log/'.$prefix.'_'.$xslt.'-inport.xml';
        $transformFile = 'xslt/'.strtolower($xslt).'.xslt';
        file_put_contents($inputFile, $formatter->format($xml));
        $cmd           = "/usr/share/flexibee/bin/flexibee2xml --from-xml $inputFile --run-xslt $transformFile --to-xml $outputFile";
        system("$cmd 2>&1 /dev/null");
        $errors        = "\n";
        if (isset($this->errors)) {
            foreach ($this->errors as $errorCode => $error) {
                if (key($error) == 0) {
                    foreach ($error as $message) {
                        $errors .= $errorCode.': '.$message['error']."\n";
                    }
                } else {
                    $errors .= $errorCode.': '.isset($error['message']) ? $error['message']
                            : $error."\n";
                }
            }
        }
        file_put_contents($outputFile,
            "<!-- \nXSLT: $transformFile\n$errors -->".$formatter->format(file_get_contents($outputFile)));
        $this->addStatusMessage($suffix.' XML Saved as: '.$outputFile, 'debug');
    }

}