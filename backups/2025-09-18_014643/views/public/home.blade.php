<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SwaEduAE Volunteering Society | UAE Volunteer Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --secondary-dark: #059669;
            --accent: #f59e0b;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --info: #3b82f6;
        }

        .hero-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .volunteer-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
        }

        .volunteer-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border-color: var(--primary-light);
        }

        .testimonial-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--secondary-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4);
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .fade-in {
            animation: fadeIn 1s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hover-lift {
            transition: transform 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-4px);
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .shadow-glow {
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.2);
        }

        .border-gradient {
            border: 2px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, var(--primary), var(--secondary)) border-box;
        }

        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-indigo-600 to-emerald-500 rounded-xl flex items-center justify-center shadow-lg">
                            <i data-feather="heart" class="text-white h-6 w-6"></i>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-emerald-500 bg-clip-text text-transparent">SwaEduAE</span>
                    </div>
                </div>
                <div class="hidden md:ml-6 md:flex md:items-center md:space-x-10">
                    <a href="/" class="nav-link text-gray-800 font-medium hover:text-indigo-600 transition-colors duration-300">Home</a>
                    <a href="/opportunities" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Opportunities</a>
                    <a href="/about" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">About</a>
                    <a href="/stories" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Stories</a>
                    <a href="/volunteers" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Volunteers</a>
                    <a href="/organizations" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Organizations</a>
                    <a href="/contact" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Contact</a>
                    <div class="flex items-center space-x-4">
                        <div class="relative group">
                            <a href="/login" class="btn-primary px-6 py-3 rounded-xl text-white font-medium flex items-center space-x-2 shadow-lg hover:shadow-xl">
                                <i data-feather="log-in" class="w-4 h-4"></i>
                                <span>Sign In</span>
                                <i data-feather="chevron-down" class="w-4 h-4 transition-transform group-hover:rotate-180"></i>
                            </a>
                            <div class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 border border-gray-100">
                                <a href="/login" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                    <span>Volunteer Sign In</span>
                                </a>
                                <a href="/org/login" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i data-feather="briefcase" class="w-4 h-4"></i>
                                    <span>Organization Sign In</span>
                                </a>
                                <a href="/my/settings" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i data-feather="settings" class="w-4 h-4"></i>
                                    <span>Profile Settings</span>
                                </a>
                            </div>
                        </div>
                        <div class="relative group">
                            <a href="/register" class="border-gradient px-6 py-3 rounded-xl text-indigo-600 font-medium flex items-center space-x-2 bg-white hover:bg-gray-50 transition-colors duration-300">
                                <i data-feather="user-plus" class="w-4 h-4"></i>
                                <span>Sign Up</span>
                                <i data-feather="chevron-down" class="w-4 h-4 transition-transform group-hover:rotate-180"></i>
                            </a>
                            <div class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-2xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 border border-gray-100">
                                <a href="/register" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i data-feather="user" class="w-4 h-4"></i>
                                    <span>Volunteer Sign Up</span>
                                </a>
                                <a href="/org/register" class="block px-6 py-3 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center space-x-2">
                                    <i data-feather="briefcase" class="w-4 h-4"></i>
                                    <span>Organization Sign Up</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center md:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-3 rounded-xl text-gray-600 hover:text-indigo-600 hover:bg-gray-100 transition-colors duration-300">
                        <i data-feather="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-gradient text-white relative overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-20"></div>
            <div class="absolute top-0 right-0 -mr-40 -mt-40 w-80 h-80 bg-white opacity-10 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -ml-40 -mb-40 w-80 h-80 bg-white opacity-10 rounded-full"></div>
        </div>
        <div class="max-w-7xl mx-auto py-32 px-4 sm:py-40 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="text-5xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl text-shadow">
                    <span class="block">Change Lives</span>
                    <span class="block gradient-text">Together</span>
                </h1>
                <p class="mt-8 max-w-3xl mx-auto text-xl text-indigo-100 leading-relaxed">
                    Join our vibrant community of passionate volunteers creating meaningful impact and transforming lives across the UAE.
                </p>
                <div class="mt-12 flex justify-center space-x-6">
                    <a href="/opportunities" class="btn-primary px-8 py-4 rounded-xl text-lg font-semibold flex items-center space-x-2">
                        <i data-feather="search" class="w-5 h-5"></i>
                        <span>Find Opportunities</span>
                    </a>
                    <a href="https://swaeduae.ae/about" class="glass-effect px-8 py-4 rounded-xl text-lg font-semibold flex items-center space-x-2 border border-white border-opacity-30 hover:border-opacity-50 transition-all duration-300">
                        <i data-feather="info" class="w-5 h-5"></i>
                        <span>Learn More</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gradient-to-br from-gray-50 to-white py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20" data-aos="fade-up">
                <h2 class="text-base font-semibold tracking-wide uppercase gradient-text">Our Impact</h2>
                <p class="mt-4 text-4xl font-extrabold text-gray-900 sm:text-5xl lg:text-6xl">
                    Together We've <span class="gradient-text">Achieved</span>
                </p>
                <p class="mt-6 max-w-2xl mx-auto text-xl text-gray-600">
                    Making a measurable difference in communities across the UAE
                </p>
            </div>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="stats-card bg-white rounded-2xl p-8 text-center shadow-lg hover:shadow-xl border border-gray-100" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                        <i data-feather="users" class="text-white h-8 w-8"></i>
                    </div>
                    <h3 class="mt-6 text-5xl font-extrabold text-gray-900">5,000+</h3>
                    <p class="mt-3 text-lg font-medium text-gray-600">Dedicated Volunteers</p>
                    <div class="mt-4 w-12 h-1 bg-gradient-to-r from-indigo-500 to-purple-600 mx-auto rounded-full"></div>
                </div>
                <div class="stats-card bg-white rounded-2xl p-8 text-center shadow-lg hover:shadow-xl border border-gray-100" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                        <i data-feather="clock" class="text-white h-8 w-8"></i>
                    </div>
                    <h3 class="mt-6 text-5xl font-extrabold text-gray-900">250K+</h3>
                    <p class="mt-3 text-lg font-medium text-gray-600">Hours Donated</p>
                    <div class="mt-4 w-12 h-1 bg-gradient-to-r from-emerald-500 to-teal-600 mx-auto rounded-full"></div>
                </div>
                <div class="stats-card bg-white rounded-2xl p-8 text-center shadow-lg hover:shadow-xl border border-gray-100" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                        <i data-feather="home" class="text-white h-8 w-8"></i>
                    </div>
                    <h3 class="mt-6 text-5xl font-extrabold text-gray-900">120+</h3>
                    <p class="mt-3 text-lg font-medium text-gray-600">Communities Served</p>
                    <div class="mt-4 w-12 h-1 bg-gradient-to-r from-amber-500 to-orange-600 mx-auto rounded-full"></div>
                </div>
                <div class="stats-card bg-white rounded-2xl p-8 text-center shadow-lg hover:shadow-xl border border-gray-100" data-aos="fade-up" data-aos-delay="400">
                    <div class="w-16 h-16 bg-gradient-to-r from-rose-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg">
                        <i data-feather="smile" class="text-white h-8 w-8"></i>
                    </div>
                    <h3 class="mt-6 text-5xl font-extrabold text-gray-900">10K+</h3>
                    <p class="mt-3 text-lg font-medium text-gray-600">Lives Changed</p>
                    <div class="mt-4 w-12 h-1 bg-gradient-to-r from-rose-500 to-pink-600 mx-auto rounded-full"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Opportunities -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-12">
                <h2 class="text-base text-indigo-600 font-semibold tracking-wide uppercase">Get Involved</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                    Featured Volunteer Opportunities
                </p>
            </div>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="bg-white rounded-lg shadow-md overflow-hidden volunteer-card transition duration-300" data-aos="fade-up">
                    <img class="h-48 w-full object-cover" src="http://static.photos/people/640x360/1" alt="Community food drive">
                    <div class="p-6">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">Ongoing</span>
                            <span class="ml-2 text-sm text-gray-500">Local</span>
                        </div>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Community Food Drive</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Help sort and distribute food to families in need at our local food bank.
                        </p>
                        <div class="mt-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <i data-feather="calendar" class="text-gray-400"></i>
                                <span class="ml-2 text-sm text-gray-500">Every Saturday</span>
                            </div>
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Learn more →</button>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden volunteer-card transition duration-300" data-aos="fade-up" data-aos-delay="100">
                    <img class="h-48 w-full object-cover" src="http://static.photos/education/640x360/2" alt="Youth mentorship">
                    <div class="p-6">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">New</span>
                            <span class="ml-2 text-sm text-gray-500">Virtual</span>
                        </div>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Youth Mentorship Program</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Mentor underprivileged youth through weekly virtual sessions.
                        </p>
                        <div class="mt-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <i data-feather="clock" class="text-gray-400"></i>
                                <span class="ml-2 text-sm text-gray-500">2 hrs/week</span>
                            </div>
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Learn more →</button>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden volunteer-card transition duration-300" data-aos="fade-up" data-aos-delay="200">
                    <img class="h-48 w-full object-cover" src="http://static.photos/nature/640x360/3" alt="Beach cleanup">
                    <div class="p-6">
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Seasonal</span>
                            <span class="ml-2 text-sm text-gray-500">Outdoors</span>
                        </div>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">Beach Cleanup Initiative</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Join our monthly beach cleanup to protect marine life and keep our shores clean.
                        </p>
                        <div class="mt-4 flex justify-between items-center">
                            <div class="flex items-center">
                                <i data-feather="map-pin" class="text-gray-400"></i>
                                <span class="ml-2 text-sm text-gray-500">Santa Monica</span>
                            </div>
                            <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Learn more →</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-12 text-center">
                <a href="/opportunities" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    View All Opportunities
                    <i data-feather="arrow-right" class="ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="relative bg-indigo-800 py-16">
        <div class="absolute inset-0 overflow-hidden">
            <img class="w-full h-full object-cover opacity-10" src="http://static.photos/people/1200x630/4" alt="">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:text-center mb-12">
                <h2 class="text-base text-indigo-300 font-semibold tracking-wide uppercase">Volunteer Stories</h2>
                <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-white sm:text-4xl">
                    What Our Volunteers Say
                </p>
            </div>
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div class="testimonial-card rounded-lg p-8" data-aos="fade-up">
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full" src="http://static.photos/people/200x200/5" alt="Sarah J.">
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-white">Sarah J.</h4>
                            <p class="text-indigo-200">Volunteer since 2018</p>
                        </div>
                    </div>
                    <p class="mt-4 text-indigo-100">
                        "Volunteering with this organization has been life-changing. The community is so supportive and the impact we make is tangible."
                    </p>
                    <div class="mt-4 flex text-yellow-400">
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                    </div>
                </div>
                <div class="testimonial-card rounded-lg p-8" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full" src="http://static.photos/people/200x200/6" alt="Michael T.">
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-white">Michael T.</h4>
                            <p class="text-indigo-200">Mentorship Program</p>
                        </div>
                    </div>
                    <p class="mt-4 text-indigo-100">
                        "Being a mentor has taught me as much as I've taught my mentee. The training and support from VolunteerTogether is exceptional."
                    </p>
                    <div class="mt-4 flex text-yellow-400">
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                    </div>
                </div>
                <div class="testimonial-card rounded-lg p-8" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center">
                        <img class="h-12 w-12 rounded-full" src="http://static.photos/people/200x200/7" alt="Priya K.">
                        <div class="ml-4">
                            <h4 class="text-lg font-medium text-white">Priya K.</h4>
                            <p class="text-indigo-200">Event Coordinator</p>
                        </div>
                    </div>
                    <p class="mt-4 text-indigo-100">
                        "I've met incredible people through volunteering. The flexibility allows me to contribute even with a busy schedule."
                    </p>
                    <div class="mt-4 flex text-yellow-400">
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                        <i data-feather="star" class="fill-current"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-indigo-700 rounded-lg shadow-xl overflow-hidden lg:grid lg:grid-cols-2 lg:gap-4">
                <div class="pt-10 pb-12 px-6 sm:pt-16 sm:px-16 lg:py-16 lg:pr-0 xl:py-20 xl:px-20">
                    <div class="lg:self-center">
                        <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                            <span class="block">Ready to make a difference?</span>
                            <span class="block">Join us today.</span>
                        </h2>
                        <p class="mt-4 text-lg leading-6 text-indigo-200">
                            Whether you have a few hours a month or want to take on a leadership role, we have opportunities for everyone.
                        </p>
                        <div class="mt-8 flex space-x-4">
                            <a href="#" class="inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-indigo-700 bg-white hover:bg-gray-50">
                                Sign Up Now
                            </a>
                            <a href="/contact" class="inline-flex items-center px-5 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-800 bg-opacity-60 hover:bg-opacity-70">
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
                <div class="-mt-6 aspect-w-5 aspect-h-3 md:aspect-w-2 md:aspect-h-1">
                    <img class="transform translate-x-6 translate-y-6 rounded-md object-cover object-left-top sm:translate-x-16 lg:translate-y-20" src="http://static.photos/people/640x360/8" alt="Volunteers working together">
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Gallery Section -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-extrabold text-gray-900 sm:text-4xl">
                    Our <span class="gradient-text">Volunteers</span> in Action
                </h2>
                <p class="mt-4 text-xl text-gray-600">
                    Capturing the spirit of community service and making a difference together
                </p>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/1" alt="Volunteer activity" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/2" alt="Community event" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/3" alt="Team volunteering" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/4" alt="Youth program" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/5" alt="Food distribution" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/6" alt="Environmental cleanup" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/7" alt="Mentorship session" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
                <div class="aspect-square overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="http://static.photos/people/400x400/8" alt="Community celebration" class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                </div>
            </div>
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    View More Photos
                    <i data-feather="arrow-right" class="ml-2 w-5 h-5"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">About</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Our Mission</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Team</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Partners</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Financials</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Volunteer</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="/opportunities" class="text-base text-gray-300 hover:text-white">Opportunities</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Corporate Programs</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Groups</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Resources</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Blog</a></li>
                        <li><a href="/stories" class="text-base text-gray-300 hover:text-white">Stories</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Toolkits</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Research</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">Connect</h3>
                    <ul class="mt-4 space-y-4">
                        <li><a href="/contact" class="text-base text-gray-300 hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Press</a></li>
                        <li><a href="#" class="text-base text-gray-300 hover:text-white">Careers</a></li>
                        <li class="flex space-x-6 mt-6">
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i data-feather="facebook"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i data-feather="twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i data-feather="instagram"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-white">
                                <i data-feather="linkedin"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 border-t border-gray-700 pt-8">
                <p class="text-base text-gray-400 text-center">
                    &copy; 2023 SwaEduAE Volunteering Society. All rights reserved.<br>
                    <a href="https://swaeduae.ae" class="hover:text-white">www.swaeduae.ae</a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
        feather.replace();
    </script>
  <!-- nav-dropdown-inline-fix-home: 20250914-220300 -->
  <script id="nav-dropdown-inline-fix-home">
  // Chevron click toggles dropdown (text still navigates)
  document.addEventListener("click", function(ev){
    const chevron = ev.target.closest("[data-feather=\"chevron-down\"]");
    if (!chevron) return;
    const a = chevron.closest("a"); if (!a) return;  // chevron inside the <a>
    ev.preventDefault();
    const group = a.closest(".group") || a.parentElement; if (!group) return;
    const menu  = group.querySelector("div.absolute"); if (!menu) return;
    menu.classList.toggle("opacity-0");
    menu.classList.toggle("invisible");
    // close when clicking outside
    const closer = (e2) => {
      if (!menu.contains(e2.target) && !a.contains(e2.target)) {
        menu.classList.add("opacity-0","invisible");
        document.removeEventListener("click", closer, true);
      }
    };
    document.addEventListener("click", closer, true);
  }, true);
  </script>
    <script src="/assets/nav-dropdown-fix.js"></script>
</body>
    <script src="/assets/feather.min.js"></script>
    <script>document.addEventListener("DOMContentLoaded",function(){ if(window.feather&&feather.replace) feather.replace();});</script>
</html>
