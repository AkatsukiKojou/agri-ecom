<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | AgriEcom</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="icon" href="{{ asset('agri-icon.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body x-data="{
    open: false,
    showLogin: {{ session('login_error') ? 'true' : 'false' }},
    showRegister: false,
    showVerify: false,
    registeredEmail: '',
    registerError: '',
    isRegistering: false,
    verifyError: '',
    // Register function
            async registerUser(e) {
                e.preventDefault();
                this.registerError = '';
                this.isRegistering = true;
                const form = e.target;
                const data = new FormData(form);
                // Use AbortController to timeout the request client-side after 15s
                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), 15000);
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: data,
                        redirect: 'manual',
                        signal: controller.signal
                    });
                    clearTimeout(timeout);
                    if (res.status === 302) {
                        this.registerError = 'Registration failed: Unexpected redirect. Please check your form and try again.';
                        return;
                    }
                    let json;
                    try {
                        json = await res.json();
                    } catch {
                        this.registerError = 'Registration failed: Invalid server response.';
                        return;
                    }
                    if (json.success) {
                        this.showRegister = false;
                        // Redirect to verification page and pass email
                        window.location.href = '/verify-code?email=' + encodeURIComponent(json.email);
                    } else {
                        this.registerError = json.message || 'Registration failed.';
                    }
                } catch (err) {
                    if (err.name === 'AbortError') {
                        this.registerError = 'Registration timed out. Please check your connection and try again.';
                    } else {
                        this.registerError = 'Registration failed. Please try again.';
                    }
                } finally {
                    clearTimeout(timeout);
                    this.isRegistering = false;
                }
            },
            async verifyCode(e) {
                e.preventDefault();
                this.verifyError = '';
                const form = e.target;
                const data = new FormData(form);
                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: data,
                        redirect: 'manual'
                    });
                    if (res.status === 302) {
                        this.verifyError = 'Verification failed: Unexpected redirect.';
                        return;
                    }
                    let json;
                    try {
                        json = await res.json();
                    } catch {
                        this.verifyError = 'Verification failed: Invalid server response.';
                        return;
                    }
                    if (json.success) {
                        this.showVerify = false;
                        alert('Email verified successfully!');
                        window.location.reload();
                    } else {
                        this.verifyError = json.message || 'Verification failed.';
                    }
                } catch (err) {
                    this.verifyError = 'Verification failed. Please try again.';
                }
            },
    // ...existing code...
}" class="bg-white min-h-screen flex flex-col text-green-900 font-sans">

    <!-- Navbar -->
    <nav class="fixed w-full z-40 bg-white shadow-lg border-b border-green-100">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button class="md:hidden text-green-900" @click="open = !open">
                    <i class="bi bi-list text-3xl"></i>
                </button>
                    <h2 class="text-2xl font-extrabold tracking-wide flex items-center gap-2 text-green-900">
                        <span class="flex items-center">
                            <img src="{{ asset('image/logo.png') }}" alt="AgriEcom" class="w-14 h-14 md:w-16 md:h-16 inline-block mr-2">
                            <span class="text-3xl md:text-4xl font-extrabold bg-gradient-to-r from-green-700 to-lime-400 bg-clip-text text-transparent drop-shadow-lg tracking-wider" style="letter-spacing:0.04em;">AgriEcom</span>
                        </span>
                    </h2>
            </div>
            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center justify-between w-full">
                <div class="flex-1 flex justify-center space-x-8 text-base font-semibold tracking-wide">
                    <a href="javascript:void(0)" @click="showLogin = true" class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                    <a href="javascript:void(0)" @click="showLogin = true" class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2">
                        <i class="bi bi-person-lines-fill"></i> LSA
                    </a>
                    <a href="javascript:void(0)" @click="showLogin = true" class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2">
                        <i class="bi bi-gear-wide-connected"></i> Services
                    </a>
                    <a href="javascript:void(0)" @click="showLogin = true" class="px-4 py-2 text-green-900 hover:bg-green-700 hover:text-white hover:scale-105 transition rounded flex items-center gap-2">
                        <i class="bi bi-basket-fill"></i> Products
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="javascript:void(0)" @click="showLogin = true" class="px-4 py-2 hover:bg-green-700 hover:text-white rounded text-green-900 bg-white font-semibold transition flex items-center gap-2 shadow">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="javascript:void(0)" @click="showRegister = true" class="px-4 py-2 hover:bg-green-700 hover:text-white rounded text-green-900 bg-white font-semibold transition flex items-center gap-2 shadow">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
    <div x-show="open" x-transition class="md:hidden bg-white px-4 pb-4 space-y-2 text-base font-medium tracking-wide shadow">
            <a href="javascript:void(0)" @click="showLogin = true" class="block py-2 text-green-900 hover:bg-green-700 hover:text-white rounded flex items-center gap-2"><i class="bi bi-house-door"></i> Home</a>
            <a href="javascript:void(0)" @click="showLogin = true" class="block py-2 text-green-900 hover:bg-green-700 hover:text-white rounded flex items-center gap-2"><i class="bi bi-person-lines-fill"></i> LSA</a>
            <a href="javascript:void(0)" @click="showLogin = true" class="block py-2 text-green-900 hover:bg-green-700 hover:text-white rounded flex items-center gap-2"><i class="bi bi-gear-wide-connected"></i> Services</a>
            <a href="javascript:void(0)" @click="showLogin = true" class="block py-2 text-green-900 hover:bg-green-700 hover:text-white rounded flex items-center gap-2"><i class="bi bi-basket-fill"></i> Products</a>
            <a href="javascript:void(0)" @click="showLogin = true" class="block py-2 text-green-900 hover:bg-green-700 hover:text-white rounded flex items-center gap-2"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            <a href="javascript:void(0)" @click="showRegister = true" class="block py-2 text-green-900 hover:bg-green-700 hover:text-white rounded flex items-center gap-2"><i class="bi bi-person-plus"></i> Register</a>
        </div>
    </nav>

    <!-- Main Content / Landing Page -->
    <main class="flex-grow flex flex-col items-center justify-center">
    <div class="w-full px-4 py-12">
    <div class="w-full bg-white py-16 px-2 sm:px-8 mb-12 shadow-lg rounded-2xl flex flex-col items-center">
                <img src="{{ asset('image/logo.png') }}" alt="AgriEcom Logo" class="w-24 h-24 md:w-28 md:h-28 object-contain mb-6">
                <h1 class="font-extrabold text-green-900 mb-6 tracking-tight text-3xl md:text-4xl drop-shadow-lg">AgriEcom: Your Gateway to Modern Agriculture</h1>
                <p class="text-green-800 mb-8 text-lg md:text-xl font-medium max-w-2xl text-center">Connect. Grow. Succeed.<br>Join thousands of satisfied farmers, buyers, and service providers on the Ilocos Region most trusted agri marketplace.</p>
                <ul class="flex flex-wrap justify-center gap-4 mb-8">
                    <li class="flex items-center gap-2 bg-lime-100 px-6 py-3 rounded-full text-green-800 text-base md:text-lg font-semibold shadow"><i class="bi bi-check-circle-fill text-green-600"></i> Fast & Secure Transactions</li>
                    <li class="flex items-center gap-2 bg-lime-100 px-6 py-3 rounded-full text-green-800 text-base md:text-lg font-semibold shadow"><i class="bi bi-check-circle-fill text-green-600"></i> Direct Access to Local Producers</li>
                    <li class="flex items-center gap-2 bg-lime-100 px-6 py-3 rounded-full text-green-800 text-base md:text-lg font-semibold shadow"><i class="bi bi-check-circle-fill text-green-600"></i> Exclusive Member Promos</li>
                </ul>
                <div class="flex flex-col items-center gap-3 mb-6">
                    <a href="javascript:void(0)" @click="showRegister = true" class="bg-green-700 hover:bg-green-800 text-white px-12 py-4 rounded-xl font-bold shadow-xl transition flex items-center gap-3 text-lg md:text-xl animate-bounce">
                        <i class="bi bi-person-plus"></i> Register Now – It’s Free!
                    </a>
                    <span class="text-green-700 text-base md:text-lg font-semibold">Sign up today and get <b>exclusive access</b> to new products, training services, and special offers!</span>
                </div>
            </div>
          
            <!-- Random Products & Services Section -->
            <div class="mt-16">
         
                <div class="w-full bg-white py-12 px-2 sm:px-8 rounded-2xl shadow-lg mb-12">
                    <div class="flex flex-col items-center mb-8">
                        <div class="w-24 h-1 bg-gradient-to-r from-lime-300 via-green-400 to-green-700 rounded-full mb-2"></div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-green-800 mb-2 tracking-wide flex items-center gap-2">
                            <i class="bi bi-basket-fill text-green-600"></i> Featured Products
                        </h2>
                        <p class="text-green-700 text-sm md:text-base max-w-xl text-center">Browse a selection of the latest and best agri products from our trusted sellers. Quality and freshness guaranteed!</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                        @if(isset($products) && count($products))
                            @foreach($products->take(8) as $product)
                                <div class="bg-white/90 rounded-xl shadow-lg p-6 flex flex-col items-center h-full group hover:bg-green-50 transition-all duration-200 border border-green-100 hover:border-green-400">
                                    <div class="w-28 h-28 mb-4 overflow-hidden rounded-full border-4 border-green-100 shadow flex items-center justify-center">
                                        <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/120x120?text=Product' }}" alt="" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                    <h3 class="text-lg font-bold text-green-900 mb-1">{{ $product->name }}</h3>
                                    <span class="text-green-700 font-semibold mb-2">₱{{ number_format($product->price,2) }} / {{ $product->unit ?? 'unit' }}</span>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-lime-100 text-green-800">Product</span>
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-4 text-center text-green-700">No products to display.</div>
                        @endif
                    </div>
                </div>
              
            </div>
            <div class="mt-16">
                <div class="w-full bg-white py-12 px-2 sm:px-8 rounded-2xl shadow-lg mb-12 mt-16">
                    <div class="flex flex-col items-center mb-8">
                        <div class="w-24 h-1 bg-gradient-to-r from-green-300 via-lime-400 to-green-700 rounded-full mb-2"></div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-green-800 mb-2 tracking-wide flex items-center gap-2">
                            <i class="bi bi-gear-wide-connected text-green-600"></i> Featured Training Services
                        </h2>
                        <p class="text-green-700 text-sm md:text-base max-w-xl text-center">Find and book agri-related services to help your farm or business grow. Reliable, affordable, and available near you!</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                        @if(isset($services) && count($services))
                            @foreach($services->take(8) as $service)
                                <div class="bg-white/90 rounded-xl shadow-lg p-6 flex flex-col items-center h-full group hover:bg-green-50 transition-all duration-200 border border-green-100 hover:border-green-400">
                                    <div class="w-28 h-28 mb-4 overflow-hidden rounded-full border-4 border-green-100 shadow flex items-center justify-center">
                                        <img src="{{ $service->images ? asset('storage/'.$service->images) : 'https://via.placeholder.com/120x120?text=Service' }}" alt="" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                    <h3 class="text-lg font-bold text-green-900 mb-1">{{ $service->service_name }}</h3>
                                    <span class="text-green-700 font-semibold mb-2">₱{{ number_format($service->price,2) }} / {{ $service->unit ?? 'unit' }}</span>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-200 text-green-900">Service</span>
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-4 text-center text-green-700">No services to display.</div>
                        @endif
                    </div>
                </div>
                
            </div>
               <div class="mt-16">
                <div class="w-full bg-white py-12 px-2 sm:px-8 rounded-2xl shadow-lg mb-12 mt-16">
                    <div class="flex flex-col items-center mb-8">
                        <div class="w-24 h-1 bg-gradient-to-r from-green-400 via-lime-400 to-green-700 rounded-full mb-2"></div>
                        <h2 class="text-2xl md:text-3xl font-extrabold text-green-800 mb-2 tracking-wide flex items-center gap-2">
                            <i class="bi bi-person-badge text-green-600"></i> Meet Our LSA
                        </h2>
                        <p class="text-green-700 text-sm md:text-base max-w-xl text-center">Get to know the dedicated LSA who keep AgriEcom running smoothly and support our community every day.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                        @if(isset($admins) && count($admins))
                            @foreach($admins->take(8) as $admin)
                                <div class="bg-white/90 rounded-xl shadow-lg p-6 flex flex-col items-center h-full group hover:bg-green-100 hover:shadow-2xl transition-all duration-200 border border-green-100 hover:border-green-400">
                                    <div class="w-28 h-28 mb-4 overflow-hidden rounded-full border-4 border-green-100 shadow flex items-center justify-center">
                                        <img src="{{ $admin->profile_image ? asset('storage/'.$admin->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($admin->first_name.' '.$admin->last_name) . '&background=8bc34a&color=fff&size=120' }}" alt="" class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110">
                                    </div>
                                    <h3 class="text-lg font-bold text-green-900 mb-1">{{ $admin->first_name }} {{ $admin->last_name }}</h3>
                                    <span class="text-green-700 font-semibold mb-2">{{ $admin->email }}</span>
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Admin</span>
                                </div>
                            @endforeach
                        @else
                            <div class="col-span-4 text-center text-green-700">No admins to display.</div>
                        @endif
                    </div>
                </div>
               
                 <div class="mt-12 text-center">
                <div class="w-full bg-white py-16 px-2 sm:px-8">
                    <h3 class="text-2xl md:text-3xl font-bold text-green-800 mb-4 text-center">Why AgriEcom?</h3>
                    <p class="text-green-700 mb-8 text-center text-[16px]">AgriEcom connects farmers, service providers, and buyers in one trusted platform. Discover, book, and buy with ease!</p>
                    <div class="flex flex-wrap justify-center gap-8 mt-6 w-full">
                        <div class="bg-white/90 rounded-2xl shadow p-8 w-full sm:w-80 flex flex-col items-center">
                            <i class="bi bi-people-fill text-4xl text-green-700 mb-3"></i>
                            <h4 class="font-semibold text-green-800 mb-2 text-lg">Community Driven</h4>
                            <p class="text-green-700 text-[15px] text-center">Join a growing network of agri-enthusiasts and professionals.</p>
                        </div>
                        <div class="bg-white/90 rounded-2xl shadow p-8 w-full sm:w-80 flex flex-col items-center">
                            <i class="bi bi-shield-check text-4xl text-green-700 mb-3"></i>
                            <h4 class="font-semibold text-green-800 mb-2 text-lg">Secure & Trusted</h4>
                            <p class="text-green-700 text-[15px] text-center">Your transactions and data are protected at every step.</p>
                        </div>
                        <div class="bg-white/90 rounded-2xl shadow p-8 w-full sm:w-80 flex flex-col items-center">
                            <i class="bi bi-lightning-charge text-4xl text-green-700 mb-3"></i>
                            <h4 class="font-semibold text-green-800 mb-2 text-lg">Fast & Convenient</h4>
                            <p class="text-green-700 text-[15px] text-center">Easily access products and services anytime, anywhere.</p>
                        </div>
                    </div>
                </div>
                    <div class="w-full bg-white py-12 px-2 sm:px-8">
                        <h3 class="text-2xl md:text-3xl font-bold text-green-800 mb-4 text-center">How AgriEcom Works</h3>
                        <p class="text-green-700 mb-8 text-center text-[16px]">In just 3 easy steps, you can start buying, selling, or booking services!</p>
                        <div class="flex flex-wrap justify-center gap-8 mt-6 w-full">
                            <div class="bg-white/90 rounded-2xl shadow p-8 w-full sm:w-80 flex flex-col items-center">
                                <span class="bg-green-700 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 text-lg">1</span>
                                <h4 class="font-semibold text-green-800 mb-2 text-lg">Register for Free</h4>
                                <p class="text-green-700 text-[15px] text-center">Create your account in less than 2 minutes.</p>
                            </div>
                            <div class="bg-white/90 rounded-2xl shadow p-8 w-full sm:w-80 flex flex-col items-center">
                                <span class="bg-green-700 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 text-lg">2</span>
                                <h4 class="font-semibold text-green-800 mb-2 text-lg">Explore & Connect</h4>
                                <p class="text-green-700 text-[15px] text-center">Browse products, services, and connect with trusted sellers and providers.</p>
                            </div>
                            <div class="bg-white/90 rounded-2xl shadow p-8 w-full sm:w-80 flex flex-col items-center">
                                <span class="bg-green-700 text-white rounded-full w-12 h-12 flex items-center justify-center mb-3 text-lg">3</span>
                                <h4 class="font-semibold text-green-800 mb-2 text-lg">Transact & Succeed</h4>
                                <p class="text-green-700 text-[15px] text-center">Enjoy fast, secure transactions and grow your agri-business!</p>
                            </div>
                        </div>
                        <div class="mt-10 flex justify-center">
                            <a href="javascript:void(0)" @click="showRegister = true" class="inline-flex items-center gap-2 px-10 py-4 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-xl shadow transition text-lg animate-bounce">
                                <i class="bi bi-person-plus"></i> Register Now &rarr;
                            </a>
                        </div>
                    </div>
            </div>
            </div>
           
        </div>

    <!-- LSA Description Footer -->
    <footer class="w-full bg-gradient-to-b from-green-100 via-lime-50 to-green-200 py-8 px-4 mt-8">
        <div class="max-w-3xl mx-auto text-center">
            <h4 class="text-xl md:text-2xl font-bold text-green-800 mb-2 flex items-center justify-center gap-2">
                <i class="bi bi-book-half text-green-700"></i> What is LSA?
            </h4>
            <p class="text-green-700 text-base md:text-lg">
                <b>LSA</b> stands for <b>Learning Site for Agriculture</b>. It is a special demonstration farm or facility accredited by the Department of Agriculture, designed to showcase modern, sustainable, and practical farming technologies. LSAs serve as hands-on learning hubs for farmers, students, and agri-enthusiasts to gain knowledge, skills, and inspiration for successful agricultural ventures.
            </p>
        </div>
    </footer>
    </main>


    <!-- Login Modal -->
    <div 
        x-data="{
            error: '{{ session('login_error') ?? '' }}',
            clearError() {
                this.error = '';
            },
            clearSessionError() {
                if (window.history.replaceState) {
                    window.history.replaceState(null, null, window.location.pathname);
                }
            },
            clearError() {
                this.error = '';
            },
            async login(e) {
                e.preventDefault();
                this.error = '';
                const form = e.target;
                const data = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(json => {
                    if (json.errors) {
                        if (json.errors.login) {
                            this.error = json.errors.login[0];
                        } else if (json.errors.password) {
                            this.error = json.errors.password[0];
                            form.password.value = '';
                        } else {
                            this.error = json.message || 'Incorrect email or password.';
                        }
                    } else if (json.success) {
                        this.error = '';
                        this.clearSessionError();
                        window.location.reload();
                    }
                })
                .catch(() => {
                    this.error = 'Login failed. Please try again.';
                });
            }
        }"
        x-show="showLogin" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40"
    >
        <div @click.away="showLogin = false" class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md relative">
            <button @click="showLogin = false; clearError()" class="absolute top-3 right-3 text-green-700 hover:text-red-500 text-2xl">&times;</button>
            <div class="flex flex-col items-center mb-6">
                <span class="inline-block bg-green-700 rounded-full p-4 mb-2">
                    <i class="bi bi-box-arrow-in-right text-lime-100 text-3xl"></i>
                </span>
                <h2 class="text-2xl font-extrabold text-green-800 mb-1 tracking-wide">Welcome Back!</h2>
                <p class="text-green-700 text-sm">Sign in to your AgriEcom account</p>
            </div>
           
            <form method="POST" action="{{ route('login') }}" >
                @csrf
                <template x-if="error">
                    <div class="mb-2 text-red-600 text-sm font-semibold text-center" x-text="error"></div>
                </template>
                <div>
                    <label for="login" class="block text-green-800 font-semibold mb-1">Email</label>
                    <input id="login" name="login" type="text" required autofocus autocomplete="username"
                        class="block w-full text-lg rounded-xl border-green-200 focus:border-green-500 focus:ring-green-500 bg-lime-50 mb-1 px-4 py-3"     placeholder="Enter your email or phone" />
                </div>
                <!-- Per-field error messages removed to show only the single top error -->
