<?php

/**
 * Convert item to another currency
 *
 * @param \FlexiPeeHP\Banka|\FlexiPeeHP\Pokladna $subject    item to change
 * @param string                                 $toCurrency code:CZK
 * @param float                                  $rate       rate 26.30
 *
 * @return boolean
 */
function exchange($subject, $toCurrency, $rate)
{
    $origPrice      = floatval($subject->getDataValue('sumOsv'));
    $convertedPrice = round($origPrice / $rate, 6, PHP_ROUND_HALF_UP);

    $converse = ['id' => $subject->getRecordID(), 'mena' => self::code($toCurrency),
        'kurz' => $rate,
        'bezPolozek' => true,
        'sumOsvMen' => round($convertedPrice, 2)
    ];

    $subject->insertToFlexiBee($converse);
    if ($subject->lastResponseCode == 201) {
        $subject->setDataValue('mena', self::code($toCurrency));
        $subject->setDataValue('sumOsvMen', round($convertedPrice, 2));
    }
    return $subject->lastResponseCode == 201;
}
