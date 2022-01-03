Case of Distributed Systems

It is hard to keep consisteny in distributed systems. There are vey famous theoretical problems like 
Byzatine Generals or Two Army problems about it. There are also several ideas addressing this issue.
One of the most important one is SAGA which is based on rolling back by compensating all local transactions 
in case of any failure and explained in https://microservices.io/patterns/data/saga.html

In my sample implementation there are two browser applications running on Javascript/PHP/File Based JSON Database
tech stack. The first application is Register application orchestrating credit card provisioning, campaign 
eligibility and voucher submitting. The second one is Loyalty application responsible for  generating & keeping 
voucher details and  applying voucher business rules.

How To Install & Run

Just download and copy all files into a folder. Implementation is so simple that runs on buit-in PHP webserver 
in public_html directory. In the project folder type

	php -S localhost:8000 -t public_html

The two applications runs on (Chrome tested) browser in the following links.

http://localhost:8000/Registry/registerHandler.php

http://localhost:8000/Loyalty/loyaltySystem.php

Application Functionalities

First application shows all transaction log (including randomly generated provision errors and success events) and
whole content of Register Database which is all successful transaction with or without voucher. 
The scneraio starts when Start purchase button pressed in this application. 
Each time browser refreshed transaction database cleared as a simplicity. 

Second application shows current set of all valid vouchers generated. Each time browser refreshed voucher 
database cleared as a simplicity. 


Details of Transaction Flow

There are 10 transaction generated in each 5 seconds. Each transaction consists of two major steps.
The first step is voucher eligibility check and generation. The second one is bank provisioning for total amount of 
purchase. Register application starts transaction and asks for voucher eligibility and generation to Loyalty 
application by ajax request. Then Register application goes to bank for provision and fails with 20% probability.
If provision fails transaction is rolledbacked that is if there is a voucher generated it is also removed for 
Loyalty system. If nothing fails then the transaction persisted in Register Ssytem as a successful purchase.


Libraries Used

https://github.com/donjajo/php-jsondb used for JSON File Based Database.
JQuery, Google materialized icons and materialized.css functionality used in very limited sections.