<div class="relative mt-2">
    <label for="login_password" class="block text-green-800 font-semibold mb-1">Password</label>
    <input id="login_password" name="password" type="password" required autocomplete="current-password"
        class="block w-full text-lg rounded-xl border-green-200 focus:border-green-500 focus:ring-green-500 bg-lime-50 mb-1 px-4 py-3 pr-10"
        placeholder="Enter your Password"  />
    <button type="button" tabindex="-1"
        class="absolute top-8 right-3 text-green-700 hover:text-green-900"
        onclick="togglePassword('login_password', this)">
        <i class="bi bi-eye"></i>
    </button>
</div>
                <!-- Per-field error messages removed to show only the single top error -->
                <div class="flex items-center justify-between mt-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-green-300 text-green-700 shadow-sm focus:ring-green-500">
                        <span class="ms-2 text-sm text-green-700">Remember me</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-900 underline">Forgot?</a>
                    @endif
                </div>
   <button type="submit"
        class="w-full mt-6 bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl shadow transition text-lg flex items-center justify-center gap-2">
        <i class="bi bi-box-arrow-in-right"></i> Log In
    </button>
            </form>
            <div class="mt-6 text-center">
                <span class="text-sm text-green-700">Don't have an account?</span>
                <a href="javascript:void(0)" @click="showLogin = false; showRegister = true" class="text-green-800 font-semibold hover:underline ml-1">Register</a>
            </div>
        </div>
    </div>
