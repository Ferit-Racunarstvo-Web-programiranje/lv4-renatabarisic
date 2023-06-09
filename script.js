// Get elements
const cartButton = document.querySelector('.cart-button');
const cartBadge = document.querySelector('.cart-badge');
const modal = document.querySelector('.modal');
const modalClose = document.querySelector('.close-modal');
const buyButton = document.querySelector('.buy-btn');
const cartItemsList = document.querySelector('.cart-items');
const cartTotal = document.querySelector('.cart-total');
const itemsGrid = document.querySelector('.items-grid');
const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');

let items = [
  {
    id: 1,
    name: 'Apple',
    price: 0.99,
  },
  {
    id: 2,
    name: 'Banana',
    price: 10,
  },
  {
    id: 3,
    name: 'Orange',
    price: 5,
  },
  {
    id: 4,
    name: 'Watermelon',
    price: 15,
  },
];

let cart = [];

// An example function that creates HTML elements using the DOM.
function fillItemsGrid() {
  const searchInput = document.getElementById('search-input');
  const sortSelect = document.getElementById('sort-select');

  searchInput.addEventListener('input', handleSearch);
  sortSelect.addEventListener('change', handleSort);

  function handleSearch() {
    const searchTerm = searchInput.value.toLowerCase();
    const filteredItems = items.filter(item => item.name.toLowerCase().includes(searchTerm));
    renderItems(filteredItems);
  }

  function handleSort() {
    const sortValue = sortSelect.value;
    let sortedItems;

    if (sortValue === 'name') {
      sortedItems = items.slice().sort((a, b) => a.name.localeCompare(b.name));
    } else if (sortValue === 'price') {
      sortedItems = items.slice().sort((a, b) => a.price - b.price);
    } else {
      sortedItems = items;
    }

    renderItems(sortedItems);
  }

  renderItems(items);
}

function renderItems(itemsToRender) {
  itemsGrid.innerHTML = '';

  for (const item of itemsToRender) {
    let itemElement = document.createElement('div');
    itemElement.classList.add('item');
    itemElement.innerHTML = `
      <img src="https://picsum.photos/200/300?random=${item.id}" alt="${item.name}">
      <h2>${item.name}</h2>
      <p>$${item.price}</p>
      <button class="add-to-cart-btn" data-id="${item.id}">Add to cart</button>
    `;
    itemsGrid.appendChild(itemElement);
  }

  const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
  addToCartButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const itemId = parseInt(button.dataset.id);
      addToCart(itemId);
    });
  });
}

// Adding the .show-modal class to an element will make it visible
// because it has the CSS property display: block; (which overrides display: none;)
// See the CSS file for more details.
function toggleModal() {
  modal.classList.toggle('show-modal');
}

// Call fillItemsGrid function when page loads
fillItemsGrid();

// Example of DOM methods for adding event handling
cartButton.addEventListener('click', toggleModal);
modalClose.addEventListener('click', toggleModal);
buyButton.addEventListener('click', () => {handleBuy();});

function addToCart(itemId) {
  const item = items.find((item) => item.id === itemId);
  if (item) {
    cart.push(item);
    updateCart();
  }
}

function updateCart() {
  cartItemsList.innerHTML = '';
  let total = 0;
  const cartItemMap = new Map();

  for (const item of cart) {
    if (cartItemMap.has(item.id)) {
      const existingItem = cartItemMap.get(item.id);
      existingItem.quantity++;
    } else {
      cartItemMap.set(item.id, { item, quantity: 1 });
    }
    total += item.price * (item.quantity || 1);
  }

  cartBadge.innerText = Array.from(cartItemMap.values()).reduce((acc, { quantity }) => acc + quantity, 0);

  for (const cartItem of cartItemMap.values()) {
    const { item, quantity } = cartItem;
    const cartItemElement = document.createElement('li');

    const removeButton = document.createElement('button');
    removeButton.classList.add('remove-item-btn');
    removeButton.setAttribute('data-id', item.id);
    removeButton.innerText = 'Remove Item';
    removeButton.addEventListener('click', removeFromCart);

    cartItemElement.innerText = `${item.name} x ${quantity} - $${(item.price * quantity).toFixed(2)}`;
    cartItemElement.appendChild(removeButton);

    cartItemsList.appendChild(cartItemElement);
  }

  cartTotal.innerText = `$${total.toFixed(2)}`;
}

function handleBuy() {
  if (cart.length === 0) {
    showModalMessage('Cart is empty. Add items before buying.', false);
  } else {
    cart = [];
    updateCart();
    showModalMessage('Purchase successful!', true);
  }
}

function showModalMessage(message, success) {
  const purchaseMessage = document.getElementById('purchase-message');
  purchaseMessage.textContent = message;

  if (success) {
    purchaseMessage.classList.remove('error');
    purchaseMessage.classList.add('success');
  } else {
    purchaseMessage.classList.remove('success');
    purchaseMessage.classList.add('error');
  }
}

function removeFromCart(event) {
  const itemId = Number(event.target.getAttribute('data-id'));
  cart = cart.filter(item => item.id !== itemId);
  updateCart();
}