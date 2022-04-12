<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Binance\Trade;
use App\Models\Binance\Order;
use Http;

class BinanceControllerMyTradesWithOrderQuantity extends BinanceController
{
    public function index($symbol)
    {
        $tradesData = (new BinanceControllerMyTrades)->index($symbol);
        
        $trades = [];

        foreach($tradesData as $item) {
            $trade = new Trade();
            $trade->fill($item);

            $orderData = (new BinanceControllerOrder)->show($trade->symbol, $trade->orderId);
            
            $order = new Order();
            $order->fill($orderData);
            
            $trade->setRelation('order', $order);

            array_push($trades, $trade);
        }

        $quantity = 0;
        $arrayQuantity = [];

        foreach($trades as $trade) {

            if($trade->order->side == "BUY") {
                $quantity += $trade->qty;
                array_push($arrayQuantity, ["time" => date('m/d/Y', $trade->time / 1000), "type" => "+BUY", "qty" => $trade->qty, "sumQty" => $quantity]);
            } else if($trade->order->side == "SELL") {
                $quantity -= $trade->qty;
                array_push($arrayQuantity, ["time" => date('m/d/Y', $trade->time / 1000), "type" => "-SELL", "qty" => $trade->qty, "sumQty" => $quantity]);
            }
        }

        dd($arrayQuantity);
        return json_encode($arrayQuantity);
    }
}
