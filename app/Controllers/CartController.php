<?php

    namespace App\Controllers;

    use Joshua\Core\Controller;
    use Joshua\Core\View;

    use App\Models\Address;
    use App\Models\Cart;
    use App\Models\Category;
    use App\Models\Product;
    use App\Models\User;

    class CartController extends Controller
    {

        public function cartActivity(\Joshua\HTTP\Request $request)
        {
            $products   = [];
            $subtotal   = 0;
            $itemcount  = 0;
            $totalitems = 0;

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {

                $productArr = Cart::getAllByUserIdSorted($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);

                foreach($productArr as $value)
                {
                    $product     = Product::getById($value->productid);
                    array_push($products, array_merge(["quantity" => $value->quantity, "id" => $value->productid], ["product" => $product]));
                    $subtotal   += $product->price * $value->quantity;
                    $totalitems += $value->quantity;
                }
            }
            else
            {
                $itemcount = count($_SESSION["cart"]);

                foreach($_SESSION["cart"] as $index => $value)
                {
                    $product     = Product::getById($value["id"]);
                    array_push($products, array_merge($value, ["product" => $product]));
                    $subtotal   += $product->price * $value["quantity"];
                    $totalitems += $value["quantity"];
                }
            }

            $categories = Category::getAll();
            View::render("front/cart.terminal.phtml", [
                "loggedInState" => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
                "categories"    => $categories,
                "itemcount"     => $itemcount,
                "products"      => $products,
                "subtotal"      => $subtotal,
                "totalitems"    => $totalitems
            ]);
        }

        public function jsonActivity(\Joshua\HTTP\Request $request)
        {
            $products   = [];

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);

                foreach($productArr as $value)
                {
                    $product     = Product::getById($value->productid);
                    array_push($products, array_merge(["quantity" => $value->quantity, "id" => $value->productid], ["product" => $product]));
                }
            }
            else
            {
                $itemcount = count($_SESSION["cart"]);

                foreach($_SESSION["cart"] as $index => $value)
                {
                    $product     = Product::getById($value["id"]);
                    array_push($products, array_merge($value, ["product" => $product]));
                }
            }

            header("Content-Type: application/json");

            $test =  '{"data":'.json_encode($products).'}';

            //var_dump(json_decode($test)->{"data"}[0]->{"product"}->{"price"});

            echo $test;
        }

        public function addToCartActivity(\Joshua\HTTP\Request $request)
        {
            // TODO check for form integrity before doing anything
            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {

                $product = [];

                $product["userid"]      = $_SESSION["user"]["id"];

                $product["productid"]   = $request->post("productId");

                $quantity = Product::getStockById($product["productid"]);

                $product["quantity"]    = ($quantity >= $request->post("quantity")) ? $request->post("quantity") : $quantity;

                $product["addeddate"]   = date('Y-m-d H:i:s');

                Cart::save($product);
            }
            else
            {
                $id = $request->post("productId");
                $quantity = $request->post("quantity");
                if(isset($id) && isset($quantity))
                {
                    $found = false;

                    foreach($_SESSION["cart"] as $index => $value)
                    {
                        if(!empty(array_intersect_assoc(array("id" => $id), $value)))
                        {
                            $found = true;
                            $_SESSION["cart"][$index] = ["id" => $id, "quantity" => intval($quantity), "addedAt" => date('Y-m-d H:i:s') ];
                        }
                    }

                    if(!$found) array_unshift($_SESSION["cart"], ["id" => $id, "quantity" => intval($quantity), "addedAt" => date('Y-m-d H:i:s') ]);
                }
            }

            header("Location: ".$request->referer());
        }

        public function removeFromCartActivity(\Joshua\HTTP\Request $request)
        {
            // TODO check for form integrity before doing anything
            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $product = [];

                $product["userid"]      = $_SESSION["user"]["id"];

                $product["productid"]   = $request->post("productId");

                Cart::remove($product);

                header("Location: ".$request->referer());
            }
            else
            {
                $id = $request->post("productId");
                if(isset($id))
                {
                    $found = false;

                    foreach($_SESSION["cart"] as $index => $value)
                    {
                        if(!empty(array_intersect_assoc(array("id" => $id), $value)))
                        {
                            $found = true;
                            array_splice($_SESSION["cart"], $index, 1);
                        }
                    }
                }
            }

            header("Location: ".$request->referer());
        }

        public function checkoutActivity()
        {

            $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
                
                
            $products   = [];
            $subtotal   = 0;
            $itemcount  = 0;
            $totalitems = 0;
            
            $clientID = $_ENV["PAYPAL_APP_CLIENT_ID"];

            if(!isset($_SESSION["user"]) || empty($_SESSION["user"]))
            {
                header("Location: /login");
                exit();
            }
            else
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);

                $address = Address::getByUserId($_SESSION["user"]["id"]);

                foreach($productArr as $value)
                {
                    $product     = Product::getById($value->productid);
                    array_push($products, array_merge(["quantity" => $value->quantity, "id" => $value->productid], ["product" => $product]));
                    $subtotal   += $product->price * $value->quantity;
                    $totalitems += $value->quantity;
                }


            }

            $categories = Category::getAll();
            View::render("front/checkout.terminal.phtml", [
                "loggedInState" => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
                "itemcount"     => $itemcount,
                "categories"    => $categories,
                "address"       => $address,
                "products"      => $products,
                "subtotal"      => $subtotal,
                "totalitems"    => $totalitems,
                "clientID"      => $clientID
            ]);
        }
    }