Instructions to run the application:

Step 1: Install the application

```bash
chmod +x install.sh
./install.sh
```

This installs a env enviroment and other necessary dependencies.

Step 2: Start/stop the application

To start/stop the application, use the following command:

```bash
docker-compose up --build
docker-compose down
```

Step 3: Uninstall the application

To uninstall the application, use the following command (this will remove the containers, volumes, and networks):

```bash
./uninstall.sh
```
