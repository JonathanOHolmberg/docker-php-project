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
