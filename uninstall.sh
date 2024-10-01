#!/bin/bash
echo "Are you sure? (y/n)"
read answer
if [ "$answer" != "${answer#[Yy]}" ]; then
    docker-compose down -v --rmi local
    docker rmi composer:latest

    rm -f .env
    rm -f uninstall.sh

    echo "Uninstallation complete."
else
    echo "Uninstallation cancelled."
fi
