(() => {
  'use strict';
  document.querySelectorAll('form[data-confirm]').forEach(form => {
    form.addEventListener('submit', event => {
      if (!window.confirm(form.dataset.confirm || 'Continue?')) event.preventDefault();
    });
  });

  document.querySelectorAll('.flash-message').forEach(toast => {
    let timer;
    const close = () => { toast.hidden = true; };
    const start = () => { timer = window.setTimeout(close, 4000); };
    const pause = () => window.clearTimeout(timer);
    toast.addEventListener('mouseenter', pause);
    toast.addEventListener('focusin', pause);
    toast.addEventListener('mouseleave', start);
    toast.addEventListener('focusout', start);
    start();
  });

  if (document.querySelector('[data-clear-cart]')) {
    try { sessionStorage.removeItem('matchaya-cart'); } catch (_) { /* Storage can be disabled. */ }
  }
  const pos = document.querySelector('[data-pos]');
  if (!pos) return;

  const tiles = [...pos.querySelectorAll('.product-tile')];
  const byId = new Map(tiles.map(tile => [tile.dataset.id, tile]));
  const cart = new Map();
  const history = [];
  const cartLines = pos.querySelector('[data-cart-lines]');
  const cartInputs = pos.querySelector('[data-cart-inputs]');
  const totalNode = pos.querySelector('[data-cart-total]');
  const chargeButton = pos.querySelector('[data-charge]');
  const cartPanel = pos.querySelector('#cart-panel');
  const cartToggle = pos.querySelector('[data-cart-toggle]');
  const undoButton = pos.querySelector('[data-undo]');
  const search = pos.querySelector('#pos-search');
  const categoryTabs = [...pos.querySelectorAll('[data-category]')];
  const money = value => '₱' + value.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  let category = 'All';

  function persist() {
    try { sessionStorage.setItem('matchaya-cart', JSON.stringify(Object.fromEntries(cart))); } catch (_) { /* Cart still works in memory. */ }
  }
  function render() {
    cartLines.replaceChildren();
    cartInputs.replaceChildren();
    let total = 0;
    if (cart.size === 0) {
      const empty = document.createElement('p');
      empty.className = 'cart-empty';
      empty.textContent = 'No items yet. Choose a product from the menu.';
      cartLines.append(empty);
    }
    for (const [id, quantity] of cart) {
      const tile = byId.get(id);
      if (!tile) continue;
      const price = Number(tile.dataset.price);
      total += price * quantity;
      const line = document.createElement('div');
      line.className = 'cart-line';
      const info = document.createElement('div');
      const name = document.createElement('strong');
      name.textContent = tile.dataset.name;
      const sub = document.createElement('small');
      sub.textContent = money(price) + ' each';
      info.append(name, sub);
      const controls = document.createElement('div');
      controls.className = 'quantity-controls';
      const minus = document.createElement('button');
      minus.type = 'button'; minus.textContent = '−'; minus.setAttribute('aria-label', 'Remove one ' + tile.dataset.name);
      minus.addEventListener('click', () => setQuantity(id, quantity - 1, false));
      const count = document.createElement('output');
      count.textContent = String(quantity);
      const plus = document.createElement('button');
      plus.type = 'button'; plus.textContent = '+'; plus.disabled = quantity >= Number(tile.dataset.stock);
      plus.setAttribute('aria-label', 'Add one ' + tile.dataset.name);
      plus.addEventListener('click', () => setQuantity(id, quantity + 1, true));
      controls.append(minus, count, plus);
      line.append(info, controls);
      cartLines.append(line);
      const hidden = document.createElement('input');
      hidden.type = 'hidden'; hidden.name = 'lines[' + id + ']'; hidden.value = String(quantity);
      cartInputs.append(hidden);
    }
    totalNode.textContent = money(total);
    chargeButton.textContent = 'Charge ' + money(total);
    cartToggle.textContent = 'View order (' + [...cart.values()].reduce((sum, quantity) => sum + quantity, 0) + ') · ' + money(total);
    chargeButton.disabled = cart.size === 0;
    undoButton.disabled = history.length === 0;
    persist();
  }
  function setQuantity(id, amount, isAdd) {
    const tile = byId.get(id);
    if (!tile || amount > Number(tile.dataset.stock) || amount > 99) return;
    const before = cart.get(id) || 0;
    if (amount <= 0) cart.delete(id); else cart.set(id, amount);
    if (isAdd && amount > before) history.push(id);
    render();
  }
  for (const tile of tiles) {
    tile.addEventListener('click', () => setQuantity(tile.dataset.id, (cart.get(tile.dataset.id) || 0) + 1, true));
  }
  undoButton.addEventListener('click', () => {
    const id = history.pop();
    if (id) setQuantity(id, (cart.get(id) || 0) - 1, false);
  });
  cartToggle.addEventListener('click', () => {
    const open = cartPanel.classList.toggle('is-open');
    cartToggle.setAttribute('aria-expanded', String(open));
    if (open) {
      const heading = cartPanel.querySelector('h2');
      heading.setAttribute('tabindex', '-1');
      heading.focus();
    } else {
      cartToggle.focus();
    }
  });
  function filterMenu() {
    const term = search.value.trim().toLocaleLowerCase();
    let shown = 0;
    for (const tile of tiles) {
      const matches = (category === 'All' || tile.dataset.category === category) && tile.dataset.name.toLocaleLowerCase().includes(term);
      tile.hidden = !matches;
      if (matches) shown++;
    }
    pos.querySelector('.no-menu-match').hidden = shown !== 0;
  }
  search.addEventListener('input', filterMenu);
  for (const tab of categoryTabs) {
    tab.addEventListener('click', () => {
      category = tab.dataset.category;
      for (const other of categoryTabs) {
        other.classList.toggle('is-active', other === tab);
        other.setAttribute('aria-pressed', other === tab ? 'true' : 'false');
      }
      filterMenu();
    });
  }
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && cartPanel.classList.contains('is-open')) {
      cartPanel.classList.remove('is-open');
      cartToggle.setAttribute('aria-expanded', 'false');
      cartToggle.focus();
    }
    if (event.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
      event.preventDefault(); search.focus();
    }
  });
  pos.querySelector('#charge-form').addEventListener('submit', event => {
    if (cart.size === 0) event.preventDefault();
  });
  try {
    const saved = JSON.parse(sessionStorage.getItem('matchaya-cart') || '{}');
    for (const [id, amount] of Object.entries(saved)) {
      if (byId.has(id) && Number.isInteger(amount) && amount > 0) cart.set(id, Math.min(amount, Number(byId.get(id).dataset.stock), 99));
    }
  } catch (_) { /* Use an empty cart when stored data is malformed. */ }
  render();
})();
