<?php 


// Change database information              Database    Username     Password
// 				              name
$pdo = new PDO("mysql:host=127.0.0.1;dbname=facemash",'facemasher','mashtheface');


function klogic($rating,$matchcount){
	if($rating>2400)
		return 20;
	else 
		return 40;
}

$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD']=="POST"){

	header('Content-Type:application/json;Charset:UTF8;');
	$input = json_decode(file_get_contents("php://input"));

	if($input->jsontype=="start"){

		$sql="SELECT * from `images` ORDER by RAND() LIMIT 2;";
		$result=array();
		$row = $pdo->query($sql);		
		$first = $row->fetch(PDO::FETCH_ASSOC);
		$result['id1']=$first['id'];	
		$result['src1']=$first['src'];	
		$first = $row->fetch(PDO::FETCH_ASSOC);
		$result['id2']=$first['id'];	
		$result['src2']=$first['src'];	
		echo json_encode($result);

	} else if($input->jsontype=="change"){

		$sql=$pdo->prepare("SELECT * from `images` where id = :id");

		$score1= $input->score1;
		$score2= $input->score2;

		if (($score1==1&&$score2==0)||($score1==0&&$score2==1)){
			$sql->execute(array(':id'=>$input->id1));
			$first = $sql->fetch(PDO::FETCH_ASSOC);
			$rating1 = $first['rating'];
			$matches1 = $first['matches'];
	
			$sql->execute(array(':id'=>$input->id2));
			$second= $sql->fetch(PDO::FETCH_ASSOC);
			$rating2 = $second['rating'];
			$matches2 = $second['matches'];
	
			$k1 = klogic($rating1,$matches1);	
			$k2 = klogic($rating2,$matches2);	
	
			$matches1++;
			$matches2++;
	
			$power=($rating2-$rating1)/400;
			$ten = pow(10,$power);
			$exp1 = 1/(1+$ten);
			$exp2 = 1-$exp1;
	
			$newrating1 =round($rating1 + $k1*( $score1 - $exp1));
			$newrating2 =round($rating2 + $k2*( $score2 - $exp2));

			$ins=$pdo->prepare("update `images` set matches = :matches, rating = :ratings where id = :id;");
			$ins->execute(array(':id'=>$input->id1, ':matches'=>$matches1,':ratings'=>$newrating1));
			$ins->execute(array(':id'=>$input->id2, ':matches'=>$matches2,':ratings'=>$newrating2));
				
			if ($score1==1){
				$result=array();

				$sel=$pdo->prepare("select * from `images` where id = :id");
				$sel->execute(array(':id'=>$input->id1));
				$lastwinner= $sel->fetch(PDO::FETCH_ASSOC);
				$result['id1']=$lastwinner['id'];	
				$result['src1']=$lastwinner['src'];	

				do {
				$sql="SELECT * from `images` ORDER by RAND() LIMIT 1;";
				$row = $pdo->query($sql);		
				$new= $row->fetch(PDO::FETCH_ASSOC);
				}while ($new['id']==$input->id1);
			
				$result['id2']=$new['id'];	
				$result['src2']=$new['src'];	
				echo json_encode($result);
			} else {
				$result=array();

				do {
				$sql="SELECT * from `images` ORDER by RAND() LIMIT 1;";
				$row = $pdo->query($sql);		
				$new= $row->fetch(PDO::FETCH_ASSOC);
				}while ($new['id']==$input->id2);
			
				$result['id1']=$new['id'];	
				$result['src1']=$new['src'];	
	
				$sel=$pdo->prepare("select * from `images` where id = :id");
				$sel->execute(array(':id'=>$input->id2));
				$lastwinner= $sel->fetch(PDO::FETCH_ASSOC);
				$result['id2']=$lastwinner['id'];	
				$result['src2']=$lastwinner['src'];	
		
				echo json_encode($result);
			}
		}
	} else if ($input->jsontype=="rankings"){

		$sql="SELECT * from `images` ORDER by rating DESC LIMIT 10;";
		$result=array();
		$i=1;
		foreach($pdo->query($sql) as $row){
			$result['r'.$i]=$row['src'];
			$i++;
		}
		echo json_encode($result);
	}

} else {

	http_response_code(405);

}

?>
