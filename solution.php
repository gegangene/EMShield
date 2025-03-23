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
	// tablica przechowująca wyliczone S [dB], l [m], d [m] oraz n_lambda2, n_x i n_y
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

	// obliczenie tłumienia S [dB] dla danego przypadku
	function S($l, $n_lambda2)
	{
		global $LAMBDA;
		return (20*log10($LAMBDA/(2*$l)))-(20*log10(sqrt($n_lambda2)));
	}

	print("lambda=$LAMBDA, max_l=$L_MAX");
	
	for($L;$L<=$L_MAX;$L+=0.001)
	{
		// wyznaczenie a
		$a=Get_a($L);
		// wyznaczenie list wszystkich możliwych n dla OX i OY
		$n_x=range(N_min(false,$a),N_max(false,$a));
		$n_y=range(N_min(true,$a),N_max(true,$a));
		print("\n\n$L\tn_x\n");
		print_r($n_x);
		print("\n$L\tn_y\n");
		print_r($n_y);
		
		foreach($n_x as $current_n_x)
		{
			// wyznaczenie d dla aktualnego n_x (zakładam że d_x=d_y, z powodu wymagania równomiernego rozłożenia otworów)
			$d=D_0(false,$a,$current_n_x);	
			foreach($n_y as $current_n_y)
			{
				// tablica zawierająca współrzędne wszystkich otworów w formacie [x][y][0=x_p,1=x_k,2=y_p,3=y_k]
				$shield=[[[]]];
				// ustalenie wartości początkowych x i y
				$current_x=$MIN_MARGIN;
				$current_y=$MIN_MARGIN;
				//wygenerowanie tablicy współrzędnych otworów ekranu
				for($i=0; $i<=$current_n_x; $i+=1)
				{
					for($ii=0; $ii<=$current_n_y; $ii+=1)
					{
						$shield[$i][$ii][0]=$current_x;
						$shield[$i][$ii][1]=$current_x+(3*$a);
						$shield[$i][$ii][2]=$current_y;
						$shield[$i][$ii][3]=$current_y+(2*$a);
						$current_y+=2*$a;
						if($current_n_y<max($n_y))
						{
							$current_y+=$d;
						}
					}
					$current_x+=3*$a;
					if($current_n_x<max($n_x))
					{
						$current_x+=$d;
					}
				}
				
			}
		}
	}




?>