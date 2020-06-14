/*
Shopping Cart Library by Danny Kay
All Rights Reserved
dannykay.developer@gmail.com
*/

var cartIndexUrl = '/cart/';

/**
 *
 * @param cost
 * @returns {number}
 */
function roundMoney(cost) {
  return Math.round((cost + Number.EPSILON) * 100) / 100;
}

function setMenuCartCount() {
  let counter = $('#menu-cart-count');
  let count = clientCart.getItemCount();
  counter.html(count);
  if (!count) {
    $('#cart-link').removeAttr('href');
  } else {
    $('#cart-link').attr('href', cartIndexUrl);
  }
}

/**
 *
 * @param curTime
 * @returns {string}
 */
function formatTime(curTime) {
  let d = new Date(curTime * 1000);
  let formattedDate = d.toLocaleString();
  return formattedDate;
}

var Cart = {

  getInstance: function () {
    let oldCart = localStorage.getItem('cart') || null;
    let myCart = JSON.parse(oldCart);
    return new cartInstance(myCart);
  },

  getClientCard: function () {
    return localStorage.getItem('cart') || null;
  },

  getServerInstance: function (oldCart) {

    let clientCart = localStorage.getItem('cart') || null;

    let selectedCart;

    if (clientCart === null) {
      selectedCart = oldCart;
    } else {

      selectedCart = JSON.parse(clientCart);
    }

    if (Array.isArray(selectedCart.items)) {
      selectedCart.items = selectedCart.items.filter(function (item) {
        return item != null;
      })
    }

    localStorage.setItem('cart', JSON.stringify(selectedCart));

    return new cartInstance(selectedCart);
  }

}

function cartInstance(oldCart) {
  let self = this;

  this.passme = {
    items: {},
    totalQty: 0.0,
    totalPrice: 0.0,
    cartId: ''
  }

  if (oldCart !== null) {

    this.passme.items = oldCart.items;
    this.passme.totalQty = oldCart.totalQty;
    this.passme.totalPrice = oldCart.totalPrice;
    this.passme.cartId = oldCart.cartId;
  }

  this.findItemIndex = function (id) {

    let result = null;
    for (let i = 0; i < Object.keys(this.passme.items).length; i++) {
      let item = this.passme.items[i];
      if (item == undefined) {
        continue;
      }
      if (id == item.item.id) {
        result = i;
      }
    }
    return result;
  }

  this.add = function (item, qty) {

    if (item != null && qty > 0) {

      let storedItem = Item.itemInstance(item);

      let storedIndex = Object.keys(this.passme.items).length;

      if (Object.keys(this.passme.items).length > 0) {

        let oldStoredIndex = this.findItemIndex(item.id);

        if (oldStoredIndex in this.passme.items) {
          storedItem = this.passme.items[oldStoredIndex];
          storedIndex = oldStoredIndex;
        }
      }

      storedItem.qty += qty;
      storedItem.price = item.price * storedItem.qty;

      // return this.totalQty;
      this.passme.items[storedIndex] = storedItem;
      this.passme.totalPrice += roundMoney(item.price * qty);
      this.passme.totalQty += qty;
      localStorage.setItem('cart', JSON.stringify(this.passme));
    }
  }

  this.all = function () {
    if (this.passme) {
      return this.passme;
    }
  }


  this.clear = function () {
    localStorage.clear('cart');
  }

  this.clearItems = function () {
    this.passme.totalQty = 0.0;
    this.passme.totalPrice = 0.0;
    this.passme.items = {};
    localStorage.setItem('cart', JSON.stringify(this.passme));
  }

  this.remove = function (item) {
    var id = this.findItemIndex(item.id);

    if (item != null && id != undefined) {

      this.passme.totalQty -= this.getSubQty(item);
      this.passme.totalPrice -= this.getSubPrice(item);

      delete this.passme.items[id];
      localStorage.setItem('cart', JSON.stringify(this.passme));

    }
  }

  this.update = function (item, qty) {
    if (qty > 0 && item != null && item.id in this.passme.items) {

      this.remove(item);
      this.add(item, qty);

    }
  }

  this.get = function (item) {
    if (Object.keys(this.passme.items).length > 0 && item != null) {
      return this.passme.items[this.findItemIndex(item.id)];
    }
    return null;
  }

  this.getTotalQty = function () {
    return this.passme.totalQty;
  }

  this.getItemCount = function () {
    let keys = Object.keys(this.passme.items);
    let count = keys.length;

    if (!count) {
      count = 0;
    }

    return count;
  }

  this.getTotalPrice = function () {
    return this.passme.totalPrice;
  }

  this.getSubQty = function (item) {
    if (item != null) {
      return this.passme.items[this.findItemIndex(item.id)].qty;
    }
    return 0.0;
  }

  this.getSubPrice = function (item) {
    if (Object.keys(this.passme.items).length > 0 && item != null) {
      return this.passme.items[this.findItemIndex(item.id)].price;
    }
    return 0.0;
  }

}

