@extends('layout.layout')

@section('content')
<x-preloader/>

<!-- Start Header Section -->
<header class="cs_site_header cs_style_1 cs_color_1 cs_fs_18 cs_sticky_header">
  <div class="cs_top_header cs_accent_bg">
    <div class="cs_top_header_in">
      <div class="cs_top_header_left">
        <ul class="cs_top_header_list cs_mp0">
          <li><b>Address: </b>3517 W. Gray St. Utica, Pennsylvania 57867</li>
          <li>info@company.com</li>
        </ul>
        <div class="ms-auto">@includeIf('partials.account_menu')</div>
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
              <a href=" {{ route('home') }}">Home</a>
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
<section class="cs_hero cs_style_2 cs_accent_bg_1">
  <div class="cs_hero_in">
    <div class="cs_hero_img"><img src="assets/images/hero_2.png" alt="Hero Thumb"></div>
    <div class="cs_hero_text">
      <h3 class="cs_hero_mini_title cs_fs_24 cs_semibold cs_accent_color wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Find Next PlaceTo Visit</h3>
      <h1 class="cs_hero_title cs_fs_85 cs_semibold">Tour Travel & Adventure Camping</h1>
      <p class="cs_hero_desc cs_fs_18">Denouncing pleasure and praising pain was born and will give you complete great explorer of the truth the master-builder.</p>
      <div class="cs_button_group">
        <a href="{{ route('about') }}" class="cs_btn cs_style_1 cs_fs_18 cs_semibold">
          Read More
          <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/><path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
          </svg>                
        </a>
        <div class="cs_hero_ratings wow fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.2s">
          <div class="cs_hero_rating_icon">
            <img src="assets/images/icons/google_icon.svg" alt="Icom">
          </div>
          <div class="cs_rating_container">
            <div class="cs_rating" data-rating="4.9">
              <div class="cs_rating_percentage"></div>
            </div>
            <div class="cs_rating_text cs_fs_25 cs_normal">4.9 Rating</div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="animated-icon-wrap">
    <div class="animated-icon"><i class="fa-solid fa-location-dot"></i></div>
    <div class="animated-icon"><i class="fa-solid fa-car"></i></div>
    <div class="animated-icon"><i class="fa-solid fa-plane"></i></div>
    <div class="animated-icon"><i class="fa-solid fa-globe"></i></div>
    <div class="animated-icon"><i class="fa-solid fa-earth-americas"></i></div>
    <div class="animated-icon"><i class="fa-regular fa-compass"></i></div>
    <div class="animated-icon"><i class="fa-solid fa-hotel"></i></div>
  </div>
</section>
<!-- End Hero Section -->

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
  <div class="container">
    <div class="row cs_gap_y_30">
      <div class="col-lg-4">
        <div class="cs_card cs_style_3 cs_white_bg">
          <a href="{{ route('events.index') }}" class="cs_card_thumb position-relative cs_zoom">
            <img src="assets/images/package_img_5.jpeg" alt="Package Thumb" class="cs_zoom_in">
            <div class="cs_package_badge cs_fs_18 cs_semibold cs_primary_color cs_primary_font position-absolute">3 Day 2 Night</div>
          </a>
          <div class="cs_card_content">
            <h2 class="cs_card_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Beauty of Solomon Island</a></h2>
            <p class="cs_card_subtitle mb-0"><i class="fa-solid fa-globe cs_accent_color"></i> Africa Portugal Mexico</p>
            <hr>
            <div class="cs_card_action">
              <span class="cs_card_price cs_fs_24 cs_semibold cs_primary_color cs_primary_font mb-0">$4500</span>
              <a href="{{ route('events.index') }}" class="cs_btn cs_style_1 cs_fs_18 cs_semibold"> Book Now</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="cs_card cs_style_3 cs_white_bg">
          <a href="{{ route('events.index') }}" class="cs_card_thumb position-relative cs_zoom">
            <img src="assets/images/package_img_6.jpeg" alt="Package Thumb" class="cs_zoom_in">
            <div class="cs_package_badge cs_fs_18 cs_semibold cs_primary_color cs_primary_font position-absolute">3 Day 2 Night</div>
          </a>
          <div class="cs_card_content">
            <h2 class="cs_card_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Believe In Your Mexico</a></h2>
            <p class="cs_card_subtitle mb-0"><i class="fa-solid fa-globe cs_accent_color"></i> New York City, USA</p>
            <hr>
            <div class="cs_card_action">
              <span class="cs_card_price cs_fs_24 cs_semibold cs_primary_color cs_primary_font mb-0">$4500</span>
              <a href="{{ route('events.index') }}" class="cs_btn cs_style_1 cs_fs_18 cs_semibold"> Book Now</a>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="cs_card cs_style_3 cs_white_bg">
          <a href="{{ route('events.index') }}" class="cs_card_thumb position-relative cs_zoom">
            <img src="assets/images/package_img_7.jpeg" alt="Package Thumb" class="cs_zoom_in">
            <div class="cs_package_badge cs_fs_18 cs_semibold cs_primary_color cs_primary_font position-absolute">3 Day 2 Night</div>
          </a>
          <div class="cs_card_content">
            <h2 class="cs_card_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Proof That Bahamas Beaty</a></h2>
            <p class="cs_card_subtitle mb-0"><i class="fa-solid fa-globe cs_accent_color"></i> Machu Picchu, Peru</p>
            <hr>
            <div class="cs_card_action">
              <span class="cs_card_price cs_fs_24 cs_semibold cs_primary_color cs_primary_font mb-0">$4500</span>
              <a href="{{ route('events.index') }}" class="cs_btn cs_style_1 cs_fs_18 cs_semibold"> Book Now</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="cs_height_140 cs_height_lg_80"></div>
