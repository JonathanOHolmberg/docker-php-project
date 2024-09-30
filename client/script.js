document.getElementById('listBtn').addEventListener('click', listProducts);
document.getElementById('emptyBtn').addEventListener('click', emptyList);
document.getElementById('populateBtn').addEventListener('click', populateDatabase);

function listProductsWithRetry(retries = 5) {
    return new Promise((resolve, reject) => {
        function attempt() {
            fetch('http://localhost:8081/app.php?action=list')
                .then(response => response.json())
                .then(data => {
                    console.log('Received data:', data);
                    resolve(data);
                })
                .catch(error => {
                    if (retries > 0) {
                        console.log(`Retrying... Attempts left: ${retries}`);
                        setTimeout(() => {
                            retries--;
                            attempt();
                        }, 1000);
                    } else {
                        reject(error);
                    }
                });
        }
        attempt();
    });
}

function listProducts() {
    const productList = document.getElementById('productList');
    productList.innerHTML = '<p>Loading...</p>';

    listProductsWithRetry()
        .then(data => {
            productList.innerHTML = '';

            if (data.error) {
                productList.innerHTML = `<p>Error: ${data.error}</p>`;
                return;
            }

            let bodyData;
            try {
                bodyData = JSON.parse(data.body);
            } catch (e) {
                bodyData = data.body;
            }

            if (Array.isArray(bodyData) && bodyData.length === 0) {
                productList.innerHTML = '<p>Database is empty</p>';
            } else if (typeof bodyData === 'string') {
                productList.innerHTML = `<p>${bodyData}</p>`;
            } else if (Array.isArray(bodyData)) {
                bodyData.forEach(product => {
                    const row = document.createElement('div');
                    row.id = `product-${product.number}`;
                    row.setAttribute('data-product-number', product.number);
                    row.innerHTML = `
                        ${product.name} - ${product.price} EUR (${product.priceGBP} GBP) - Order Amount: <span class="order-amount">${product.orderamount}</span>
                        <button onclick="addOrder(${product.number})">Add</button>
                        <button onclick="clearOrder(${product.number})">Clear</button>
                    `;
                    productList.appendChild(row);
                });
            } else {
                productList.innerHTML = '<p>Unexpected data format</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('productList').innerHTML = `<p>Error: ${error.message}</p>`;
        });
}

function emptyList() {
    document.getElementById('productList').innerHTML = '';
}

function addOrder(productNumber) {
    fetch(`http://localhost:8081/app.php?action=add&number=${productNumber}`)
        .then(response => response.json())
        .then(data => {
            console.log('Add order response:', data);
            if (data.body) {
                updateProductRow(JSON.parse(data.body));
            } else {
                console.error('Unexpected response format:', data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function clearOrder(productNumber) {
    fetch(`http://localhost:8081/app.php?action=clear&number=${productNumber}`)
        .then(response => response.json())
        .then(data => {
            console.log('Clear order response:', data);
            if (data.body) {
                updateProductRow(JSON.parse(data.body));
            } else {
                console.error('Unexpected response format:', data);
            }
        })
        .catch(error => console.error('Error:', error));
}

function updateProductRow(product) {
    const row = document.getElementById(`product-${product.number}`);
    if (row) {
        const orderAmountSpan = row.querySelector('.order-amount');
        if (orderAmountSpan) {
            orderAmountSpan.textContent = product.orderamount;
        } else {
            console.error('Order amount span not found');
        }
    } else {
        console.error(`Product row not found for product number ${product.number}`);
    }
}

function populateDatabase() {
    fetch('http://localhost:8081/app.php?action=populate')
        .then(response => response.json())
        .then(data => {
            console.log('Database population result:', data);
            if (data.error) {
                console.error('Error populating database:', data.error);
            } else {
                console.log('Database populated successfully');
                listProducts();
            }
        })
        .catch(error => console.error('Error:', error));
}
