#!/bin/bash


cat << EOF > .env
CURRENCY_API_KEY=6841aa872019a2b92027a6a73a07a903
DB_HOST=db
DB_NAME=alkodb
DB_USER=user
DB_PASSWORD=password
DB_ROOT_PASSWORD=rootpassword
ALKO_URL=https://www.alko.fi/INTERSHOP/static/WFS/Alko-OnlineShop-Site/-/Alko-OnlineShop/fi_FI/Alkon%20Hinnasto%20Tekstitiedostona/alkon-hinnasto-tekstitiedostona.xlsx
EOF
echo ".env file created successfully."


cat << 'EOF' > uninstall.sh
#!/bin/bash
echo "Are you sure? (y/n)"
read answer
if [ "$answer" != "${answer#[Yy]}" ]; then
    docker-compose down -v --rmi local
    docker rmi composer:latest

    rm -f .env
    rm -f run_tests.sh uninstall.sh
    rm -f populate_db.sh

    echo "Uninstallation complete."
else
    echo "Uninstallation cancelled."
fi
EOF
chmod +x uninstall.sh
echo "uninstall.sh script created successfully."


echo "Installing Composer..."
docker run --rm -v "$PWD/api:/app" composer install
docker run --rm -v "$PWD/app:/app" composer install
echo "Composer installed."


cat << 'EOF' > populate_db.sh
#!/bin/bash
docker-compose exec app php /var/www/html/app.php action=populate
EOF
chmod +x populate_db.sh
echo "populate_db.sh script created successfully."


echo "Runnable scripts:"
echo "docker-compose up --build: Build and run the application"
echo "docker-compose down: Stop and remove the application"
echo "./uninstall.sh: Uninstall the application"