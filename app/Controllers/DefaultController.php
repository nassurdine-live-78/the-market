<?php

    namespace App\Controllers;

    use Joshua\Core\Controller;
    use Joshua\Core\View;

    use App\Models\CarouselSlide;
    use App\Models\Cart;
    use App\Models\Category;
    use App\Models\Product;

    class DefaultController extends Controller
    {

        protected function mount(\Joshua\HTTP\Request $request)
        {
            if(!isset($_SESSION["user"]) || empty($_SESSION["user"]))
            {
                if(!isset($_SESSION["cart"]))
                {
                    $_SESSION["cart"] = [];
                }
            }
        }

        public function indexActivity(\Joshua\HTTP\Request $request)
        {
            $itemcount = 0;

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);
            }
            else
            {
                $itemcount = count($_SESSION["cart"]);
            }

            $categories = Category::getAll();
            $slides = CarouselSlide::getAll();
            View::render("front/home.terminal.phtml", [
                "loggedInState" => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
                "slides"        => $slides,
                "categories"    => $categories,
                "itemcount"     => $itemcount
            ]);
        }

        public function browseActivity(string $category)
        {
            $itemcount = 0;

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);
            }
            else
            {
                $itemcount = count($_SESSION["cart"]);
            }

            $categories = Category::getAll();
            $products = Product::getAllByCategoryId(Category::getByURI($category)->id);
            View::render("front/browse.terminal.phtml", [
                "loggedInState" => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
                "categories"    => $categories,
                "products"      => $products,
                "itemcount"     => $itemcount
            ]);
        }

        public function productActivity(string $upc)
        {
            $itemcount = 0;

            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount = count($productArr);
            }
            else
            {
                $itemcount = count($_SESSION["cart"]);
            }

            $categories = Category::getAll();
            $product = Product::getByUPC($upc);
            View::render("front/product.terminal.phtml", [
                "loggedInState" => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
                "categories"    => $categories,
                "product"       => $product,
                "itemcount"     => $itemcount
            ]);
        }
    }