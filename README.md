Shift4 & ACI Payment Integration in Symfony
--------------------------------------------
🚀 Installation & Setup
git clone
cd 
composer install
cp .env.example .env and edit .env file with your data (SHIFT4_API_KEY , ACI_API_KEY , ACI_ENTITY_ID)
php bin/console cache:clear
php app/console server:run
-----------------------------------------
📌 API Endpoints
POST /app/example/{provider} (shift4  - aci )
------------------------------------------
📌 Running Payments via CLI
php bin/console app:example shift4 100 USD 4111111111111111 2025 12 123 
or
php bin/console app:example aci 92.00 EUR 4200000000000000 2034 05 123
---------------------------------------------------
🛠 Running Tests
php bin/phpunit 
