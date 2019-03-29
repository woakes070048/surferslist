<?php
// Heading
$_['heading_title']      = 'Contact';
$_['meta_keyword']       = 'contact form, private message';
$_['meta_description']   = 'Contact the owner of any profile on SurfersList and send a private message via email.';

// Text
$_['text_members']   = 'Profiles';
$_['text_intro']     = 'Email a private message to a <a href="%s">profile</a>.';
$_['text_footer']  	 = '<a href="%s" title="%s" class="grey-text">Public Discussions</a>';
$_['text_listing_questions']   = 'Discuss any listing or profile';
$_['text_location']  = 'Contact Info';
$_['text_contact']   = 'Private Message';
$_['text_from']      = 'From:';
$_['text_address']   = 'Address:';
$_['text_name']      = 'Name:';
$_['text_email']     = 'E-Mail:';
$_['text_email_reply'] = 'Reply-To:';
$_['text_message_body'] = 'Message:';
$_['text_telephone'] = 'Telephone:';
$_['text_fax']       = 'Fax:';
$_['text_message']   = 'Your message has been sent! Thank you.';
$_['text_message_recipient']   = 'Your message has been sent to <a href="%s">%s</a>!<br />Thank you.';
$_['text_message_listing']   = <<<EOT
I have a question about the listing "%s".

---

%s
EOT;
$_['text_message_profile']   = <<<EOT
I would like to claim the profile "%s".

---

%s
EOT;

// Entry
$_['entry_member']   = 'Profile Name';
$_['entry_name']     = 'Your Name';
$_['entry_email']    = 'Your E-Mail';
$_['entry_message']  = 'Your Message';
$_['button_submit']  = 'Send';

// Help
$_['help_member']   = 'Enter a Profile Name';
$_['help_member_info'] = 'Type at least 3 letters and select a profile from the drop-down list.';
$_['help_admin']    = 'Leave empty to contact SurfersList.';
$_['help_name']     = 'Enter your name';
$_['help_email']    = 'Enter your e-mail';
$_['help_message']  = 'Enter your message';
$_['help_unauthorized'] = '<a href="%s">Log in</a> or <a href="%s">register</a> to message faster.';

// Email
$_['email_subject']  = '[%s] New Message from %s';
$_['email_intro']    = 'A message has been submitted to profile %s (%s).';
$_['email_contact']  = '<a href="mailto:%1$s">%1$s</a>';
$_['email_do_not_reply'] = '*** DO NOT REPLY directly to this email ***';
$_['email_reply']    = 'Please reply to the "REPLY-TO" e-mail address listed above, or send a private message using the contact form at: %s.';
$_['email_message']   = <<<EOT
A new private message has been submitted using the SurfersList contact form.

TO: %s (%s)

FROM: %s (%s)

MESSAGE:
%s

EOT;

// Errors
$_['error_warning']    = 'Check the form carefully for errors!';
$_['error_member']     = 'Please select a valid profile!';
$_['error_member_email'] = 'Sorry, profile selected can not receive private messages!';
$_['error_name']       = 'Name must be between %s - %s characters!';
$_['error_email']      = 'E-Mail does not appear to be valid!';
$_['error_message']    = 'Message must be between %s and %s characters!';
$_['error_too_fast']   = 'Too fast! Please wait %s seconds and try again, or <a href="%s">login</a> to relax this restriction.';
$_['error_timeout']    = 'Time expired! Please wait %s seconds and try again, or <a href="%s">login</a> to relax this restriction.';


