<?php
	$SCREEN=[0.5, 0.5]; // wymiary ekranu [m]
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
	// tablica przechowująca wyliczone S [dB], l [m], d [m] oraz n_lambda2, n_x i n_y oraz stosunek powierzchni otworów do powierzchni ekranu
	$S_ARR=[[]];

	// obliczenie podstawowej składowej boku prostokąta a z przekątnej l
	function Get_a($l)
	{
		return sqrt(($l**2)/13);
	}

	// obliczenie maksymalnej ilości otworów w OX/OY dla zadanego a; x=false, y=true
	function N_max($xy, $a)
	{
		global $XY_MARGINS, $LAMBDA10;
		$ratio=$xy?2:3;
		return floor(($XY_MARGINS)/($ratio*$a+$LAMBDA10));
	}

	// obliczenie minimalnej ilości otworów w OX/OY dla zadanego a; x=false, y=true
	function N_min($xy, $a)
	{
		global $XY_MARGINS, $LAMBDA2;
		$ratio=$xy?2:3;
		return ceil(($XY_MARGINS)/($ratio*$a+$LAMBDA2));
	}

	// obliczenie d między otworami; x=false, y=true
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

	//print("lambda=$LAMBDA, max_l=$L_MAX");
	
	for($L;$L<=$L_MAX;$L+=0.001)
	{
		// wyznaczenie a
		$a=Get_a($L);
		// wyznaczenie list wszystkich możliwych n dla OX i OY
		$n_x=range(N_min(false,$a),N_max(false,$a));
		$n_y=range(N_min(true,$a),N_max(true,$a));
		//print("\n\n$L\tn_x\n");
		//print_r($n_x);
		//print("\n$L\tn_y\n");
		//print_r($n_y);
		
		foreach($n_x as $current_n_x)
		{
			// wyznaczenie d dla aktualnego n_x (zakładam że d_x=d_y, z powodu wymagania równomiernego rozłożenia otworów)
			$d=D_0(false,$a,$current_n_x);	
			foreach($n_y as $current_n_y)
			{
				// tablica zawierająca współrzędne wszystkich otworów w formacie [x][y][x_p,x_k,y_p,y_k]
				$shield=[[[]]];
				// ustalenie wartości początkowych x i y
				$current_x=$MIN_MARGIN;
				$current_y=$MIN_MARGIN;
				//wygenerowanie tablicy współrzędnych otworów ekranu
				for($i=0; $i<=$current_n_x; $i+=1)
				{
					for($ii=0; $ii<=$current_n_y; $ii+=1)
					{
						$shield[$i][$ii]["x_p"]=$current_x;
						$shield[$i][$ii]["x_k"]=$current_x+(3*$a);
						$shield[$i][$ii]["y_p"]=$current_y;
						$shield[$i][$ii]["y_k"]=$current_y+(2*$a);
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
				unset($current_x, $current_y);
				// badamy dla najgorszego przypadku - otworu na samym środku - najwięcej potencjalnych sąsiadów
				$considerated_x_label=floor($current_n_x/2);
				$considerated_y_label=floor($current_n_y/2);
				// ostateczne n_lambda2 do S
				$n_lambda2_max=1;
				// tymczasowe n_lambda2 dla każdego z 3 testów: pionowo, po skosie i poziomo
				$n_lambda2_local=1;
				// testuję pionowo, po skosie i poziomo tylko dla jednego zwrotu: dążąc do 0, gdyż dla środkowego otworu inne zwroty dałyby taką samą wartość n_lambda2
				
				// test pionowy
				for($i=0; $i<$considerated_y_label; $i++)
				{
					if(($shield[$considerated_x_label][$considerated_y_label]["y_p"]-$shield[$considerated_x_label][$i]["y_k"])<$LAMBDA2)
					{
						if($considerated_y_label-$i>$n_lambda2_local)
							$n_lambda2_local=$considerated_y_label-$i;
					}
				}

				if($n_lambda2_max<$n_lambda2_local) $n_lambda2_max=$n_lambda2_local;

				// test ukośny
				$test=$considerated_x_label<=$considerated_y_label?$considerated_x_label:$considerated_y_label;
				for($i=0; $i<$test; $i++)
				{
					if(sqrt(($shield[$considerated_x_label][$considerated_y_label]["y_p"]-$shield[$i][$i]["y_k"])**2+(($shield[$considerated_x_label][$considerated_y_label]["x_p"]-$shield[$i][$i]["x_k"]))**2)<$LAMBDA2)
					{
						if($considerated_x_label-$i>$n_lambda2_local)
							$n_lambda2_local=$considerated_x_label-$i;
					}
				}

				if($n_lambda2_max<$n_lambda2_local) $n_lambda2_max=$n_lambda2_local;

				// test poziomy
				for($i=0; $i<$considerated_x_label; $i++)
				{
					if(($shield[$considerated_x_label][$considerated_y_label]["x_p"]-$shield[$i][$considerated_y_label]["x_k"])<$LAMBDA2)
					{
						if($considerated_y_label-$i>$n_lambda2_local)
							$n_lambda2_local=$considerated_y_label-$i;
					}
				}

				if($n_lambda2_max<$n_lambda2_local) $n_lambda2_max=$n_lambda2_local;

				// wyznaczenie S dla aktualnego przypadku i zapisanie wyniku w tablicy jeśli spełnia warunek minimalnego tłumienia
				$current_s=S($L, $n_lambda2_max);
				if($current_s>=$S_MIN)
					$S_ARR[]=["s" => $current_s, "l" => $L, "d" => $d, "n_lambda2" => $n_lambda2_max, "n_x" => $current_n_x, "n_y" => $current_n_y, "P_o/P_c" => (3*$a*2*$a*$current_n_x*$current_n_y)/($SCREEN[0]*$SCREEN[1])];
			}
		}
	}
	//print_r($S_ARR);
	//$temp_max_arr = max(array_column($S_ARR, "P_o/P_c"));
	//print_r(array_filter($S_ARR, fn($item) => $item["P_o/P_c"] === $temp_max_arr));
	usort($S_ARR, fn($a, $b) => $b["P_o/P_c"] <=> $a["P_o/P_c"]);
	print_r(array_slice($S_ARR, 0, 4));

?>