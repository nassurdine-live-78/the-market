<?php

    namespace App\Controllers;

    use Joshua\Core\Controller;
    use Joshua\Core\View;

    use App\Models\Address;
    use App\Models\Cart;
    use App\Models\Category;
    use App\Models\Order;
    use App\Models\OrderAddress;
    use App\Models\Product;

    class OrderController extends Controller
    {
        public function __construct()
        {
            
        }

        public function ordersActivity()
        {
            $itemcount = 0;

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);

                
                $orders = Order::getAllByUserId($_SESSION["user"]["id"]);

                $orderContent = [];

                foreach($orders as $order)
                {
                    $items = Order::getAllItemsByOrderId($order->id);
                    $orderContent[$order->ordernum]["status"]   = $order->status;
                    $orderContent[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                    $orderContent[$order->ordernum]["content"]  = $items;
                }

                $categories = Category::getAll();
                View::render("front/orders.terminal.phtml", [
                    "loggedInState" => true,
                    "categories"    => $categories,
                    "itemcount"     => $itemcount,
                    "orders"        => $orderContent
                ]);
            }
            else
            {
                $itemcount = count($_SESSION["cart"]);
            }
        }

        public function orderActivity(string $ordernum)
        {
            $itemcount = 0;

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount  = count($productArr);

                $order      = Order::getDetailsByUserAndOrder($_SESSION["user"]["id"], $ordernum);

                $orderitems = Order::getAllItemsByOrderId($order->id);

                $total      = 0;

                foreach($orderitems as $item)
                {
                    $total += $item->unitprice * $item->quantity;
                }

                $status = "";
                $hasTracknum = FALSE;
                switch($order->status)
                {
                    case "PENDING":
                        $status         = "Order is pending";
                        break;
                    case "PROCESSING":
                        $status         = "Order is being processed";
                        break;
                    case "SHIPPED":
                        $status         = "Order was shipped";
                        $hasTracknum    = TRUE;
                        break;
                }

                $categories = Category::getAll();
                View::render("front/orderdetails.terminal.phtml", [
                    "loggedInState" => true,
                    "categories"    => $categories,
                    "itemcount"     => $itemcount,
                    "order"         => $order,
                    "status"        => $status,
                    "total"         => $total,
                    "hasTracknum"   => $hasTracknum,
                    "orderitems"    => $orderitems
                ]);
            }
            else
            {
                header("Location: /");
            }
        }

        public function paypalCreateActivity(\Joshua\HTTP\Request $request)
        {

            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();

            $json = json_decode(file_get_contents('php://input'));

            $productTotalPrice = 0;
            foreach($json->{"cart"} as $entry ) {
                $productTotalPrice     += Product::getByUPC($entry->{"upc"})->price * intval($entry->{"quantity"});
            }

            // Création d'une nouvelle ressource cURL
            $ch = curl_init();

            // Configuration de l'URL et d'autres options
            $authorization = "Authorization: Basic ".base64_encode($_ENV["PAYPAL_APP_CLIENT_ID"].":".$_ENV["PAYPAL_APP_SECRET"]);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                $authorization,
            ));
            curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $post = '{

                "intent": "AUTHORIZE",
              
                "purchase_units": [
              
                  {
              
                    "reference_id": "d9f80740-38f0-11e8-b467-0ed5f89f718b",
              
                    "amount": {
              
                      "currency_code": "USD",
              
                      "value": '. $productTotalPrice .'
              
                    }
              
                  }
              
                ]
              
              }';

            curl_setopt($ch, CURLOPT_POSTFIELDS,$post);

            // Récupération de l'URL et affichage sur le navigateur
            
            header("Content-Type: application/json");
            if( ! $result = curl_exec($ch))

            {

                trigger_error(curl_error($ch));

            }

            // Fermeture de la session cURL
            curl_close($ch);
        }

        public function paypalCaptureActivity(string $id)
        {

            $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);

            foreach($productArr as $product) 
            {
                Product::subtractStockById($product->productid, $product->quantity);
            }
            // Création d'une nouvelle ressource cURL
            $ch = curl_init();

            // Configuration de l'URL et d'autres options
            $authorization = "Authorization: Basic ".base64_encode("Aco1id4JtlvJ4a4YQP_12Nng8IKK9IolBEv2r-dAy1HNz0ombSLs_izHT69laSHG9Lqyn0h22d3khXQh:EC8yxx8NfiCPpqIFXSiQIxtZa3VnQxymtJJPZyUSsqlC293Jxl28ZEI3dPtq0exs6UDwLXdLVndslKVX");
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                $authorization,
            ));
            curl_setopt($ch, CURLOPT_URL, "https://api-m.sandbox.paypal.com/v2/checkout/orders/$id/authorize");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_VERBOSE, FALSE);

            // Récupération de l'URL et affichage sur le navigateur
            
            header("Content-Type: application/json");
            if(!curl_exec($ch))
            {

                trigger_error(curl_error($ch));

            }

            // Fermeture de la session cURL
            curl_close($ch);
       
            $ordernum = uniqid() . "-" . $this->uniqidReal();

        

            $_SESSION["order"] = [];

            $address = Address::getByUserId($_SESSION["user"]["id"]);

            $id = OrderAddress::getIdOfMatching($address->shippingfullname, $address->shippingcountrycode, $address->shippingphone, $address->shippinglineone, $address->shippinglinetwo, $address->shippingzipcode, $address->shippingcity, $address->billingfullname, $address->billingcountrycode, $address->billinglineone, $address->billinglinetwo, $address->billingzipcode, $address->billingcity);

            if($id === -1)
            {
                OrderAddress::save($address->shippingfullname, $address->shippingcountrycode, $address->shippingphone, $address->shippinglineone, $address->shippinglinetwo, $address->shippingzipcode, $address->shippingcity, $address->billingfullname, $address->billingcountrycode, $address->billinglineone, $address->billinglinetwo, $address->billingzipcode, $address->billingcity);
                $id = OrderAddress::getLastId();
            }

            Order::create($ordernum, $_SESSION["user"]["id"], $id);

            $orderId = Order::getLastId();

            foreach($productArr as $product)
            {
                Order::addToOrder($orderId, ["userid" => $product->userid, "productid" => $product->productid, "quantity" => $product->quantity, "price" => Product::getById($product->productid)->price]);
                array_push($_SESSION["order"], $product->productid);
            }

            Cart::emptyByUserId($_SESSION["user"]["id"]);
            
        }

        public function confirmationActivity()
        {

            if(isset($_SESSION["order"]) && !empty($_SESSION["order"]))
            {
                $products = [];

                foreach($_SESSION["order"] as $productId)
                {
                    array_push($products, Product::getById($productId));
                }

                $_SESSION["order"] = NULL;
                unset($_SESSION["order"]);

                $categories = Category::getAll();
                View::render("front/confirmation.terminal.phtml", [
                    "loggedInState" => true,
                    "categories"    => $categories,
                    "itemcount"     => 0,
                    "products"      => $products
                ]);
            }
            else
            {
                header("Location: /");
            }
        }

        /*https://www.php.net/manual/fr/function.uniqid.php*/
        private function uniqidReal($length = 13)
        {
            // uniqid gives 13 chars, but you could adjust it to your needs.
            if (function_exists("random_bytes"))
            {
                $bytes = random_bytes(ceil($length / 2));
            }
            elseif (function_exists("openssl_random_pseudo_bytes"))
            {
                $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
            }
            else
            {
                throw new Exception("no cryptographically secure random function available");
            }

            return substr(bin2hex($bytes), 0, $length);
        }
    }