</section>
<!-- End Package Section -->

<!-- Start CTA Section -->
<section class="cs_cta cs_style_1 cs_bg_filed cs_primary_bg cs_bg_fixed" data-src="assets/images/banner_bg_2.jpeg">
  <div class="cs_height_150 cs_height_lg_80"></div>
  <div class="container">
    <div class="row cs_gap_y_40">
      <div class="col-lg-6">
        <div class="cs_cta_text">
          <h3 class="cs_cta_title_mini cs_fs_24 cs_medium cs_white_color cs_ternary_font wow fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.2s">27% DISCOUNT OFFER </h3>
          <h2 class="cs_cta_title cs_fs_56 cs_bold cs_white_color">Discount Popup Examples to Elevate</h2>
          <p class="cs_cta_subtitle cs_fs_18 cs_white_color">Denouncing pleasure and praising pain was born and will give you <br> complete great explorer of the truth the master-builder.</p>
          <a href="" class="cs_btn cs_style_1 cs_fs_18 cs_semibold">
            Read More
            <svg width="20" height="10" viewBox="0 0 20 10" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19.5866 5.69629H0.41235C0.184269 5.69629 0 5.46776 0 5.1849C0 4.90204 0.184269 4.67352 0.41235 4.67352H18.5906L16.0881 1.57004C15.927 1.37028 15.927 1.04587 16.0881 0.846109C16.2492 0.646349 16.5108 0.646349 16.6718 0.846109L19.8792 4.82374C19.9977 4.97076 20.0325 5.1897 19.9681 5.38147C19.9036 5.57164 19.7529 5.69629 19.5866 5.69629Z" fill="currentColor"/><path d="M16.3435 9.11986C16.2384 9.11986 16.1333 9.08012 16.0538 8.99935C15.8935 8.83909 15.8935 8.57884 16.0538 8.41858L19.2487 5.22371C19.4089 5.06345 19.6692 5.06345 19.8294 5.22371C19.9897 5.38396 19.9897 5.64422 19.8294 5.80448L16.6346 8.99935C16.5538 9.08012 16.4487 9.11986 16.3435 9.11986Z" fill="currentColor"/>
            </svg>                
          </a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="cs_partners_wrap overflow-hidden">
          <div class="cs_partners">
            <div class="cs_partner">
              <img src="assets/images/brand_6.png" alt="Brand Logo">
            </div>
            <div class="cs_partner">
              <img src="assets/images/brand_7.png" alt="Brand Logo">
            </div>
            <div class="cs_partner">
              <img src="assets/images/brand_8.png" alt="Brand Logo">
            </div>
            <div class="cs_partner">
              <img src="assets/images/brand_9.png" alt="Brand Logo">
            </div>
            <div class="cs_partner">
              <img src="assets/images/brand_10.png" alt="Brand Logo">
            </div>
            <div class="cs_partner">
              <img src="assets/images/brand_11.png" alt="Brand Logo">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="cs_height_150 cs_height_lg_80"></div>
