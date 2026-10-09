<?php
// Anything that reaches a mail header must be one line. A CR or LF would let a
// visitor add headers of their own, such as a Bcc that relays mail through us.
function one_line($s) {
	return is_string($s) ? trim(str_replace(array("\r", "\n"), '', $s)) : '';
}

function back($text, $to) { ?>
	<script language="javascript" type="text/javascript">
		alert(<?php echo json_encode($text); ?>);
		window.location = <?php echo json_encode($to); ?>;
	</script>
<?php
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	header('Allow: POST');
	exit;
}

$field_name = one_line($_POST['name'] ?? '');
$field_email = one_line($_POST['email'] ?? '');
$field_message = is_string($_POST['message'] ?? null) ? trim($_POST['message']) : '';

if ($field_name === '' || $field_message === '' || !filter_var($field_email, FILTER_VALIDATE_EMAIL)) {
	back('Please fill in your name, a valid e-mail address and a message.', 'index.html#contact');
}

$mail_to = 'bjfultn@gmail.com';
$subject = 'Message from a site visitor '.$field_name;

$body_message = 'From: '.$field_name."\n";
$body_message .= 'E-mail: '.$field_email."\n";
$body_message .= 'Message: '.$field_message;

// From is fixed on our own domain; the visitor's checked address goes in Reply-To only.
$headers = "From: Website contact form <noreply@benjaminfulton.com>\r\n";
$headers .= 'Reply-To: '.$field_email."\r\n";

if (mail($mail_to, $subject, $body_message, $headers)) {
	back('Thank you for the message. We will contact you shortly.', 'index.html#contact');
}
back('Message failed. Please try again later.', 'index.html#contact');
