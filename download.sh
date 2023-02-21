wget https://github.com/PHPMailer/PHPMailer/archive/master.zip
unzip master.zip
mv PHPMailer-master phpmailer
cd phpmailer
rm VERSION SECURITY.md README.md LICENSE get_oauth_token.php composer.json COMMITMENT master.zip
rm -r language/