</section>
<!-- End CTA Section -->

<!-- Start Why Choose Us Section -->
<section>
  <div class="cs_height_135 cs_height_lg_75"></div>
  <div class="container">
    <div class="cs_section_heading cs_style_1 text-center">
      <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">Why Choose Us</h3>
      <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Get The Best Travel Experience</h2>
    </div>
    <div class="cs_height_55 cs_height_lg_40"></div>
    <div class="cs_iconbox_4_wrap">
      <div>
        <div class="row cs_gap_y_45">
          <div class="col-lg-12 col-6">
            <div class="cs_iconbox cs_style_4">
              <div class="cs_iconbox_icon cs_center">
                <img src="assets/images/icons/calendar_icon_2.svg" alt="Calendar Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Set Travel Plan</h2>
              <p class="cs_iconbox_subtitle mb-0">Distinctively impact client-centered ideas via future-proof paradigms.</p>
            </div>
          </div>
          <div class="col-lg-12 col-6">
            <div class="cs_iconbox cs_style_4">
              <div class="cs_iconbox_icon cs_center">
                <img src="assets/images/icons/hotel-icon.svg" alt="Hotel Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Luxary Hotel</h2>
              <p class="cs_iconbox_subtitle mb-0">Distinctively impact client-centered ideas via future-proof paradigms.</p>
            </div>
          </div>
        </div>
      </div>
      <div>
        <div class="cs_iconbox_4_thumb cs_center">
          <img src="assets/images/about_4.png" alt="About Thumb">
        </div>
      </div>
      <div>
        <div class="row cs_gap_y_45">
          <div class="col-lg-12 col-6">
            <div class="cs_iconbox cs_style_4">
              <div class="cs_iconbox_icon cs_center">
                <img src="assets/images/icons/compass_icon.svg" alt="Calendar Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Explore Around</h2>
              <p class="cs_iconbox_subtitle mb-0">Distinctively impact client-centered ideas via future-proof paradigms.</p>
            </div>
          </div>
          <div class="col-lg-12 col-6">
            <div class="cs_iconbox cs_style_4">
              <div class="cs_iconbox_icon cs_center">
                <img src="assets/images/icons/headset_icon.svg" alt="Hotel Icon">
              </div>
              <h2 class="cs_iconbox_title cs_fs_24 cs_semibold">Support 24/7</h2>
              <p class="cs_iconbox_subtitle mb-0">Distinctively impact client-centered ideas via future-proof paradigms.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="cs_height_135 cs_height_lg_75"></div>
</section>
<!-- End Why Choose Us Section -->

