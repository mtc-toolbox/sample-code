<div class="card shadowed">
    <div class="row">
        @foreach($productList as $product)
            <div class="col-12 col-md-6 col-lg-4 log-xl-3 col-xs-12">
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
                    <input type="number" class="input-spinner original-input" value="0" min="0" max="100"
                           step="{{$product['InputStep']}}"
                           data-decimals="{{$product['Decimals']}}"
                           product-id="{{ $product['Id'] }}">
                    <div class="input-group">
                        <button type="button" class="btn btn-success add-button" product-id="{{ $product['Id'] }}"
                                product-price="{{$product['Cost']}}" disabled>
                            Add to cart
                        </button>
                    </div>
                    <div class="input-group rating-container">
                        <input class="rating" name="Rate" type="number" value="{{(int)round($product['AverageRate'])}}"
                               data-icon-lib="fa" data-active-icon="fa-star" data-inactive-icon="fa-star-o"
                               product-id="{{ $product['Id'] }}"
                        />
                        <div
                                class="rating-shadow"
                                product-id="{{ $product['Id'] }}"
                                {{!isset($product['RateId']) ? 'style=display: none' : 'style=display:block'}}
                        ></div>
                    </div>
                    <div class="rating-amount-container" product-id="{{ $product['Id'] }}">
                        {{round($product['AverageRate'], 2)}} / {{$product['RateCount']}}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<script>

  //var serverCart = null;
  //localStorage.clear('cart');
  //var clientCart = Cart.getInstance();

  var serverCart = {!! $currentCart !!};
  var clientCart = Cart.getServerInstance(serverCart);

  var serverCartUpdateUrl = '/site/cart';

  setMenuCartCount();

  $("input.input-spinner").inputSpinner();

  $('input.rating').rating({
    clearable: false
  });

  $('input.original-input').on('change input keyUp', function () {
    let productId = parseInt($(this).attr('product-id'));
    let productQty = parseFloat($(this).val());
    let addButton = $('button.add-button[product-id=' + productId + ']');
    if (productQty > 0) {
      addButton.removeAttr('disabled');
    } else {
      addButton.attr('disabled', '');
    }
  });
  $('.add-button').on('click', function () {
    let product = new Product();
    product.id = parseInt($(this).attr('product-id'));
    product.price = parseFloat($(this).attr('product-price'));
    let productQtyField = $('.input-spinner[product-id=' + product.id + ']');
    let productQty = parseFloat(productQtyField.val());
    product.qty = productQty;

    let oldCart = Cart.getClientCard();

    clientCart.add(product, productQty);

    let data = {
      cart: Cart.getClientCard()
    };

    $.post(
      serverCartUpdateUrl,
      data
    ).done(function (data) {

      if (!data.code) {
        clientCart = Cart.getServerInstance(data.data);

        setMenuCartCount();
        productQtyField.val(0);
        let addButton = $('button.add-button[product-id=' + product.id + ']');
        addButton.attr('disabled', '');
      }
    }).error(function () {
      clientCart = Cart.getServerInstance(oldCart);
    });
  });

    $('.rating').on('change', function () {
      let data = {
        id: $(this).attr('product-id'),
        vote: $(this).val(),
        cart: Cart.getClientCard()
      };
      $.post(
        'site/vote',
        data
      ).done(function (data) {
        if (!data.code) {
          let productRates = data.data;
          let rateText = $('.rating-amount-container[product-id=' + productRates.Id + ']');
          rateText.html((Math.round(productRates.AverageRate * 100) / 100) + ' / ' + productRates.RateCount);
          let rateInput = $('.rating[product-id=' + productRates.Id + ']');
          rateInput.val(Math.round(productRates.AverageRate));

          let rateShadow = $('.rating-shadow[product-id=' + productRates.Id + ']');
          rateShadow.css('display', 'block');
        }
      });
    })
</script>
