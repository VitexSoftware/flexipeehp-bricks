<?php

/**
 * Loguje info o výsledku operace importu.
 *
 * @param \FlexiPeeHP\FlexiBeeRW $engine
 * @param string $id ID záznamu
 * @param string $caption
 */
function logOperationResult($engine, $id, $caption)
{
    if ($engine->lastResponseCode == 201) {
        if ($engine->lastResult['stats']['created'] == 1) {
            $operation    = 'Insert';
            $importStatus = 'success';
        } else {
            $operation    = 'Update';
            $importStatus = 'warning';
        }
    } else {
        $importStatus = 'error';
        if ($engine->recordExists(['id' => $id])) {
            $operation = 'Update';
        } else {
            $operation = 'Insert';
        }
    }
    $engine->addStatusMessage($operation.' '.$id.' '.$caption, $importStatus);
}
