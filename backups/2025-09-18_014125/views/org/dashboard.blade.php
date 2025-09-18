<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organization Dashboard | SwaEduAE Volunteering Society</title>
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
        }
        .dashboard-card{background:#fff;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,.05);transition:all .3s ease}
        .dashboard-card:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.1)}
        .stat-number{font-size:2.5rem;font-weight:800;background:linear-gradient(135deg,var(--primary) 0%,var(--secondary) 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
        .nav-pill{padding:.75rem 1rem;border-radius:.5rem;transition:all .3s ease}
        .nav-pill:hover{background:#f8fafc}
        .nav-pill.active{background:var(--primary);color:#fff}
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
                    <a href="index.html" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Home</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Opportunities</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">About</a>
                    <a href="volunteer-profile.html" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Volunteers</a>
                    <a href="#" class="nav-link text-gray-600 hover:text-indigo-600 transition-colors duration-300">Contact</a>
                    <div class="flex items-center space-x-4">
                        <div class="relative group">
                            <button class="flex items-center space-x-3 bg-gray-100 rounded-xl px-4 py-2 hover:bg-gray-200 transition-colors">
                                <img src="http://static.photos/office/40x40/11" alt="Profile" class="w-8 h-8 rounded-full">
                                <span class="text-gray-800 font-medium">Hope Foundation</span>
                                <i data-feather="chevron-down" class="w-4 h-4"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl py-2 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100">
                                <a href="organization-dashboard.html" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Dashboard</a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Settings</a>
                                <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 hover:text-indigo-600">Sign Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="dashboard-card p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Organization Menu</h3>
                    <nav class="space-y-2">
                        <a href="#" class="nav-pill active flex items-center space-x-3"><i data-feather="home" class="w-5 h-5"></i><span>Dashboard</span></a>
                        <a href="#" class="nav-pill flex items-center space-x-3"><i data-feather="calendar" class="w-5 h-5"></i><span>Events</span></a>
                        <a href="#" class="nav-pill flex items-center space-x-3"><i data-feather="users" class="w-5 h-5"></i><span>Volunteers</span></a>
                        <a href="#" class="nav-pill flex items-center space-x-3"><i data-feather="bar-chart-2" class="w-5 h-5"></i><span>Analytics</span></a>
                        <a href="#" class="nav-pill flex items-center space-x-3"><i data-feather="settings" class="w-5 h-5"></i><span>Settings</span></a>
                    </nav>
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <button class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center space-x-2">
                            <i data-feather="plus" class="w-4 h-4"></i><span>Create Event</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Welcome Header -->
                <div class="dashboard-card p-6 mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome back, Hope Foundation!</h1>
                    <p class="text-gray-600">Here&apos;s what&apos;s happening with your organization today.</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="dashboard-card p-6 text-center">
                        <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="users" class="text-indigo-600 w-6 h-6"></i>
                        </div>
                        <div class="stat-number">245</div>
                        <p class="text-gray-600 mt-2">Total Volunteers</p>
                    </div>
                    <div class="dashboard-card p-6 text-center">
                        <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="calendar" class="text-emerald-600 w-6 h-6"></i>
                        </div>
                        <div class="stat-number">18</div>
                        <p class="text-gray-600 mt-2">Active Events</p>
                    </div>
                    <div class="dashboard-card p-6 text-center">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-feather="clock" class="text-amber-600 w-6 h-6"></i>
                        </div>
                        <div class="stat-number">1,240</div>
                        <p class="text-gray-600 mt-2">Hours Contributed</p>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="dashboard-card p-6 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Activity</h2>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-feather="user-plus" class="text-green-600 w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">5 new volunteers joined</p>
                                <p class="text-sm text-gray-500">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i data-feather="check-circle" class="text-blue-600 w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Food Drive completed successfully</p>
                                <p class="text-sm text-gray-500">Yesterday</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 p-4 bg-purple-50 rounded-lg">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i data-feather="message-square" class="text-purple-600 w-5 h-5"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">3 new messages from volunteers</p>
                                <p class="text-sm text-gray-500">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="dashboard-card p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Upcoming Events</h2>
                        <a href="#" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View All</a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Community Food Distribution</h4>
                                <p class="text-sm text-gray-500">December 16, 2023 • 9:00 AM - 2:00 PM</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-sm text-gray-600">📍 Downtown Dubai</span>
                                    <span class="text-sm text-gray-600">👥 15/25 volunteers</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Manage</button>
                                <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">View</button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-900">Youth Mentorship Program</h4>
                                <p class="text-sm text-gray-500">December 20, 2023 • 4:00 PM - 6:00 PM</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-sm text-gray-600">📍 Virtual Event</span>
                                    <span class="text-sm text-gray-600">👥 8/12 mentors</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Manage</button>
                                <button class="px-3 py-1 border border-gray-300 rounded-lg text-sm hover:bg-gray-50">View</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function(){
        try{ if(window.AOS) AOS.init(); }catch(e){}
        try{ if(window.feather && feather.replace) feather.replace(); }catch(e){}
      });
    </script>
</body>
</html>
