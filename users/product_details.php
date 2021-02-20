<?php
 require_once("../inc/connect.php");

include "./userFunctions.php";
if (!empty($_GET)) {
        $pid = $_GET["pid"];
        $result = mysqli_query($conn, "SELECT * FROM products WHERE productId = $pid");

        
        $resultVendor = mysqli_query($conn, "SELECT DISTINCT r.retailer_id, r.retailer_name FROM products p, retailer r WHERE p.vendor_id = r.retailer_id && p.productId= $pid");
        $query2 =  mysqli_query($conn,"SELECT DISTINCT m.mat_id, m.mat_title FROM products p, material m WHERE p.mat_id = m.mat_id && p.productId= $pid");

    } else {
        header("location: home.php");
        exit;
    }

    $UF = new UserFunctions();

    $curProPrice = $UF->productDeatails($_GET['pid'])[0]['productPrice'];

    $boxes = $UF->getProBoxes($_GET['pid']);

   if(isset($_POST['onImport'])){
      $UF = new UserFunctions();
      
      $UF->insertIntoWatchList($_GET["pid"]);

      header("Location:./importproduct.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/style.css" />
    <link rel="stylesheet" href="style/styles.css" />
    <link rel="stylesheet" href="style/owl/owl.carousel.min.css" />
    <link rel="stylesheet" href="style/owl/owl.theme.default.min.css" />
    <script src="style/owl.carousel.js"></script>
    <script src="style/owl.navigation.js"></script>
    <title>Find Product</title>
  </head>

  <body>
    <div id="app">
      <div id="clickripple-merchants" class="clickripple-merchants">
        <div
          class="page-layout product-source-product product-layout-full-width-mobile"
        >
          <div class="page-layout_nav">
            <div class="main-wrapper">
              <nav class="wrapper">
                <div class="nav">
                  <div class="logo">
                    <a href="">
                      <img src="image/logo.png" alt="clickripple" />
                    </a>
                  </div>
                  <div class="scroll">
                    <ul class="pages">
                      <li class="nav-item">
                        <a href="totalSale.php">
                          <span class="nav-item_icon">
                            <svg class="icon-base">
                              <use xlink:href="#icon-sidebar-home">
                                <symbol
                                  id="icon-sidebar-home"
                                  viewBox="0 0 20 20"
                                >
                                  <path
                                    d="M16 10a3.97 3.97 0 0 1-2-.54 3.97 3.97 0 0 1-4 0 3.97 3.97 0 0 1-4 0A3.97 3.97 0 0 1 4 
                                      10a4.1 4.1 0 0 1-1-.14V16a2 2 0 0 0 2 2h3v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4h3a2 2 0 0 0
                                       2-2V9.86a3.94 3.94 0 0 1-1 .14zm-1.24-8H5.24a2 2 0 0 0-1.8 1.1L2 6a2 2 0 1 0 4 0 2 2 0
                                        1 0 4 0 2 2 0 1 0 4 0 2 2 0 1 0 4 0l-1.45-2.9A2 2 0 0 0 14.76 2z"
                                  />
                                </symbol>
                              </use>
                            </svg>
                          </span>
                          <span class="nav-item_title">Home</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#">
                          <span class="nav-item_icon">
                            <svg class="icon-base">
                              <use xlink:href="#icon-sidebar-import-list">
                                <symbol
                                  id="icon-sidebar-import-list"
                                  viewBox="0 0 20 20"
                                >
                                  <path
                                    d="M18 17a1 1 0 01-1 1H3a1 1 0 01-1-1v-3a1 1 0 112 0v2h12v-2a1 1 0 112 0v3zm-9-6.414V3a1 
                                  1 0 112 0v7.586l2.293-2.293a.999.999 0 111.414 1.414l-3.984 3.983a.998.998 0 01-1.446 0L5.293 
                                  9.707a.997.997 0 010-1.414.999.999 0 011.414 0L9 10.586z"
                                  />
                                </symbol>
                              </use>
                            </svg>
                          </span>
                          <span class="nav-item_title">Import List</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="import_list.html">
                          <span class="nav-item_icon">
                            <svg class="icon-base">
                              <use xlink:href="#icon-sidebar-products">
                                <symbol
                                  id="icon-sidebar-products"
                                  viewBox="0 0 20 20"
                                >
                                  <path
                                    d="M10.983 7.022a2 2 0 1 1 3.999-.002 2 2 0 0 1-4 .002zm-.98-5c-.55-.003-1.633.613-2.02 1l-5 5c-1.73 1.729-.814 3.186 
                                  0 4l5 5c.814.814 2.28 1.719 4 0l5.009-5c.386-.387 1.008-1.47 1.008-2.02v-4.98c0-1.981-1.026-3-3.025-3h-4.972z"
                                  />
                                </symbol>
                              </use>
                            </svg>
                          </span>
                          <span class="nav-item_title">Products</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#">
                          <span class="nav-item_icon">
                            <svg class="icon-base">
                              <use xlink:href="#icon-sidebar-orders">
                                <symbol
                                  id="icon-sidebar-orders"
                                  viewBox="0 0 20 20"
                                >
                                  <path
                                    d="M10 1a1 1 0 011 1v5.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L9 7.586V2a1 1 0 011-1z"
                                  />
                                  <path
                                    d="M6 3a1 1 0 00-1-1H4a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2h-1a1 1 0 100 2h1v8h-1.764a2 2 0 00-1.789 
                                1.106L12 14H8l-.447-.894A2 2 0 005.763 12H4V4h1a1 1 0 001-1z"
                                  />
                                </symbol>
                              </use>
                            </svg>
                          </span>
                          <span class="nav-item_title">Orders</span>
                        </a>
                      </li>
                      <li class="nav-item">
                        <a href="#">
                          <span class="nav-item_icon">
                            <svg class="icon-base">
                              <use xlink:href="#icon-sidebar-notification">
                                <symbol
                                  id="icon-sidebar-notification"
                                  viewBox="0 0 20 20"
                                >
                                  <path
                                    d="M9.7 18a2.99 2.99 0 0 0 2.82-2H6.9a2.99 2.99 0 0 0 2.82 2zm7.3-6a1 1 0 0 1-1-1V8A6 6 0 1 0 4 8v3a1
                                   1 0 0 1-1 1 1 1 0 0 0 0 2h14a1 1 0 0 0 0-2z"
                                  />
                                </symbol>
                              </use>
                            </svg>
                          </span>
                          <span class="nav-item_title">Notifications</span>
                        </a>
                      </li>
                    </ul>
                    <ul class="sources">
                      <li class="nav-item nav-item-active">
                        <a href="#">
                          <span class="nav-item_icon">
                            <svg class="icon-base">
                              <use xlink:href="#icon-sidebar-search">
                                <symbol
                                  id="icon-sidebar-search"
                                  viewBox="0 0 20 20"
                                >
                                  <path
                                    d="M17.7 16.3l-3.1-3.11A6.94 6.94 0 0 0 16 9 6.96 6.96 0 0 0 4.05 4.05a6.96 6.96 0 0 0 0 9.9A6.96 6.96 0 0 0 9 16a6.94 6.94 0 0 0 4.19-1.4l3.1 3.1a1 1 0 0 0 1.42 0 1 1
                                   0 0 0 0-1.4zm-5.16-3.76a4.97 4.97 0 0 1-7.07 0 4.97 4.97 0 0 1 0-7.08 4.96 4.96 0 0 1 7.07 0 4.97 4.97 0 0 1 0 7.08z"
                                  />
                                </symbol>
                              </use>
                            </svg>
                          </span>
                          <span class="nav-item_title">Products</span>
                        </a>
                      </li>
                      <div class="sources_items"></div>
                    </ul>
                  </div>
                  <ul class="account">
                    <li class="nav-item">
                      <a href="#">
                        <span class="nav-item_icon">
                          <svg class="icon-base">
                            <use xlink:href="#icon-sidebar-setting">
                              <symbol
                                id="icon-sidebar-setting"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  d="M17.12 8.9l-1.26-.17a5.96 5.96 0 0 0-.82-1.98l.78-1a1 1 0 0
                                 0-.08-1.32l-.17-.17a1 1 0 0 0-1.32-.08l-1 .78a5.95 5.95 0 0 0-1.98-.82l-.16-1.26a1 1 0 0 0-1-.88h-.23a1 1 0 0
                                  0-.99.88l-.16 1.26a5.96 5.96 0 0 0-1.98.82l-1-.78a1 1 0 0 0-1.32.08l-.17.17a1 1 0 0 0-.08 1.32l.78 1a5.96 5.96 
                                  0 0 0-.82 1.98l-1.26.16a1 1 0 0 0-.88 1v.23a1 1 0 0 0 .88.99l1.26.16c.15.71.43 1.38.82 1.98l-.78 1a1 1 0
                                   0 0 .08 1.32l.17.17a1 1 0 0 0 1.32.08l1-.78a5.95 5.95 0 0 0 1.98.82l.16 1.26a1 1 0 0 0 1 .88h.23a1 1 0 
                                   0 0 .99-.88l.16-1.26a5.96 5.96 0 0 0 1.98-.82l1 .78a1 1 0 0 0 1.32-.08l.17-.17a1 1 0 0 0
                                    .08-1.32l-.78-1a5.95 5.95 0 0 0 .82-1.98l1.26-.16a1 1 0 0 0 .88-1v-.23a1 1 0 0 0-.88-.99zM10 13a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"
                                />
                              </symbol>
                            </use>
                          </svg>
                        </span>
                        <span class="nav-item_title">Settings</span>
                      </a>
                    </li>
                    <li class="nav-item">
                      <a href="#">
                        <span class="nav-item_icon">
                          <svg class="icon-base">
                            <use xlink:href="#icon-sidebar-support">
                              <symbol
                                id="icon-sidebar-support"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  d="M10 2a8 8 0 1 0 0 16 8 8 0 0 0 0-16zm.7 12.74a1 1 0 0 1-.7.26c-.26 0-.5-.09-.69-.26A.94.94 0 0 1 9 14c0-.28.1-.52.3-.71a1.02 1.02 
                                0 0 1 1.41 0c.2.19.29.43.29.7 0 .31-.1.56-.3.75zm2.32-6.08c-.14.26-.3.49-.5.68l-.96.87a3.52 3.52 0 0 0-.28.28.94.94 0 
                                0 0-.13.19.94.94 0 0 0-.07.17l-.09.37c-.09.5-.42.78-.92.78a.93.93 0 0 1-.67-.26 1 1 0 0 1-.28-.75 2.32 2.32 0 0 
                                1 .6-1.6l.67-.63.5-.45c.09-.1.17-.2.22-.3a.6.6 0 0 0 .08-.3.72.72 0 0 0-.25-.55.95.95 0 0 0-.67-.24c-.33 0-.57.08-.71.23a2.1 
                                2.1 0 0 0-.43.81c-.2.67-.64.8-.97.8a.96.96 0 0 1-.72-.29.93.93 0 0 1-.28-.66c0-.4.12-.8.37-1.21.25-.4.62-.73 1.1-.99s1.02-.39 
                                1.64-.39c.57 0 1.09.11 1.53.33.46.21.81.52 1.06.9a2.25 2.25 0 0 1 .16 2.2z"
                                />
                              </symbol>
                            </use>
                          </svg>
                        </span>
                        <span class="nav-item_title">Help Center</span>
                        <span class="nav-item_counter">
                          <svg class="icon-base">
                            <use xlink:href="#icon-sidebar-external-link">
                              <symbol
                                id="icon-sidebar-external-link"
                                viewBox="0 0 20 20"
                              >
                                <path
                                  fill-rule="nonzero"
                                  d="M14 12h2v2a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2v2H6v8h8v-2zm-1.414-6H11a1 1 0 
                                0 1 0-2h5v5a1 1 0 0 1-2 0V7.414l-3.793 3.793a1 1 0 1 1-1.414-1.414L12.586 6z"
                                />
                              </symbol>
                            </use>
                          </svg>
                        </span>
                      </a>
                    </li>
                  </ul>
                </div>
              </nav>
            </div>
          </div>
          <div id="page-content" class="page-layout_content">
            <div class="page-layout_content-container">
              <div class="page-layout_header">
                <div class="main-layout-header">
                  <div class="main-layout-header_title">
                    <div class="main-layout-header_logo-wrapper">
                      <img
                        class="main-layout-header_logo"
                        src="image/fur.png"
                      />
                    </div>
                    <h2>clickripple</h2>
                  </div>
                  <div class="main-layout-header_middle">
                    <div class="main-layout-header_content">
                      <form class="product-searchbar">
                        <div
                          id="keywords"
                          class="input-block product-searchbar_input product-searchbar-has-icon"
                          placeholder="Search products"
                          type="search"
                          value=""
                        >
                          <div
                            class="input-field input-field-no-value input-group"
                            placeholder="Search products"
                          >
                            <input
                              id="keywords"
                              class="form-control"
                              placeholder="Serach products"
                              type="search"
                            />

                            <div class="product-searchbar_addon-wrapper">
                              <svg
                                class="product-searchbar_icon icon-base"
                                aria-hidden="true"
                              >
                                <use xlink:href="#icon-sidebar-search">
                                  <symbol
                                    id="icon-sidebar-search"
                                    viewBox="0 0 20 20"
                                  >
                                    <path
                                      d="M17.7 16.3l-3.1-3.11A6.94 6.94 0 0 0 16 9 6.96 6.96 0 0 0 4.05 4.05a6.96 6.96 0 0 0 0 9.9A6.96 6.96 0 0 0 9 16a6.94 6.94 0 0 0 4.19-1.4l3.1 3.1a1 1 0 0 0 1.42 0 1 1
                                     0 0 0 0-1.4zm-5.16-3.76a4.97 4.97 0 0 1-7.07 0 4.97 4.97 0 0 1 0-7.08 4.96 4.96 0 0 1 7.07 0 4.97 4.97 0 0 1 0 7.08z"
                                    />
                                  </symbol>
                                </use>
                              </svg>
                              <div
                                class="popover dropdown product-searchbar_dropdown"
                                data-trekkie-id="dropdown"
                              >
                                <span class="popover-wrapper">
                                  <button
                                    class="dropdown_toggle btn btn-basic btn-regular btn-dropdown"
                                    type="button"
                                    tabindex="0"
                                  >
                                    <span class="btn-title">
                                      <span class="dropdown_title">
                                        <span>in All Categories</span>
                                      </span>
                                    </span>
                                    <span class="btn-icon-wrap">
                                      <svg class="icon-base">
                                        <use
                                          xlink:href="#icon-small-arrow-down"
                                        >
                                          <symbol
                                            id="icon-small-arrow-down"
                                            viewBox="0 0 20 20"
                                          >
                                            <path
                                              d="M13.7 8.3a1 1 0 0 0-1.4 0L10 10.58l-2.3-2.3A1 1 0 1 0 6.3 9.7l3 3a1 1 0 0 0 1.4 0l3-3a1 1 0 0 0 0-1.42z"
                                            />
                                          </symbol>
                                        </use>
                                      </svg>
                                    </span>
                                  </button>
                                </span>
                                <div class="popover-body popover-body-right">
                                  <ul class="dropdown_list">
                                    <li tabindex="-1">
                                      <button
                                        class="btn btn-basic btn-regular btn-block active"
                                        type="button"
                                        tabindex="0"
                                      >
                                        <span class="btn-title">
                                          <span>All Categories</span>
                                        </span>
                                      </button>
                                    </li>
                                  </ul>
                                  <ul class="dropdown_list">
                                    <li tabindex="-1">
                                      <button
                                        class="btn btn-basic btn-regular btn-block active"
                                        type="button"
                                        tabindex="0"
                                      >
                                        <span class="btn-title">
                                          <span>tables</span>
                                        </span>
                                      </button>
                                    </li>
                                    <li tabindex="-1">
                                      <button
                                        class="btn btn-basic btn-regular btn-block active"
                                        type="button"
                                        tabindex="0"
                                      >
                                        <span class="btn-title">
                                          <span>chairs</span>
                                        </span>
                                      </button>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <button class="btn btn-primary btn-lg" type="button">
                          <span class="btn-title">Search</span>
                        </button>
                      </form>
                    </div>
                    <div class="main-layout-header_actions">
                      <!------>
                      <!------->
                    </div>
                  </div>
                  <div class="main-layout-header_end">
                    <div class="main-layout-header_account-menu">
                      <div class="account-menu-wrapper">
                        <div
                          id="clickrippleAccountMenuContainer"
                          class="AccountMenu_AccountMenu"
                        >
                          <div class="MenuHeader_Header">
                            <button class="Toggle_Toggle">
                              <div class="Toggle_PersoanlInfo">
                                <span class="Avatar_Conatiner">
                                  <span class="Avatar_Avatar">R</span>
                                </span>
                              </div>
                              <span class="Toggle_UserInfo">
                                <span class="Toggle_storeName">Account</span>
                                <span class="Toggle_FirstName">
                                  <span>rawat.manish416@</span>
                                  <svg class="Toggle_Arrow"></svg>
                                </span>
                              </span>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="page-layout_body">
                <div class="page-layout_body-content">
                  <div class="notification-messages"></div>
				  
				  <?php 
				  
				    while($row=mysqli_fetch_array($result)){
				  
				  ?>
                  <div class="product-details">
                    <div>
                      <div>
                        <div class="panel -margin-top-sm">
                          <div class="panel-body product-info">
                            <div class="product-info_gallery">
                              <div class="img-gallery">
                              <!--  <div class="img-gallery-menu">
                                  <div>
                                    <div class="image-container">
                                      <img
                                        class="image-container_image"
                                        src="image/sofa.jpg"
                                      />
                                    </div>
                                  </div>
                                  <div>
                                    <div class="image-container">
                                      <img
                                        class="image-container_image"
                                        src="image/sofa.jpg"
                                      />
                                    </div>
                                  </div>
                                  <div>
                                    <div class="image-container">
                                      <img
                                        class="image-container_image"
                                        src="image/sofa.jpg"
                                      />
                                    </div>
                                  </div>
                                  <div>
                                    <div class="image-container">
                                      <img
                                        class="image-container_image"
                                        src="image/sofa.jpg"
                                      />
                                    </div>
                                  </div>
                                  <div>
                                    <div class="image-container">
                                      <img
                                        class="image-container_image"
                                        src="image/sofa.jpg"
                                      />
                                    </div>
                                  </div>
                                  <div>
                                    <div class="image-container">
                                      <img
                                        class="image-container_image"
                                        src="image/sofa.jpg"
                                      />
                                    </div>
                                  </div>
                                </div>-->
                                <div class="big-image-container">
									<img  class="big-image-container_image" src="images/<?php echo $row['productImage']?>" alt="#" />
                                  
                                </div>
                              </div>
                            </div>
                            <div class="product-info_details">
                              <h3 class="product-info_heading">
                              <?php echo $row['productName'] ?>
                              </h3>
                              <div class="product-info_header-details">
                                <span><?php echo $row['Stock'] ?> in Stock</span>
                                <div class="product-info_vertical-hr"></div>
                                <a class="product-info_stats-link" href="#">
                                  <svg class="icon-base">
                                    <use xlink:href="#icon-dashboard">
                                      <symbol
                                        id="icon-dashboard"
                                        viewBox="0 0 20 20"
                                      >
                                        <path
                                          d="M6 11a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-6zm10-4a1 1 0 0 0-1-1h-1a1 1
                                           0 0 0-1 1v10a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V7zm-5-4a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h1a1 
                                           1 0 0 0 1-1V3z"
                                        />
                                      </symbol>
                                    </use>
                                  </svg>
                                  
                                </a>
                                
                              </div>
                              <div class="product-actions">
                               
                               
                                <div>
                                 
								  
								  <form method="POST">
                        <input type="submit" value="Add To Import List" name="onImport" class="product-actions_edit-product btn btn-basic btn-regular"/>
									</form>
                                  
                                </div>
                              </div>
                              <div class="product-options">
                                <div class="product-options_row">
                                  <span
                                    class="product-options_key product-options_key-price"
                                    >Price</span
                                  >
                                  
                                     <?php 
                     
                        $priceMargin = $UF->getMargin();
                        $priceToAdd = $curProPrice*((int)$priceMargin / 100);
                        echo $curProPrice+$priceToAdd;

                     ?>
                                    </div>
                                    
                                  
                                
                                <div class="product-options_row">
                                  <span class="product-options_key">Size</span>
                                  <div
                                    class="product-options_value product-options_value-has-blocks"
                                  >
                                    
                                    <button
                                      class="tag-default product-options_variant-tag"
                                    >
                                      United States
                                    </button>
                                    <button
                                      class="tag-default product-options_variant-tag"
                                    >
                                      Canada
                                    </button>
                                    
                                    
                                  </div>
                                </div>
                                <div class="product-options_row">
                                  <span class="product-options_key"
                                    >Shipping</span
                                  >
                                  <span class="product-options_value">
                                    <div class="shipping-details">
                                      <div>
                                        <div
                                          class="shipping-details_shipping-info"
                                        >
                                          <span class="shipping-details_price"
                                            >CAD 50</span
                                          >
                                          <span>to</span>
                                          <button
                                            class="shipping-details_more-info"
                                          >
                                            <span>
                                              <span
                                                class="shipping-details_country"
                                                >United States</span
                                              >
                                              <span>via</span>
                                              <span
                                                class="shipping-details_method"
                                                >AliExpress Standard
                                                Shipping</span
                                              >
                                            </span>
                                          </button>
                                        </div>
                                        <div
                                          class="shipping-details_estimated-delivery"
                                        >
                                          Estimated delivery time 15-45 days
                                        </div>
                                      </div>
                                    </div>
                                  </span>
                                </div>
                                <div class="product-options_row">
                                  <div class="product-options_key">
                                    Supplier
                                  </div>
                                  <div
                                    class="product-options_value product-options_value-has-blocks"
                                  >
                                    <a href="#">Homeware Store</a>
                                  </div>
                                  <div class="product-options_feedback-rating">
                                    <div class="supplier-feedback-rating">
                                      <span
                                        class="badge badge-default supplier-feedback-rating_summary"
                                      >
                                        <span
                                          class="supplier-feedback-rating-small supplier-feedback-rating_overall-rating"
                                          >95.50% positive feedback</span
                                        >
                                      </span>
                                      <span
                                        class="badge badge-default supplier-feedback-rating_summary"
                                      >
                                        <span
                                          class="supplier-feedback-rating-small supplier-feedback-rating_years-open"
                                          >Open 7 years</span
                                        >
                                      </span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div id="product-stats" class="product-stats-anchor">
                          <div class="product-stats panel">
                            <div class="panel-header product-stats_panel">
                              <h3>Product statistics</h3>
                            </div>
                            <div class="panel-body product-stats_body">
                              <div
                                class="product-stats-item product-stats-item-narrow"
                              >
                                <label
                                  class="product-stats-item_title product-stats-item_title-reviews"
                                >
                                  <div class="product-stats-item_stars">
                                    <svg class="icon-star">
                                      <use xlink:href="#icon-star">
                                        <symbol
                                          id="icon-star"
                                          viewBox="0 0 20 20"
                                        >
                                          <path
                                            d="M10.92 3.73l1.6 3.33 3.6.54c.84.12 1.18 1.17.57 1.78l-2.6 2.59.61 3.66c.14.85-.73 1.5-1.48
                                             1.1L10 15l-3.22 1.73c-.75.4-1.62-.25-1.48-1.1l.61-3.66-2.6-2.6c-.6-.6-.27-1.65.57-1.77l3.6-.54 
                                             1.6-3.33a1.01 1.01 0 0 1 1.84 0z"
                                          />
                                        </symbol>
                                      </use>
                                    </svg>
                                    <svg class="icon-star">
                                      <use xlink:href="#icon-star">
                                        <symbol
                                          id="icon-star"
                                          viewBox="0 0 20 20"
                                        >
                                          <path
                                            d="M10.92 3.73l1.6 3.33 3.6.54c.84.12 1.18 1.17.57 1.78l-2.6 2.59.61 3.66c.14.85-.73 1.5-1.48
                                               1.1L10 15l-3.22 1.73c-.75.4-1.62-.25-1.48-1.1l.61-3.66-2.6-2.6c-.6-.6-.27-1.65.57-1.77l3.6-.54 
                                               1.6-3.33a1.01 1.01 0 0 1 1.84 0z"
                                          />
                                        </symbol>
                                      </use>
                                    </svg>
                                    <svg class="icon-star">
                                      <use xlink:href="#icon-star">
                                        <symbol
                                          id="icon-star"
                                          viewBox="0 0 20 20"
                                        >
                                          <path
                                            d="M10.92 3.73l1.6 3.33 3.6.54c.84.12 1.18 1.17.57 1.78l-2.6 2.59.61 3.66c.14.85-.73 1.5-1.48
                                               1.1L10 15l-3.22 1.73c-.75.4-1.62-.25-1.48-1.1l.61-3.66-2.6-2.6c-.6-.6-.27-1.65.57-1.77l3.6-.54 
                                               1.6-3.33a1.01 1.01 0 0 1 1.84 0z"
                                          />
                                        </symbol>
                                      </use>
                                    </svg>
                                    <svg class="icon-star">
                                      <use xlink:href="#icon-star">
                                        <symbol
                                          id="icon-star"
                                          viewBox="0 0 20 20"
                                        >
                                          <path
                                            d="M10.92 3.73l1.6 3.33 3.6.54c.84.12 1.18 1.17.57 1.78l-2.6 2.59.61 3.66c.14.85-.73 1.5-1.48
                                               1.1L10 15l-3.22 1.73c-.75.4-1.62-.25-1.48-1.1l.61-3.66-2.6-2.6c-.6-.6-.27-1.65.57-1.77l3.6-.54 
                                               1.6-3.33a1.01 1.01 0 0 1 1.84 0z"
                                          />
                                        </symbol>
                                      </use>
                                    </svg>
                                    <svg class="icon-star">
                                      <use xlink:href="#icon-star">
                                        <symbol
                                          id="icon-star"
                                          viewBox="0 0 20 20"
                                        >
                                          <path
                                            d="M10.92 3.73l1.6 3.33 3.6.54c.84.12 1.18 1.17.57 1.78l-2.6 2.59.61 3.66c.14.85-.73 1.5-1.48
                                               1.1L10 15l-3.22 1.73c-.75.4-1.62-.25-1.48-1.1l.61-3.66-2.6-2.6c-.6-.6-.27-1.65.57-1.77l3.6-.54 
                                               1.6-3.33a1.01 1.01 0 0 1 1.84 0z"
                                          />
                                        </symbol>
                                      </use>
                                    </svg>
                                  </div>
                                </label>
                                <div class="product-stats-item_rating">
                                  <strong>4.8</strong>/5
                                </div>
                                <p class="product-stats-item_description">
                                  1 reviews on ClickRipple
                                  <a class="product-stats-item_reviews-link"
                                    >Read Reviews</a
                                  >
                                </p>
                              </div>
                              <div class="product-stats-item">
                                <label class="product-stats-item_title"
                                  >Imports</label
                                >
                                <div
                                  class="product-stats-item_counter product-stats-item_counter-imports"
                                >
                                  10
                                </div>
                                <p class="product-stats-item_description">
                                  This product has been added to 10 ClickRipple
                                  stores
                                </p>
                              </div>
                              <div class="product-stats-item">
                                <label class="product-stats-item_title"
                                  >Orders (6 months)</label
                                >
                                <div
                                  class="product-stats-item_counter product-stats-item_counter-orders-180-days"
                                >
                                  1
                                </div>
                                <p class="product-stats-item_description">
                                  This product has been ordered through ClickRipple
                                  and/or AliExpress 1 times in the past 6
                                  months.
                                </p>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="panel expanding-panel -margin-top-sm">
                          <div class="panel-header">
                            <h3>Product Description</h3>
                          </div>
                         <!-- <div class="panel-body panel-body-with-fade">-->
                            <div
                              class="expanding-panel-body"
                            >
                             <span>
							 
							 
							 
							 <?php echo $row['Description'] ?>
							 </span>
                            <!--</div>-->
                            
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
				  <?php
					}
					?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
