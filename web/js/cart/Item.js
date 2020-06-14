/*
Shopping Cart Library by Danny Kay
All Rights Reserved
dannykay.developer@gmail.com
*/

var Item = {

  itemInstance: function (item) {
    return new ItemInstance(item);
  }
}

function ItemInstance(item) {
  this.qty = 0;
  this.id = item.id;
  this.price = item.price;
  this.item = item;

}
