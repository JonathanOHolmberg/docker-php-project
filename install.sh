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


# Create uninstall script
cat << 'EOF' > uninstall.sh
#!/bin/bash
echo "Are you sure? (y/n)"
read answer
if [ "$answer" != "${answer#[Yy]}" ]; then
    # Stop and remove Docker containers, volumes, and images
    docker-compose down -v --rmi all

    # Remove .env file from root
    rm -f .env

    # Remove scripts from root
    rm -f run_tests.sh uninstall.sh

    echo "Uninstallation complete."
else
    echo "Uninstallation cancelled."
fi
EOF
chmod +x uninstall.sh
echo "uninstall.sh script created successfully."


echo "Installing Composer..."
docker run --rm -v "$PWD/src/api:/app" composer install
docker run --rm -v "$PWD/src/app:/app" composer install
echo "Composer installed."


echo "Runnable scripts:"
echo "docker-compose up --build: Build and run the application"
echo "docker-compose down: Stop and remove the application"
echo "./uninstall.sh: Uninstall the application"