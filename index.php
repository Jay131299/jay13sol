<!DOCTYPE html>
<head>
    <title>Xkcd-Subscription</title>
	<style>
			body {
				background-color: black;
			}

			#title-Tag-Line {
				font-size: 20px;
			}
			
			.form {
				background: #fff;
				box-shadow: 0 30px 60px 0 rgba(90, 116, 148, 0.4);
				border-radius: 5px;
				max-width: 480px;
				margin-top: 220px;
				margin-left: auto;
				margin-right: auto;
				padding-top: 5px;
				padding-bottom: 5px;
				left: 0;
				right: 0;
				position: absolute;
				border-top: 5px solid #0e3721;
				z-index: 1; 
				
			}
			::-webkit-input-placeholder {
				font-size: 1.3em;
			}

			.title{
				display: block;
				font-family: sans-serif;
				margin: 10px auto 5px;
				width: 300px;
			}
			.termsConditions{
				margin: 0 auto 5px 80px;
			}

			.pageTitle{
				font-size: 2em;
				font-weight: bold;
			}
			.secondaryTitle{
				color: grey;
			}

			.name {
				background-color: #ebebeb;
			}
			.name:hover {
				border-bottom: 5px solid #0e3721;
				height: 30px;
				width: 380px;
				transition: ease 0.5s;
			}

			.email {
				background-color: #ebebeb;
				height: 2em;
			}

			.message {
				background-color: #ebebeb;
				overflow: hidden;
				height: 10rem;
			}

			.message:hover {
				border-bottom: 5px solid #0e3721;
				height: 12em;
				width: 380px;
				transition: ease 0.5s;
			}

			.formEntry {
				display: block;
				margin: 30px auto;
				min-width: 300px;
				padding: 10px;
				border-radius: 2px;
				border: none;
				transition: all 0.5s ease 0s;
			}

			.submit {
				width: 200px;
				color: white;
				background-color:black;
				font-size: 20px;
			}

			.submit:hover {
				box-shadow: 15px 15px 15px 5px rgba(78, 72, 77, 0.219);
				transform: translateY(-3px);
				width: 300px;
				border-top: 5px solid #0e3750;
				border-radius: 0%;
			}

			@keyframes bounce {
			0% {
				tranform: translate(0, 4px);
			}
			50% {
				transform: translate(0, 8px);
			}
			}
			span {
				padding:10px;
				color: red;
				margin-left: 30%;
			}
	</style>
	<script>
		function checkEmail()   
		{   
			var formate_mail = /^[a-zA-Z0-9\-_]+(\.[a-zA-Z0-9\-_]+)*@[a-z0-9]+(\-[a-z0-9]+)*(\.[a-z0-9]+(\-[a-z0-9]+)*)*\.[a-z]{2,4}$/;
			var email= document.getElementById('email').value;
				if (formate_mail.test(email)) {
					return true;
				} else {
					document.getElementById("emailErr").innerHTML = "<br>Enter valid Email";
					return false;
				}
		}
	</script>
</head>
<body>
	<div class="wrapper">

	  	<form class="form" action="Subscriber.php" onsubmit="return checkEmail()" method="POST">

		  	<?php if (!empty($_COOKIE['subscribed'])) { echo '<div class="secondaryTitle title">'.$_COOKIE['subscribed'].'</div><br>'; } ?>
			<div class="pageTitle title">Subscribe Your Self </div>
			<div class="secondaryTitle title">Enter The Email Address</div>
			<input type="email" name="email" class="name formEntry" placeholder="Email" id="email" />
			<span id="emailErr" class="secondaryTitle title"></span>
			<button class="submit formEntry" onclick="thanks()">Submit</button>

	  	</form>
	</div>
  </body>
</html>

<?php 
include __DIR__.'/Subscriber.php';

$subs = new Subscriber();

if (!empty($_GET['tokenAccess']) && !empty($_COOKIE['token']) && !empty($_COOKIE['user'])) {
	$user_email = filter_var($_COOKIE['user'], FILTER_SANITIZE_EMAIL);
    $token = str_replace("'", "", $_GET['tokenAccess']);
    if ($_COOKIE['token'] === $token) {
        try {
            if ($subs->checkEmailExist($user_email)){
                $unSubToken = bin2hex(random_bytes(18));
                $unSubToken .= time().date('Ymd',time());
                
                $addNewSub = $subs->connection->prepare('INSERT INTO subscriber_list (subs_email, unsub) VALUES (?,?)');
                $addNewSub->bind_param('ss', $user_email, $unSubToken);
                $addNewSub->execute(); ?>
	            <h2>You have successfuly verified the Mail</h2>   
	        <?php  
            } else { ?>
                <h2>You have already verified the Mail !</h2>
            <?php
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        setcookie('token', time()-1);
        setcookie('user', time()-1);
	} 
}
?>