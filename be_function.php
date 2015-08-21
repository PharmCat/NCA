<?php
// Bioequivalence calculation function for 2x2 balanced design for log-transformed data
// $array [x][y]
// x - subject number
// y = 0 - sequence (1 - TR; 2 - RT); y = 1 - T values, y = 2 - R values
// $t T value 

// Расчет параметров биоэквивалентности для сбалансированного 2X2 дизайна для лог-преобразованных данных


	function be_calc ($array, $t){

		$n = count ($array); // количество субъектов // subject number
		$df = $n - 2;

		$sumall = 0;     // сумма всех значений                 // sum for all values
		$sum1   = 0;     // квадрат суммы / n*2                 // sum square (SS)
		$sumsqall = 0;   // сумма квадратов всех значений       // SS all values
		$sumsqsubj = 0;  // квадрат суммы инд                   // SS for subject
		$sumf1 = 0;   	 // сумма кв препарата 1  (T)           // SS for formulation T
		$sumf2 = 0;   	 // сумма кв препарата 2  (R)           // SS for formulation R
		$sump1 = 0;   	 // сумма кв периода 1                  // SS for period 1
		$sump2 = 0;  	 // сумма кв периода 2                  // SS for period 2
		$sumsq1= 0;      // сумма кв последовательности 1       // SS for sequence 1 (TR)
		$sumsq2= 0;      // сумма кв последовательности 2       // SS for sequence 1 (RT)

		for ($i = 0; $i < $n; ++$i){

			$sumall = $sumall + $array[$i][1] + $array[$i][2];
			$sumsqall = $sumsqall + sq($array[$i][1]) + sq($array[$i][2]);
			$sumsqsubj = $sumsqsubj + sq($array[$i][1] + $array[$i][2]);
			$sumf1 = $sumf1 + $array[$i][1];
			$sumf2 = $sumf2 + $array[$i][2];

				if ($array[$i][0] == 1){

					$sumsq1 = $sumsq1 + $array[$i][1] + $array[$i][2];
					$sump1 = $sump1 + $array[$i][1];
					$sump2 = $sump2 + $array[$i][2];
				}

				else if ($array[$i][0] == 2) {

					$sumsq2 = $sumsq2 + $array[$i][1] + $array[$i][2];
					$sump1 = $sump1 + $array[$i][2];
					$sump2 = $sump2 + $array[$i][1];
				}
		}

		$sum1 = $sumall*$sumall/($n*2);

		$totalvar = $sumsqall      - $sum1;
		$btwvar   = ($sumsqsubj/2) - $sum1;

		$sqvar    = (sq($sumsq1)+sq($sumsq2))/$n - $sum1;
		$pvar     = (sq($sump1)+sq($sump2))/$n   - $sum1;
		$fvar     = (sq($sumf1)+sq($sumf2))/$n   - $sum1;
		$ssvar    =  $btwvar - $sqvar;

		$errorvar = $totalvar - $btwvar - $pvar - $fvar;
		$mserror  = $errorvar/$df;
		$seerror  = sqrt (2*$mserror/$n);

		$intracv  = sqrt (exp($mserror) - 1);

		$ratio    = exp($sumf1/$n)/exp($sumf2/$n);
		$diff     = ($sumf1/$n) - ($sumf2/$n);

		$lncil = $diff - $t*$seerror;
		$lnciu = $diff + $t*$seerror;

		$cil = exp($lncil);
		$ciu = exp($lnciu);

		$result['cil'] = $cil;                 //Low 90% CI 
		$result['ciu'] = $ciu;                 //Upper 90% CI
		$result['intracv'] = $intracv;         //Intra subject CV
		$result['ratio'] = $ratio;             //Ratio
		$result['diff'] = $diff;               //Diff
		$result['mserror'] = $mserror;         //MSE error
		$result['mse'] = $seerror;             //MSE
		$result['n'] = $n;                     //Subject number


		return $result;
	}

//-------------------------------------

	function sq ($a){ 
		return $a*$a;
	}

php?>
