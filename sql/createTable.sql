USE emails;
CREATE TABLE IF NOT EXISTS successful_emails (
	id int NOT NULL AUTO_INCREMENT,
	affiliate_id int NOT NULL, #the list s affiliate ID, ignore
	envelope text NOT NULL, #field from the Sendgrid webook, ignore
	`from` varchar(255) NOT NULL, #field from the Sendgrid webook, ignore
	subject text NOT NULL, #field from the Sendgrid webook, ignore
	dkim varchar(255) DEFAULT NULL, #field from the Sendgrid webook, ignore
	SPF varchar(255) DEFAULT NULL, #field from the Sendgrid webook, ignore
	spam_score float DEFAULT NULL, #field from the Sendgrid webook, ignore
	email longtext NOT NULL, #the raw email payload, including headers. parse this
	raw_text longtext NOT NULL, #save the plain text content in this column
	sender_ip varchar(50) DEFAULT NULL, #field from the Sendgrid webook, ignoreto 
	`to` text NOT NULL, #field from the Sendgrid webook, ignore
	timestamp int NOT NULL, #incoming timestamp of the email, ignore
	PRIMARY KEY (id),
	KEY affiliate_index (affiliate_id)
);
