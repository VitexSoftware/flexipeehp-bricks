<?php

/**
 * @covers FlexiPeeHP\UcetniObdobi::createYearsFrom
 */
public function testCreateYearsFrom()
{
    //Načíst stávající roky
    $fbyears = $this->object->getColumnsFromFlexibee(['kod'], null, 'kod');
    $years   = [];
    foreach ($fbyears as $fbyear) {
        if (is_numeric($fbyear['kod'])) {
            $years[] = $fbyear['kod'];
        }
    }
    asort($years);
    $firstyear = current($years);
    $testyear  = $firstyear - 2;


    //Založit další dva předcházející roky
    $this->object->createYearsFrom($testyear, $testyear + 1);

    //Znovu přečíst roky z FlexiBee
    $newfbyears = $this->object->getColumnsFromFlexibee(['kod'], null, 'kod');
    $newyears   = [];
    foreach ($newfbyears as $newfbyear) {
        if (is_numeric($newfbyear['kod'])) {
            $newyears[$newfbyear['kod']] = $newfbyear['kod'];
        }
    }

    //Byly požadované roky založeny ?
    $this->assertArrayHasKey($testyear, $newyears, 'Year missing: '.$testyear);
    $this->assertArrayHasKey($testyear + 1, $newyears,
        'Year missing: '.$testyear + 1
    );

    //Zkusit založit již existující období
    $wrong = $this->object->createYearsFrom(date('Y'));
    $this->assertEquals('false', $wrong[0]['success'],
        'current year does not exist ?');

    //Uklid
    $this->object->deleteFromFlexiBee('code:'.$testyear);
    $this->object->deleteFromFlexiBee('code:'.$testyear + 1);
}
