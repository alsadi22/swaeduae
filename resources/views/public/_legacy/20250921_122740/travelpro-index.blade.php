@extends('public.layout')
@section('title','Travelpro-Index.Blade')
@section('content')
<section class="py-16"><div class="wrap">
@extends("public.layout-travelpro")
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Premium Tailwind CSS Admin & Dashboard Script" />

    <!-- Site Tiltle -->
    <title>{{ config('app.name','SwaedUAE') }}</title>

    <!-- Site favicon -->
    <link rel="shortcut icon" href="{{ asset('vendor/travelpro/assets') }}/images/favicon.svg" />

    <!-- Custom Style -->
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/travelpro/assets') }}/css/style.css" />
</head>

<body class="antialiased font-inter text-black text-base">
    <!-- Start Main Content -->
    <div class="flex">
        <!-- Start Sidebar -->
        <nav class="sidebar fixed top-0 bottom-0 z-40 flex-none w-[212px] border-r border-black/10 transition-all duration-300 md:block hidden">
            <div class="bg-white h-full">
                <!-- Start Logo -->
                <div class="flex px-4 py-6">
                    <a href="#" class="main-logo flex-1 w-full">
                        <img src="{{ asset('vendor/travelpro/assets') }}/images/logo.svg" alt="logo" />
                    </a>
                </div>
                <!-- End Logo -->
                <!-- Start Menu -->
                <ul class="relative h-[calc(100vh-74px)] text-sm flex flex-col gap-1 overflow-y-auto overflow-x-hidden p-4 py-0" x-data="{ activeMenu: 'apps' }">
                    <li>
                        <a href="#intro" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Introduction</a>
                    </li>
                    <li>
                        <a href="#insto" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Installation</a>
                    </li>
                    <li>
                        <a href="#structure" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Structure</a>
                    </li>
                    <li>
                        <a href="#css" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Css & Javascript</a>
                    </li>
                    <li>
                        <a href="#cre" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Credit & Resources</a>
                    </li>
                    <li>
                        <a href="#support" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Support</a>
                    </li>
                    <li>
                        <a href="#change" class="mb-1 px-2 py-1 whitespace-nowrap block rounded-md text-black hover:bg-black/10  hover:text-black">Change Log</a>
                    </li>
                </ul>
                <!-- End Menu -->
            </div>
        </nav>
        <!-- End sidebar -->

        <!-- Start Content Area -->
        <div class="flex-1 md:ml-[212px]">
            <div class="border-b border-black/10 py-5 px-7 flex items-center justify-between">
                <p class="font-bold">Travel Pro Laravel 11 Documentation 1.0.0</p>
            </div>
            <!-- Start Content -->
            <div class="h-[calc(100vh-73px)] overflow-y-auto overflow-x-hidden">
                <div class="p-4 sm:p-7 !pb-0 min-h-[calc(100vh-145px)]">
                    <div class="grid grid-cols-1 gap-7">
                        <div class="border border-black/10 p-5 rounded-md" id="intro">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">Thank you very much for your purchase!</p>
                            </div>
                            <div class="rounded bg-[#01ba9a]/10 px-6 py-3 text-[#01ba9a]">
                                <p>If you have any questions that are beyond the scope of this documentation, please feel free to email or contact us via our Support mail : <a href = "mailto:pixcelsthemes@gmail.com"><b>Support</b></a></p>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">Introduction</p>
                            </div>
                            <div>
                                <p class="text-base leading-[1.8]">
                                    <b>TravelPro</b> is a Adventure Tour and Travel <b>Laravel 11</b> Template. It’s the best choice for travel agency, tour, travel website, tour operator, tourism, trip, destinations, trip booking, adventure, accommodation and all other travel & tour websites.
                                    TravelPro template is the super creative layout, added appropriate features & pages. And we use very smooth animation which makes our website elegant. It is <b>100% responsive</b> and looks stunning on all types of screens and devices.
                                </p>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md" id="insto">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">Installation</p>
                            </div>
                            <div>
                                <p class="text-base font-semibold">To setup the TravelPro theme, follow below-mentioned steps</p>
                                <ul class="mt-3 list-decimal ps-20 list_install   space-y-1.5">
                                    <li><b>Install Server</b> (Recommended PHP version: >8.2)
                                        <ul>
                                            <li>Make sure to have the Xampp/WampServer/Lampp installed & running in your computer. If you already have installed server on your computer, you can skip this step if your existing PHP version is greater than >8.2.</li>
                                        </ul>
                                    </li> 
                                    <li><b>Install Composer</b> (Recommended version: >= 2.2)
                                        <ul>
                                            <li>Make sure to have the composer installed & running in your computer. If you already have installed server on your computer, you can skip this step.</li>
                                        </ul>
                                    </li>                                
                                    <li><b>This would install all the required packages in the vendor folder.</b>
                                        <ul>
                                            <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">composer install </code> </pre>
                                            <li>or</li>
                                            <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">composer i </code> </pre>
                                        </ul>
                                    </li>                                
                                    <li><b>This would install all the required dependencies in the node_modules folder.</b>
                                        <ul>
                                            <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">npm install</code> </pre>
                                            <li>or</li>
                                            <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">npm i </code> </pre>
                                        </ul>
                                    </li>  
                                    <li><b>Generate CSS From tailwindcss:</b>
                                        <ul>
                                            <li>Run the following command to Generate CSS:
                                                <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">npm run build:css </code> </pre>
                                            </li>
                                        </ul>
                                    </li>                                 
                                    <li><b>The development server is accessible at http://localhost:8000. To run on other port just run command : php artisan serve --port=8001</b>
                                        <ul>
                                            <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">php artisan serve </code> </pre>
                                            <li>or</li>
                                            <pre class="p-3 border rounded-md border-black/10 comand_line" tabindex="0"><code class="language-markup">php artisan serve --port=8001 </code> </pre>
                                        </ul>
                                    </li>                                                                                         
                                </ul>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md" id="structure">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">File Structure</p>
                            </div>
                            <div class="overflow-x-auto w-full">
                                <pre class="prettyprint">