<!-- Start Destination Section -->
<section class="cs_accent_bg_1">
  <div class="cs_height_135 cs_height_lg_75"></div>
  <div class="container">
    <div class="cs_section_heading cs_style_1">
      <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">POPULAR DESTINATION</h3>
      <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.2s">Most Popular Destination</h2>
    </div>
    <div class="cs_height_55 cs_height_lg_40"></div>
    <div class="cs_card_4_list">
      <div class="cs_card cs_style_4 cs_hover_active active">
        <a href="{{ route('events.index') }}" class="cs_card_thumb cs_bg_filed" data-src="assets/images/casestudy_1.jpeg"></a>
        <a href="{{ route('events.index') }}" class="cs_card_icon cs_center position-absolute"><i class="fa-solid fa-chevron-right"></i></a>
        <div class="cs_card_in">
          <h2 class="cs_card_title cs_fs_35 cs_white_color mb-0"><a href="{{ route('events.index') }}">Copenhagen,<br> Denmark</a></h2>
        </div>
      </div>
      <div class="cs_card cs_style_4 cs_hover_active">
        <a href="{{ route('events.index') }}" class="cs_card_thumb cs_bg_filed" data-src="assets/images/casestudy_2.jpeg"></a>
        <a href="{{ route('events.index') }}" class="cs_card_icon cs_center position-absolute"><i class="fa-solid fa-chevron-right"></i></a>
        <div class="cs_card_in">
          <h2 class="cs_card_title cs_fs_35 cs_white_color mb-0"><a href="{{ route('events.index') }}">California,<br> USA</a></h2>
        </div>
      </div>
      <div class="cs_card cs_style_4 cs_hover_active">
        <a href="{{ route('events.index') }}" class="cs_card_thumb cs_bg_filed" data-src="assets/images/casestudy_3.jpeg"></a>
        <a href="{{ route('events.index') }}" class="cs_card_icon cs_center position-absolute"><i class="fa-solid fa-chevron-right"></i></a>
        <div class="cs_card_in">
          <h2 class="cs_card_title cs_fs_35 cs_white_color mb-0"><a href="{{ route('events.index') }}">Rome,<br> Italy</a></h2>
        </div>
      </div>
      <div class="cs_card cs_style_4 cs_hover_active">
        <a href="{{ route('events.index') }}" class="cs_card_thumb cs_bg_filed" data-src="assets/images/casestudy_4.jpeg"></a>
        <a href="{{ route('events.index') }}" class="cs_card_icon cs_center position-absolute"><i class="fa-solid fa-chevron-right"></i></a>
        <div class="cs_card_in">
          <h2 class="cs_card_title cs_fs_35 cs_white_color mb-0"><a href="{{ route('events.index') }}">London,<br> United Lingdom</a></h2>
        </div>
      </div>
      <div class="cs_card cs_style_4 cs_hover_active">
        <a href="{{ route('events.index') }}" class="cs_card_thumb cs_bg_filed" data-src="assets/images/casestudy_5.jpeg"></a>
        <a href="{{ route('events.index') }}" class="cs_card_icon cs_center position-absolute"><i class="fa-solid fa-chevron-right"></i></a>
        <div class="cs_card_in">
          <h2 class="cs_card_title cs_fs_35 cs_white_color mb-0"><a href="{{ route('events.index') }}">Paris,<br> French</a></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="cs_height_140 cs_height_lg_80"></div>
</section>
<!-- End Destination Section -->

<!-- Start Team Section -->
 <section>
  <div class="cs_height_135 cs_height_lg_75"></div>
  <div class="container">
    <div class="cs_section_heading cs_style_1 text-center">
      <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">Travel Agents</h3>
      <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Our Experts Team Member</h2>
    </div>
    <div class="cs_height_55 cs_height_lg_40"></div>
    <div class="row cs_gap_y_30">
      <div class="col-lg-4">
        <div class="cs_team cs_style_1 position-relative">
          <div class="cs_team_thumb cs_zoom overflow-hidden">
            <img src="assets/images/team_1.jpeg" alt="Team Thumb" class="cs_zoom_in">
          </div>
          <div class="cs_team_info text-center position-absolute">
            <h2 class="cs_team_title cs_fs_24 cs_medium cs_white_color">David Cooper</h2>
           <p class="cs_team_subtitle cs_white_color">CO FOUNDER</p>
           <div class="cs_social_btns">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="cs_team cs_style_1 position-relative">
          <div class="cs_team_thumb cs_zoom overflow-hidden">
            <img src="assets/images/team_2.jpeg" alt="Team Thumb" class="cs_zoom_in">
          </div>
          <div class="cs_team_info text-center position-absolute">
            <h2 class="cs_team_title cs_fs_24 cs_medium cs_white_color">David Cooper</h2>
           <p class="cs_team_subtitle cs_white_color">CO FOUNDER</p>
           <div class="cs_social_btns">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="cs_team cs_style_1 position-relative">
          <div class="cs_team_thumb cs_zoom overflow-hidden">
            <img src="assets/images/team_3.jpeg" alt="Team Thumb" class="cs_zoom_in">
          </div>
          <div class="cs_team_info text-center position-absolute">
            <h2 class="cs_team_title cs_fs_24 cs_medium cs_white_color">David Cooper</h2>
           <p class="cs_team_subtitle cs_white_color">CO FOUNDER</p>
           <div class="cs_social_btns">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="cs_height_140 cs_height_lg_80"></div>
</section>
<!-- End Team Section -->

