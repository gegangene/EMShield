<?php
	$SCREEN=array(0.5, 0.5); // wymiary ekranu [m]
	$FREQUENCY=2.88; // [GHz]
	$LIGHTSPEED=299792458; // [m/s]
	$S_MIN = 11; // minimalne zakładane tłumienie [dB]
	$LAMBDA=$LIGHTSPEED/($FREQUENCY*(10**9)); // [m]
	$LAMBDA2=$LAMBDA/2;
	$LAMBDA10=$LAMBDA/10;
	$L=0.05*(10**(-3)); // minimalny zakładany wymiar liniowy otworu [m]
	$L_MAX=($LAMBDA)/(20**($S_MIN/20)); // maksymalny wymiar liniowy otworu przy którym ekran spełnia tłumienie $S_MIN [dB]
	$MIN_MARGIN=0.01; // minimalny margines na brzegu ekranu [m]
	$XY_MARGINS=$SCREEN[0]-($MIN_MARGIN*2);
	// tablica przechowująca wyliczone S [dB], l [m], d [m] oraz n_x i n_y
	$S_ARR=[[]];

	// obliczenie podstawowej składowej boku prostokąta a z przekątnej l
	function Get_a($l)
	{
		return sqrt(($l**2)/13);
	}

	// obliczenie maksymalnej ilości otworów w OX/OY dla zadanego a
	function N_max($xy, $a)
	{
		global $XY_MARGINS, $LAMBDA10;
		$ratio=$xy?2:3;
		return floor(($XY_MARGINS)/($ratio*$a+$LAMBDA10));
	}

	// obliczenie minimalnej ilości otworów w OX/OY dla zadanego a
	function N_min($xy, $a)
	{
		global $XY_MARGINS, $LAMBDA2;
		$ratio=$xy?2:3;
		return ceil(($XY_MARGINS)/($ratio*$a+$LAMBDA2));
	}

	// obliczenie d między otworami
	function D_0($xy, $a, $n)
	{
		global $XY_MARGINS;
		$ratio=$xy?2:3;
		return ($XY_MARGINS-($n*$ratio*$a))/($n-1);
	}

	for($L;$L_MAX;$L+=0.001)
	{
		// wyznaczenie a
		$A=Get_a($L);
		// wyznaczenie list wszystkich możliwych n dla OX i OY
		$n_x=range(N_min(false,$A),N_max(false,$A));
		$n_y=range(N_min(true,$A),N_max(true,$A));
		// wyznaczenie listy d dla wyżej wyliczonych n_x (zakładam że d_x=d_y, z powodu wymagania równomiernego rozłożenia otworów)
		$d=[];
		foreach($n_x as $n)
		{
			$d[]=D_0(false,$A,$n);
		}

	}




?>