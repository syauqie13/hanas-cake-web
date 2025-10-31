<div>
    <!-- Navbar Background -->
    <div class="navbar-bg" style="background-color: #34395e;"></div>

    <!-- Navbar -->
    <nav class="shadow-sm navbar navbar-expand-lg main-navbar">
        <!-- Left Section: Toggle & Search -->
        <form class="mr-auto form-inline">
            <ul class="mr-3 navbar-nav align-items-center">
                <!-- Sidebar Toggle -->
                <li>
                    <a href="{{ route('karyawan.dashboard') }}" data-toggle="sidebar" class="text-white nav-link nav-link-lg">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>

                <!-- Search (Mobile) -->
                <li class="d-sm-none">
                    <a href="#" data-toggle="search" class="text-white nav-link nav-link-lg">
                        <i class="fas fa-search"></i>
                    </a>
                </li>
            </ul>

            <!-- Search Input -->
            <div class="search-element">
                <input class="form-control rounded-pill" type="search" placeholder="Cari sesuatu..." aria-label="Search" data-width="250">
                <button class="ml-2 btn btn-primary rounded-pill" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        <!-- Right Section: Messages, Notifications, User -->
        <ul class="navbar-nav navbar-right align-items-center">
            <!-- Messages -->
            <li class="dropdown dropdown-list-toggle">
                <a href="#" data-toggle="dropdown" class="text-white nav-link nav-link-lg message-toggle beep">
                    <i class="far fa-envelope"></i>
                </a>
                <div class="border-0 shadow-lg dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="text-white dropdown-header bg-primary d-flex justify-content-between align-items-center">
                        Pesan
                        <a href="#" class="text-light small">Tandai Dibaca</a>
                    </div>

                    <div class="dropdown-list-content dropdown-list-message">
                        <a href="#" class="dropdown-item dropdown-item-unread">
                            <div class="dropdown-item-avatar">
                                <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="rounded-circle">
                                <div class="is-online"></div>
                            </div>
                            <div class="dropdown-item-desc">
                                <b>Kusnaedi</b>
                                <p>Hello, Bro!</p>
                                <div class="time text-primary">10 Jam Lalu</div>
                            </div>
                        </a>
                    </div>
                    <div class="text-center dropdown-footer">
                        <a href="#">Lihat Semua <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </li>

            <!-- Notifications -->
            <li class="dropdown dropdown-list-toggle">
                <a href="#" data-toggle="dropdown" class="text-white nav-link nav-link-lg notification-toggle beep">
                    <i class="far fa-bell"></i>
                </a>
                <div class="border-0 shadow-lg dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="text-white dropdown-header bg-primary d-flex justify-content-between align-items-center">
                        Notifikasi
                        <a href="#" class="text-light small">Tandai Dibaca</a>
                    </div>
                    <div class="dropdown-list-content dropdown-list-icons">
                        <a href="#" class="dropdown-item dropdown-item-unread">
                            <div class="text-white dropdown-item-icon bg-success">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="dropdown-item-desc">
                                Transaksi POS berhasil disimpan!
                                <div class="time text-primary">Baru saja</div>
                            </div>
                        </a>
                    </div>
                    <div class="text-center dropdown-footer">
                        <a href="#">Lihat Semua <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </li>

            <!-- User Dropdown -->
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="text-white nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <img alt="image" src="{{ asset('assets/img/avatar/avatar-1.png') }}" class="mr-2 rounded-circle" width="35">
                    <span>{{ optional(auth()->user())->name }}</span>
                    <small class="ml-1 text-light">| {{ optional(auth()->user())->role }}</small>
                </a>
                <div class="border-0 shadow dropdown-menu dropdown-menu-right">
                    <div class="text-center dropdown-title small text-muted">Login 5 menit lalu</div>
                    <a href="#" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> Profil
                    </a>
                    <a href="#" class="dropdown-item has-icon">
                        <i class="fas fa-cog"></i> Pengaturan
                    </a>
                    <div class="dropdown-divider"></div>
                    <livewire:auth.logout />
                </div>
            </li>
        </ul>
    </nav>
</div>