<!-- Start Accordian Section -->
<section class="cs_primary_bg cs_bg_filed cs_bg_fixed cs_parallax" data-src="assets/images/accordian_bg.jpeg">
  <div class="cs_height_150 cs_height_lg_80"></div>
  <div class="container">
    <div class="row cs_gap_y_40 align-items-center">
      <div class="col-xl-6 col-lg-5">
        <div class="cs_adventure_logo_wrap">
          <div class="cs_adventure_logo">
            <img src="assets/images/adventure_logo.svg" alt="Logo" class="cs_to_rotate">
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-lg-7">
        <div class="cs_accordian_wrap">
          <div class="cs_accordian cs_style_1 cs_white_bg cs_radius_5">
            <h3 class="cs_accordian_head cs_fs_20 cs_semibold mb-0">
              <span>What type of travel packages does Vacasky offer?</span>
                <span class="cs_accordian_toggle cs_center">
                  <i class="fa-regular fa-eye"></i>
                  <i class="fa-regular fa-eye-slash"></i>
                </span>
            </h3>
            <div class="cs_accordian_body">
              There are many variations of passages of available but the Ut elit tellus luctus nec ullamcorper at mattis variations of passages of available.
            </div>
          </div>
          <div class="cs_accordian cs_style_1 cs_white_bg cs_radius_5 active">
            <h3 class="cs_accordian_head cs_fs_20 cs_semibold mb-0">
              How do I book a trip with Vacasky?
                <span class="cs_accordian_toggle cs_center">
                  <i class="fa-regular fa-eye"></i>
                  <i class="fa-regular fa-eye-slash"></i>
                </span>
            </h3>
            <div class="cs_accordian_body">
              There are many variations of passages of available but the Ut elit tellus luctus nec ullamcorper at mattis variations of passages of available.
            </div>
          </div>
          <div class="cs_accordian cs_style_1 cs_white_bg cs_radius_5">
            <h3 class="cs_accordian_head cs_fs_20 cs_semibold mb-0">
              What is the payment process for Vacasky?
              <span class="cs_accordian_toggle cs_center">
                <i class="fa-regular fa-eye"></i>
                <i class="fa-regular fa-eye-slash"></i>
              </span>
            </h3>
            <div class="cs_accordian_body">
              There are many variations of passages of available but the Ut elit tellus luctus nec ullamcorper at mattis variations of passages of available.
            </div>
          </div>
          <div class="cs_accordian cs_style_1 cs_white_bg cs_radius_5">
            <h3 class="cs_accordian_head cs_fs_20 cs_semibold mb-0">
              What Payment Methods are Supported?
                <span class="cs_accordian_toggle cs_center">
                  <i class="fa-regular fa-eye"></i>
                  <i class="fa-regular fa-eye-slash"></i>
                </span>
            </h3>
            <div class="cs_accordian_body">
              There are many variations of passages of available but the Ut elit tellus luctus nec ullamcorper at mattis variations of passages of available.
            </div>
          </div>
          <div class="cs_accordian cs_style_1 cs_white_bg cs_radius_5">
            <h3 class="cs_accordian_head cs_fs_20 cs_semibold mb-0">
              How to cancel my booking in Vacasky?
                <span class="cs_accordian_toggle cs_center">
                  <i class="fa-regular fa-eye"></i>
                  <i class="fa-regular fa-eye-slash"></i>
                </span>
            </h3>
            <div class="cs_accordian_body">
              There are many variations of passages of available but the Ut elit tellus luctus nec ullamcorper at mattis variations of passages of available.
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="cs_height_150 cs_height_lg_80"></div>
</section>
<!-- End Accordian Section -->

