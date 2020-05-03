<?php

//скрипт анализирует порядок набора тибетских букв, записанный  в файле _namelist_uni.liga
//для каждой буквы определяется из каких частей она должна быть составлена
//каждая часть буквы устанавливается в букве в соответствии с данными о контрольных точках Anchor Point
//составных частей букв OpenType записанными в файле _anchorList.txt
//_anchorList.txt создается в программе FontForge как результат работы скрипта getAnchor.pe
//При установки каждой последующей части буквы Anchor Point совмещается с соответствующей Anchor Point предыдущей буквы
//или с Anchor Point первой буквы, в соответствии с правилами набора.
//Сарва Мангалам!

$needClean=0;  //set 1 if need remove all ligature glyphs before build font
$p=0;
$glyph="";

//global
$joinStackLength=0;

include ("/Volumes/WORK_DHARMA_3TB/SAMSUNG/MainYagpoOCR/Font/__SCRIPT_FONTFORGE/@liga_glyph_init.php");
include ("/Volumes/WORK_DHARMA_3TB/SAMSUNG/MainYagpoOCR/Font/__SCRIPT_FONTFORGE/@liga_glyph_setNexLetter.php");
	
	
	$index=0;

 	foreach($ligaArray as $name=>$uniArray){
		$stackL=count($uniArray);
 		if($name=="")continue;
		if($stackL==1)continue;
		if($glyph!=""&&$name!=$glyph)continue;

		$codeText.="\n";		
		if($p)print "\n@1@".$name."\n";
		//проверяем сколько базовых букв в стеке.
		$l=0;
		//сначала устанавливаем порядок набора тибетских надписных согласных lce_tsa_can

		if($stackL>1&&($uni[$uniArray[$stackL-1]]=="lce_tsa_can"||$uni[$uniArray[$stackL-1]]=="mchu_can"||$uni[$uniArray[$stackL-1]]=="gru_can_rgyings"
		||$uni[$uniArray[$stackL-1]]=="gru_med_rgyings")){
			$t=$uniArray[$stackL-1];
			for($i=$stackL-1;$i>0;$i--){
				$uniArray[$i]=$uniArray[$i-1];
			}
			$uniArray[0]=$t;
		}
		
		for($i=0;$i<$stackL;$i++){
			if(isStopLetter($uni[$uniArray[$i]])){
				//проверяем есть ли после подписной буквы еще базовые буквы (встречается в санскрите)
				if($i+1<$stackL){
					if(!isStopLetter($uni[$uniArray[$i+1]])){
						$l++;
						continue;
					}
				}
				break;
			}	
 			$l++;
		}
		if($p)print "1stack base letters count: l:".$l."\n";
		
		//анализируем стек
		$rootGlyph="";  //коренная буква стека
		$baseGlyph="";  //базовая буква это последняя смонтированная буква стека
		$baseGlyphX=0;  //смещение базовой буквы относительно коренной буквы стека
		$baseGlyphY=0;
		$prefix="";     //дискретный размер букв из которых собирается стек
		$stackL=$stackL;
		

 		for($i=0;$i<$stackL;$i++){
 			if($p)print "/".$uniArray[$i]."/\n";
 			
 			//установка стека с одной коренной буквой и одной базовой буквой
 			if($i==0){
 				//ставим в стек коренную букву нужного размера.
 				$b=$uni[$uniArray[0]];
 				$n=$uni[$uniArray[1]];
 				if($l==2)$prefix="c1.";
 				if($l==3)$prefix="c2.";
 				if($l>3)$prefix="c3.";
 				$nextNext="";
 				if($stackL>$i+2)$nextNext=$uni[$uniArray[$i+2]];
 				
 				//проверяем меняет ли базовая буква свою форму за при присоединении следующей за ней буквы
				$newNext=isJoined($b,$uni[$uniArray[$i+1]],$nextNext);
				if($p)print "@1@ b:$b n:$n prefix:$prefix newNext:$newNext stackL:$stackL\n";
				if($newNext!=""){
						if($stackL==2||($stackL>2&&isTopLetter($uni[$uniArray[2]]))){
							$rootGlyph="c0_.".$newNext;
						}else{
							if($l==1)$rootGlyph="c_.".$newNext;
							if($l==2)$rootGlyph="c1.".$newNext;
							if($l==3)$rootGlyph="c2.".$newNext;
							if($l>3)$rootGlyph="c3.".$newNext;
						}	
						setLetter($name,$rootGlyph);
						$codeText.=setLetter($name,$rootGlyph);
						$baseGlyph=$rootGlyph;
						$i+=$joinStackLength;
						continue;
				}
 				$rootGlyph=$prefix.$b;
 				if($l>1&&($b=="ra"&&$n!="nya"&&$n!="la")){
					$rootGlyph="ra_short";
					if($stackL==2||($stackL>2 &&isTopLetter($uni[$uniArray[2]]) ) )$prefix="c0.";
				}

				if($l==2&&$b=="ta"){
					$rootGlyph="c2.ta";
				}
				if($l==2&&$b=="Ta"){
					$rootGlyph="c2.Ta";
				}
		

				if($b=="mchu_can"){
					$rootGlyph="mchu_can";
				}
				if($b=="lce_tsa_can"){
					$rootGlyph="lce_tsa_can";
				}
				if($b=="mchu_can"){
					$rootGlyph="mchu_can";
				}
				if($b=="gru_can_rgyings"){
					$rootGlyph="gru_can_rgyings";
				}
				if($b=="gru_med_rgyings"){
					$rootGlyph="gru_med_rgyings";
				}

				if($l<2&&($n=="vasur"||$n=="_A"||$n=="_u"||$n=="ratag")&&($b=="ha"||$b=="ka"||$b=="ga"||$b=="ta"||$b=="da"||$b=="na"||$b=="nya"||$b=="sha"||$b=="zha")){
					if($b=="ha"&&$stackL>2&&$uni[$uniArray[2]]=="_u"){
						$rootGlyph.="2";
					}else{
						$rootGlyph.="1";
					}
				}
				
 				$codeText.=setLetter($name,$rootGlyph);
 				$baseGlyph=$rootGlyph;
 				continue;
 			}
 			
 			if($i>0){
 				//если в стеке коренная буква и базовая буква, то ставим в стек базовую букву размера $prefix.
 				$next=$uni[$uniArray[$i]];
 				if($p)print "@@@0 i:$i next:$next baseGlyph:$baseGlyph root:$rootGlyph stackL:$stackL\n";
 				
 				
 				if($stackL>$i+1){
					//проверяем меняет ли базовая буква свою форму за при присоединении следующей за ней буквы
					$b=$uni[$uniArray[$i]];
	 				$n=$uni[$uniArray[$i+1]];
	 				
	 				if($stackL>$i+2)$nextNext=$uni[$uniArray[$i+2]];
	 				
					$newNext=isJoined($next,$uni[$uniArray[$i+1]],$nextNext);
					
					if($p)print "@@@1 i:$i b:$b n:$n newNext:$newNext root:$rootGlyph stackL:$stackL\n";
					if($newNext!=""){
						if(isTopLetter($next)){
	 						setNextLetter($name,$rootGlyph,$newNext,$uniArray,$i,$l);
	 						$i+=$joinStackLength;
 							continue;
 						}
						if($i==1&&$rootGlyph=="ra_short"){
							if($stackL==3){
								$prefix="c_.";
							}
							if($stackL>3&&isTopLetter($uni[$uniArray[3]])){
								$prefix="c_.";
							}
						}	
						setNextLetter($name,$baseGlyph,$prefix.$newNext,$uniArray,$i,$l);
						$baseGlyph=$prefix.$newNext;
						$i++;
						continue;
					}
					
				}	
				//проверяем меняет ли присоединяемая буква свою форму в зависимости от базовой буквы
 				$b=$uni[$uniArray[$i-1]];
				$n=$uni[$uniArray[$i]];

			
				if($b=="ta"||$b=="sa"){
					if($n=="sa"){
						$next.="1";
					}	
				}
				if($b=="Sha"||$b=="Tha"){ 
					if($n=="ratag"){
						$next.="_";
					}	
				}
				
				//if($l==2&&$rootGlyph!="ra_short"&&($n=="ta"||$n=="Ta")){
				//	$prefix="c2.";
				//}
		
				
				if(isTopLetter($next)){
 					setNextLetter($name,$rootGlyph,$next,$uniArray,$i,$l);
 					continue;
 				}

 				setNextLetter($name,$baseGlyph,$prefix.$next,$uniArray,$i,$l);
 				$baseGlyph=$prefix.$next;
 				
 				continue;
 			}

 			
 		}
 		//$codeText.="\n		CenterInWidth();\n";
 		$index++;
 		//if($index>1000)break;
 	}
 
 	
	$path_list_name="/Volumes/WORK_DHARMA_3TB/SAMSUNG/MainYagpoOCR/Font/__SCRIPT_FONTFORGE/_build.pe";
	//writeText($codeText,$path_list_name);
	if($p)print $codeText."\n";
	//exit(0);

	writeText($codeText,$path_list_name);

?>
