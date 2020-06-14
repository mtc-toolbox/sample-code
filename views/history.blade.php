<div class="card shadowed total-row">
    <div class="row">
        <div class="col-12">
            <h2>
                Total
            </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Deposit:
        </div>
        <div class="col-3 right-justified">
            ${{$currentSession['Deposit'] ?? '0.00'}}
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Income:
        </div>
        <div class="col-3 right-justified">
            ${{$currentSession['Incoming'] ?? '0.00'}}
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Expenses:
        </div>
        <div class="col-3 right-justified">
            ${{$currentSession['Expenses'] ?? '0.00'}}
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            Balance:
        </div>
        <div class="col-3 right-justified">
            ${{$currentSession['Balance'] ?? '0.00'}}
        </div>
    </div>
</div>
<div class="card shadowed expenses-row">
    <div class="row">
        <div class="col-12">
            <h2>
                Expenses
            </h2>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <b>Date</b>
        </div>
        <div class="col-3">
            <b>Cart</b>
        </div>
        <div class="col-2">
            <b>Tax</b>
        </div>
        <div class="col-3">
            <b>Total</b>
        </div>
    </div>
    @foreach($orderList as $order)
        <div class="row">
            <div class="col-4 order-time border-right border-left-0 border-bottom" time="{{$order['BTime']}}">
            </div>
            <div class="col-3 right-justified  border-right border-left-0 border-bottom">
                ${{$order['Total']}}
            </div>
            <div class="col-2 right-justified border-right border-left-0 border-bottom">
                ${{$order['DeliveryTax']}}
            </div>
            <div class="col-3 right-justified total-amount border-bottom"
                 amount="{{$order['Total'] + $order['DeliveryTax']}}">
            </div>
        </div>
    @endforeach
</div>
<script>
  var serverCart = {!! $currentCart !!};
  var clientCart = Cart.getServerInstance(serverCart);

  setMenuCartCount();

  $('.order-time').each(function (i, item) {
    $(item).html(formatTime($(item).attr('time')));
  });
  $('.total-amount').each(function (i, item) {
    $(item).html('$' + parseFloat($(item).attr('amount')).toFixed(2));
  });
</script>
