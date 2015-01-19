<?php

/********************************************************************

Checks if the player has won
Calls the recursive function four times, 
once for horizontal, left and right
vertical, bottom,
left diagonal, top-left to bottom-right
right diagonal, top-right to bottom-left
********************************************************************/

function didIWin($currentBoard, $lastMove){
	$rows = explode('|',$currentBoard);
	for($i = 0; $i < count($rows); $i++)
	{
		$board[$i] = explode('~',$rows[$i]);
	}
	$rowLen = count($board);
	$colLen = count($board[0]);
	$piece = explode('|', $lastMove);

	$playerPlayed = substr($piece[0], 6);

	$boardX = $piece[2];
	$boardY = $piece[3];

	$count = 1;
	$direction = 1;

	//Along the horizontal line
	$win = checkWin($board,$playerPlayed,$boardX,$boardY,0,'-',$direction,$count,$rowLen,$colLen,$boardX,$boardY);

	//Along the vertical line
	if($win != $playerPlayed){
		$win = checkWin($board,$playerPlayed,$boardX,$boardY,'+',0,$direction,$count,$rowLen,$colLen,$boardX,$boardY);
	}

	//Along the / diagonal
	if($win != $playerPlayed){
		$win = checkWin($board,$playerPlayed,$boardX,$boardY,'-','+',$direction,$count,$rowLen,$colLen,$boardX,$boardY);
	}

	//Along the \ diagonal
	if($win != $playerPlayed){
		$win = checkWin($board,$playerPlayed,$boardX,$boardY,'-','-',$direction,$count,$rowLen,$colLen,$boardX,$boardY);
	}
	return $win;
}

/********************************************************************

Recursive function to count the number hits a player has in row, 
horizontal, vertical or diagonally and makes him a winner if he has 
4.

********************************************************************/

function checkWin($board,$player,$x,$y,$symX,$symY,$direction,$count,$lenRow,$lenCol,$boardX,$boardY){ 
	// symX & symY are +,- or 0
	// + for going in +ve x or y direction
	// - for going in -ve x or y direction
	// 0 for performing no operation
	if($count < 4){
		if($symX != '0' ){
			$tempX = $x . $symX . '1';
			$tempX = calculate_string($tempX);
		}else{
			$tempX = (int)$x;
		}

		if($symY != '0' ){
			$tempY = $y . $symY . '1';
			$tempY = calculate_string($tempY);
		}else{
			$tempY = (int)$y;
		}
		if($tempX >= 0 && $tempY >=0 && $tempX < $lenRow && $tempY < $lenCol && $board[$tempX][$tempY] == $player){
			++$count;
			return checkWin($board,$player,$tempX, $tempY, $symX, $symY,$direction,$count,$lenRow,$lenCol,$boardX,$boardY);
		}
		else if($direction == 1){
			$direction = 0;

			//Switching the direction
			if($symX == '+' && $symX != '0'){
				$symX = '-';
			}else if($symX == '-' && $symX != '0'){
				$symX = '+';
			}

			//Switching the direction
			if($symY == '+' && $symY != '0'){
				$symY = '-';
			}else if($symY == '-'  && $symY != '0'){
				$symY = '+';
			}
			return checkWin($board,$player,$boardX, $boardY, $symX, $symY,$direction,$count,$lenRow,$lenCol,$boardX,$boardY);
		}else{
			$win = 0;
			return $win;
		}
	}else{
		$win = $player;
		return $win;
	}	
}

/********************************************************************

Evaluates a string which is as a mathematical equation

********************************************************************/

function calculate_string($mathString){
    $mathString = trim($mathString);     // trim white spaces
    $mathString = preg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    // remove any non-numbers chars; exception for math operators
    $compute = create_function("", "return (" . $mathString . ");" );
    return 0 + $compute();
}

?>