<!-- Removed duplicate <body> tag and x-data. All modals are now inside the main Alpine.js scope. -->
<!-- Register Modal -->
    <div x-show="showRegister" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 overflow-y-auto py-6">
    <div @click.away="showRegister = false" class="bg-white rounded-3xl shadow-2xl border border-green-100 w-full max-w-2xl relative max-h-[90vh] overflow-y-auto p-12 transition-all duration-300">
        <!-- ... -->
         <button @click="showRegister = false"
                class="absolute top-3 right-3 text-green-700 hover:text-red-500 text-2xl transition-colors duration-200">
                &times;
            </button>

            <!-- Header -->
            <div class="flex flex-col items-center mb-6">
                <span class="inline-block bg-green-700 rounded-full p-4 mb-2">
                    <i class="bi bi-person-plus text-lime-100 text-3xl"></i>
                </span>
                <h2 class="text-2xl font-extrabold text-green-800 mb-1 tracking-wide">Create Account</h2>
                <p class="text-green-700 text-sm">Register to join AgriEcom</p>
            </div>
        <form method="POST" action="{{ url('/register') }}" x-on:submit.prevent="registerUser" id="registerForm">
                @csrf
                <template x-if="registerError">
                    <div class="mb-2 text-red-600 text-sm font-semibold text-center" x-text="registerError"></div>
                </template>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="first_name" class="block text-green-800 font-semibold mb-1">First Name</label>
                        <input type="text" id="first_name" name="first_name" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="First Name">
                    </div>
                    <div>
                        <label for="last_name" class="block text-green-800 font-semibold mb-1">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Last Name">
                    </div>
                    <div>
                        <label for="username" class="block text-green-800 font-semibold mb-1">Username</label>
                        <input type="text" id="username" name="username" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Username">
                    </div>
                    <div>
                        <label for="phone" class="block text-green-800 font-semibold mb-1">Phone Number</label>
                        <input type="text" id="phone" name="phone" required
                            class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Phone Number" maxlength="11" pattern="[0-9]{11}" inputmode="numeric">
                    </div>
                </div>
                <template x-if="registerError && registerError.toLowerCase().includes('first name')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <template x-if="registerError && registerError.toLowerCase().includes('last name')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <div class="mb-4">
                    <label for="email" class="block text-green-800 font-semibold mb-1">Email</label>
                    <input type="email" id="email" name="email" required
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Email Address">
                </div>
                <template x-if="registerError && registerError.toLowerCase().includes('email')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <label for="address" class="block text-green-800 font-semibold mb-1">Address</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Region</label>
                        <select id="register_region" name="region" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            <option value="">Select Region</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Province</label>
                        <select id="register_province" name="province" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            <option value="">Select Province</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">City/Municipality</label>
                        <select id="register_city" name="city" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            <option value="">Select City/Municipality</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Barangay</label>
                        <select id="register_barangay" name="barangay" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                            <option value="">Select Barangay</option>
                        </select>
                    </div>
                    <div class="col-span-1 md:col-span-2">
                        <label for="street" class="block text-green-800 font-semibold mb-1">Street</label>
                        <input type="text" id="street" name="address" required class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Street, House No., etc.">
                    </div>
                </div>
                <template x-if="registerError && registerError.toLowerCase().includes('region')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <template x-if="registerError && registerError.toLowerCase().includes('province')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <template x-if="registerError && registerError.toLowerCase().includes('city')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <template x-if="registerError && registerError.toLowerCase().includes('barangay')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
