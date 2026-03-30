<!-- Mobile Hamburger Button -->
<button class="btn btn-sm d-lg-none mobile-nav-toggle" id="mobile-nav-toggle">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<!-- Mobile Logout Button -->
<a href="{{ 'logout' }}" class="btn btn-sm d-lg-none mobile-logout" id="mobile-logout">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M16 17l5-5-5-5M21 12H9M5 19H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h1" />
    </svg>
    <span class="tooltip-text">Logout</span>
</a>

<!-- Sidebar -->
<div class="deznav" id="mobile-nav">
    <div class="deznav-scroll">

        <!-- Close Button (only visible on mobile) -->
        <div class="d-lg-none" style="text-align:right; padding:10px;">
            <button id="mobile-nav-close" style="background:none; border:none; font-size:22px; cursor:pointer;">
                &times;
            </button>
        </div>

        <ul class="metismenu" id="menu">
            <li class="nav-label">Home</li>
            <li>
                <a href="{{ '/Surveyor' }}" class="ai-icon">
                    <svg id="icon-home1" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="feather feather-home">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <!--  Surveyors Management (Admin Only) -->
            @php
                // Get user role
                $userRole = Session::get('user')['role'] ?? 'surveyor';
            @endphp

            @if($userRole === 'admin')
            <li>
                <a href="{{ route('admin.addSurveyor') }}" class="ai-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <span class="nav-text">Surveyors Management</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('admin.addSurveyor') }}"> <i class="fa-solid fa-user-gear"></i>Add Surveyors</a></li>
                </ul>
            </li>
            @endif

           
            {{--
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                        <g fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path d="M4,12.2L13,14V4c4,0.6,7,4,7,8s-3.6,8-8,8S4,16.5,4,12.2Z" fill="#000"
                                opacity="0.3" />
                            <path d="M3.1,10C3.5,6.1,6.9,3,11,3v8.6L3.1,10Z" fill="#000" />
                        </g>
                    </svg>
                    <span class="nav-text">Reports</span>
                </a>
                <ul aria-expanded="false">
                    {{-- <li><a href="{{ route('survey.report') }}"> <i class="fa-solid fa-receipt"></i>Survey Fee Report</a></li> --
                </ul>
            </li>
            --}}

            <!-- Email Log (ONLY for surveyor, NOT for admin) -->
            @if($userRole !== 'admin')
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                        <g fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path d="M3 6h18v12H3z" fill="#000" opacity="0.3"/>
                            <path d="M3 6l9 6 9-6" fill="#000"/>
                        </g>
                    </svg>
                    <span class="nav-text">Email Log</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('email.logs') }}">
                            <i class="fa-solid fa-envelope"></i> Email Logs
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <!-- Documents Library (ONLY for surveyor, NOT for admin) -->
            @if($userRole !== 'admin')
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                        <g fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path d="M4 4h16v16H4z" fill="#000" opacity="0.3"/>
                            <path d="M7 7h10v2H7zm0 4h10v2H7zm0 4h6v2H7z" fill="#000"/>
                        </g>
                    </svg>
                    <span class="nav-text">Documents</span>
                </a>
                <ul aria-expanded="false">
                    <li>
                        <a href="{{ route('resources.page') }}">
                            <i class="fa-solid fa-folder-open"></i> Resource Documents 
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            <li class="nav-label">Utils</li>
            <li>
                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24">
                        <g fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <rect fill="#000" opacity="0.3" x="4" y="4" width="8" height="16" />
                            <path
                                d="M6,18h3c1,0 1,1 1,1s0,1-1,1H4v-5c0-1,1-1,1-1s1,0,1,1V18Zm12,0v-3c0-1,1-1,1-1s1,0,1,1v5h-5c-1,0-1-1-1-1s0-1,1-1h3Zm0-12h-3c-1,0-1-1-1-1s0-1,1-1h5v5c0,1-1,1-1,1s-1,0-1-1V6ZM6,6v3c0,1-1,1-1,1s-1,0-1-1V4h5c1,0,1,1,1,1s0,1-1,1H6Z"
                                fill="#000" />
                        </g>
                    </svg>
                    <span class="nav-text">Utils</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ url('/changePassword') }}"><i class="fas fa-key"></i>Change Password</a></li>
                    <li>
                        <a href="{{ url('/logout') }}" class="sidebar-logout">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 17l5-5-5-5M21 12H9M5 19H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h1" />
                            </svg>
                            Logout
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>

<!-- Mobile Overlay -->
<div class="mobile-overlay" id="mobile-overlay"></div>