<!-- Start Testimonial Section -->
<section class="cs_testimonial cs_slider cs_style_1">
  <div class="cs_height_140 cs_height_lg_80"></div>
  <div class="container cs_large">
    <div class="row cs_gap_y_40">
    <div class="col-lg-6">
      <div class="cs_section_heading cs_style_1">
        <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">TESTIMONIALS</h3>
        <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0">Our Valuable Customers
          Awesome Feedback</h2>
      </div>
      <div class="cs_height_20 cs_height_lg_20"></div>
      <p class="cs_section_text mb-0">Completely reinvent worldwide testing procedures with cooperative initiatives to leverage existing excellent best practices with functional</p>
      <div class="cs_height_40 cs_height_lg_30"></div>
      <div class="cs_slider_container" data-autoplay="0" data-loop="1" data-speed="600" data-center="0" data-slides-per-view="responsive" data-xxs-slides="1" data-xs-slides="1" data-sm-slides="1" data-md-slides="1"
        data-lg-slides="1" data-add-slides="1">
        <div class="cs_slider_wrapper">
          <div class="slick_slide_in">
            <div class="cs_feedback cs_style_1 cs_radius_5 position-relative">
              <div class="cs_feedback_avatar">
                <div class="cs_avatar_thumb cs_radius_5 overflow-hidden">
                  <img src="assets/images/testimonial_avatar_1.jpeg" alt="Avatar">
                </div>
                <div class="cs_avatar_info">
                  <h3 class="cs_avatar_title cs_fs_24 cs_medium cs_secondary_font">Alexon Shen</h3>
                  <p class="cs_avatar_subtitle cs_accent_color mb-0">Founder & CEO</p>
                </div>
              </div>
              <div>
                <blockquote class="cs_fs_24 cs_medium cs_secondary_font cs_primary_color">“Quality Servies Good!”</blockquote>
                <p class="mb-0">Completely reinvent worldwide testing new proceds with cooperative initiatives. Seemly leverage market excellent best practices chains</p>
              </div>
            </div>
          </div>
          <div class="slick_slide_in">
            <div class="cs_feedback cs_style_1 cs_radius_5 position-relative">
              <div class="cs_feedback_avatar">
                <div class="cs_avatar_thumb cs_radius_5 overflow-hidden">
                  <img src="assets/images/testimonial_avatar_2.jpeg" alt="Avatar">
                </div>
                <div class="cs_avatar_info">
                  <h3 class="cs_avatar_title cs_fs_24 cs_medium cs_secondary_font">Alexon Shen</h3>
                  <p class="cs_avatar_subtitle cs_accent_color mb-0">Founder & CEO</p>
                </div>
              </div>
              <div>
                <blockquote class="cs_fs_24 cs_medium cs_secondary_font cs_primary_color">“Quality Servies Good!”</blockquote>
                <p class="mb-0">Completely reinvent worldwide testing new proceds with cooperative initiatives. Seemly leverage market excellent best practices chains</p>
              </div>
            </div>
          </div>
          <div class="slick_slide_in">
            <div class="cs_feedback cs_style_1 cs_radius_5 position-relative">
              <div class="cs_feedback_avatar">
                <div class="cs_avatar_thumb cs_radius_5 overflow-hidden">
                  <img src="assets/images/testimonial_avatar_2_1.jpeg" alt="Avatar">
                </div>
                <div class="cs_avatar_info">
                  <h3 class="cs_avatar_title cs_fs_24 cs_medium cs_secondary_font">Alexon Shen</h3>
                  <p class="cs_avatar_subtitle cs_accent_color mb-0">Founder & CEO</p>
                </div>
              </div>
              <div>
                <blockquote class="cs_fs_24 cs_medium cs_secondary_font cs_primary_color">“Quality Servies Good!”</blockquote>
                <p class="mb-0">Completely reinvent worldwide testing new proceds with cooperative initiatives. Seemly leverage market excellent best practices chains</p>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_pagination cs_style_1"></div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="cs_testimonial_thumb cs_radius_5 overflow-hidden position-relative">
        <img src="assets/images/testimonial_right.jpeg" alt="Testimonial Thumb" class="w-100 h-100 object-fit-cover">
        <div class="cs_shape_1 position-absolute"></div>
        <div class="cs_shape_2 position-absolute">
          <img src="assets/images/Quote.png" alt="Quote">
        </div>
      </div>
    </div>
  </div>
  </div>
  <div class="cs_height_140 cs_height_lg_80"></div>
</section>
<!-- End Testimonial Section -->

