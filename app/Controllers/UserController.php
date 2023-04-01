<?php

    namespace App\Controllers;

    use Joshua\Core\Controller;
    use Joshua\Core\View;


    use App\Models\Address;
    use App\Models\Cart;
    use App\Models\Category;
    use App\Models\Product;
    use App\Models\User;

    class UserController extends Controller
    {
    
        protected function mount(\Joshua\HTTP\Request $request)
        {
            //TODO
        }

        public function registerActivity(\Joshua\HTTP\Request $request)
        {

            $errors     = [];
            $email      = "";
            $password   = "";

            if($request->post("submit") !== null)
            {
                if($request->post("email") !== null && $request->post("password") !== null && $request->post("password-confirm") !== null && $request->post("token") !== null)
                {
                    $success = true;

                    if(strlen(trim($request->post("email"))) > 0)
                    {
                        if(!filter_var(trim($request->post("email")), FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE))
                        {
                            array_push($errors, "Email should be a valid email format (e.g. eggy@example.com).");
                            $success = false;
                        }
                    }
                    else
                    {
                        array_push($errors, "Email should not be empty.");
                        $success = false;
                    }

                    if(strlen($request->post("password")) === 0)
                    {
                        array_push($errors, "Password should not be empty.");
                        $success = false;
                    }

                    if(strlen($request->post("password-confirm")) === 0)
                    {
                        array_push($errors, "Password must be confirmed");
                        $success = false;
                    }
                    else if(strlen($request->post("password")) > 0 && strlen($request->post("password-confirm")) > 0)
                    {
                        if($request->post("password") !== $request->post("password-confirm"))
                        {
                            array_push($errors, "Confirmation password must match password");
                            $success = false;
                        }
                    }

                    if(strlen(trim($request->post("token"))) > 0)
                    {
                        if($_SESSION["token"] !== $request->post("token"))
                        {
                            array_push($errors, "Unexpected error when processing registration. Try again");
                            $success = false;
                        }
                    }
                    else
                    {
                        array_push($errors, "Unexpected error when processing registration. Try again");
                        $success = false;
                    }

                    if($success)
                    {
                        $user = User::getByEmail(trim($request->post("email")));

                        if($user)
                        {
                            array_push($errors, "Email already in use");
                            $success = false;
                        }
                        else
                        {
                            $email      = trim($request->post("email"));
                            $password   = password_hash($request->post("password"), PASSWORD_DEFAULT);

                            if(User::save($email, $password))
                            {
                                $user = User::getByEmail($email);
                                $_SESSION["user"] = [];
                                $_SESSION["user"]["id"] = $user->id;
                                $_SESSION["user"]["email"] = $user->email;
                                $_SESSION["user"]["usertype"] = $user->usertype;
                                $_SESSION["user"]["createdat"] = $user->createdat;

                                if(isset($_SESSION["cart"]))
                                {
                                    $cart = $_SESSION["cart"];

                                    foreach(array_reverse($cart) as $value)
                                    {
                                        $product = [];

                                        $product["userid"]      = $_SESSION["user"]["id"];

                                        $product["productid"]   = $value["id"];

                                        $quantity = Product::getStockById($product["productid"]);

                                        $product["quantity"]    = ($quantity >= $value["quantity"]) ? $value["quantity"] : $quantity;

                                        $product["addeddate"]   = $value["addedAt"];

                                        Cart::save($product);
                                    }

                                    $_SESSION["cart"] = [];
                                }


                                header("Location: /account");
                                exit();
                            }
                            else
                            {
                                throw new \Exception("Unexpected error when saving user information.", 500);
                            }
                        }

                    }

                    $email      = $request->post("email");
                    $password   = $request->post("password");
                }
                else
                {
                    array_push($errors, "Unexpected error when processing registration. Try again");
                }
            }

            $_SESSION["token"] = md5(uniqid(mt_rand(), true));


            $categories = Category::getAll();
            View::render("front/register.terminal.phtml", [
                "categories"    => $categories,
                "token"         => $_SESSION["token"],
                "email"         => $email,
                "password"      => $password,
                "errors"        => $errors
            ]);
        }

        public function loginActivity(\Joshua\HTTP\Request $request)
        {

            $errors     = [];
            $email      = "";
            $password   = "";

            if($request->post("submit") !== null)
            {
                if($request->post("email") !== null && $request->post("password") !== null && $request->post("token") !== null)
                {
                    $success = true;

                    if(strlen(trim($request->post("email"))) > 0)
                    {
                        if(!filter_var(trim($request->post("email")), FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE))
                        {
                            array_push($errors, "Email should be a valid email format (e.g. eggy@example.com).");
                            $success = false;
                        }
                    }
                    else
                    {
                        array_push($errors, "Email should not be empty.");
                        $success = false;
                    }

                    if(strlen(trim($request->post("password"))) === 0)
                    {
                        array_push($errors, "Password should not be empty.");
                        $success = false;
                    }

                    if(strlen(trim($request->post("token"))) > 0)
                    {
                        if($_SESSION["token"] !== $request->post("token"))
                        {
                            array_push($errors, "Unexpected error when processing login. Try again");
                            $success = false;
                        }
                    }
                    else
                    {
                        array_push($errors, "Unexpected error when processing login. Try again");
                        $success = false;
                    }

                    if($success)
                    {
                        $user = User::getByEmail(trim($request->post("email")));

                        if(!$user)
                        {
                            array_push($errors, "Invalid email and/or password");
                            $success = false;
                        }
                        else
                        {
                            if(password_verify($request->post("password"), $user->password))
                            {
                                $_SESSION["user"]               = [];
                                $_SESSION["user"]["id"]         = $user->id;
                                $_SESSION["user"]["email"]      = $user->email;
                                $_SESSION["user"]["createdat"]  = $user->createdat;









                                if(isset($_SESSION["cart"]))
                                {
                                    $cart = $_SESSION["cart"];

                                    foreach(array_reverse($cart) as $value)
                                    {
                                        $product = [];

                                        $product["userid"]      = $_SESSION["user"]["id"];

                                        $product["productid"]   = $value["id"];

                                        $quantity = Product::getStockById($product["productid"]);

                                        $product["quantity"]    = ($quantity >= $value["quantity"]) ? $value["quantity"] : $quantity;

                                        $product["addeddate"]   = $value["addedAt"];

                                        Cart::save($product);
                                    }

                                    $_SESSION["cart"] = [];
                                }










                                header("Location: /account");
                                exit();
                            }
                            else
                            {
                                array_push($errors, "Invalid email and/or password");
                                $success = false;
                            }
                        }

                    }

                    $email = $request->post("email");
                    $password = $request->post("password");
                }
                else
                {
                    array_push($errors, "Unexpected error when processing login. Try again");
                }
            }

            $_SESSION["token"] = md5(uniqid(mt_rand(), true));


            $categories = Category::getAll();
            View::render("front/login.terminal.phtml", [
                "categories"    => $categories,
                "token"         => $_SESSION["token"],
                "email"         => $email,
                "password"      => $password,
                "errors"        => $errors
            ]);
        }

        public function logoutActivity()
        {
            session_unset();
            header("Location: /");
        }

        public function accountActivity(\Joshua\HTTP\Request $request)
        {


            if(isset($_SESSION["user"]) && !empty($_SESSION["user"]))
            {

                
                $productArr = Cart::getAllByUserId($_SESSION["user"]["id"]);
                
                $itemcount  = count($productArr);


                $errors                 = [];
                $email                  = $_SESSION["user"]["email"];

                $address    = Address::getByUser(User::getByEmail($_SESSION["user"]["email"]));
                
                $shippingFullname       = ($address) ? $address->shippingfullname : "";
                $shippingCountryCode    = ($address) ? $address->shippingcountrycode : "";
                $shippingPhoneNumber    = ($address) ? $address->shippingphone : "";
                $shippingAddressOne     = ($address) ? $address->shippinglineone : "";
                $shippingAddressTwo     = ($address) ? $address->shippinglinetwo : "";
                $shippingZipCode        = ($address) ? $address->shippingzipcode : "";
                $shippingCity           = ($address) ? $address->shippingcity : "";
                
                $billingFullname        = ($address) ? $address->billingfullname : "";
                $billingCountryCode     = ($address) ? $address->billingcountrycode : "";
                $billingAddressOne      = ($address) ? $address->billinglineone : "";
                $billingAddressTwo      = ($address) ? $address->billinglinetwo : "";
                $billingZipCode         = ($address) ? $address->billingzipcode : "";
                $billingCity            = ($address) ? $address->billingcity : "";

                $countries              = ["FR" => "France", "UK" => "United-Kingdom", "US" => "United States"];



                if($request->post("submit-password") !== null)
                {
                    if($request->post("password-old") !== null && $request->post("password-new") !== null && $request->post("password-confirm") !== null && $request->post("token") !== null)
                    {
                        $success = true;

                        if(strlen($request->post("password-old")) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }

                        if(strlen($request->post("password-new")) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }

                        if(strlen($request->post("password-confirm")) === 0)
                        {
                            array_push($errors, "Password must be confirmed");
                            $success = false;
                        }
                        else if(strlen($request->post("password-new")) > 0 && strlen($request->post("password-confirm")) > 0)
                        {
                            if($request->post("password-new") !== $request->post("password-confirm"))
                            {
                                array_push($errors, "Confirmation password must match password");
                                $success = false;
                            }
                        }

                        if(strlen(trim($request->post("token"))) > 0)
                        {
                            if($_SESSION["token"] !== $request->post("token"))
                            {
                                array_push($errors, "Unexpected error when processing registration. Try again");
                                $success = false;
                            }
                        }
                        else
                        {
                            array_push($errors, "Unexpected error when processing registration. Try again");
                            $success = false;
                        }

                        if($success)
                        {

                            
                            $user = User::getByEmail(trim($_SESSION["user"]["email"]));

                            if(!$user)
                            {
                                array_push($errors, "Unexpected error when updating password. Try again");
                                $success = false;
                            }
                            else
                            {
                                if(password_verify($request->post("password-old"), $user->password))
                                {
                                    $password   = password_hash($request->post("password-new"), PASSWORD_DEFAULT);
                                    User::updatePasswordById($_SESSION["user"]["id"], $password);
                                    header("Location: /account");
                                    exit();
                                }
                                else
                                {
                                    array_push($errors, "Old password does not match.");
                                    $success = false;
                                }
                            }
                        }
                    }
                    else
                    {
                        array_push($errors, "Unexpected error when processing password update. Try again");
                    }
                }

                if($request->post("submit-address") !== null)
                {
                    if(
                        $request->post("shipping-country")      !== null &&
                        $request->post("shipping-fullname")     !== null &&
                        $request->post("shipping-phonenumber")  !== null &&
                        $request->post("shipping-address-one")  !== null &&
                        $request->post("shipping-address-two")  !== null &&
                        $request->post("shipping-zip-code")     !== null &&
                        $request->post("shipping-city")         !== null &&
                        $request->post("billing-country")       !== null &&
                        $request->post("billing-fullname")      !== null &&
                        $request->post("billing-address-one")   !== null &&
                        $request->post("billing-address-two")   !== null &&
                        $request->post("billing-zip-code")      !== null &&
                        $request->post("billing-city")          !== null &&
                        $request->post("token")                 !== null
                    )
                    {
                        $success = true;

                        if(strlen(trim($request->post("shipping-country"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingCountryCode       = $request->post("shipping-country");
                        }

                        if(strlen(trim($request->post("shipping-fullname"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingFullname       = $request->post("shipping-fullname");
                        }

                        if(strlen(trim($request->post("shipping-phonenumber"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingPhoneNumber    = $request->post("shipping-phonenumber");
                        }

                        if(strlen(trim($request->post("shipping-address-one"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingAddressOne     = $request->post("shipping-address-one");
                        }

                        if(strlen(trim($request->post("shipping-address-two"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingAddressTwo     = $request->post("shipping-address-two");
                        }

                        if(strlen(trim($request->post("shipping-zip-code"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingZipCode        = $request->post("shipping-zip-code");
                        }

                        if(strlen(trim($request->post("shipping-city"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $shippingCity           = $request->post("shipping-city");
                        }

                        if(strlen(trim($request->post("billing-country"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $billingCountryCode        = $request->post("billing-country");
                        }

                        if(strlen(trim($request->post("billing-fullname"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {                            
                            $billingFullname        = $request->post("billing-fullname");
                        }

                        if(strlen(trim($request->post("billing-address-one"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $billingAddressOne      = $request->post("billing-address-one");
                        }

                        if(strlen(trim($request->post("billing-address-two"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $billingAddressTwo      = $request->post("billing-address-two");
                        }

                        if(strlen(trim($request->post("billing-zip-code"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $billingZipCode         = $request->post("billing-zip-code");
                        }

                        if(strlen(trim($request->post("billing-city"))) === 0)
                        {
                            array_push($errors, "Password should not be empty.");
                            $success = false;
                        }
                        else
                        {
                            $billingCity            = $request->post("billing-city");
                        }

                        if(strlen(trim($request->post("token"))) > 0)
                        {
                            if($_SESSION["token"] !== $request->post("token"))
                            {
                                array_push($errors, "Unexpected error when processing login. Try again");
                                $success = false;
                            }
                        }
                        else
                        {
                            array_push($errors, "Unexpected error when processing login. Try again");
                            $success = false;
                        }

                        if($success)
                        {
                            $user = User::getByEmail($_SESSION["user"]["email"]);

                            if(!$user)
                            {
                                array_push($errors, "Invalid email");
                                $success = false;
                            }
                            else
                            {
                                Address::updateByUser($user, $shippingFullname, $shippingCountryCode, $shippingPhoneNumber, $shippingAddressOne, $shippingAddressTwo, $shippingZipCode, $shippingCity, $billingFullname, $billingCountryCode, $billingAddressOne, $billingAddressTwo, $billingZipCode, $billingCity);

                                header("Location: /account");
                            }

                        }

                        $email = $request->post("email");
                        $password = $request->post("password");
                    }
                    else
                    {
                        array_push($errors, "Unexpected error when processing login. Try again");
                    }
                }

                
                

                $_SESSION["token"] = md5(uniqid(mt_rand(), true));

                $categories = Category::getAll();
                View::render("front/account.terminal.phtml", [
                    "loggedInState"         => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
                    "errors"                => $errors,
                    "categories"            => $categories,
                    "itemcount"             => $itemcount,
                    "email"                 => $email,
                    "token"                 => $_SESSION["token"],
                    "countries"             => $countries,
                
                    "shippingFullname"      => $shippingFullname,
                    "shippingCountryCode"   => $shippingCountryCode,
                    "shippingPhoneNumber"   => $shippingPhoneNumber,
                    "shippingAddressOne"    => $shippingAddressOne,
                    "shippingAddressTwo"    => $shippingAddressTwo,
                    "shippingZipCode"       => $shippingZipCode,
                    "shippingCity"          => $shippingCity,
                    
                    "billingFullname"       => $billingFullname,
                    "billingCountryCode"    => $billingCountryCode,
                    "billingAddressOne"     => $billingAddressOne,
                    "billingAddressTwo"     => $billingAddressTwo,
                    "billingZipCode"        => $billingZipCode,
                    "billingCity"           => $billingCity
                ]);

            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function orderActivity()
        {
            View::render("front/order.terminal.phtml");
        }
    }