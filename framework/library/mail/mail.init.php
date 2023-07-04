<?php
/*
 * @company: 	Symbiotic Infotech Pvt. Ltd.
 * @copyright: 	© Symbiotic Infotech Pvt. Ltd. 2011
 *				All rights reserved.Any redistribution or reproduction of part
 * 				or all of the contents in any form is prohibited. You may not,
 * 				except with express written permission, distribute or
 * 				commercially exploit or personally use the content.
 * 				Nor may you transmit it or store it in any other media or
 * 				other form of electronic or physical retrieval system.
 *
 * @filename:	mail.init.php
 * @filetype:	PHP
 * @filedesc:	this file is used to instantiate the mail library
 *
 */

global $mail;

require_abs ( 'framework/library/mail/smtp.class.inc' );
require_abs ( 'framework/library/mail/mail.class.inc' );

$mail = new Mailer();
?>