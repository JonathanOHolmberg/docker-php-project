document.getElementById('listBtn').addEventListener('click', listProducts);
document.getElementById('emptyBtn').addEventListener('click', emptyList);
document.getElementById('populateBtn').addEventListener('click', populateDatabase);

function listProducts() {
    fetch('http://localhost:8081/app.php?action=list')
        .then(response => response.json())
        .then(data => {
            console.log('Received data:', data);
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            const productList = document.getElementById('productList');
            productList.innerHTML = '';
            data.forEach(product => {
                const row = document.createElement('div');
                row.innerHTML = `
                    ${product.name} - ${product.price} EUR (${product.priceGBP} GBP) - Order Amount: ${product.orderamount}
                    <button onclick="addOrder(${product.number})">Add</button>
                    <button onclick="clearOrder(${product.number})">Clear</button>
                `;
                productList.appendChild(row);
            });
        })
        .catch(error => console.error('Error:', error));
}

function emptyList() {
    document.getElementById('productList').innerHTML = '';
}

function addOrder(productNumber) {
    fetch(`http://localhost:8081/app.php?action=add&number=${productNumber}`)
        .then(response => response.json())
        .then(() => listProducts())
        .catch(error => console.error('Error:', error));
}

function clearOrder(productNumber) {
    fetch(`http://localhost:8081/app.php?action=clear&number=${productNumber}`)
        .then(response => response.json())
        .then(() => listProducts())
        .catch(error => console.error('Error:', error));
}

function populateDatabase() {
    fetch('http://localhost:8081/app.php?action=populate')
        .then(response => response.json())
        .then(data => {
            console.log('Database populated:', data);
            listProducts(); // Refresh the product list after populating
        })
        .catch(error => console.error('Error:', error));
}
