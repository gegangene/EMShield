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
	// tablica przechowująca wyliczone S [dB], a [m], d [m] oraz n_x i n_y
	$S_ARR=array(array());

	// obliczenie podstawowej składowej boku prostokąta z przekątnej
	function Get_a($l)
	{
		return sqrt(($l**2)/13);
	}

	function N_max($xy, $a)
	{

	}

	function D_0($xy, $a, $n)
	{
		
	}

	for($L;$L_MAX;$L+=0.001)
	{
		$A=Get_a($L);
	}




?>