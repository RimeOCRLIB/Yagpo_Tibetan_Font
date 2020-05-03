<?php

//функция устанавливает следующую букву стека на основании правил шрифтовой грамматики и данных
//о контрольных якорных точках базовой и следующей буквы.
//координаты следующей буквы устанавливаются с учетом смещения позиции базовой буквы относительно коренной буквы стека


//Anchor-0  - установка верхней буквы по центру
//Anchor-1	- установка нижней буквы по центру
//Anchor-3	- установка нижней присоединенной буквы yatag, ratag, vasur

//Сарва Мангалам!
if($glyph!="")$p=1;


function setNextLetter($name,$baseGlyph,$next,$uniArray,$i,$l){
	global $codeText;
	global $anchorArray;
	global $baseGlyphX,$baseGlyphY;
	global $p;
	global $uni;
	
	$stackL=count($uniArray);
	
	$anchorBase="Anchor-1";
	$anchorNext="Anchor-0";

	$b=str_replace("c_.","",$baseGlyph);
	$b=str_replace("c0.","",$b);
	$b=str_replace("c1.","",$b);
	$b=str_replace("c2.","",$b);
	$b=str_replace("c3.","",$b);
	
	$n=str_replace("c_.","",$next);
	$n=str_replace("c0.","",$n);
	$n=str_replace("c1.","",$n);
	$n=str_replace("c2.","",$n);
	$n=str_replace("c3.","",$n);
		
	if($p)print "@@@ b:$b n:$n\n";
	
	//замняем имена подписных букв, не меняющих свой размер
	
	if($n[0]=="_")$next=$n;

	if($b=="vasur"){
		$baseGlyph="vasur";
	}
	if($b=="_A"){
		$baseGlyph="_A";
	}
	if($b=="ratag"||$b=="ratag1"||$b=="ratag_1"||$b=="ratag_"){
		$baseGlyph=$b;
	}
	
	//определяем необходимый тип якорной точки базовой буквы	
	if($n[0]=="_"&&$n[1]!="u"){
		$anchorBase="Anchor-0";
		$anchorNext="Anchor-0";
		if($n=="_i"||$n=="_iM"||$n=="_iMM"||$n=="_e"||$n=="_ai"||$n=="_aiM"||$n=="_aiMM"){  //||$n=="__i"||$n=="__iM"
			$anchorBase="Anchor-7";
			$anchorNext="Anchor-7";
			if($b=="tsa"||$b=="dza"||$b=="tsha"||$b=="dz.ra"||$b=="dz.ya"){
				$anchorBase="Anchor-11";
				$next.="1";
			}
		}
	}
	
	if($n=="ratag"||$n=="ratag_"){
		$anchorBase="Anchor-2";
		$anchorNext="Anchor-0";
		if($l>1)$n.="1";
			
		if($b=="la"||$b=="za"||$b=="aa"||$b=="pa"||$b=="pha"||$b=="ba"||$b=="tha"||$b=="kha"||$b=="wa"
		||$b=="sa"||$b=="sa1"||$b=="ya"||$b=="aa"){
			$anchorBase="Anchor-2";
			$anchorNext="Anchor-5";
		}elseif($b=="ma"){
			$anchorBase="Anchor-2";
			$anchorNext="Anchor-9";
		}elseif($b=="ca"||$b=="cha"||$b=="tsa"||$b=="tsha"||$b=="rtsa"||$b=="rdza"){
			$anchorBase="Anchor-3";
			$anchorNext="Anchor-3";
		}elseif($b=="ga1"||$b=="ka1"||$b=="sha1"){
			if($i<$stackL-1&&!isTopLetter($uni[$uniArray[$i+1]])){
				$anchorBase="Anchor-2";
				$anchorNext="Anchor-0";
			}else{
				$anchorBase="Anchor-2";
				$anchorNext="Anchor-5";
			}	
		}elseif($baseGlyph=="c1.ga"||$baseGlyph=="c1.ka"||$baseGlyph=="c1.sha"){
			if($i<$stackL-1&&!isTopLetter($uni[$uniArray[$i+1]])){
				$anchorBase="Anchor-2";
				$anchorNext="Anchor-7";
			}else{
				$anchorBase="Anchor-2";
				$anchorNext="Anchor-5";
			}	
		}elseif($b=="Sha"||$b=="Tha"){ 
			$anchorBase="Anchor-3";
			$anchorNext="Anchor-5";
		}elseif($b=="vasur"){ 
			$anchorBase="Anchor-2";
			$anchorNext="Anchor-5";
		}elseif($b=="k.ya"||$b=="g.ya"||$b=="h.ya"||$b=="t.ya"||$b=="Sh.ya"||
		$b=="n.ya"||$b=="ny.ya"||$b=="sh.ya"||$b=="N.ya"||$b=="Th.ya"||$b=="D.ya"||$b=="T.ya"||
		$b=="d.ya"||$b=="j.ya"||$b=="dz.ya"||$b=="ng.ya"||$b=="r.ya"||$b=="kh.ya"||$b=="g.ya"||$b=="a_chung.ya"){ 
			$anchorBase="Anchor-5";
			$anchorNext="Anchor-3";
		}elseif($b=="yatag"||$b=="yatag1"||$b=="yatag2"){ 
			$anchorBase="Anchor-11";
			$anchorNext="Anchor-3";
		}
		$next=$n;
			
	}	
	if($p)print "//1@/__  set $n in $b anchorBase:$anchorBase anchorNext:$anchorNext\n";
	
	if($n=="yatag"){
		$anchorBase="Anchor-2";
		$anchorNext="Anchor-0";
		if($b=="ca"||$b=="cha"||$b=="tsa"||$b=="tsha"||$b=="rtsa"||$b=="rdza"){
			$anchorBase="Anchor-3";
			$anchorNext="Anchor-3";
		}
		$next=$n;
	}
	if($n=="yatag3_1"||$n=="yatag3_2"||$n=="yatag3_3"||$n=="yatag3_4"){
			$index=$l;
			if($index>4)$index=4;
			$n="yatag3_".$index;
			$anchorBase="Anchor-12";
			$anchorNext="Anchor-0";
			$next=$n;
	}	
	
	if($b=="yatag"||$b=="yatag1"||$b=="yatag2"||$b=="yatag3_1"||$b=="yatag3_2"||$b=="yatag3_3"||$b=="yatag3_4"){   //||$b=="lce_tsa_can"||$b=="gter_yig"||$b=="mchu_can"||$b=="gru_can_rgyings"||$b=="gru_med_rgyings"
		$baseGlyph=$b;
	}

	
	if(isTopLetter($n)){
			if($p)print "@@@ top letter\n";
			//$anchorBase="Anchor-0";
			//$anchorNext="Anchor-0";
			$baseGlyphX=0;
			$baseGlyphY=0;
	}	
	if($n=="_u"){
		$anchorBase="Anchor-2";
		$anchorNext="Anchor-0";
		$next="_u";
		if($b=="yatag"||$b=="yatag2"||$b=="yatag3_1"||$b=="yatag3_2"||$b=="yatag3_3"||$b=="yatag3_4"||$b=="k.ya"||$b=="kh.ya"||$b=="g.ya"||$b=="ny.ya"||
		$b=="h.ya"||$b=="t.ya"||$b=="T.ya"||$b=="n.ya"||$b=="d.ya"||$b=="D.ya"||$b=="sh.ya"||$b=="Sh.ya"||$b=="Th.ya"||$b=="ca"||$b=="cha"||$b=="tsa"||$b=="tsha"){
			$anchorBase="Anchor-4";
			$next="_u5";
		}
		if($b=="aa"||$b=="ma"||$b=="la"||$b=="ya"||$b=="tha"){
			$next="_u3";
		}
		
		if($b=="ha"||$b=="ta"||$b=="da"||$b=="na"||$b=="nya"||$b=="zha"||$b=="sha"||$b=="ha1"||$b=="ta1"||$b=="da1"||$b=="na1"||$b=="nya1"||$b=="zha1"||$b=="sha1"){
				if($l==1){
					$next="_u4";
					if($b=="ha1"||$b=="nya1"||$b=="ta1"){
						$anchorBase="Anchor-3";	
					}
					if($b=="sha1"||$b=="na1"){
						$anchorNext="Anchor-3";	
					}
				}else{
					$next="_u4_1";
					if($b=="da"){
						$anchorNext="Anchor-2";	
					}
					if($b=="sha"||$b=="na"){
						$anchorNext="Anchor-3";	
					}
				}	
				
		}
		if($l>1&&($b=="pa"||$b=="ba"||$b=="pha"))$next="_u3";
		if($b=="Ta"){
			$next="_u6";
		}
		if($b=="h.ra"||$b=="t.ra"||$b=="ny.ra"){
			$anchorBase="Anchor-5";
		}
	}
	if($n=="_A"||$n=="_U"){
		$anchorBase="Anchor-2";
		$anchorNext="Anchor-0";
		$next=$n;
		if($b=="na"||$b=="da"){
			$anchorNext="Anchor-3";
		}elseif($b=="kha"||$b=="nga"||$b=="na"||$b=="sa"||$b=="ratag"||$b=="la"||$b=="ya"||$b=="za"||$b=="aa"||
			$b=="pa"||$b=="pha"||$b=="ba"||$b=="ma"||$b=="vasur"||
			$b=="tha"||$b=="wa"||$b=="d.ra"||$b=="dz.ra"||$b=="n.ra"||$b=="ng.ra"||$b=="a_chung.ra"||
			$b=="ka"||$b=="ga"||$b=="na"||$b=="da"||$b=="sha"||$b=="zha"||
			$b=="ka1"||$b=="ga1"||$b=="na1"||$b=="da1"||$b=="sha1"||$b=="zha1"||
			$b=="kh.ra"||$b=="k.ra"||$b=="g.ra"||$b=="d.ra"||$b=="zh.ra"||$b=="n.ra"||$b=="j.ra"||$b=="dz.ra"||$b=="r.ra"||$b=="a_chung.ra"||$b=="ng.ra"){ 
			$anchorNext="Anchor-4";
		}elseif($b=="ha"||$b=="ta"||$b=="nya"
			  ||$b=="ha1"||$b=="ta1"||$b=="nya1"){
			$anchorNext="Anchor-0";
		}elseif($b=="ha2"){
			$anchorNext="Anchor-4";
		}
	}
	if($n=="vasur"){
		$anchorBase="Anchor-2";
		$anchorNext="Anchor-0";
		$next="vasur";
		if($b=="ka1"||$b=="ga1"||$b=="sha1"){
			$anchorNext="Anchor-5";
		}
		if($b=="ta"||$b=="nya"||$b=="ha"||$b=="ta1"||$b=="nya1"||$b=="ha1"){
			$anchorNext="Anchor-4";
		}
	
	}
		
	if($n=="halanta"){
		$anchorBase="Anchor-2";
		$anchorNext="Anchor-0";
		$next="halanta";
	}
	
	if($b=="la"||$b=="ma"||$b=="sa"||$b=="ya"||$b=="Ya"||$b=="pa"||$b=="pha"||$b=="ba"||$b=="wa"||$b=="aa"||$b=="za"||
		$b=="ratag"||$b=="ratag1"||$b=="k.ra"||$b=="g.ra"){
		
		if($n=="kha"||$n=="nga"||$n=="ca"||$n=="cha"||$n=="ja"||$n=="ny"||$n=="ta"||$n=="Tha"||$n=="Da"||$n=="Ta"||$n=="Na"||
		$n=="da"||$n=="na"||$n=="zha"||$n=="za"||$n=="a_chung"||$n=="ra"||$n=="sha"||$n=="Sha"||$n=="ha"||$n=="Ra"||$n=="nya"||
		$n=="h.ya"||$n=="t.ya"||$n=="n.ya"||$n=="ny.ya"||$n=="sh.ya"||$n=="N.ya"||$n=="Th.ya"||$n=="D.ya"||$n=="T.ya"||$n=="d.ya"||$n=="j.ya"||
		$n=="ng.ya"||$n=="r.ya"||$n=="kh.ya"||$n=="a_chung.ya"||$n=="ny.ya"||
		$n=="Ta1"||$n=="Da1"||$n=="Na1"||$n=="da1"||$n=="na1"||$n=="zha1"||$n=="ha1"||$n=="nya1"||$n=="ta1"||$n=="nya1"||
		$n=="Ta2"||$n=="Da2"||$n=="Na2"||$n=="da2"||$n=="na2"||$n=="zha2"||$n=="ha2"||$n=="nya2"||$n=="ta2"||$n=="nya1"||
		$n=="kh.ra"||$n=="d.ra"||$n=="t.ra"||$n=="zh.ra"||$n=="n.ra"||$n=="h.ra"||$n=="j.ra"||$n=="dz.ra"||$n=="r.ra"||$n=="a_chung.ra"||$n=="ny.ra"||
		$n=="ng.ra"||$n=="T.ra"||$n=="N.ra"||$n=="D.ra"||$n=="Sh.ra"||$n=="rtsa"||$n=="rdsa"||$n=="r.dz.ra"||$n=="r.dz.ya"){
			$anchorBase="Anchor-6";
			$anchorNext="Anchor-7";
		}elseif($n=="tsa"||$n=="tsha"||$n=="dza"||$n=="dz.ya"){
				$anchorBase="Anchor-6";
				$anchorNext="Anchor-11";	
		
		}else{
			if($n[0]!="_"&&$n!="ratag"&&$n!="ratag1"&&$n!="ratag_"&&$n!="ratag_1"&&$n!="yatag"&&$n!="yatag2"&&$n!="yatag3_1"&&$n!="yatag3_2"&&$n!="yatag3_3"&&$n!="yatag3_4"&&
			$n!="vasur"&&$n!="halanta"){
				$anchorBase="Anchor-6";
				$anchorNext="Anchor-9";	
			}
		}
	}	
	
	if($b=="ha"||$b=="nya"){
		if($n=="ma"||$n=="aa"||$n=="sa"||$n=="la"){
			$anchorBase="Anchor-6";
			$anchorNext="Anchor-11";
		}
	
	}
	if($b=="sha"||$b=="ka"||$b=="ga"){
		if($n=="kha"||$n=="nga"||$n=="ca"||$n=="cha"||$n=="Ta"||$n=="Tha"||$n=="Da"||$n=="Na"||$n=="ta"||$n=="da"||$n=="na"||$n=="ha"||$n=="Ra"
		||$n=="h.ya"||$n=="t.ya"||$n=="n.ya"||$n=="Sha"||$n=="ja"||$n=="sha"){
			$anchorBase="Anchor-6";
			$anchorNext="Anchor-7";
		}
		if($n=="sa"||$n=="la"||$n=="pa"||$n=="pha"||$n=="ka"||$n=="ga"||$n=="ma"||$n=="ya"
		||$n=="k.ya"||$n=="g.ya"||$n=="kh.ya"||$n=="k.ra"||$n=="g.ra"||$n=="kh.ra"){
			$anchorBase="Anchor-8";
			$anchorNext="Anchor-9";
		}
		if($n=="dza"||$n=="tsa"||$n=="tsha"||$n=="dzra"){
			$anchorBase="Anchor-8";
			$anchorNext="Anchor-11";
		}
	}
	
	
	if($n=="_xa"){
		$anchorBase="Anchor-7";
		$anchorNext="Anchor-0";
		$next="_xa";
	}
	
	if($p)print "//@/__  set $n in $b anchorBase:$anchorBase anchorNext:$anchorNext\n";
		
	$codeText.="
		Select(\"$next\");
		CopyReference();
		Select(\"$name\");";
		
	//необходимо установить букву таким образом, чтобы совпали координатам контрольных точек буквы и базовой буквы лигатуры.
	//сначала определяем координаты контрольной точки Anchor-0 базовой буквы
	$n=0;
	
	
	if(isset($anchorArray[$baseGlyph]))$n=count($anchorArray[$baseGlyph]);
	//if($p)print "@@@@ l:$l set next $next in $baseGlyph anchorBase:$anchorBase anchorNext:$anchorNext count:$n  baseGlyphX:$baseGlyphX baseGlyphY:$baseGlyphY\n"; 
	$xBase=-1;
	$yBase=-1;
	$dx=0;
	$dy=0;
	if($n!=0){
		for($i=0;$i<$n;$i+=3){
			//print $baseGlyph." -> ".$anchorArray[$baseGlyph][$i]."\n";
			if($anchorArray[$baseGlyph][$i]==$anchorBase){
				if($p)print $baseGlyph." -> match base ".$anchorArray[$baseGlyph][$i]." x=".$anchorArray[$baseGlyph][$i+1]."\n";
				$xBase=$anchorArray[$baseGlyph][$i+1];
				$yBase=$anchorArray[$baseGlyph][$i+2];
				break;
			}	
		}	
	}
	if($xBase!=-1){
		if($p)print $baseGlyph." -> xBase:$xBase yBase:$yBase\n";

		//определяем координаты контрольной точки Anchor-0 устанавливаемой буквы
		$n=0;
		if(isset($anchorArray[$next]))$n=count($anchorArray[$next]);
		if($p)print "set $next  by $anchorNext $n\n";
		$xGlyph=-1;
		$yGlyph=-1;
		if($n!=0){
			for($i=0;$i<$n;$i+=3){
				//print $next." -> ".$anchorArray[$next][$i]."\n";
				if($anchorArray[$next][$i]==$anchorNext){
					if($p)print $next." -> match next ".$anchorArray[$next][$i]." x=".$anchorArray[$next][$i+1]."\n";
					$xGlyph=$anchorArray[$next][$i+1];
					$yGlyph=$anchorArray[$next][$i+2];
					break;
				}	
			}	
		}
		if($xGlyph!=-1){
			$dx=$xBase-$xGlyph+$baseGlyphX;
			$dy=$yBase-$yGlyph+$baseGlyphY;
			if($p)print $next." -> dx:$dx dy:$dy xBase:$xBase yBase:$yBase xGlyph:$xGlyph yGlyph:$yGlyph baseGlyphX:$baseGlyphX baseGlyphY:$baseGlyphY\n";
			if(($dx!=0||$dy!=0)&&!isTopLetter($next)){  
				$baseGlyphX=$dx;
				$baseGlyphY=$dy;
			}
		}	
		
	}
	$codeText.="\n		PasteWithOffset($dx,$dy);";
}
?>