├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> travelpro/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> app/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> bootstrap/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> config/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> database/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> public/
│   │   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> assets/
│   │   │   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> css/
│   │   │   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> fonts/
│   │   │   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> images/
│   │   │   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> js/
│   │   │   └── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> sass/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> resources/
│   │   │   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> views/
│   │   │   │   └── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> All .blade.php files including components and layouts
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> routes/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> storage/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> tests/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> vendor/
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> .env
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> .env.example
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> artisan
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> composer.json
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> package.json
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> phpunit.xml
│   ├── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> README.md
│   └── <img src="{{ asset('vendor/travelpro/assets') }}/images/file.svg" class="w-4 inline-block"> vite.config.js
├── <img src="{{ asset('vendor/travelpro/assets') }}/images/folder.svg" class="w-4 inline-block"> Documentation/
│    └── index.html - Index file for documentation.
                                </pre>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">.blade.php Structure</p>
                            </div>
                            <div class="text-sm">
                               
                                <!-- HTML generated using hilite.me --><div style="background: #ffffff; overflow:auto;width:auto;border:solid gray;border-width:.1em .1em .1em .8em;padding:.2em .6em;"><pre style="margin: 0; line-height: 125%">        
&lt;!DOCTYPE html&gt;
&lt;html class="no-js" lang="en"&gt;

    &lt;x-head/&gt;
    &lt;body&gt;
    
    &lt;x-preloader/&gt;

    &lt;!-- Start Header Section --&gt;
    &lt;x-header/&gt;
    
    &lt;x-header_search/&gt;
    &lt;!-- End Header Section --&gt;

    @yield('content')
    
    &lt;!-- Start footer --&gt;
    &lt;x-footer/&gt;
    &lt;!-- End footer --&gt;

    &lt;!-- Script --&gt;
    &lt;x-script/&gt;

    &lt;/body&gt;
&lt;/html&gt;
                                    
</pre></div>
                            </div>
                           
                                </div>
                        <div class="border border-black/10 p-5 rounded-md" id="css">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">CSS</p>
                            </div>
                            <p class="font-semibold">Travel Pro Built with latest version of CSS3.</p>
                            <div class="overflow-x-auto w-full mt-4">
                                <table class="w-full m-0 border whitespace-nowrap border-[#dee2e6]">
                                    <thead>
                                        <tr class="text-left">
                                            <th class="p-3 border border-[#dee2e6]">File Name</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">animate.css</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">bootstrap.min.css</code></td>
                                        </tr>
                                       <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">fontawesome.min.css</code></td>
                                        </tr>
                                       <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">light_gallery.min.css</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">odometer.css</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">select2.min.css</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">slick.min.css</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">style.css</code></td>
                                        </tr>                                                                                
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">Javascript</p>
                            </div>
                            <p class="font-semibold">Travel Pro Built with Javascript</p>
                            <div class="overflow-x-auto w-full mt-4">
                                <table class="w-full m-0 border whitespace-nowrap border-[#dee2e6]">
                                    <thead>
                                        <tr class="text-left">
                                            <th class="p-3 border border-[#dee2e6]">File Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">jquery.slick.min.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">jquery-3.6.0.min.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">light_gallery.min.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">main.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">odometer.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">ripples.min.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">select2.min.js</code></td>
                                        </tr>
                                        <tr>
                                            <td class="p-3 border border-[#dee2e6]"><code class="text-[#e83e8c]">wow.min.js</code></td>
                                        </tr>                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md" id="support">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">Support</p>
                            </div>
                            <div class="mt-3">
                                <p>Thank you for become a part of us. If you have any query, suggestion and complain. Contact us anytime. If you have any questions that are beyond the scope of this documentation, please feel free to email us via our Support mail : <a href = "mailto:pixcelsthemes@gmail.com"><b>Support</b></a></p>
                            </div>
                        </div>
                        <div class="border border-black/10 p-5 rounded-md" id="change">
                            <div class="mb-5">
                                <p class="text-xl font-semibold text-black">Changelog</p>
                            </div>
                            <div>
                                <h4 class="font-bold text-xl"><span class="text-green-600">1.0.0</span> - 27th August 2024</h4>
                                <h5 class="mt-4 font-bold">General</h5>
                                <p class="text-muted mt-1"><span class="bg-green-600 h-2 w-2 mr-2 relative -top-[2px] rounded-full inline-block"></span> Initial Released</p>
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="p-7 bg-white flex items-center justify-between">
                    <p class="text-xs text-black/40">&copy; 2024 Travel Pro</p>
                </footer>
            </div>
            <!-- End Content -->
        </div>
        <!-- End Content Area -->
    </div>
    <!-- End Main Content -->
</body>

</html>
</div></section>
@endsection
