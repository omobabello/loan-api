# 54Gene lite loan API 

A basic loan API with the following functionalities: 

- A user can register as a lender or a borrower.
- A user (lender or borrower) should have only one wallet to hold funds.
- A lender can top up his wallet and issue loans from this wallet. 
- A borrower can request a loan.
- A lender can view all requests and accept to offer a loan to a borrower.
- A borrower can accept or decline a loan from a lender. If the loan is accepted, the borrower's wallet is credited.

# Mails 

- Currently use mail traps to implement mailing system. So you will not recieve it in your inbox. If you want to change that, change the `MAIL` variables 
in the `docker-compose.yml` file and/or in your `.env`


# Reference. 

- For documentation, please refer to the /references folder 

# Build. 

- You can run `php artisan serve` 
- You can also use the docker command `docker-compose up --build`
