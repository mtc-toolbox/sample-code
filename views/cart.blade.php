<div class="card shadowed">
    <div class="row">
        @foreach($productList as $product)
            <div class="col-12 col-md-6 col-lg-4 log-xl-3 col-xs-12 product-card" product-id="{{ $product['Id'] }}">
                <div class="image-preview-container">
                    <img src="{{'/images/'.$product['Filename'] ?? '/images/no-image.png'}}"
                         title="{{$product['Title'] ?? 'No image'}}">
                    <div class="product-code">
                        {{$product['Code'] ?? ''}}
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col product-list-name align-content-start">
                                {{ $product['Name'] ?? '' }}
                            </div>
                            <div class="col product-list-cost align-content-end">
                                ${{ $product['Cost'] ?? '0.00' }}
                                {{ isset($product['Short']) ? '/ '.$product['Short']: '' }}
                            </div>
                        </div>
                    </div>
                    <input type="number" class="input-spinner original-input" value="{{$product['Quantity']}}"
                           min="{{$product['InputStep'] ?? 1}}"
                           max="100"
                           step="{{$product['InputStep']}}"
                           data-decimals="{{$product['Decimals']}}"
                           product-id="{{ $product['Id'] }}"
                           product-price="{{$product['Cost']}}"
                    >
                    <div class="input-group">
                        <button type="button" class="btn btn-danger delete-button" product-id="{{ $product['Id'] }}"
                                product-price="{{$product['Cost']}}">
                            Remove from cart
                        </button>
                    </div>
                    <div class="input-group rating-container">
                        <input class="rating" name="Rate" type="number" value="{{(int)round($product['AverageRate'])}}"
                               data-icon-lib="fa" data-active-icon="fa-star" data-inactive-icon="fa-star-o"
                               product-id="{{ $product['Id'] }}"
                               data-readonly
                        />
                    </div>
                    <div class="rating-amount-container" product-id="{{ $product['Id'] }}">
                        {{round($product['AverageRate'], 2)}} / {{$product['RateCount']}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="card shadowed delivery-row">
    <div class="row">
        <div class="col-12">
            <h3>Delivery</h3>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @foreach($deliveryData as $delivery)
                <input type="radio" name="Delivery" value="{{$delivery['Id']}}"
                       cost="{{$delivery['Cost']}}">{{$delivery['Name']}}
                (${{$delivery['Cost']}})
            @endforeach
            <div id="delivery-message">
                Please select a delivery type.
            </div>
        </div>
    </div>
</div>
<div class="card shadowed total-row">
    <div class="row">
        <div class="col-12">
            <h2>Total</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Order:
        </div>
        <div class="col-3 right-justified" id="order-sum">
            0.00
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Delivery:
        </div>
        <div class="col-3 right-justified" id="order-delivery">
            0.00
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Total:
        </div>
        <div class="col-3 right-justified" id="order-total">
            0.00
        </div>
    </div>
</div>

<div class="card shadowed buy-row">
    <div class="row">
        <div class="col-12" id="pay-error">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 log-xl-3 col-xs-12">
            <div class="input-group">
                <button type="button" class="btn btn-danger cart-action">
                    Clear cart
                </button>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4 log-xl-3 col-xs-12">
            <div class="input-group">
                <button type="button" class="btn btn-success cart-action">
                    Pay Now!
                </button>
            </div>
        </div>
    </div>
</div>

<script>

  var deliveryTypeErrorMessage = 'Please select a delivery type.';

  var serverCart = {!! $currentCart !!};
  var clientCart = Cart.getServerInstance(serverCart);

  var serverUpdateCartUrl = '/site/cart';
  var serverProcessCartUrl = '/cart/process';
  var serverClearCartUrl = '/cart/clear';
  var serverShowHistoryUrl = '/cart/history';

  var anyThingMessage = 'Anything went wrong';

  setMenuCartCount();

  $("input.input-spinner").inputSpinner();

  $('input.rating').rating({
    clearable: false
  });

  $('input.original-input[type=number]').on('change', function () {

    let product = new Product();

    product.id = parseInt($(this).attr('product-id'));
    product.price = parseFloat($(this).attr('product-price'));

    let productQtyField = $('.input-spinner[product-id=' + product.id + ']');
    let productQty = parseFloat(productQtyField.val());

    product.qty = productQty;

    let oldCart = Cart.getClientCard();

    clientCart.update(product, productQty);

    let data = {
      cart: Cart.getClientCard()
    };

    $.post(
      serverUpdateCartUrl,
      data
    ).done(function (data) {

      if (!data.code) {
        clientCart = Cart.getServerInstance(data.data);
        calcTotals();
      } else {
        clientCart = Cart.getServerInstance(oldCart);
      }
      $('#pay-error').html('&nbsp;')
    }).error(function () {
      clientCart = Cart.getServerInstance(oldCart);
      $('#pay-error').html(anyThingMessage);
    });

  });

  $('.delete-button').on('click', function () {

    let product = new Product();
    let oldCart = Cart.getClientCard();

    product.id = parseInt($(this).attr('product-id'));
    product.price = parseFloat($(this).attr('product-price'));

    clientCart.remove(product);

    let data = {
      cart: Cart.getClientCard()
    };

    $.post(
      serverUpdateCartUrl,
      data
    ).done(function (data) {

      if (!data.code) {

        clientCart = Cart.getServerInstance(data.data);

        if (clientCart.getTotalQty() == 0) {
          window.location = '/'
        }

        $('.product-card[product-id=' + product.id + ']').remove();

        setMenuCartCount();
        calcTotals();
      }
    }).error(function () {
      clientCart = Cart.getServerInstance(oldCart);
      $('#pay-error').html(anyThingMessage);
    });

  });

  $('.btn-danger.cart-action').on('click', function () {

    let oldCart = Cart.getClientCard();

    clientCart.clearItems();

    $.get(
      serverClearCartUrl
    ).done(function (data) {

      if (!data.code) {
        window.location = '/'
      }
    }).error(function () {
      clientCart = Cart.getServerInstance(oldCart);
    });

  });

    /**
   *
   * @returns {boolean}
   */
  function checkDeliveryMessage() {
    let checkedValue = $('input[name=Delivery]:checked').val();
    if (!checkedValue) {
      $('#delivery-message').html(deliveryTypeErrorMessage);
      $('.btn-success.cart-action').attr('disabled', '');
      return false;
    }
    $('#delivery-message').html('&nbsp;');
    $('.btn-success.cart-action').removeAttr('disabled');
    return true;
  }

  $('input[name=Delivery]').on('change input click', function () {
    checkDeliveryMessage();
    calcTotals();
  });

  function calcTotals() {
    let cartSum = roundMoney(clientCart.getTotalPrice());
    $('#order-sum').html('$'+cartSum.toFixed(2));

    let deliveryObject = $('input[name=Delivery]:checked')
    let deliveryValue = '-.--'

    if (deliveryObject.val()) {
      let deliverySum = roundMoney(parseFloat(deliveryObject.attr('cost')));
      deliveryValue = deliverySum.toFixed(2);
      cartSum += deliverySum;
    }

    $('#order-delivery').html('$'+deliveryValue);

    $('#order-total').html('$'+cartSum.toFixed(2));
  }

  $('.btn-success.cart-action').on('click', function () {

    let deliveryObject = $('input[name=Delivery]:checked')

    let data = {
      cart: Cart.getClientCard(),
      taxId: deliveryObject.val(),
      taxCost: deliveryObject.attr('cost')
    };

    $.post(
      serverProcessCartUrl,
      data
    ).done(function (data) {

      clientCart.clear();

      clientCart = Cart.getServerInstance(data.data);

      if (!data.code) {

        $('#pay-error').html('&nbsp;');

        if (clientCart.getTotalQty() == 0) {
          window.location = serverShowHistoryUrl;
        }

      } else {
        $('#pay-error').html(data.message);
      }

      setMenuCartCount();
      calcTotals();

    }).error(function () {
      clientCart = Cart.getServerInstance(oldCart);
      $('#pay-error').html(anyThingMessage);
    });

  });

  checkDeliveryMessage();

  calcTotals();

</script>
