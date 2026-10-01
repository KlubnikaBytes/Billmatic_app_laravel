<?php

namespace App\Helpers;

class NumberToWords
{
    public static function convert($amount)
    {
        $amount = round((float) $amount, 2);

        $rupees = (int) floor($amount);
        $paise  = (int) round(($amount - $rupees) * 100);

        $words = self::numberToIndianWords($rupees);

        if ($paise > 0) {

            $words .= ' and '
                . self::numberToIndianWords($paise)
                . ' Paise';

        }

        return ucfirst($words);
    }


    private static function numberToIndianWords($number)
    {
        if ($number == 0) {
            return 'Zero';
        }

        $ones = [
            '',
            'One',
            'Two',
            'Three',
            'Four',
            'Five',
            'Six',
            'Seven',
            'Eight',
            'Nine',
            'Ten',
            'Eleven',
            'Twelve',
            'Thirteen',
            'Fourteen',
            'Fifteen',
            'Sixteen',
            'Seventeen',
            'Eighteen',
            'Nineteen'
        ];

        $tens = [
            '',
            '',
            'Twenty',
            'Thirty',
            'Forty',
            'Fifty',
            'Sixty',
            'Seventy',
            'Eighty',
            'Ninety'
        ];


        $result = '';


        if ($number >= 10000000) {

            $result .= self::numberToIndianWords(
                intdiv($number, 10000000)
            );

            $result .= ' Crore ';

            $number %= 10000000;
        }


        if ($number >= 100000) {

            $result .= self::numberToIndianWords(
                intdiv($number, 100000)
            );

            $result .= ' Lakh ';

            $number %= 100000;
        }


        if ($number >= 1000) {

            $result .= self::numberToIndianWords(
                intdiv($number, 1000)
            );

            $result .= ' Thousand ';

            $number %= 1000;
        }


        if ($number >= 100) {

            $result .= $ones[
                intdiv($number, 100)
            ];

            $result .= ' Hundred ';

            $number %= 100;
        }


        if ($number > 0) {

            if ($number < 20) {

                $result .= $ones[$number];

            } else {

                $result .= $tens[
                    intdiv($number, 10)
                ];

                if (($number % 10) > 0) {

                    $result .= ' '
                        . $ones[$number % 10];

                }

            }

        }

        return trim($result);
    }
}