<!-- Styles -->
<style>
    /* Mobile Navigation Buttons */
    .mobile-nav-toggle,
    .mobile-logout {
        position: fixed;
        right: 8px;
        z-index: 10000;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        cursor: pointer;
        transition: top 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        transform: translateY(-20px);
    }

    .mobile-logout {
        right: 60px;
        z-index: 10001 !important;
    }

    @media (max-width: 991px) {
        .mobile-nav-toggle,
        .mobile-logout {
            animation: slideDown 0.5s forwards 0.2s;
        }

        @keyframes slideDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }

    /* Tooltip for logout button */
    .mobile-logout .tooltip-text {
        visibility: hidden;
        opacity: 0;
        width: auto;
        background-color: #333;
        color: #fff;
        text-align: center;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        position: absolute;
        top: 120%;
        left: 50%;
        transform: translateX(-50%) translateY(-10px);
        white-space: nowrap;
        transition: opacity 0.3s ease, transform 0.3s ease;
        z-index: 10001;
    }

    @media (hover: hover) and (pointer: fine) {
        .mobile-logout:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    /* Desktop styles */
    @media (min-width: 992px) {
        .mobile-logout {
            display: none !important;
        }
    }

    /* Mobile styles */
    @media (max-width: 991px) {
        .sidebar-logout {
            display: none !important;
        }

        .deznav {
            position: fixed;
            top: 0;
            left: -260px;
            width: 260px;
            height: 100%;
            background: #fff;
            transition: left 0.3s ease-in-out;
            z-index: 9999;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.1);
        }

        .deznav.open {
            left: 0;
        }

        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-nav-toggle {
            top: 1vh;
        }

        .mobile-logout {
            top: 1vh;
        }

        /* Dropdown arrow styles */
        .has-arrow::after {
            content: '';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #8e9297;
            transition: transform 0.2s ease;
        }

        .has-arrow[aria-expanded="true"]::after {
            transform: translateY(-50%) rotate(180deg);
        }

        /* Dropdown animation */
        .metismenu ul {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .metismenu ul.show {
            max-height: 200px;
        }
    }

    /* Hover effects */
    .mobile-logout:hover {
        background: #f8f9fa;
    }

    /* Tablet and larger mobile styles */
    @media (min-width: 768px) and (max-width: 1224px),
    (min-width: 820px) and (max-width: 1180px),
    (min-width: 912px) and (max-width: 1368px),
    (min-width: 853px) and (max-width: 1280px) {
        .deznav {
            left: 0 !important;
            position: relative !important;
            box-shadow: none !important;
        }

        .mobile-nav-toggle {
            display: none !important;
        }

        .mobile-logout {
            display: none !important;
        }

        .sidebar-logout {
            display: block !important;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const mobileToggle = document.getElementById("mobile-nav-toggle");
        const mobileNav = document.getElementById("mobile-nav");
        const mobileClose = document.getElementById("mobile-nav-close");
        const mobileOverlay = document.getElementById("mobile-overlay");

        // Open mobile sidebar
        if (mobileToggle && mobileNav) {
            mobileToggle.addEventListener("click", () => {
                mobileNav.classList.add("open");
                if (mobileOverlay) {
                    mobileOverlay.classList.add("active");
                }
            });
        }

        // Close mobile sidebar
        if (mobileClose && mobileNav) {
            mobileClose.addEventListener("click", () => {
                mobileNav.classList.remove("open");
                if (mobileOverlay) {
                    mobileOverlay.classList.remove("active");
                }
            });
        }

        // Close sidebar when clicking overlay
        if (mobileOverlay) {
            mobileOverlay.addEventListener("click", () => {
                mobileNav.classList.remove("open");
                mobileOverlay.classList.remove("active");
            });
        }

        // Initialize MetisMenu if available
        if (typeof $('#menu').metisMenu === 'function') {
            $('#menu').metisMenu();
        }

        // Custom dropdown functionality for mobile
        const dropdownLinks = document.querySelectorAll('.has-arrow');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const submenu = this.nextElementSibling;
                const isExpanded = this.getAttribute('aria-expanded') === 'true';

                // Close other dropdowns
                dropdownLinks.forEach(otherLink => {
                    if (otherLink !== this) {
                        otherLink.setAttribute('aria-expanded', 'false');
                        const otherSub = otherLink.nextElementSibling;
                        if (otherSub) otherSub.classList.remove('show');
                    }
                });

                // Toggle current dropdown
                this.setAttribute('aria-expanded', !isExpanded);
                if (submenu) submenu.classList.toggle('show');
            });
        });

        // Touch tooltip for mobile logout button
        const logoutBtn = document.getElementById('mobile-logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('touchstart', function(e) {
                const tooltip = this.querySelector('.tooltip-text');
                if (tooltip) {
                    tooltip.style.visibility = 'visible';
                    tooltip.style.opacity = '1';
                    setTimeout(() => {
                        tooltip.style.visibility = 'hidden';
                        tooltip.style.opacity = '0';
                    }, 1500);
                }
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const isClickInsideSidebar = mobileNav.contains(event.target);
            const isClickOnToggle = mobileToggle.contains(event.target);
            const isClickOnLogout = logoutBtn.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggle && !isClickOnLogout && 
                mobileNav.classList.contains('open') && window.innerWidth <= 991) {
                mobileNav.classList.remove('open');
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('active');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 991 && mobileNav.classList.contains('open')) {
                mobileNav.classList.remove('open');
                if (mobileOverlay) {
                    mobileOverlay.classList.remove('active');
                }
            }
        });
    });
</script>