<?php
/**
 * FlexiPeeHP - Example how to show connection check InfoBox
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

namespace FlexiPeeHP\Bricks;

include_once './config.php';
include_once '../vendor/autoload.php';
include_once './common.php';

                $modul                                    = 'banka'; //pokladna
                $documentID                               = null;
                $this->banker->defaultUrlParams['detail'] = 'custom:id, poznam';

                list( $method, $document ) = explode(':',
                    $this->getDataValue('poznam'));

                if (empty($document)) {
                    $this->addStatusMessage('Dokument bez cisla', 'error');
                } else {
                    switch ($method) {
                        case 'CASH':
                            $documentID                               = $this->getCashCode([
                                'ExtCode' => $document], 'ext');
                            $this->casher->ignore404(true);
                            $this->casher->defaultUrlParams['detail'] = 'id';
                            $this->casher->loadFromFlexiBee(rawurlencode($documentID));
                            if ($this->casher->lastResponseCode == 200) {

                                $cashID = $this->casher->getDataValue('id');

                                $this->defaultHttpHeaders['Accept'] = 'text/html';
                                $this->setPostFields(http_build_query(['modul' => $modul,
                                    'submit' => 'OK']));
                                $this->performRequest($invoiceID.'/vytvor-vazbu-zdd/'.$cashID,
                                    'GET', 'json');
                                $responseArr                        = explode("\n",
                                    $this->lastCurlResponse);
                                $result                             = true;
                                $message                            = '';
                                foreach ($responseArr as $lineNo => $responseLine) {
                                    if (strstr($responseLine,
                                            '<ul class = "flexibee-errors">')) {
                                        $message = trim($responseArr[$lineNo + 1]);
                                        $result  = false;
                                    }
                                    if (strstr($responseLine,
                                            '<div class = "alert alert-success">')) {
                                        $message = strip_tags(html_entity_decode(trim($responseArr[$lineNo
                                                    + 1])));
                                        $result  = true;
                                    }
                                }

                                if ($result === true) {
                                    $this->addStatusMessage(empty($message) ? $this->getDataValue('kod').'/vytvor-vazbu-zdd/'.$documentID
                                                : $message, 'success');
                                } else {
                                    $this->addStatusMessage($this->getDataValue('kod').'/vytvor-vazbu-zdd/'.$documentID,
                                        'warning');
                                }
                            } elseif ($this->casher->lastResponseCode == 404) {
                                $this->casher->addStatusMessage('Platba do pokladny  nenalezena: '.$documentID,
                                    'warning');
                            }
                            break;
                        case 'CARD':
                            if ($document[0] == 'K') {
                                $documentID = $this->getCardCode(['ExtCode' => $document],
                                    'ext');
                            } else {
                                $documentID = $this->getCardCode(['IDDocumentItem' => $document],
                                    'idd');
                            }

                            $this->banker->ignore404(true);

                            $this->banker->loadFromFlexiBee(rawurlencode($documentID));
                            if ($this->banker->lastResponseCode == 200) {

                                $bankID = $this->banker->getDataValue('id');

                                $this->defaultHttpHeaders['Accept'] = 'text/html';
                                $this->setPostFields(http_build_query(['modul' => $modul,
                                    'submit' => 'OK']));
                                $this->performRequest($invoiceID.'/vytvor-vazbu-zdd/'.$bankID,
                                    'GET', 'json');

                                $responseArr = explode("\n",
                                    $this->lastCurlResponse);
                                $result      = true;
                                $message     = '';
                                foreach ($responseArr as $lineNo => $responseLine) {
                                    if (strstr($responseLine,
                                            '<ul class = "flexibee-errors">')) {
                                        $message = trim($responseArr[$lineNo + 1]);
                                        $result  = false;
                                    }
                                    if (strstr($responseLine,
                                            '<div class = "alert alert-success">')) {
                                        $message = strip_tags(html_entity_decode(trim($responseArr[$lineNo
                                                    + 1])));
                                        $result  = true;
                                    }
                                }

                                if ($result === true) {
                                    $this->addStatusMessage(empty($message) ? $this->getDataValue('kod').'/vytvor-vazbu-zdd/'.$documentID
                                                : $message, 'success');
                                } else {
                                    $this->addStatusMessage($this->getDataValue('kod').'/vytvor-vazbu-zdd/'.$documentID,
                                        'warning');
                                }
                            } elseif ($this->banker->lastResponseCode == 404) {
                                $this->banker->addStatusMessage('Platba nenalezena: '.$documentID,
                                    'warning');
                            }
                            break;
                        case 'BANKTRANSFER':
                            break;
                        default :
                            $this->addStatusMessage('Neznama platebni metoda '.$method
                                , 'warning');
                    }
                }
                $this->defaultHttpHeaders = $headersBackup;
