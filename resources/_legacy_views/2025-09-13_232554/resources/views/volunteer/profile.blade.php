<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Volunteer Profile | SwaEduAE Volunteering Society</title>
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
        }

        .profile-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e5e7eb;
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .badge {
            background: linear-gradient(135deg, var(--accent) 0%, #f97316 100%);
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
                    <a href="/" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Home</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Opportunities</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">About</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Stories</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Contact</a>
                    <div class="flex items-center space-x-4">
                        <a href="/organizations" class="px-4 py-2 text-gray-600 hover:text-indigo-600 font-medium">Organization</a>
                        <div class="relative group">
                            <button class="flex items-center space-x-3 bg-gray-100 rounded-xl px-4 py-2 hover:bg-gray-200 transition-colors">
                                <img src="http://static.photos/people/40x40/9" alt="Profile" class="w-8 h-8 rounded-full">
                                <span class="text-gray-800 font-medium">Sarah J.</span>
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100">
                                <a href="/my/profile"volunteer.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">My Profile</a>
                                <a href="/my/settings" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Settings</a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Sign Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile Header -->
    <div class="profile-header text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center space-x-6">
                <img src="http://static.photos/people/120x120/10" alt="Profile" class="w-24 h-24 rounded-full border-4 border-white/20">
                <div>
                    <h1 class="text-3xl font-bold">Sarah Johnson</h1>
                    <p class="text-indigo-100 mt-1">Volunteer since 2018</p>
                    <div class="flex items-center space-x-4 mt-3">
                        <span class="flex items-center"><i data-feather="map-pin" class="w-4 h-4 mr-1"></i> Dubai, UAE</span>
                        <span class="flex items-center"><i data-feather="clock" class="w-4 h-4 mr-1"></i> 120 hours volunteered</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h3>
                    <div class="space-y-4">
                        <div class="stats-card bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Hours Volunteered</span>
                                <span class="text-2xl font-bold text-indigo-600">120</span>
                            </div>
                        </div>
                        <div class="stats-card bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Events Attended</span>
                                <span class="text-2xl font-bold text-emerald-600">24</span>
                            </div>
                        </div>
                        <div class="stats-card bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Skills Gained</span>
                                <span class="text-2xl font-bold text-amber-600">8</span>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mt-8 mb-4">My Skills</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">Teaching</span>
                        <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm">Mentoring</span>
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-sm">Event Planning</span>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">First Aid</span>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mt-8 mb-4">Achievements</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="badge w-8 h-8 rounded-full flex items-center justify-center text-white">
                                <i data-feather="award" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm">100+ Hours Club</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="badge w-8 h-8 rounded-full flex items-center justify-center text-white">
                                <i data-feather="star" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm">Top Mentor 2023</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Upcoming Events -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Events</h2>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div>
                                <h4 class="font-medium text-gray-900">Community Food Drive</h4>
                                <p class="text-sm text-gray-500">Saturday, Dec 16 • 9:00 AM - 12:00 PM</p>
                            </div>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Confirmed</span>
                        </div>
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div>
                                <h4 class="font-medium text-gray-900">Youth Mentorship Session</h4>
                                <p class="text-sm text-gray-500">Wednesday, Dec 20 • 4:00 PM - 6:00 PM</p>
                            </div>
                            <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Pending</span>
                        </div>
                    </div>
                </div>

                <!-- Volunteer History -->
                <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Volunteer History</h2>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                    </div>
                    <div class="space-y-4">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">Beach Cleanup Initiative</h4>
                                <span class="text-sm text-gray-500">Nov 25, 2023</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Helped clean up 2km of coastline with 50+ volunteers</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">4 hours • 12 volunteers participated</span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                            </div>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-medium text-gray-900">Food Distribution</h4>
                                <span class="text-sm text-gray-500">Nov 18, 2023</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Distributed food packages to 200+ families in need</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">6 hours • 25 volunteers participated</span>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Completed</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificates -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">My Certificates</h2>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i data-feather="award" class="text-blue-600 w-5 h-5"></i>
                                </div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">Verified</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Volunteer Leadership Certificate</h4>
                            <p class="text-sm text-gray-600 mb-3">SwaEduAE Volunteering Society</p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Issued: Dec 10, 2023</span>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800">Download</a>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i data-feather="award" class="text-green-600 w-5 h-5"></i>
                                </div>
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Verified</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">First Aid Training Certificate</h4>
                            <p class="text-sm text-gray-600 mb-3">Red Crescent Authority</p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Issued: Nov 15, 2023</span>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800">Download</a>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                    <i data-feather="award" class="text-amber-600 w-5 h-5"></i>
                                </div>
                                <span class="bg-amber-100 text-amber-800 px-2 py-1 rounded text-xs">Pending</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Youth Mentorship Program</h4>
                            <p class="text-sm text-gray-600 mb-3">SwaEduAE Education Department</p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Issued: Dec 5, 2023</span>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800">Download</a>
                            </div>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i data-feather="award" class="text-purple-600 w-5 h-5"></i>
                                </div>
                                <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">Verified</span>
                            </div>
                            <h4 class="font-medium text-gray-900 mb-2">Community Service Excellence</h4>
                            <p class="text-sm text-gray-600 mb-3">Dubai Community Development Authority</p>
                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <span>Issued: Oct 20, 2023</span>
                                <a href="#" class="text-indigo-600 hover:text-indigo-800">Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        AOS.init();
        feather.replace();
    </script>
</body>
</html>
