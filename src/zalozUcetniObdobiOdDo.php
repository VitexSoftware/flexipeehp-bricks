<?php

/**
 * Create requested Accounting period
 *
 * @param int $startYear first year to create
 * @param int $endYear   last yar to create - default is current year
 *
 * @return array Results
 */
function createYearsFrom($startYear, $endYear = null)
{
    $this   = new \FlexiPeeHP\UcetniObdobi();
    $result = [];
    if (is_null($endYear)) {
        $endYear = date('Y');
    }

    for ($year = $startYear; $year <= $endYear; ++$year) {
        $obdobi = ['kod' => $year,
            'platiOdData' => $year.'-01-01T00:00:00',
            'platiDoData' => $year.'-12-31T23:59:59',
        ];
        if ($this->idExists('code:'.$year)) {
            $this->addStatusMessage(sprintf(_('%s already exists.'), $year));
        } else {
            $this->setData($obdobi);
            $result[] = $this->insertToFlexibee();
            $this->dataReset();
        }
    }
    return $result;
}