<div class="relative">
    <label for="register_password" class="block text-green-800 font-semibold mb-1">Password</label>
    <input type="password" id="register_password" name="password" required
        class="mt-1 block w-full border border-gray-300 rounded-md p-2 pr-10"
        placeholder="Password">
    <button type="button" tabindex="-1"
        class="absolute top-8 right-3 text-green-700 hover:text-green-900"
        onclick="togglePassword('register_password', this)">
        <i class="bi bi-eye"></i>
    </button>
</div>
                <template x-if="registerError && registerError.toLowerCase().includes('password')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
<div class="relative">
    <label for="register_password_confirmation" class="block text-green-800 font-semibold mb-1">Confirm Password</label>
    <input type="password" id="register_password_confirmation" name="password_confirmation" required
        class="mt-1 block w-full border border-gray-300 rounded-md p-2 pr-10"
        placeholder="Confirm Password">
    <button type="button" tabindex="-1"
        class="absolute top-8 right-3 text-green-700 hover:text-green-900"
        onclick="togglePassword('register_password_confirmation', this)">
        <i class="bi bi-eye"></i>
    </button>
</div>
                <template x-if="registerError && registerError.toLowerCase().includes('confirm')">
                    <div class="mb-2 text-red-600 text-xs font-semibold" x-text="registerError"></div>
                </template>
                <button type="submit"
                    :disabled="isRegistering"
                    x-bind:class="isRegistering ? 'opacity-60 cursor-not-allowed' : ''"
                    class="w-full mt-6 bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl shadow transition text-lg flex items-center justify-center gap-2">
                    <template x-if="isRegistering">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        Registering...
                    </template>
                    <template x-if="!isRegistering">
                        <i class="bi bi-person-plus"></i> Register
                    </template>
                </button>
            </form>
        <!-- OTP Verification Modal -->
        <div x-show="showVerify" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 overflow-y-auto py-6">
            <div @click.away="showVerify = false" class="bg-white rounded-3xl shadow-2xl border border-green-100 w-full max-w-md relative max-h-[90vh] overflow-y-auto p-8 transition-all duration-300">
                <button @click="showVerify = false" class="absolute top-3 right-3 text-green-700 hover:text-red-500 text-2xl transition-colors duration-200">&times;</button>
                <div class="flex flex-col items-center mb-6">
                    <span class="inline-block bg-green-700 rounded-full p-4 mb-2">
                        <i class="bi bi-shield-check text-lime-100 text-3xl"></i>
                    </span>
                    <h2 class="text-2xl font-extrabold text-green-800 mb-1 tracking-wide">Email Verification</h2>
                    <p class="text-green-700 text-sm">Enter the 6-digit code sent to <span class="font-bold" x-text="registeredEmail"></span></p>
                </div>
                <form method="POST" action="{{ url('/verify-email') }}" x-on:submit.prevent="verifyCode">
                    @csrf
                    <template x-if="verifyError">
                        <div class="mb-2 text-red-600 text-sm font-semibold text-center" x-text="verifyError"></div>
                    </template>
                    <input type="hidden" name="email" :value="registeredEmail">
                    <div class="mb-4">
                        <label for="verification_code" class="block text-green-800 font-semibold mb-1">Verification Code</label>
                        <input type="text" id="verification_code" name="verification_code" maxlength="6" required pattern="[0-9]{6}" class="mt-1 block w-full border border-gray-300 rounded-md p-2 text-center text-lg tracking-widest" placeholder="Enter code">
                    </div>
                    <button type="submit" class="w-full mt-6 bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl shadow transition text-lg flex items-center justify-center gap-2">
                        <i class="bi bi-shield-check"></i> Verify
                    </button>
                </form>
            </div>
        </div>
          
