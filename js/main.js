axios
  .get("https://api.example.com/products")
  .then((res) => {
    const products = res.data;
    renderProducts(products);
  })
  .catch((err) => console.error(err));

function renderProducts(products) {
  const container = document.getElementById("product-list");
  container.innerHTML = "";
  products.forEach((p) => {
    container.innerHTML += `
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card">
                <img src="${p.image}" class="card-img-top" alt="${p.name}">
                <div class="card-body">
                    <h5 class="card-title">${p.name}</h5>
                    <p class="card-text">${p.price} VNĐ</p>
                </div>
            </div>
        </div>`;
  });
}
