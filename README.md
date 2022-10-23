Docker deployment  

Host environment: AWS EC2 Ubuntu 22.04  
Deployed url in https: https://ierg4210.luciuswong.com  
Location of docker-compose: /srv/www/ierg4210  

---  

PayPal sandbox account:  

Business Email id: sb-s8tzs21136414@business.example.com  
System-generated password: /E5+gfx*  

Personal Email id: sb-uzsni21108113@personal.example.com  
System-generated password: }h5dZ#Dd  

Paypal Credit card numbers for testing  

Card Type Number  
American Express 378282246310005 American Express 371449635398431 American Express Corporate 378734493671000 Diners Club 30569309025904 Discover 6011111111111117 Discover 6011000990139424 JCB 3530111333300000 JCB 3566002020360505 Mastercard 2221000000000009 Mastercard 2223000048400011 Mastercard 2223016768739313 Mastercard 5555555555554444 Mastercard 5105105105105100 Visa 4111111111111111 Visa 4012888888881881 Visa 4222222222222

---  

Useful Certbot commands  

Dry run for certificates

```docker compose run --rm  certbot certonly --webroot --webroot-path /var/www/certbot/ --dry-run -d ierg4210.luciuswong.com```

Renew certificates

```$ docker compose run --rm certbot renew```

--- 

Referred documentation

https://mindsers.blog/post/https-using-nginx-certbot-docker/