<!-- Verification Modal removed -->
    <!-- Footer (optional) -->
      <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>

    // PSGC Dropdown logic: always repopulate regions when register modal opens
    function populateRegions() {
        const regionSelect = document.getElementById('register_region');
        if (!regionSelect) return;
        regionSelect.length = 1; // Reset except default
        axios.get('https://psgc.gitlab.io/api/regions/')
            .then(response => {
                response.data.forEach(region => {
                    let opt = document.createElement('option');
                    opt.value = region.name;
                    opt.text = region.name;
                    regionSelect.add(opt);
                });
            });
    }

    function setupDropdowns() {
        const regionSelect = document.getElementById('register_region');
        const provinceSelect = document.getElementById('register_province');
        const citySelect = document.getElementById('register_city');
        const barangaySelect = document.getElementById('register_barangay');
        if (!regionSelect || !provinceSelect || !citySelect || !barangaySelect) return;

        regionSelect.onchange = function() {
            provinceSelect.length = 1;
            citySelect.length = 1;
            barangaySelect.length = 1;
            if (!this.value) return;
            axios.get('https://psgc.gitlab.io/api/regions/')
                .then(response => {
                    let region = response.data.find(r => r.name === regionSelect.value);
                    if (!region) return;
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            response2.data.forEach(province => {
                                let opt = document.createElement('option');
                                opt.value = province.name;
                                opt.text = province.name;
                                provinceSelect.add(opt);
                            });
                        });
                });
        };

        provinceSelect.onchange = function() {
            citySelect.length = 1;
            barangaySelect.length = 1;
            if (!this.value) return;
            axios.get('https://psgc.gitlab.io/api/regions/')
                .then(response => {
                    let region = response.data.find(r => r.name === regionSelect.value);
                    if (!region) return;
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            let province = response2.data.find(p => p.name === provinceSelect.value);
                            if (!province) return;
                            // Cities
                            axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                                .then(response3 => {
                                    response3.data.forEach(city => {
                                        let opt = document.createElement('option');
                                        opt.value = city.name;
                                        opt.text = city.name + " (City)";
                                        citySelect.add(opt);
                                    });
                                })
                                .finally(() => {
                                    // Municipalities
                                    axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                        .then(response4 => {
                                            response4.data.forEach(mun => {
                                                let opt = document.createElement('option');
                                                opt.value = mun.name;
                                                opt.text = mun.name + " (Municipality)";
                                                citySelect.add(opt);
                                            });
                                        });
                                });
                        });
                });
        };

        citySelect.onchange = function() {
            barangaySelect.length = 1;
            if (!this.value) return;
            axios.get('https://psgc.gitlab.io/api/regions/')
                .then(response => {
                    let region = response.data.find(r => r.name === regionSelect.value);
                    if (!region) return;
                    axios.get(`https://psgc.gitlab.io/api/regions/${region.code}/provinces/`)
                        .then(response2 => {
                            let province = response2.data.find(p => p.name === provinceSelect.value);
                            if (!province) return;
                            axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/cities/`)
                                .then(response3 => {
                                    let city = response3.data.find(c => c.name === citySelect.value.replace(' (City)', ''));
                                    if (city) {
                                        axios.get(`https://psgc.gitlab.io/api/cities/${city.code}/barangays/`)
                                            .then(response4 => {
                                                response4.data.forEach(barangay => {
                                                    let opt = document.createElement('option');
                                                    opt.value = barangay.name;
                                                    opt.text = barangay.name;
                                                    barangaySelect.add(opt);
                                                });
                                            });
                                    } else {
                                        // Try as municipality
                                        axios.get(`https://psgc.gitlab.io/api/provinces/${province.code}/municipalities/`)
                                            .then(response5 => {
                                                let mun = response5.data.find(m => m.name === citySelect.value.replace(' (Municipality)', ''));
                                                if (mun) {
                                                    axios.get(`https://psgc.gitlab.io/api/municipalities/${mun.code}/barangays/`)
                                                        .then(response6 => {
                                                            response6.data.forEach(barangay => {
                                                                let opt = document.createElement('option');
                                                                opt.value = barangay.name;
                                                                opt.text = barangay.name;
                                                                barangaySelect.add(opt);
                                                            });
                                                        });
                                                }
                                            });
                                    }
                                });
                        });
                });
        };
    }


    // Always repopulate regions when modal opens, and if dropdown is empty
    document.addEventListener('DOMContentLoaded', function () {
        setupDropdowns();
        function tryPopulateRegions() {
            const regionSelect = document.getElementById('register_region');
            if (regionSelect && regionSelect.options.length <= 1) {
                populateRegions();
            }
            // Always reset province/city/barangay
            const provinceSelect = document.getElementById('register_province');
            const citySelect = document.getElementById('register_city');
            const barangaySelect = document.getElementById('register_barangay');
            if (provinceSelect) provinceSelect.length = 1;
            if (citySelect) citySelect.length = 1;
            if (barangaySelect) barangaySelect.length = 1;
        }

        // Watch for modal open
        document.querySelectorAll('[x-show="showRegister"]').forEach(function(modal) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (modal.style.display !== 'none') {
                        tryPopulateRegions();
                    }
                });
            });
            observer.observe(modal, { attributes: true, attributeFilter: ['style'] });
        });

        // Also try to populate on first load
        tryPopulateRegions();
    });
    </script>

</body>
</html>