<!-- Start Blog Section -->
<section class="cs_accent_bg_1">
  <div class="cs_height_135 cs_height_lg_75"></div>
  <div class="container">
    <div class="cs_section_heading cs_style_1 text-center">
      <h3 class="cs_section_title_up cs_ternary_font cs_accent_color cs_normal cs_fs_24">News & Blogs</h3>
      <h2 class="cs_section_title cs_semibold cs_fs_56 mb-0 wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.2s">Last Minute Amazing Deals</h2>
    </div>
    <div class="cs_height_55 cs_height_lg_40"></div>
    <div class="row cs_gap_y_24">
      <div class="col-lg-4">
        <article class="cs_post cs_style_2 cs_white_bg">
          <a href="{{ route('events.index') }}" class="cs_post_thumb cs_zoom overflow-hidden position-relative">
            <img src="assets/images/post_3.jpeg" alt="Post Thumb" class="cs_zoom_in">
            <div class="cs_posted_by cs_radius_2 cs_fs_16 cs_medium cs_white_color position-absolute">30 May</div>
            <div class="cs_post_overlay cs_radius_5 position-absolute"></div>
          </a>
          <div class="cs_post_info">
            <ul class="cs_post_meta cs_mp0">
              <li>
                <span class="cs_primary_color"><i class="fa-regular fa-circle-user"></i></span>By<a href="#">admin</a>
              </li>
              <li>
                <span class="cs_primary_color"><i class="fa-regular fa-comment"></i></span> 
                <a href="#">3 comments</a>
              </li>
            </ul>
            <h2 class="cs_post_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Things to see and do when visiting Japan</a></h2>
            <hr>
              <a href="{{ route('events.index') }}" class="cs_post_btn cs_fs_18 cs_medium cs_primary_color">
               Read More
               <i class="fa-solid fa-arrow-right-long"></i>                 
              </a>
          </div>
        </article>
      </div>
      <div class="col-lg-4">
        <article class="cs_post cs_style_2 cs_white_bg">
          <a href="{{ route('events.index') }}" class="cs_post_thumb cs_zoom overflow-hidden position-relative">
            <img src="assets/images/post_4.jpeg" alt="Post Thumb" class="cs_zoom_in">
            <div class="cs_posted_by cs_radius_2 cs_fs_16 cs_medium cs_white_color position-absolute">30 May</div>
            <div class="cs_post_overlay cs_radius_5 position-absolute"></div>
          </a>
          <div class="cs_post_info">
            <ul class="cs_post_meta cs_mp0">
              <li>
                <span class="cs_primary_color"><i class="fa-regular fa-circle-user"></i></span>By<a href="#">admin</a>
              </li>
              <li>
                <span class="cs_primary_color"><i class="fa-regular fa-comment"></i></span> 
                <a href="#">7 comments</a>
              </li>
            </ul>
            <h2 class="cs_post_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">A place where start new life with adventure travel</a></h2>
            <hr>
              <a href="{{ route('events.index') }}" class="cs_post_btn cs_fs_18 cs_medium cs_primary_color">
                Read More
                <i class="fa-solid fa-arrow-right-long"></i>                   
              </a>
          </div>
        </article>
      </div>
      <div class="col-lg-4">
        <article class="cs_post cs_style_2 cs_white_bg">
          <a href="{{ route('events.index') }}" class="cs_post_thumb cs_zoom overflow-hidden position-relative">
            <img src="assets/images/post_5.jpeg" alt="Post Thumb" class="cs_zoom_in">
            <div class="cs_posted_by cs_radius_2 cs_fs_16 cs_medium cs_white_color position-absolute">30 May</div>
            <div class="cs_post_overlay cs_radius_5 position-absolute"></div>
          </a>
          <div class="cs_post_info">
            <ul class="cs_post_meta cs_mp0">
              <li>
                <span class="cs_primary_color"><i class="fa-regular fa-circle-user"></i></span>By<a href="#">admin</a>
              </li>
              <li>
                <span class="cs_primary_color"><i class="fa-regular fa-comment"></i></span> 
                <a href="#">4 comments</a>
              </li>
            </ul>
            <h2 class="cs_post_title cs_fs_24 cs_semibold"><a href="{{ route('events.index') }}">Travel the most beautiful places in the world</a></h2>
            <hr>
              <a href="{{ route('events.index') }}" class="cs_post_btn cs_fs_18 cs_medium cs_primary_color">
               Read More
               <i class="fa-solid fa-arrow-right-long"></i>                   
              </a>
          </div>
        </article>
      </div>
    </div>
  </div>
  <div class="cs_height_140 cs_height_lg_80"></div>
</section>
<div class="cs_height_135 cs_height_lg_75"></div>
<!-- End Blog Section -->

@endsection