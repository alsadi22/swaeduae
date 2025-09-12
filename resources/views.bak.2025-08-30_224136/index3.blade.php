@extends('layout.layout')
@section('content')
    <!-- Start Header Section -->
    <header class="cs_site_header cs_style_1 cs_color_1 cs_fs_18 cs_sticky_header">
      <div class="cs_top_header cs_accent_bg">
        <div class="cs_top_header_in">
          <div class="cs_top_header_left">
            <ul class="cs_top_header_list cs_mp0">
              <li><b>Address: </b>3517 W. Gray St. Utica, Pennsylvania 57867</li>
              <li>info@company.com</li>
            </ul>
          </div>
          <div class="cs_top_header_right">
            <ul class="cs_top_header_list cs_mp0">
              <li><a href="#">Help</a></li>
              <li><a href="#">Support</a></li>
              <li><a href="#">Contact</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="cs_main_header">
        <div class="cs_main_header_in">
          <div class="cs_main_header_left">
            <a class="cs_site_branding" href="{{ route('home') }}">
              <img src="assets/images/logo_2.svg" alt="Logo">
            </a>
          </div>
          <div class="cs_main_header_center">
            <div class="cs_nav cs_medium cs_primary_font">
              <ul class="cs_nav_list">
                <li class="menu-item-has-children">
                  <a href="{{ route('home') }}">Home</a>
                  <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('home') }}">Home v2</a></li>
                    <li><a href="{{ route('home') }}">Home v3</a></li>
                  </ul>
                </li>
                <li><a href="{{ route('about') }}">About Us</a></li>
                <li class="menu-item-has-children">
                  <a href="{{ route('events.index') }}">Destinations</a>
                  <ul>
                    <li><a href="{{ route('events.index') }}">Destination</a></li>
                    <li><a href="{{ route('events.index') }}">Destination Details</a></li>
                  </ul>
                </li>
                <li class="menu-item-has-children">
                  <a href="{{ route('events.index') }}">Tours</a>
                  <ul>
                    <li><a href="{{ route('events.index') }}">Tour</a></li>
                    <li><a href="{{ route('events.index') }}">Tour Details</a></li>
                  </ul>
                </li>
                <li class="menu-item-has-children">
                  <a href="{{ route('events.index') }}">Blog</a>
                  <ul>
                    <li><a href="{{ route('events.index') }}">Blog</a></li>
                    <li><a href="{{ route('events.index') }}">Blog Details</a></li>
                  </ul>
                </li>
                <li><a href="{{ route('contact.show') }}">Contacts</a></li>
              </ul>
            </div>
          </div>
          <div class="cs_main_header_right">
            <div class="cs_header_toolbox">
              <div>
                <button class="cs_search_btn cs_fs_24" type="button"><i class="fa-solid fa-magnifying-glass"></i></button>
              </div>
              <div class="cs_fs_20 cs_medium">+8 (123) 985 789</div>
            </div>
          </div>
        </div>
      </div>
    </header>

     <x-header_search/>
    <!-- End Header Section -->
    
    <!-- Start Hero Section -->
    <section class="cs_hero cs_style_3 cs_bg_filed" data-src="assets/images/hero_bg_3.jpeg">
      <div class="cs_hero_in">
        <div class="cs_hero_img">
          <img src="assets/images/hero_3.png" alt="Hero Thumb">
        </div>
        <div class="cs_hero_text">
          <h3 class="cs_hero_mini_title cs_fs_25 text-uppercase cs_normal cs_accent_color cs_ternary_font">Tour And Travels</h3>
          <h1 class="cs_hero_title cs_fs_85 cs_semibold text-uppercase wow fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.2s">Lets Travel The World With Us</h1>
          <p class="cs_hero_desc cs_fs_18">Denouncing pleasure and praising pain was born and will give you complete great explorer of the truth the master-builder.</p>
          <a href="{{ route('about') }}" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
            Read More
            <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/><path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
            </svg>                
          </a>
        </div>
      </div>
      <div class="cs_wave_animation"></div>
    </section>
    <!-- End Hero Section -->

    <!-- Start About Section -->
    <section class="cs_about cs_style_2">
      <div class="cs_height_140 cs_height_lg_80"></div>
      <div class="container">
        <div class="row align-items-center cs_gap_y_40">
          <div class="col-lg-6">
            <div class="cs_about_thumb position-relative">
              <img src="assets/images/about_left_1.png" alt="About Thumb">
              <div class="cs_about_img_sm position-absolute">
                <img src="assets/images/about_shape.png" alt="Rotating Logo">
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cs_about_info">
              <div class="cs_section_heading cs_style_1">
                <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">Adventure & Travels</h3>
                <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.2s">We are Most Funning Company About Travel And adventure</h2>
              </div>
              <div class="cs_height_28 cs_height_lg_20"></div>
              <p class="cs_about_text">Quisque dignissim enim diam, eget pulvinar ex viverra id. Nulla a lobortis lectus, id volutpat magna. Morbi consequat porttitor fermentum. Nulla vestibulum tincidunt viverra. Vestibulum accumsan molestie lorem, non laoreet massa. Duis at dui sem. Vivamus ut gravida libero Quisque dignissim enim diam, eget pulvinar ex viverra id. Nulla a lobortis Quisdignissim enim diam, eget pulvinar ex viverra id. Nulla a lobortis lectus, id volutpat magna. Morbti consequat porttitor fermentum. Nulla vestibulum tincidunt </p>
              <a href="{{ route('about') }}" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
                Read More
                <svg width="20" height="10" viewBox="0 0 20 10" fill="none"
                xmlns="http://www.w3.org/2000/svg"><path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/><path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
                </svg>                
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_140 cs_height_lg_80"></div>
    </section>
    <!-- End About Section -->

    <!-- Start Video Section -->
    <section class="cs_video_block cs_style_1 cs_bg_filed cs_type_1 cs_primary_bg cs_bg_fixed" data-src="assets/images/video_block_2.jpeg">
      <div class="cs_height_170 cs_height_lg_80"></div>
      <div class="container position-relative">
        <a href="https://www.youtube.com/embed/eSUmkFPln_U" class="cs_player_btn cs_center cs_accent_bg cs_video_open cs_right_align">
          <svg width="40" height="47" viewBox="0 0 40 47" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M36.9921 17.8114L9.63992 0.951019C7.66105 -0.267256 5.26855 -0.317908 3.23984 0.815524C1.21113 1.94878 0 4.01294 0 6.3367V39.9039C0 43.4175 2.83109 46.2914 6.31071 46.3104C6.32021 46.3104 6.32971 46.3105 6.33902 46.3105C7.42642 46.3104 8.55958 45.9696 9.61794 45.3238C10.4693 44.8043 10.7384 43.693 10.219 42.8417C9.69952 41.9902 8.58807 41.7212 7.73693 42.2407C7.2419 42.5426 6.75844 42.6988 6.33016 42.6987C5.01727 42.6916 3.61159 41.5669 3.61159 39.904V6.33679C3.61159 5.33994 4.13113 4.4547 5.00127 3.96853C5.87149 3.48236 6.89764 3.50407 7.74543 4.02606L35.0977 20.8864C35.9198 21.3926 36.3902 22.2366 36.3882 23.2021C36.3862 24.1674 35.9124 25.0095 35.0857 25.514L15.31 37.6224C14.4594 38.1432 14.192 39.2549 14.7128 40.1054C15.2335 40.956 16.3453 41.2234 17.1959 40.7026L36.9693 28.5956C38.8625 27.4407 39.9955 25.4272 40 23.2093C40.0045 20.9916 38.8797 18.9735 36.9921 17.8114Z" fill="currentColor" />
          </svg>
        </a>
        <h2 class="cs_video_title cs_fs_56 cs_semibold cs_white_color mb-0">Ready to Travel With <br> Real Adventure And <br> Enjoy Natural</h2>
      </div>
      <div class="cs_height_170 cs_height_lg_80"></div>
    </section>
    <!-- End Video Section -->

    <!-- Start Package Section -->
    <section>
      <div class="cs_height_135 cs_height_lg_75"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">CHOOSE YOUR PACKAGE</h3>
          <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Popular Tours Packages</h2>
        </div>
        <div class="cs_height_55 cs_height_lg_40"></div>
      </div>
      <div class="container-fluid">
        <div class="row cs_gap_y_24">
          <div class="col-lg-3 col-sm-6">
            <div class="cs_card cs_style_1 cs_bg_filed cs_radius_5 position-relative" data-src="assets/images/package_img_1.jpeg">
              <div class="cs_card_overlay cs_radius_5 position-absolute w-100 h-100"></div>
              <div class="cs_card_content cs_accent_bg position-absolute">
                <div class="cs_card_meta cs_white_color">
                  <div>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Paris</span>
                  </div>
                  <div>
                    <i class="fa-regular fa-clock"></i>
                    <span>07 Days</span>
                  </div>
                  <div>
                    <i class="fa-solid fa-star"></i>
                    <span> 5K+ Rating</span>
                  </div>
                </div>
                <h2 class="cs_card_title cs_fs_24 cs_medium cs_white_color"><a href="">Aegean Adventure</a></h2>
                <div class="cs_card_action">
                  <a href="" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
                    Book Now
                    <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/>
                      <path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
                    </svg>                
                  </a>
                  <span class="cs_card_price cs_fs_24 cs_medium cs_white_color mb-0">$370</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_card cs_style_1 cs_bg_filed cs_radius_5 position-relative" data-src="assets/images/package_img_2.jpeg">
              <div class="cs_card_overlay cs_radius_5 position-absolute w-100 h-100"></div>
              <div class="cs_card_content cs_accent_bg position-absolute">
                <div class="cs_card_meta cs_white_color">
                  <div>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Paris</span>
                  </div>
                  <div>
                    <i class="fa-regular fa-clock"></i>
                    <span>07 Days</span>
                  </div>
                  <div>
                    <i class="fa-solid fa-star"></i>
                    <span> 4.5K+ Rating</span>
                  </div>
                </div>
                <h2 class="cs_card_title cs_fs_24 cs_medium cs_white_color"><a href="">Aegean Adventure</a></h2>
                <div class="cs_card_action">
                  <a href="" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
                    Book Now
                    <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/>
                      <path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
                    </svg>                
                  </a>
                  <span class="cs_card_price cs_fs_24 cs_medium cs_white_color mb-0">$380</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_card cs_style_1 cs_bg_filed cs_radius_5 position-relative" data-src="assets/images/package_img_3.jpeg">
              <div class="cs_card_overlay cs_radius_5 position-absolute w-100 h-100"></div>
              <div class="cs_card_content cs_accent_bg position-absolute">
                <div class="cs_card_meta cs_white_color">
                  <div>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Paris</span>
                  </div>
                  <div>
                    <i class="fa-regular fa-clock"></i>
                    <span>07 Days</span>
                  </div>
                  <div>
                    <i class="fa-solid fa-star"></i>
                    <span> 4K+ Rating</span>
                  </div>
                </div>
                <h2 class="cs_card_title cs_fs_24 cs_medium cs_white_color"><a href="">Aegean Adventure</a></h2>
                <div class="cs_card_action">
                  <a href="" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
                    Book Now
                    <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/>
                      <path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
                    </svg>                
                  </a>
                  <span class="cs_card_price cs_fs_24 cs_medium cs_white_color mb-0">$390</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_card cs_style_1 cs_bg_filed cs_radius_5 position-relative" data-src="assets/images/package_img_4.jpeg">
              <div class="cs_card_overlay cs_radius_5 position-absolute w-100 h-100"></div>
              <div class="cs_card_content cs_accent_bg position-absolute">
                <div class="cs_card_meta cs_white_color">
                  <div>
                    <i class="fa-solid fa-location-dot"></i>
                    <span>Paris</span>
                  </div>
                  <div>
                    <i class="fa-regular fa-clock"></i>
                    <span>07 Days</span>
                  </div>
                  <div>
                    <i class="fa-solid fa-star"></i>
                    <span> 5K+ Rating</span>
                  </div>
                </div>
                <h2 class="cs_card_title cs_fs_24 cs_medium cs_white_color"><a href="">Aegean Adventure</a></h2>
                <div class="cs_card_action">
                  <a href="" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
                    Book Now
                    <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/>
                      <path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
                    </svg>                
                  </a>
                  <span class="cs_card_price cs_fs_24 cs_medium cs_white_color mb-0">$360</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_140 cs_height_lg_80"></div>
    </section>
    <!-- End Package Section -->

    <!-- Start Service Section -->
     <section class="cs_services_1 cs_accent_bg_1">
      <div class="cs_height_140 cs_height_lg_75"></div>
      <div class="container">
        <div class="row cs_gap_y_24">
          <div class="col-lg-6">
            <div class="cs_section_heading cs_style_1">
              <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">Our Services</h3>
              <h2 class="cs_section_title cs_semibold cs_fs_56 text-capitalise mb-0">What We Offer For Our Great Packages</h2>
            </div>
            <div class="cs_height_37 cs_height_lg_20"></div>
            <a href="{{ route('contact.show') }}" class="cs_btn cs_style_1 cs_fs_18 cs_medium">
              Schedule a Visit
              <svg width="20" height="10" viewBox="0 0 20 10" fill="none"
                xmlns="http://www.w3.org/2000/svg"><path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/><path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
              </svg>                
            </a>
            <div class="cs_height_20 cs_height_lg_20"></div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_iconbox cs_style_2 text-center">
              <div class="cs_iconbox_icon cs_radius_15 cs_center">
                <img src="assets/images/icons/home_icon.svg" alt="Featured Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_bold">Smart Homes</h2>
              <p class="cs_iconbox_subtitle mb-0">Immigration advisory visa a foundation was establishe with a  ideaImmigration advisory visa a foundation</p>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_iconbox cs_style_2 text-center">
              <div class="cs_iconbox_icon cs_radius_15 cs_center">
                <img src="assets/images/icons/location_icon.svg" alt="Featured Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_bold">Atractive Location</h2>
              <p class="cs_iconbox_subtitle mb-0">Immigration advisory visa a foundation was establishe with a  ideaImmigration advisory visa a foundation</p>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_iconbox cs_style_2 text-center">
              <div class="cs_iconbox_icon cs_radius_15 cs_center">
                <img src="assets/images/icons/pool_icon.svg" alt="Featured Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_bold">Swimming Pool</h2>
              <p class="cs_iconbox_subtitle mb-0">Immigration advisory visa a foundation was establishe with a  ideaImmigration advisory visa a foundation</p>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_iconbox cs_style_2 text-center">
              <div class="cs_iconbox_icon cs_radius_15 cs_center">
                <img src="assets/images/icons/wifi_icon.svg" alt="Featured Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_bold">Fast Speed WI-FI</h2>
              <p class="cs_iconbox_subtitle mb-0">Immigration advisory visa a foundation was establishe with a  ideaImmigration advisory visa a foundation</p>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_iconbox cs_style_2 text-center">
              <div class="cs_iconbox_icon cs_radius_15 cs_center">
                <img src="assets/images/icons/car_icon.svg" alt="Featured Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_bold">Parking Space</h2>
              <p class="cs_iconbox_subtitle mb-0">Immigration advisory visa a foundation was establishe with a  ideaImmigration advisory visa a foundation</p>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="cs_iconbox cs_style_2 text-center">
              <div class="cs_iconbox_icon cs_radius_15 cs_center">
                <img src="assets/images/icons/fitness_icon.svg" alt="Featured Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_bold">Fitness Center</h2>
              <p class="cs_iconbox_subtitle mb-0">Immigration advisory visa a foundation was establishe with a  ideaImmigration advisory visa a foundation</p>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_140 cs_height_lg_80"></div>
     </section>
    <!-- End Service Section -->

    <!-- Start gallery Section -->
    <section>
      <div class="cs_height_135 cs_height_lg_75"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">OUR PORTFOLIO</h3>
          <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Explore our photos</h2>
        </div>
        <div class="cs_height_55 cs_height_lg_40"></div>
        <div class="cs_grid_3 cs_gallery_list cs_style_1">
          <a href="assets/images/gallery_1.jpeg" class="cs_gallery_item cs_zoom">
            <img src="assets/images/gallery_1.jpeg" alt="Gallery Image" class="cs_zoom_in">
            <div class="cs_gallery_overlay"></div>
            <div class="cs_gallery_icon position-absolute">
              <img src="assets/images/icons/plus_icon.svg" alt="Icon">
            </div>
          </a>
          <a href="assets/images/gallery_2.jpeg" class="cs_gallery_item cs_zoom">
            <img src="assets/images/gallery_2.jpeg" alt="Gallery Image" class="cs_zoom_in">
            <div class="cs_gallery_overlay"></div>
            <div class="cs_gallery_icon position-absolute">
              <img src="assets/images/icons/plus_icon.svg" alt="Icon">
            </div>
          </a>
          <a href="assets/images/gallery_3.jpeg" class="cs_gallery_item cs_zoom">
            <img src="assets/images/gallery_3.jpeg" alt="Gallery Image" class="cs_zoom_in">
            <div class="cs_gallery_overlay"></div>
            <div class="cs_gallery_icon position-absolute">
              <img src="assets/images/icons/plus_icon.svg" alt="Icon">
            </div>
          </a>
          <a href="assets/images/gallery_4.jpeg" class="cs_gallery_item cs_zoom">
            <img src="assets/images/gallery_4.jpeg" alt="Gallery Image" class="cs_zoom_in">
            <div class="cs_gallery_overlay"></div>
            <div class="cs_gallery_icon position-absolute">
              <img src="assets/images/icons/plus_icon.svg" alt="Icon">
            </div>
          </a>
          <a href="assets/images/gallery_5.jpeg" class="cs_gallery_item cs_zoom">
            <img src="assets/images/gallery_5.jpeg" alt="Gallery Image" class="cs_zoom_in">
            <div class="cs_gallery_overlay"></div>
            <div class="cs_gallery_icon position-absolute">
              <img src="assets/images/icons/plus_icon.svg" alt="Icon">
            </div>
          </a>
          <a href="assets/images/gallery_6.jpeg" class="cs_gallery_item cs_zoom">
            <img src="assets/images/gallery_6.jpeg" alt="Gallery Image" class="cs_zoom_in">
            <div class="cs_gallery_overlay"></div>
            <div class="cs_gallery_icon position-absolute">
              <img src="assets/images/icons/plus_icon.svg" alt="Icon">
            </div>
          </a>
        </div>
      </div>
      <div class="cs_height_140 cs_height_80"></div>
    </section>
    <!-- End gallery Section -->

    <!-- Start Support Section -->
    <section class="cs_accent_bg_1">
      <div class="cs_height_140 cs_height_lg_80"></div>
      <div class="container">
        <div class="row cs_gap_y_24">
          <div class="col-lg-4">
            <div class="cs_iconbox cs_style_3">
              <div class="cs_iconbox_icon cs_radius_15">
                <img src="assets/images/icons/support_icon.svg" alt="Icon">
              </div>
              <div>
                <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Support</h2>
                <p class="cs_iconbox_subtitle mb-0">Condimentum lobortis donec nibh molestie massa dictumst cursus.</p>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="cs_iconbox cs_style_3">
              <div class="cs_iconbox_icon cs_radius_15">
                <img src="assets/images/icons/security_icon.svg" alt="Icon">
              </div>
              <div>
                <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Security</h2>
                <p class="cs_iconbox_subtitle mb-0">Condimentum lobortis donec nibh molestie massa dictumst cursus.</p>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="cs_iconbox cs_style_3">
              <div class="cs_iconbox_icon cs_radius_15">
                <img src="assets/images/icons/quality_icon.svg" alt="Icon">
              </div>
              <div>
                <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Quality</h2>
                <p class="cs_iconbox_subtitle mb-0">Condimentum lobortis donec nibh molestie massa dictumst cursus.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_140 cs_height_lg_80"></div>
    </section>
    <!-- End Support Section -->

    <!-- Start destination Section -->
    <section>
      <div class="cs_height_135 cs_height_lg_75"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">POPULAR DESTINATION</h3>
          <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Popular Destinations</h2>
        </div>
        <div class="cs_height_55 cs_height_lg_40"></div>
        <div class="cs_grid_2">
          <div class="cs_grid_item">
            <a href="{{ route('events.index') }}" class="cs_card cs_style_2 cs_zoom position-relative cs_radius_8">
              <div class="cs_card_thumb w-100 h-100">
                <img src="assets/images/popular_destination_6.jpeg" alt="Card Image" class="w-100 h-100 cs_zoom_in">
              </div>
              <div class="cs_card_content position-absolute">
                <h2 class="cs_card_title cs_fs_35 cs_medium cs_white_color">Lao Lading Island</h2>
                <p class="cs_card_subtitle cs_fs_18 cs_medium cs_white_color mb-0">Krabal, 12 Trips</p>
              </div>
            </a>
          </div>
          <div class="cs_grid_item">
            <a href="{{ route('events.index') }}" class="cs_card cs_style_2 cs_zoom position-relative cs_radius_8">
              <div class="cs_card_thumb w-100 h-100">
                <img src="assets/images/popular_destination_7.jpeg" alt="Card Image" class="w-100 h-100 cs_zoom_in">
              </div>
              <div class="cs_card_content position-absolute">
                <h2 class="cs_card_title cs_fs_35 cs_medium cs_white_color">
                  Taj Mahal</h2>
                <p class="cs_card_subtitle cs_fs_18 cs_medium cs_white_color mb-0">
                  Thailand, 50 Trips</p>
              </div>
            </a>
          </div>
          <div class="cs_grid_item">
            <a href="{{ route('events.index') }}" class="cs_card cs_style_2 cs_zoom position-relative cs_radius_8">
              <div class="cs_card_thumb w-100 h-100">
                <img src="assets/images/popular_destination_8.jpeg" alt="Card Image" class="w-100 h-100 cs_zoom_in">
              </div>
              <div class="cs_card_content position-absolute">
                <h2 class="cs_card_title cs_fs_35 cs_medium cs_white_color">Ton Kwen Temple</h2>
                <p class="cs_card_subtitle cs_fs_18 cs_medium cs_white_color mb-0">Thailand, 20 Trips</p>
              </div>
            </a>
          </div>
          <div class="cs_grid_item">
            <a href="{{ route('events.index') }}" class="cs_card cs_style_2 cs_zoom position-relative cs_radius_8">
              <div class="cs_card_thumb w-100 h-100">
                <img src="assets/images/popular_destination_9.jpeg" alt="Card Image" class="w-100 h-100 cs_zoom_in">
              </div>
              <div class="cs_card_content position-absolute">
                <h2 class="cs_card_title cs_fs_35 cs_medium cs_white_color">Pryde Mountains</h2>
                <p class="cs_card_subtitle cs_fs_18 cs_medium cs_white_color mb-0">Prydelands, 100 Trips</p>
              </div>
            </a>
          </div>
          <div class="cs_grid_item">
            <a href="{{ route('events.index') }}" class="cs_card cs_style_2 cs_zoom position-relative cs_radius_8">
              <div class="cs_card_thumb w-100 h-100">
                <img src="assets/images/popular_destination_10.jpeg" alt="Card Image" class="w-100 h-100 cs_zoom_in">
              </div>
              <div class="cs_card_content position-absolute">
                <h2 class="cs_card_title cs_fs_35 cs_medium cs_white_color">Eiffel Tower</h2>
                <p class="cs_card_subtitle cs_fs_18 cs_medium cs_white_color mb-0">Paris, 24 Trips</p>
              </div>
            </a>
          </div>
        </div>
      </div>
      <div class="cs_height_140 cs_height_lg_80"></div>
    </section>
    <!-- End destination Section -->

    <!-- Start Testimonial Section -->
    <section class="cs_slider cs_style_2 cs_accent_bg_1 position-relative">
      <div class="cs_height_135 cs_height_lg_75"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Our Testimonial</h3>
          <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0">What Our Clients Says</h2>
        </div>
        <div class="cs_height_55 cs_height_lg_40"></div>
        <div class="position-relative">
          <div class="cs_slider_container" data-autoplay="0" data-loop="1" data-speed="600" data-slides-per-view="responsive" data-xxs-slides="1" data-xs-slides="1" data-sm-slides="1" data-md-slides="2" data-lg-slides="2" data-add-slides="2">
            <div class="cs_slider_wrapper">
              <div class="slick_slide_in">
                <div class="cs_testimonial cs_style_2 cs_white_bg position-relative">
                  <blockquote class="cs_fs_18">The most advanced revenue than this. Iwill refer everyone I like Level more and more each day because it makes my easier. It really saves me time and effort. Level is exactly business has been lacking.</blockquote>
                  <div class="cs_testimonial_avatar">
                    <div class="cs_avatar_thumb">
                      <img src="assets/images/testimonial_avatar_3.jpeg" alt="Avatar">
                    </div>
                    <div class="cs_avatar_info">
                      <h3 class="cs_avatar_title cs_fs_24 cs_medium">Cameron Williamson</h3>
                      <p class="cs_avatar_subtitle cs_accent_color mb-0">Ceo & Founder</p>
                    </div>
                  </div>
                  <div class="cs_quote_shape position-absolute">
                    <img src="assets/images/quote_shape.svg" alt="Quote Shape">
                  </div>
                </div>
              </div>
              <div class="slick_slide_in">
                <div class="cs_testimonial cs_style_2 cs_white_bg position-relative">
                  <blockquote class="cs_fs_18">The most advanced revenue than this. Iwill refer everyone I like Level more and more each day because it makes my easier. It really saves me time and effort. Level is exactly business has been lacking.</blockquote>
                  <div class="cs_testimonial_avatar">
                    <div class="cs_avatar_thumb">
                      <img src="assets/images/testimonial_avatar_4.jpeg" alt="Avatar">
                    </div>
                    <div class="cs_avatar_info">
                      <h3 class="cs_avatar_title cs_fs_24 cs_medium">Cameron Williamson</h3>
                      <p class="cs_avatar_subtitle cs_accent_color mb-0">Ceo & Founder</p>
                    </div>
                  </div>
                  <div class="cs_quote_shape position-absolute">
                    <img src="assets/images/quote_shape.svg" alt="Quote Shape">
                  </div>
                </div>
              </div>
              <div class="slick_slide_in">
                <div class="cs_testimonial cs_style_2 cs_white_bg position-relative">
                  <blockquote class="cs_fs_18">The most advanced revenue than this. Iwill refer everyone I like Level more and more each day because it makes my easier. It really saves me time and effort. Level is exactly business has been lacking.</blockquote>
                  <div class="cs_testimonial_avatar">
                    <div class="cs_avatar_thumb">
                      <img src="assets/images/testimonial_avatar_3.jpeg" alt="Avatar">
                    </div>
                    <div class="cs_avatar_info">
                      <h3 class="cs_avatar_title cs_fs_24 cs_medium">Cameron Williamson</h3>
                      <p class="cs_avatar_subtitle cs_accent_color mb-0">Ceo & Founder</p>
                    </div>
                  </div>
                  <div class="cs_quote_shape position-absolute">
                    <img src="assets/images/quote_shape.svg" alt="Quote Shape">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="cs_pagination cs_style_1 cs_mobile_show justify-content-center"></div>
          <div class="cs_slider_arrows cs_style_1 cs_mobile_hide">
            <div class="cs_left_arrow cs_slider_arrow">
              <i class="fa-solid fa-arrow-left"></i>
            </div>
            <div class="cs_right_arrow cs_slider_arrow">
              <i class="fa-solid fa-arrow-right"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_135 cs_height_lg_75"></div>
    </section>
    <!-- End Testimonial Section -->

    <!-- Start Blog Section -->
    <section>
      <div class="cs_height_135 cs_height_lg_80"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">News & Blogs</h3>
          <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Last Minute Amazing Deals</h2>
        </div>
        <div class="cs_height_55 cs_height_lg_40"></div>
        <div class="cs_grid_4">
          <div class="cs_grid_item">
            <article class="cs_post cs_style_3">
              <a href="{{ route('events.index') }}" class="cs_post_thumb cs_zoom">
                <img src="assets/images/post_6.jpeg" alt="Post Thumb" class="cs_zoom_in">
                <span class="cs_link_hover"><i class="fas fa-link"></i></span>
              </a>
              <div class="cs_post_info">
                <div class="cs_post_meta">
                  <img src="assets/images/icons/calendar_icon.svg" alt="Icon"> <span>September 26, 2024</span>
                </div>
                <h2 class="cs_post_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Things to take care while Travelling in long </a></h2>
                <p class="cs_post_subtitle">Completely reinvent worldwide testing new with cooperative leverage multimedia</p>
                <a href="{{ route('events.index') }}" class="cs_post_btn cs_fs_18 cs_medium cs_accent_color">
                  Read More
                  <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 5.71429L0 4.28571L9 4.28571L6 1.42857L6.75 0L12 5L6.75 10L6 8.57143L9 5.71429L0 5.71429Z" fill="currentColor"></path></svg>                    
                 </a>
              </div>
            </article>
          </div>
          <div class="cs_grid_item">
            <article class="cs_post cs_style_3">
              <a href="{{ route('events.index') }}" class="cs_post_thumb cs_zoom">
                <img src="assets/images/post_8.jpeg" alt="Post Thumb" class="cs_zoom_in">
                <span class="cs_link_hover"><i class="fas fa-link"></i></span>
              </a>
              <div class="cs_post_info">
                <div class="cs_post_meta">
                  <img src="assets/images/icons/calendar_icon.svg" alt="Icon"> <span>September 26, 2024</span>
                </div>
                <h2 class="cs_post_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Our Business Thrives To Contribute Global</a></h2>
                <p class="cs_post_subtitle">Completely reinvent worldwide testing new with cooperative leverage multimedia</p>
                <a href="{{ route('events.index') }}" class="cs_post_btn cs_fs_18 cs_medium cs_accent_color">
                  Read More
                  <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 5.71429L0 4.28571L9 4.28571L6 1.42857L6.75 0L12 5L6.75 10L6 8.57143L9 5.71429L0 5.71429Z" fill="currentColor"></path></svg>                    
                 </a>
              </div>
            </article>
          </div>
          <div class="cs_grid_item">
            <article class="cs_post cs_style_3">
              <a href="{{ route('events.index') }}" class="cs_post_thumb cs_zoom">
                <img src="assets/images/post_7.jpeg" alt="Post Thumb" class="cs_zoom_in">
                <span class="cs_link_hover"><i class="fas fa-link"></i></span>
              </a>
              <div class="cs_post_info">
                <div class="cs_post_meta">
                  <img src="assets/images/icons/calendar_icon.svg" alt="Icon"> <span>September 26, 2024</span>
                </div>
                <h2 class="cs_post_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Travelling alone with the best Accomodations.</a></h2>
                <p class="cs_post_subtitle">Completely reinvent worldwide testing new with cooperative leverage multimedia</p>
                <a href="{{ route('events.index') }}" class="cs_post_btn cs_fs_18 cs_medium cs_accent_color">
                  Read More
                  <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0 5.71429L0 4.28571L9 4.28571L6 1.42857L6.75 0L12 5L6.75 10L6 8.57143L9 5.71429L0 5.71429Z" fill="currentColor"></path></svg>                    
                 </a>
              </div>
            </article>
          </div>
        </div>
      </div>
      <div class="cs_height_140 cs_height_lg_80"></div>
    </section>
    <!-- End Blog Section -->
    @endsection


