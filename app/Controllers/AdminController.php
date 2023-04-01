<?php

    namespace App\Controllers;

    use Joshua\Core\Controller;
    use Joshua\Core\View;
    use Joshua\Core\FileUpload;
    
    use App\Models\CarouselSlide;
    use App\Models\Category;
    use App\Models\Order;
    use App\Models\Product;
    use App\Models\User;

    class AdminController extends Controller
    {
        public function __construct()
        {
            
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
                        $user = User::getAdminByEmail(trim($request->post("email")));

                        if(!$user)
                        {
                            array_push($errors, "Invalid email and/or password");
                            $success = false;
                        }
                        else
                        {
                            if(password_verify($request->post("password"), $user->password))
                            {
                                $_SESSION["admin"]               = [];
                                $_SESSION["admin"]["id"]         = $user->id;
                                $_SESSION["admin"]["email"]      = $user->email;
                                $_SESSION["admin"]["createdat"]  = $user->createdat;
                                header("Location: /admin/dashboard");
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

            View::render("back/login.terminal.phtml", [
                "loggedInState" => (isset($_SESSION["user"]) && !empty($_SESSION["user"])),
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

        public function dashboardActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {

                $csrfToken  = md5(uniqid()."");

                $_SESSION["admin-carousel-update-csrf"] = $csrfToken;

                
                $slides = CarouselSlide::getAll();

                View::render("back/dashboard.terminal.phtml", [
                    "loggedInState" => (isset($_SESSION["admin"]) && !empty($_SESSION["admin"])),
                    "token"         => $csrfToken,
                    "slides"        => $slides
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function accountsActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                View::render("back/accounts.terminal.phtml");
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function categoriesActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $categories = Category::getAll();

                $csrfToken  = md5(uniqid()."");

                $_SESSION["admin-category-create-csrf"] = $csrfToken;
                $_SESSION["admin-category-delete-csrf"] = $csrfToken;

                View::render("back/categories.terminal.phtml", [
                    "categories"    => $categories,
                    "token"         => $csrfToken
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function categoryDetailsActivity(string $category)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $cat        = Category::getByURI($category);
                $products   = Product::getAllByCategoryId($cat->id);

                $csrfToken  = md5(uniqid()."");

                $_SESSION["admin-category-details-csrf"] = $csrfToken;
                $_SESSION["admin-category-delete-csrf"] = $csrfToken;

                View::render("back/categorydetails.terminal.phtml", [
                    "category"  => $cat,
                    "products"  => $products,
                    "token"     => $csrfToken
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function categoryCreateActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $_SESSION["error"] = [];

                if(isset($_SESSION["admin-category-create-csrf"]) && $_SESSION["admin-category-create-csrf"] === $request->post("token"))
                {
                    $uri            = $request->post("uri");
                    $name           = $request->post("name");

                    if(isset($uri) && isset($name))
                    {
                        $guid = null;

                        do
                        {
                            $guid = strtoupper(uniqid())."-".strtoupper(uniqid());
                        }
                        while(Category::getByGUID($guid));

                        Category::create($name, $uri, $guid);

                        header("Location: /admin/categories".$request->post("upc"));
                    }
                    else
                    {
                        array_push($_SESSION["errors"], "An error occurred");
    
                        header("Location: /admin/categories");
                    }
                }
                else
                {
                    array_push($_SESSION["errors"], "An error occurred");

                    header("Location: /admin/categories");
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function categoryUpdateDetailsActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $name           = $request->post("name");
                $uri            = $request->post("uri");
                $guid           = $request->post("guid");

                if(isset($_SESSION["admin-category-details-csrf"]) && $_SESSION["admin-category-details-csrf"] === $request->post("token") && isset($name) && isset($uri) && isset($guid))
                {

                    Category::updateByGUID($guid, $name, $uri);

                    header("Location: /admin/category/details/".$request->post("uri"));
                }
                else
                {
                    $_SESSION["errors"] = "An error occurred";

                    header("Location: /");
                    exit();
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function categoryDeleteActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $guid   = $request->post("guid");
                $token  = $request->post("token");

                if(isset($_SESSION["admin-category-delete-csrf"]) && $_SESSION["admin-category-delete-csrf"] ===  $token && isset($guid))
                {

                    Category::deleteByGUID($guid);

                    header("Location: /admin/categories");
                }
                else
                {
                    $_SESSION["errors"] = "An error occurred";

                    header("Location: /");
                    exit();
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function productsActivity(\Joshua\HTTP\Request $request, $page = 1)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                if($page !== NULL)
                {
                    $COUNT_PER_PAGE = 1;

                    $total      = Product::countAll();

                    $maxPages   = floor($total/$COUNT_PER_PAGE);

                    if($maxPages < $page-1)
                    {
                        header("Location: /admin/products/".$maxPages);
                        exit();
                    }
                    else
                    {
                        $headDependencies       = '<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">';
                        $scripts                = <<<EOT
                        
                                                    <!-- Include the Quill library -->
                                                    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                                                
                                                    <!-- Initialize Quill editor -->
                                                    <script>
                                                        var quill = new Quill(
                                                            '#editor',
                                                            {
                                                                theme: 'snow'
                                                            }
                                                        );
                                                    </script>
                                                
                                                    <script>
                                                        let detailForm = document.forms.details;
                                                        if(detailForm !== undefined)
                                                        {
                                                            detailForm.addEventListener(
                                                                "submit",
                                                                (e) => {
                                                                    let description = document.querySelector("#editor .ql-editor[contenteditable=true]").innerHTML;
                                                                    detailForm.elements.description.value = description;
                                                                }
                                                            );
                                                            
                                                        }
                                                    </script>
                                                    EOT;
                        
                        $offset     = ($page-1 > $maxPages) ? 0 : $page-1; 
    
                        $products   = Product::getAllPaged($offset, $COUNT_PER_PAGE);

                        $categories = Category::getAll();

                        $csrfToken = md5(uniqid()."");

                        $_SESSION["admin-product-create-csrf"] = $csrfToken;
    
                        View::render("back/products.terminal.phtml", [
                            "products"          => $products,
                            "categories"        => $categories,
                            "offset"            => $offset,
                            "maxPages"          => $maxPages,
                            "token"             => $csrfToken,
                            "headDependencies"  => $headDependencies,
                            "scripts"           => $scripts
                        ]);
                    }
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function productDetailsActivity(string $upc)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $product    = Product::getByUPC($upc);

                $csrfToken = md5(uniqid()."");

                $_SESSION["admin-product-details-csrf"] = $csrfToken;
                        $headDependencies       = '<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">';
                        $scripts                = <<<EOT
                        
                                                    <!-- Include the Quill library -->
                                                    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                                                
                                                    <!-- Initialize Quill editor -->
                                                    <script>
                                                        var quill = new Quill(
                                                            '#editor',
                                                            {
                                                                theme: 'snow'
                                                            }
                                                        );
                                                    </script>
                                                
                                                    <script>
                                                        let detailForm = document.forms.details;
                                                        if(detailForm !== undefined)
                                                        {
                                                            detailForm.addEventListener(
                                                                "submit",
                                                                (e) => {
                                                                    let description = document.querySelector("#editor .ql-editor[contenteditable=true]").innerHTML;
                                                                    detailForm.elements.description.value = description;
                                                                }
                                                            );
                                                            
                                                        }
                                                    </script>
                                                    EOT;

                View::render("back/productdetails.terminal.phtml", [
                    "product"       => $product,
                    "upc"           => $upc,
                    "token"         => $csrfToken,
                    "headDependencies"  => $headDependencies,
                    "scripts"           => $scripts
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function productCreateActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $_SESSION["error"] = [];

                if(isset($_SESSION["admin-product-create-csrf"]) && $_SESSION["admin-product-create-csrf"] === $request->post("token"))
                {
                    $upc            = $request->post("upc");
                    $name           = $request->post("name");
                    $image          = $request->file("image");
                    $price          = $request->post("price");
                    $description    = $request->post("description");
                    $category       = $request->post("category");

                    if(isset($upc) && isset($name) && isset($image) && isset($price) && isset($description) && isset($category))
                    {
                        $uploaddir = "img/uploads/items";
                        
                        if (!is_dir($uploaddir) && !mkdir($uploaddir)){
                          die("Error creating folder $uploaddir");
                        }
                        
                        $imageUpload = new FileUpload($uploaddir);

                        $imageUpload->setMaxFileSize(500000);
                        $imageUpload->setAllowedMIME(["image/jpeg"]);
                        $filename = $imageUpload->process($image);

                        if(empty($filename))
                        {
                            array_push($_SESSION["error"], "Image must be a valid jpeg smaller than 500 kilobytes");

                            header("Location: /admin/products");
                        }
                        else
                        {
                            Product::create(strtoupper($upc), $name, $filename[0], floatval($price), $description, intval($category));

                            header("Location: /admin/product/details/".strtoupper($upc));
                        }
                    }
                    else
                    {
                        echo "<pre>";
                        var_dump(isset($upc));
                        var_dump(isset($image));
                        var_dump($request->postArray());
                        echo "</pre>";
                    }
                }
                else
                {
                    array_push($_SESSION["errors"], "An error occurred");

                    header("Location: /admin/product/");
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function carouselUpdateActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $_SESSION["errors"] = [];

                $images          = $request->file("slides");
                $token          = $request->post("token");

                if(isset($_SESSION["admin-carousel-update-csrf"]) && $_SESSION["admin-carousel-update-csrf"] === $token)
                {

                    if(isset($images) && gettype($images) === 'array')
                    {
                        $uploaddir = "img/uploads/carousel";
                        
                        if (!is_dir($uploaddir) && !mkdir($uploaddir)){
                          die("Error creating folder $uploaddir");
                        }
                        
                        $imageUpload = new FileUpload($uploaddir);

                        $imageUpload->setMaxFileSize(500000);
                        $imageUpload->setAllowedMIME(["image/jpeg"]);
                        
                        $filenames = $imageUpload->process($images);

                        if(empty($filenames))
                        {
                            array_push($_SESSION["error"], "Image must be a valid jpeg smaller than 500 kilobytes");

                            //header("Location: /admin/products");
                        }
                        else
                        {
                            CarouselSlide::update($filenames);

                            //header("Location: /admin/product/details/".strtoupper($upc));
                        }
                    }
                    else
                    {
                        echo "<pre>";
                        var_dump(isset($image));
                        var_dump($request->postArray());
                        echo "</pre>";
                    }
                }
                else
                {
                    array_push($_SESSION["errors"], "An error occurred");
                    echo "Failure";
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function productUpdateDetailsActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                if(isset($_SESSION["admin-product-details-csrf"]) && $_SESSION["admin-product-details-csrf"] === $request->post("token"))
                {
                    $upc            = $request->post("upc");
                    $name           = $request->post("name");
                    $price          = $request->post("price");
                    $description    = $request->post("description");

                    Product::updateByUPC($upc, $name, $price, $description);

                    header("Location: /admin/product/details/".$request->post("upc"));
                }
                else
                {
                    $_SESSION["errors"] = "An error occurred";

                    header("Location: /admin/product/details/".$request->post("upc"));
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function productUpdateStockActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                if(isset($_SESSION["admin-product-details-csrf"]) && $_SESSION["admin-product-details-csrf"] === $request->post("token"))
                {
                    $upc        = $request->post("upc");
                    $stock      = $request->post("stock");
                    $submission = $request->post("submit");
                    $product    = Product::getByUPC($upc);

                    if(isset($upc) && isset($stock) && isset($submission) && $product !== FALSE)
                    {
                        switch($submission)
                        {
                            case "increase":
                                Product::addStockById($product->id, intval($stock));
                                break;
                            case "decrease":
                                Product::subtractStockById($product->id, intval($stock));
                                break;
                        }
                    }
                    

                    header("Location: /admin/product/details/".$request->post("upc"));
                }
                else
                {
                    $_SESSION["errors"] = "An error occurred";

                    header("Location: /admin/product/details/".$request->post("upc"));
                }
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function ordersActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {

                $pendingOrders          = [];
                $processingOrders       = [];
                $shippedOrders          = [];

                $pendingOrdersCount     = Order::getAllPendingCount();
                $shippedOrdersCount     = Order::getAllShippedCount();
                $processingOrdersCount  = Order::getAllProcessingCount();


                if($pendingOrdersCount > 0)
                {
                    $orders = Order::getAllPendingPaged(0, 2);

                    foreach($orders as $order)
                    {
                        $items = Order::getAllItemsByOrderId($order->id);
                        $pendingOrders[$order->ordernum]["status"]   = $order->status;
                        $pendingOrders[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                        $pendingOrders[$order->ordernum]["content"]  = $items;
                    }
                }


                if($processingOrdersCount > 0)
                {
                    $orders = Order::getAllProcessingPaged(0, 2);

                    foreach($orders as $order)
                    {
                        $items = Order::getAllItemsByOrderId($order->id);
                        $processingOrders[$order->ordernum]["status"]   = $order->status;
                        $processingOrders[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                        $processingOrders[$order->ordernum]["content"]  = $items;
                    }
                }


                if($shippedOrdersCount > 0)
                {
                    $orders = Order::getAllShippedPaged(0, 2);

                    foreach($orders as $order)
                    {
                        $items = Order::getAllItemsByOrderId($order->id);
                        $shippedOrders[$order->ordernum]["status"]   = $order->status;
                        $shippedOrders[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                        $shippedOrders[$order->ordernum]["content"]  = $items;
                    }
                }

                $categories = Category::getAll();

                View::render("back/orders.terminal.phtml", [
                    "pendingOrders"         => $pendingOrders,
                    "processingOrders"      => $processingOrders,
                    "shippedOrders"         => $shippedOrders,
                    "pendingOrdersCount"    => $pendingOrdersCount,
                    "shippedOrdersCount"    => $shippedOrdersCount,
                    "processingOrdersCount" => $processingOrdersCount
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function pendingOrdersActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {

                $orderList      = [];

                $orderListCount = Order::getAllPendingCount();


                if($orderListCount > 0)
                {
                    $orders = Order::getAllPendingPaged(0, 2);

                    foreach($orders as $order)
                    {
                        $items = Order::getAllItemsByOrderId($order->id);
                        $orderList[$order->ordernum]["status"]   = $order->status;
                        $orderList[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                        $orderList[$order->ordernum]["content"]  = $items;
                    }
                }

                $categories = Category::getAll();

                View::render("back/pendingorders.terminal.phtml", [
                    "orderList"         => $orderList,
                    "orderListCount"    => $orderListCount,
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function processingOrdersActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {

                $orderList      = [];

                $orderListCount  = Order::getAllProcessingCount();


                if($orderListCount > 0)
                {
                    $orders = Order::getAllProcessingPaged(0, 2);

                    foreach($orders as $order)
                    {
                        $items = Order::getAllItemsByOrderId($order->id);
                        $orderList[$order->ordernum]["status"]   = $order->status;
                        $orderList[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                        $orderList[$order->ordernum]["content"]  = $items;
                    }
                }

                $categories = Category::getAll();

                View::render("back/processingorders.terminal.phtml", [
                    "orderList"         => $orderList,
                    "orderListCount"    => $orderListCount,
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function shippedOrdersActivity()
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {

                $orderList      = [];

                $orderListCount     = Order::getAllShippedCount();


                if($orderListCount > 0)
                {
                    $orders = Order::getAllShippedPaged(0, 2);

                    foreach($orders as $order)
                    {
                        $items = Order::getAllItemsByOrderId($order->id);
                        $orderList[$order->ordernum]["status"]   = $order->status;
                        $orderList[$order->ordernum]["tracknum"] = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;
                        $orderList[$order->ordernum]["content"]  = $items;
                    }
                }

                $categories = Category::getAll();

                View::render("back/shippedorders.terminal.phtml", [
                    "orderList"         => $orderList,
                    "orderListCount"    => $orderListCount,
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function orderStatusUpdateActivity(\Joshua\HTTP\Request $request)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {
                $ordernum   = $request->post("ordernum");
                $status     = $request->post("next-step");
                $tracknum   = $request->post("tracknum");

                if(isset($ordernum) && isset($status))
                {
                    Order::updateStatusByOrderNum($status, $ordernum);

                    if(isset($tracknum))
                    {
                        Order::updateTrackingByOrderNum($tracknum, $ordernum);
                    }
                }

                header("Location: /admin/order/details/" . $ordernum);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }

        public function orderDetailsActivity(string $ordernum)
        {
            if(isset($_SESSION["admin"]) && !empty($_SESSION["admin"]))
            {

                $order      = Order::getDetailsByNum($ordernum);

                $orderitems = Order::getAllItemsByOrderId($order->id);

                $total      = 0;

                $tracknum   = (strlen($order->tracknum) > 0) ? $order->tracknum : NULL;

                foreach($orderitems as $item)
                {
                    $total += $item->unitprice * $item->quantity;
                }

                $status         = "";
                $nextStep       = "";
                $isProcessing   = NULL;
                switch($order->status)
                {
                    case "PENDING":
                        $status         = "PENDING";
                        $nextStep       = "PROCESSING";
                        break;
                    case "PROCESSING":
                        $status         = "PROCESSING";
                        $nextStep       = "SHIPPED";
                        $isProcessing   = TRUE;
                        break;
                    case "SHIPPED":
                        $status         = "SHIPPED";
                        $nextStep       = FALSE;
                        $isProcessing   = FALSE;
                        break;
                }

                View::render("back/orderdetails.terminal.phtml", [
                    "order"         => $order,
                    "total"         => $total,
                    "status"        => $status,
                    "nextStep"      => $nextStep,
                    "isProcessing"  => $isProcessing,
                    "tracknum"      => $tracknum,
                    "orderitems"    => $orderitems
                ]);
            }
            else
            {
                header("Location: /");
                exit();
            }
        }
    }